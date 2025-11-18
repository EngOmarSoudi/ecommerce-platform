<?php

namespace App\Services\Shipping;

use App\Contracts\CarrierAdapterInterface;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AramexCarrierAdapter implements CarrierAdapterInterface
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $accountNumber;

    public function __construct()
    {
        $this->apiUrl = config('services.aramex.api_url');
        $this->apiKey = config('services.aramex.api_key');
        $this->accountNumber = config('services.aramex.account_number');
    }

    public function createShipment(Order $order, array $data): array
    {
        // Aramex API Integration
        $payload = [
            'AccountNumber' => $this->accountNumber,
            'Shipper' => [
                'Name' => config('app.name'),
                'Address' => $data['origin_address'] ?? [],
            ],
            'Consignee' => [
                'Name' => $order->shippingAddress->full_name,
                'Address' => [
                    'Line1' => $order->shippingAddress->address_line_1,
                    'City' => $order->shippingAddress->city,
                    'StateOrProvinceCode' => $order->shippingAddress->state,
                    'PostCode' => $order->shippingAddress->postal_code,
                    'CountryCode' => $order->shippingAddress->country,
                ],
                'Phone' => $order->shippingAddress->phone,
            ],
            'Shipment' => [
                'Weight' => $data['weight'] ?? 1,
                'NumberOfPieces' => $order->items->count(),
                'Description' => "Order #{$order->order_number}",
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/shipments/create", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => true,
                    'awb_number' => $result['AWBNumber'] ?? null,
                    'tracking_url' => $result['TrackingURL'] ?? null,
                    'label_url' => $result['LabelURL'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Shipment creation failed',
            ];

        } catch (\Exception $e) {
            Log::error('Aramex shipment creation failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getRates(array $shipmentData): array
    {
        // TODO: Implement Aramex rate calculation API
        return [
            'rates' => [
                ['service' => 'Express', 'cost' => 25.00, 'currency' => 'USD'],
                ['service' => 'Standard', 'cost' => 15.00, 'currency' => 'USD'],
            ],
        ];
    }

    public function trackShipment(string $awbNumber): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->get("{$this->apiUrl}/tracking/{$awbNumber}");

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'unknown', 'error' => 'Tracking failed'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function cancelShipment(string $awbNumber): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->delete("{$this->apiUrl}/shipments/{$awbNumber}");

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Aramex shipment cancellation failed', ['awb' => $awbNumber, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function generateLabel(Shipment $shipment): string
    {
        // Return URL to label PDF
        return "{$this->apiUrl}/labels/{$shipment->awb_number}";
    }

    public function getCarrierName(): string
    {
        return 'aramex';
    }
}
