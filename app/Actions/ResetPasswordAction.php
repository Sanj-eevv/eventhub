<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use SensitiveParameter;

final readonly class ResetPasswordAction
{
    public function __construct(
        private PasswordBroker $passwordBroker,
        private Hasher $hasher,
    ) {}

    /** @param array<string, mixed> $data */
    public function __invoke(array $data): string|false
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
