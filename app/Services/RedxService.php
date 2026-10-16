<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedxService
{
    protected string $baseUrl;

    protected string $apiToken;

    public function __construct()
    {
        $this->baseUrl = (string) (Setting::get('courier_redx_api_url') ?? 'https://openapi.redx.com.bd/v1.0.0-beta');
        $this->apiToken = (string) (Setting::get('courier_redx_api_key') ?? '');
    }

    public function isEnabled(): bool
    {
        return Setting::get('courier_redx_enabled') === '1'
            && $this->apiToken !== '';
    }

    protected function authHeaders(): array
    {
        $token = str_starts_with($this->apiToken, 'Bearer ') ? $this->apiToken : 'Bearer '.$this->apiToken;

        return [
            'API-ACCESS-TOKEN' => $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function fetchAreas(): array
    {
        return Cache::remember('redx_areas', 3600, function () {
            try {
                $response = Http::withHeaders($this->authHeaders())
                    ->timeout(30)
                    ->get($this->baseUrl.'/areas');

                $body = $response->json();

                if ($response->successful() && isset($body['areas'])) {
                    Log::info('RedX areas fetched', ['count' => count($body['areas'])]);

                    return $body['areas'];
                }

                Log::warning('RedX areas fetch failed', ['status' => $response->status(), 'body' => $body]);

                return [];
            } catch (\Exception $e) {
                Log::error('RedX areas fetch exception', ['error' => $e->getMessage()]);

                return [];
            }
        });
    }

    protected function findAreaForOrder(Order $order): array
    {
        $areas = $this->fetchAreas();
        if (empty($areas)) {
            return ['delivery_area' => $order->district ?? 'Dhaka', 'delivery_area_id' => 1];
        }

        $district = $order->district;
        $thana = $order->thana;

        $districtAreas = array_filter($areas, fn ($a) => strcasecmp($a['district_name'] ?? '', $district) === 0);

        if (! empty($districtAreas) && $thana) {
            $thanaArea = array_values(array_filter($districtAreas, function ($a) use ($thana) {
                $name = strtolower($a['name'] ?? '');
                $search = strtolower($thana);

                return str_contains($name, $search) || str_contains($search, $name);
            }));

            if (! empty($thanaArea)) {
                return [
                    'delivery_area' => $thanaArea[0]['name'],
                    'delivery_area_id' => $thanaArea[0]['id'],
                ];
            }
        }

        if (! empty($districtAreas)) {
            $first = array_values($districtAreas)[0];

            return [
                'delivery_area' => $first['name'],
                'delivery_area_id' => $first['id'],
            ];
        }

        return ['delivery_area' => $district ?? 'Dhaka', 'delivery_area_id' => 1];
    }

    public function createConsignment(Order $order): array
    {
        if (! $this->isEnabled()) {
            return ['success' => false, 'message' => 'RedX courier is not enabled or credentials are missing.'];
        }

        $area = $this->findAreaForOrder($order);

        $payload = [
            'customer_name' => trim($order->first_name.' '.($order->last_name ?? '')),
            'customer_phone' => $order->phone,
            'delivery_area' => $area['delivery_area'],
            'delivery_area_id' => $area['delivery_area_id'],
            'customer_address' => $order->address ?? $order->district ?? 'N/A',
            'cash_collection_amount' => (string) $order->total_price,
            'parcel_weight' => '0.5',
            'value' => (string) $order->total_price,
            'merchant_invoice_id' => $order->order_number,
        ];

        if ($order->order_note) {
            $payload['instruction'] = $order->order_note;
        }

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(30)
                ->post($this->baseUrl.'/parcel', $payload);

            $body = $response->json();

            Log::info('RedX create response', ['status' => $response->status(), 'body' => $body, 'payload' => $payload]);

            if ($response->successful() && isset($body['tracking_id'])) {
                $trackingId = $body['tracking_id'];

                $order->update([
                    'consignment_id' => $trackingId,
                    'tracking_code' => $trackingId,
                    'tracking_link' => 'https://redx.com.bd/track-global-parcel/?trackingId='.$trackingId,
                    'courier_status' => 'pending',
                    'courier_name' => 'redx',
                    'courier_pushed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'consignment_id' => $trackingId,
                    'tracking_code' => $trackingId,
                    'status' => 'pending',
                    'message' => 'Consignment created successfully via RedX.',
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? $body['error'] ?? ('API error (HTTP '.$response->status().')'),
            ];
        } catch (\Exception $e) {
            Log::error('RedX API exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
            ];
        }
    }

    public function trackByConsignmentId(string $consignmentId): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(15)
                ->get($this->baseUrl.'/parcel/track/'.$consignmentId);

            $body = $response->json();

            Log::info('RedX track response', ['consignment_id' => $consignmentId, 'status' => $response->status(), 'body' => $body]);

            if ($response->successful() && isset($body['tracking'])) {
                $tracking = $body['tracking'];

                return [
                    'success' => true,
                    'status' => $tracking['status'] ?? null,
                    'updated_at' => $tracking['updated_at'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('RedX track exception', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
