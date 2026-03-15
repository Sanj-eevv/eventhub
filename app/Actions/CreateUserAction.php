<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\UserData;
use App\Models\User;
use Illuminate\Hashing\HashManager;

final class CreateUserAction
{
    public function __construct(private HashManager $hash) {}

    public function execute(UserData $userData): User
    {
        return User::query()->create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => $this->hash->make($userData->password),
            'role_id' => $userData->role_id,
            'organization_id' => $userData->organization_id,
        ]);
    }
}
