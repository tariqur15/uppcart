<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastService
{
    protected string $baseUrl = 'https://portal.packzy.com/api/v1';

    protected string $apiKey;

    protected string $secretKey;

    public function __construct()
    {
        $this->apiKey = (string) (Setting::get('courier_steadfast_api_key') ?? '');
        $this->secretKey = (string) (Setting::get('courier_steadfast_secret') ?? '');
    }

    public function isEnabled(): bool
    {
        return Setting::get('courier_steadfast_enabled') === '1'
            && $this->apiKey !== ''
            && $this->secretKey !== '';
    }

    public function createConsignment(Order $order): array
    {
        if (! $this->isEnabled()) {
            return ['success' => false, 'message' => 'Steadfast courier is not enabled or credentials are missing.'];
        }

        $merchantId = (string) (Setting::get('courier_steadfast_merchant_id') ?? '');

        $payload = [
            'invoice' => $order->order_number,
            'recipient_name' => $order->first_name.' '.$order->last_name,
            'recipient_phone' => $order->phone,
            'recipient_address' => $order->address ?? $order->district ?? 'N/A',
            'cod_amount' => (float) $order->total_price,
            'note' => $order->order_note ?? '',
            'delivery_type' => 0,
            'merchant_id' => $merchantId !== '' ? $merchantId : null,
        ];

        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl.'/create_order', $payload);

            $body = $response->json();

            if ($response->successful() && isset($body['consignment'])) {
                $consignment = $body['consignment'];

                Log::info('Steadfast create response', ['body' => $body]);

                $order->update([
                    'consignment_id' => $consignment['consignment_id'] ?? null,
                    'tracking_code' => $consignment['tracking_code'] ?? null,
                    'tracking_link' => $consignment['tracking_link'] ?? null,
                    'courier_status' => $consignment['status'] ?? 'in_review',
                    'courier_name' => 'steadfast',
                    'courier_pushed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'consignment_id' => $consignment['consignment_id'] ?? null,
                    'tracking_code' => $consignment['tracking_code'] ?? null,
                    'status' => $consignment['status'] ?? null,
                    'message' => $body['message'] ?? 'Consignment created successfully.',
                ];
            }

            Log::warning('Steadfast API error', ['status' => $response->status(), 'body' => $body]);

            return [
                'success' => false,
                'message' => $body['message'] ?? ('API error (HTTP '.$response->status().'): '.($body ?? 'Unknown error')),
            ];
        } catch (\Exception $e) {
            Log::error('Steadfast API exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
            ];
        }
    }

    public function trackByTrackingCode(string $trackingCode): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->get($this->baseUrl.'/status_by_trackingcode/'.$trackingCode);

            $body = $response->json();

            if ($response->successful() && isset($body['delivery_status'])) {
                Log::info('Steadfast track response', ['tracking_code' => $trackingCode, 'body' => $body]);

                return [
                    'success' => true,
                    'status' => $body['delivery_status'],
                    'consignment_id' => $body['consignment_id'] ?? null,
                    'tracking_code' => $body['tracking_code'] ?? null,
                    'public_tracking_code' => $body['public_tracking_code'] ?? null,
                    'tracking_url' => $body['tracking_url'] ?? null,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Steadfast track exception', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
