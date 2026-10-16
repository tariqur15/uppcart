<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FraudCheckService
{
    protected string $baseUrl = 'https://api.bdcourier.com';

    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string) (Setting::get('fraud_check_api_key') ?? '');
    }

    public function isEnabled(): bool
    {
        return Setting::get('fraud_check_enabled') === '1'
            && $this->apiKey !== '';
    }

    public function getBaseUrl(): string
    {
        return (string) (Setting::get('fraud_check_api_url') ?? $this->baseUrl);
    }

    /**
     * @return array{success: bool, message?: string, data?: array, reports?: array}
     */
    public function checkByPhone(string $phone): array
    {
        if (! $this->isEnabled()) {
            return ['success' => false, 'message' => 'Fraud check is not enabled or API key is missing.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(15)->post($this->getBaseUrl().'/courier-check', [
                'phone' => $phone,
            ]);

            if ($response->failed()) {
                Log::warning('FraudCheck API error', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return ['success' => false, 'message' => 'API returned status '.$response->status()];
            }

            $body = $response->json();

            if (($body['status'] ?? '') !== 'success') {
                return ['success' => false, 'message' => $body['message'] ?? 'Unknown API error'];
            }

            return [
                'success' => true,
                'data' => $body['data'] ?? [],
                'reports' => $body['reports'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('FraudCheck API exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Connection failed: '.$e->getMessage()];
        }
    }
}
