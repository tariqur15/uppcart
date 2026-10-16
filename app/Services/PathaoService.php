<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoService
{
    protected string $baseUrl;

    protected string $clientId;

    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = (string) (Setting::get('courier_pathao_api_url') ?? 'https://merchant-api-live.pathao.com');
        $this->clientId = (string) (Setting::get('courier_pathao_merchant_id') ?? '');
        $this->clientSecret = (string) (Setting::get('courier_pathao_secret_key') ?? '');
    }

    public function isEnabled(): bool
    {
        return Setting::get('courier_pathao_enabled') === '1'
            && $this->clientId !== ''
            && $this->clientSecret !== '';
    }

    protected function getAccessToken(): ?string
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl.'/aladdin/api/v1/external/login', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['access_token'])) {
                return $body['access_token'];
            }

            Log::warning('Pathao token error', ['status' => $response->status(), 'body' => $body]);

            return null;
        } catch (\Exception $e) {
            Log::error('Pathao token exception', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function createConsignment(Order $order): array
    {
        if (! $this->isEnabled()) {
            return ['success' => false, 'message' => 'Pathao courier is not enabled or credentials are missing.'];
        }

        $token = $this->getAccessToken();
        if (! $token) {
            return ['success' => false, 'message' => 'Failed to authenticate with Pathao. Check your Client ID and Secret.'];
        }

        $storeId = (int) (Setting::get('courier_pathao_store_id') ?? 0);

        $payload = [
            'store_id' => $storeId,
            'merchant_order_id' => $order->order_number,
            'recipient_name' => $order->first_name.' '.$order->last_name,
            'recipient_phone' => $order->phone,
            'recipient_address' => $order->address ?? $order->district ?? 'N/A',
            'delivery_type' => 48,
            'item_type' => 2,
            'item_quantity' => max(1, $order->items->sum('quantity')),
            'item_weight' => 0.5,
            'amount_to_collect' => (int) $order->total_price,
            'item_description' => $order->items->pluck('product.name')->filter()->implode(', ') ?: 'Order '.$order->order_number,
            'special_instruction' => $order->order_note ?? '',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'source' => 'web',
            ])->timeout(30)->post($this->baseUrl.'/aladdin/api/v1/orders', $payload);

            $body = $response->json();

            Log::info('Pathao API response', ['status' => $response->status(), 'body' => $body]);

            if ($response->successful() && isset($body['data']['consignment_id'])) {
                $data = $body['data'];

                $order->update([
                    'consignment_id' => $data['consignment_id'] ?? null,
                    'tracking_code' => $data['tracking_code'] ?? ($data['consignment_id'] ?? null),
                    'tracking_link' => 'https://merchant.pathao.com/tracking?consignment_id='.($data['consignment_id'] ?? '').'&phone='.$order->phone,
                    'courier_status' => $data['order_status'] ?? 'pending',
                    'courier_name' => 'pathao',
                    'courier_pushed_at' => now(),
                ]);

                return [
                    'success' => true,
                    'consignment_id' => $data['consignment_id'] ?? null,
                    'tracking_code' => $data['tracking_code'] ?? null,
                    'delivery_fee' => $data['delivery_fee'] ?? null,
                    'status' => $data['order_status'] ?? null,
                    'message' => 'Consignment created successfully.',
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? ('API error (HTTP '.$response->status().')'),
            ];
        } catch (\Exception $e) {
            Log::error('Pathao API exception', ['error' => $e->getMessage()]);

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

        $token = $this->getAccessToken();
        if (! $token) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Accept' => 'application/json',
            ])->timeout(15)->get($this->baseUrl.'/aladdin/api/v1/orders/'.$consignmentId.'/info');

            $body = $response->json();

            Log::info('Pathao track response', ['consignment_id' => $consignmentId, 'status' => $response->status(), 'body' => $body]);

            if ($response->successful() && isset($body['data'])) {
                $data = $body['data'];

                return [
                    'success' => true,
                    'status' => $data['order_status'] ?? null,
                    'status_slug' => $data['order_status_slug'] ?? null,
                    'updated_at' => $data['updated_at'] ?? null,
                    'consignment' => $data,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Pathao track exception', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
