<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\UserData;
use App\Enums\PreservedRoleList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class RegisterRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'max:191', Password::defaults(), 'confirmed'],
        ];
    }

    public function toDto(): UserData
    {
        $validated = $this->validated();

        return new UserData(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role_slug: PreservedRoleList::User->value,
        );
    }
}
