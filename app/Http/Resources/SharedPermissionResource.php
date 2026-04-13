<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final readonly class SharedPermissionResource
{
    public function __construct(private User $user) {}

    public static function make(User $user): self
    {
        return new self($user);
    }

    /**
     * @return array{
     *     organization: array{viewAny: bool, create: bool, update: bool, delete: bool},
     *     user: array{viewAny: bool, create: bool, update: bool, delete: bool},
     *     role: array{viewAny: bool, create: bool, update: bool, delete: bool},
     *     event: array{viewAny: bool, create: bool, update: bool, delete: bool, publish: bool, cancel: bool, checkIn: bool, reserve: bool},
     *     order: array{viewAny: bool, cancel: bool},
     *     setting: array{update: bool},
     *     dashboard: array{access: bool},
     * }
     */
    public function toArray(): array
    {
        return [
            'organization' => $this->checksFor(Organization::class, ['viewAny', 'create', 'update', 'delete']),
            'user' => $this->checksFor(User::class, ['viewAny', 'create', 'update', 'delete']),
            'role' => $this->checksFor(Role::class, ['viewAny', 'create', 'update', 'delete']),
            'event' => $this->checksFor(Event::class, ['viewAny', 'create', 'update', 'delete', 'publish', 'cancel', 'checkIn', 'reserve']),
            'order' => $this->checksFor(Order::class, ['viewAny', 'cancel']),
            'setting' => $this->checksFor(Setting::class, ['update']),
            'dashboard' => ['access' => Gate::forUser($this->user)->allows('access-dashboard')],
        ];
    }

    /**
     * @param string[] $abilities
     *
     * @return array<string, bool>
     */
    private function checksFor(string $model, array $abilities): array
    {
        return collect($abilities)
            ->mapWithKeys(fn (string $ability): array => [
                $ability => Gate::forUser($this->user)->allows($ability, $model),
            ])
            ->all();
    }
}
