<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class OrderConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject("Order Confirmed — {$this->order->event->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your order for **{$this->order->event->title}** has been confirmed.")
            ->line('Booking references: '.$this->order->tickets->pluck('booking_reference')->implode(', '))
            ->action('View Order', route('orders.show', $this->order));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Order Confirmed',
            'body' => "Your order for {$this->order->event->title} has been confirmed.",
            'url' => route('orders.show', $this->order),
        ];
    }
}
