<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OrderConfirmedNotification extends Notification implements ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Order Confirmed — '.$this->order->event->title)
            ->greeting(sprintf('Hi %s,', $notifiable->name))
            ->line(sprintf('Your order for **%s** has been confirmed.', $this->order->event->title))
            ->line('Booking references: '.$this->order->tickets->pluck('booking_reference')->implode(', '))
            ->action('View Order', route('orders.show', $this->order));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order Confirmed',
            'body' => sprintf('Your order for %s has been confirmed.', $this->order->event->title),
            'url' => route('orders.show', $this->order),
        ];
    }
}
