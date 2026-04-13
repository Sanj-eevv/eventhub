<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Organization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OrganizationRejected implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Organization $organization,
        public readonly bool $notify = true,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-approvals')];
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'organization_uuid' => $this->organization->uuid,
            'title' => $this->organization->title,
        ];
    }

    public function broadcastAs(): string
    {
        return 'organization.rejected';
    }
}
