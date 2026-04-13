<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\UserData;
use App\Enums\PreservedRoleList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

final class UserRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(Request $request): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', 'string', 'exists:roles,slug'],
            'organization' => [Rule::requiredIf(fn (): bool => $request->input('role') === PreservedRoleList::OrganizationAdmin->value),
                Rule::prohibitedIf(fn (): bool => $request->input('role') !== PreservedRoleList::OrganizationAdmin->value),
                'string', 'exists:organizations,uuid'],
            'password' => [Rule::prohibitedIf(fn (): bool => (bool) $user), Rule::requiredIf(fn (): bool => ! $user), 'string', 'max:191', Password::defaults(), 'confirmed'],
        ];
    }

    public function toDto(): UserData
    {
        $password = $this->validated('password');
        $organizationUuid = $this->validated('organization');

        return new UserData(
            name: (string) $this->validated('name'),
            email: (string) $this->validated('email'),
            password: is_string($password) ? $password : null,
            role_slug: (string) $this->validated('role'),
            organization_uuid: is_string($organizationUuid) ? $organizationUuid : null,
        );
    }
}
