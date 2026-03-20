<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\DataTransferObjects\OrganizationData;
use App\DataTransferObjects\UserData;
use App\Enums\OrganizationStatus;
use App\Enums\PreservedRoleList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class OrganizationRegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:5000'],
            'contact_address' => ['required', 'string', 'max:500'],
            'contact_email' => ['required', 'string', 'email', 'max:191', 'unique:organizations,contact_email'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'max:191', Password::defaults(), 'confirmed'],
        ];
    }

    public function toOrganizationDto(): OrganizationData
    {
        $validated = $this->validated();

        return new OrganizationData(
            title: $validated['title'],
            description: $validated['description'],
            contact_address: $validated['contact_address'],
            contact_email: $validated['contact_email'],
            status: OrganizationStatus::Pending,
        );
    }

    public function toUserDto(): UserData
    {
        $validated = $this->validated();

        return new UserData(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            role_slug: PreservedRoleList::OrganizationAdmin->value,
        );
    }
}
