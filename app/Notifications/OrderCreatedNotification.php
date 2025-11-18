<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $orderId,
        public float $amount
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your order has been placed')
            ->line('Thank you for your purchase!')
            ->line('Order ID: '.$this->orderId)
            ->line('Amount: $'.number_format($this->amount, 2))
            ->action('View Order', url('/orders/'.$this->orderId));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->orderId,
            'amount' => $this->amount,
            'status' => 'created',
        ];
    }
}
