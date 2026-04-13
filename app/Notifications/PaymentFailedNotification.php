<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Order $order) {}

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /** @param User $notifiable */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Payment Failed — '.$this->order->event->title)
            ->greeting(sprintf('Hi %s,', $notifiable->name))
            ->line(sprintf('Your payment for **%s** could not be processed.', $this->order->event->title))
            ->line('Please try again with a different payment method.')
            ->action('View Order', route('orders.show', $this->order));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment Failed',
            'body' => sprintf('Your payment for %s could not be processed.', $this->order->event->title),
            'url' => route('orders.show', $this->order),
        ];
    }
}
