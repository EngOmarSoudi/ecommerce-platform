<?php

namespace App\Contracts;

use App\Models\Order;
use App\Models\Shipment;

interface CarrierAdapterInterface
{
    /**
     * Create shipment with carrier
     */
    public function createShipment(Order $order, array $data): array;

    /**
     * Get shipping rates quote
     */
    public function getRates(array $shipmentData): array;

    /**
     * Track shipment by AWB number
     */
    public function trackShipment(string $awbNumber): array;

    /**
     * Cancel shipment
     */
    public function cancelShipment(string $awbNumber): bool;

    /**
     * Generate shipping label
     */
    public function generateLabel(Shipment $shipment): string;

    /**
     * Get carrier name
     */
    public function getCarrierName(): string;
}
