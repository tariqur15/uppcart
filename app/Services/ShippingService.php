<?php

namespace App\Services;

use App\Models\Setting;

class ShippingService
{
    public function zones(): array
    {
        $raw = Setting::get('shipping_zones');
        $zones = json_decode((string) $raw, true);

        return is_array($zones) ? $zones : [];
    }

    public function isEmpty(): bool
    {
        return count($this->zones()) === 0;
    }

    public function hasZones(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Find the zone that covers the given address (upazila, district, or division).
     *
     * @param  string|null  $district
     * @param  string|null  $division
     * @param  string|null  $upazila
     * @return array{zone: array, index: int}|null
     */
    public function zoneForDistrict(?string $district, ?string $division = null, ?string $upazila = null): ?array
    {
        $district = mb_strtolower(trim((string) $district));
        $division = mb_strtolower(trim((string) $division));
        $upazila = mb_strtolower(trim((string) $upazila));

        foreach ($this->zones() as $index => $zone) {
            foreach ($zone['regions'] ?? [] as $region) {
                $region = mb_strtolower(trim((string) $region));

                if ($region === 'all') {
                    return ['zone' => $zone, 'index' => $index];
                }

                if ($upazila !== '' && $upazila === $region) {
                    return ['zone' => $zone, 'index' => $index];
                }

                if ($district !== '' && $district === $region) {
                    return ['zone' => $zone, 'index' => $index];
                }

                if ($division !== '' && $division === $region) {
                    return ['zone' => $zone, 'index' => $index];
                }
            }
        }

        return null;
    }

    /**
     * First enabled zone (used as a default before an address is provided).
     *
     * @return array|null
     */
    public function defaultZone(): ?array
    {
        if ($this->isEmpty()) {
            return null;
        }

        return ['zone' => $this->zones()[0], 'index' => 0];
    }

    /**
     * Build the list of enabled methods for a zone, each with a stable id.
     *
     * @return array<int, array{id: string, type: string, title: string, description: string, amount: float, per_kg: float}>
     */
    public function methodsForZone(?array $matched, ?string $district = null, ?string $division = null): array
    {
        $resolved = $matched ?? $this->zoneForDistrict($district, $division) ?? $this->defaultZone();

        if ($resolved === null) {
            return [];
        }

        $methods = [];
        foreach ($resolved['zone']['methods'] ?? [] as $methodIndex => $method) {
            if (empty($method['enabled'])) {
                continue;
            }

            $methods[] = [
                'id' => $this->methodId($resolved['index'], $methodIndex),
                'type' => (string) ($method['type'] ?? 'flat_rate'),
                'title' => (string) ($method['title'] ?? 'Delivery'),
                'description' => (string) ($method['description'] ?? ''),
                'amount' => (float) ($method['amount'] ?? 0),
                'per_kg' => (float) ($method['per_kg'] ?? 0),
            ];
        }

        return $methods;
    }

    public function methodId(int $zoneIndex, int $methodIndex): string
    {
        return 'z'.$zoneIndex.'m'.$methodIndex;
    }

    public function chargeForMethod(array $method, float $weight): float
    {
        $weight = max(0.0, $weight);

        return match ($method['type'] ?? 'flat_rate') {
            'free' => 0.0,
            'weight_based' => (float) ($method['amount'] ?? 0) + ((float) ($method['per_kg'] ?? 0) * $weight),
            default => (float) ($method['amount'] ?? 0),
        };
    }
}
