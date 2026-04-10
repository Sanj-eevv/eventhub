<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
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

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $formatted = number_format($this->refundAmount / 100, 2).' '.mb_strtoupper($this->order->currency);

        return (new MailMessage())
            ->subject("Refund Processed — {$this->order->event->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your refund for **{$this->order->event->title}** has been processed.")
            ->line("Refund amount: {$formatted}")
            ->action('View Order', route('orders.show', $this->order));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Refund Processed',
            'body' => "Your refund for {$this->order->event->title} has been processed.",
            'url' => route('orders.show', $this->order),
        ];
    }
}
