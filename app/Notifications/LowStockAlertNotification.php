<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $skuId,
        public int $warehouseId,
        public int $availableQuantity
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low stock alert')
            ->line('SKU ID: '.$this->skuId)
            ->line('Warehouse ID: '.$this->warehouseId)
            ->line('Available Quantity: '.$this->availableQuantity)
            ->action('Manage Inventory', url('/admin/stock-levels'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'sku_id' => $this->skuId,
            'warehouse_id' => $this->warehouseId,
            'available_quantity' => $this->availableQuantity,
            'type' => 'low_stock',
        ];
    }
}
