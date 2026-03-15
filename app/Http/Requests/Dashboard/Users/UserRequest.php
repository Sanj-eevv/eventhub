<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard\Users;

use App\DataTransferObjects\UserData;
use App\Enums\PreservedRoleList;
use App\Models\Organization;
use App\Models\Role;
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
            'organization' => [Rule::requiredIf(fn () => $request->input('role') === PreservedRoleList::ORGANIZATION_ADMIN->value),
                Rule::prohibitedIf(fn () => $request->input('role') !== PreservedRoleList::ORGANIZATION_ADMIN->value),
                'string', 'exists:organizations,uuid'],
            'password' => [Rule::prohibitedIf(fn () => (bool) $user), Rule::requiredIf(fn () => ! $user), 'string', 'max:191', Password::defaults(), 'confirmed'],
        ];
    }

    public function toDto(): UserData
    {
        $validatedData = $this->validated();

        $role = Role::query()->where('slug', $validatedData['role'])->first();
        $organization = ! empty($validatedData['organization']) ? Organization::query()->where('uuid', $validatedData['organization'])->first() : null;

        $validatedData = omit(['role', 'organization'], $validatedData);
        $validatedData['role_id'] = $role->id;
        $validatedData['organization_id'] = $organization?->id;

        return UserData::fromArray($validatedData);
    }
}
