<?php

declare(strict_types=1);

namespace App\Mail\Organizations;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class StatusRejected extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Organization $organization,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Organization Application Has Been Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.organizations.status-rejected',
        );
    }

    /** @return array<mixed> */
    public function attachments(): array
    {
        return [];
    }
}
