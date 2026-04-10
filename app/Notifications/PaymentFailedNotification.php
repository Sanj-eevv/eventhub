<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PaymentFailedNotification extends Notification implements ShouldQueue
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
            ->subject("Payment Failed — {$this->order->event->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your payment for **{$this->order->event->title}** could not be processed.")
            ->line('Please try again with a different payment method.')
            ->action('View Order', route('orders.show', $this->order));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment Failed',
            'body' => "Your payment for {$this->order->event->title} could not be processed.",
            'url' => route('orders.show', $this->order),
        ];
    }
}
