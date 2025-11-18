<?php

namespace App\Services;

use App\Contracts\CarrierAdapterInterface;
use App\Models\Address;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingZone;
use App\Models\ShippingRate;
use Illuminate\Support\Facades\DB;

class ShippingService
{
    protected CarrierAdapterInterface $carrier;

    public function __construct(CarrierAdapterInterface $carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * Calculate shipping rates for address
     */
    public function calculateRates(Address $address, float $weight, int $quantity = 1): array
    {
        $zone = ShippingZone::where('is_active', true)
            ->get()
            ->first(fn($zone) => $zone->containsAddress($address));

        if (!$zone) {
            return [];
        }

        $rates = ShippingRate::where('shipping_zone_id', $zone->id)
            ->where('is_active', true)
            ->where(function($query) use ($weight) {
                $query->where('min_weight', '<=', $weight)
                    ->orWhereNull('min_weight');
            })
            ->where(function($query) use ($weight) {
                $query->where('max_weight', '>=', $weight)
                    ->orWhereNull('max_weight');
            })
            ->get();

        return $rates->map(function($rate) use ($weight, $quantity) {
            return [
                'id' => $rate->id,
                'name' => $rate->name,
                'cost' => $rate->calculateCost($weight, [], $quantity),
                'estimated_days' => "{$rate->estimated_days_min}-{$rate->estimated_days_max}",
                'carrier' => $rate->carrier->name ?? 'Standard',
            ];
        })->toArray();
    }

    /**
     * Create shipment for order
     */
    public function createShipment(Order $order, array $data): Shipment
    {
        return DB::transaction(function () use ($order, $data) {
            // Call carrier API
            $result = $this->carrier->createShipment($order, $data);

            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'Shipment creation failed');
            }

            // Create shipment record
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier_id' => $data['carrier_id'] ?? null,
                'awb_number' => $result['awb_number'],
                'status' => 'pending',
                'weight' => $data['weight'] ?? 0,
                'tracking_url' => $result['tracking_url'] ?? null,
                'label_url' => $result['label_url'] ?? null,
            ]);

            // Update order status
            $order->update(['status' => 'processing']);

            return $shipment;
        });
    }

    /**
     * Track shipment
     */
    public function trackShipment(Shipment $shipment): array
    {
        $tracking = $this->carrier->trackShipment($shipment->awb_number);

        // Update shipment status based on tracking
        if (isset($tracking['status'])) {
            $shipment->update(['status' => $this->mapCarrierStatus($tracking['status'])]);
        }

        return $tracking;
    }

    /**
     * Map carrier status to internal status
     */
    protected function mapCarrierStatus(string $carrierStatus): string
    {
        return match(strtolower($carrierStatus)) {
            'pending', 'created' => 'pending',
            'picked_up', 'in_transit' => 'in_transit',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'failed', 'returned' => 'failed',
            default => 'pending',
        };
    }
}
