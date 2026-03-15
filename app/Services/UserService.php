<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\UserDto;
use App\Models\User;

final class UserService
{
    public function create(UserDto $data): User
    {
        $user = User::query()->create($data->toArray());

        return $user;
    }

    public function update(User $user, UserDto $data): User
    {
        return tap($user)->update(omit('password', $data->toArray()));
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
