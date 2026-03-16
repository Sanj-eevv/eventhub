<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use SensitiveParameter;

final class ResetPasswordAction
{
    public function __construct(
        private readonly PasswordBroker $passwordBroker,
        private readonly Hasher $hasher,
    ) {}

    public function execute(array $data): string
    {
        return $this->passwordBroker->reset(
            $data,
            function (User $user, #[SensitiveParameter] string $newPassword): void {
                $user->password = $this->hasher->make($newPassword);
                $user->remember_token = Str::random(60);
                $user->save();
            },
        );
    }
}
