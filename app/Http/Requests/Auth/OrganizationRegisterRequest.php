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
    /** @return array<string, mixed> */
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
        return new OrganizationData(
            title: $this->string('title')->value(),
            description: $this->string('description')->value(),
            contact_address: $this->string('contact_address')->value(),
            contact_email: $this->string('contact_email')->value(),
            status: OrganizationStatus::Pending,
        );
    }

    public function toUserDto(): UserData
    {
        return new UserData(
            name: $this->string('name')->value(),
            email: $this->string('email')->value(),
            password: $this->string('password')->value(),
            role_slug: PreservedRoleList::OrganizationAdmin->value,
        );
    }
}
