<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use App\ValueObjects\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class RefundCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly int $refundAmount,
    ) {}

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /** @param User $notifiable */
    public function toMail(object $notifiable): MailMessage
    {
        $formatted = Money::fromCents($this->refundAmount, $this->order->currency)->format();

        return (new MailMessage())
            ->subject('Refund Processed — '.$this->order->event->title)
            ->greeting(sprintf('Hi %s,', $notifiable->name))
            ->line(sprintf('Your refund for **%s** has been processed.', $this->order->event->title))
            ->line('Refund amount: '.$formatted)
            ->action('View Order', route('orders.show', $this->order));
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Refund Processed',
            'body' => sprintf('Your refund for %s has been processed.', $this->order->event->title),
            'url' => route('orders.show', $this->order),
        ];
    }
}
