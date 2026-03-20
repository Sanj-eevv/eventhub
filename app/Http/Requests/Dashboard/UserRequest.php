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
    public function rules(Request $request): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', 'string', 'exists:roles,slug'],
            'organization' => [Rule::requiredIf(fn () => $request->input('role') === PreservedRoleList::OrganizationAdmin->value),
                Rule::prohibitedIf(fn () => $request->input('role') !== PreservedRoleList::OrganizationAdmin->value),
                'string', 'exists:organizations,uuid'],
            'password' => [Rule::prohibitedIf(fn () => (bool) $user), Rule::requiredIf(fn () => ! $user), 'string', 'max:191', Password::defaults(), 'confirmed'],
        ];
    }

    public function toDto(): UserData
    {
        return new UserData(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            role_slug: $this->validated('role'),
            organization_uuid: $this->validated('organization'),
        );
    }
}
