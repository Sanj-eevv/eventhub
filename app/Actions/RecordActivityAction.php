<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class RecordActivityAction
{
    public function execute(ActivityEvent $event, Model $subject, ?User $causer = null, array $properties = []): void
    {
        ActivityLog::create([
            'event' => $event,
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'causer_type' => $causer?->getMorphClass(),
            'causer_id' => $causer?->getKey(),
            'properties' => $properties ?: null,
        ]);
    }
}
