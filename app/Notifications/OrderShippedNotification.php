<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public ?string $trackingUrl
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your order has been shipped')
            ->line('Order ID: '.$this->orderId)
            ->line('We will deliver your items soon.');

        if ($this->trackingUrl) {
            $mail->action('Track Shipment', $this->trackingUrl);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->orderId,
            'status' => 'shipped',
            'tracking_url' => $this->trackingUrl,
        ];
    }
}
