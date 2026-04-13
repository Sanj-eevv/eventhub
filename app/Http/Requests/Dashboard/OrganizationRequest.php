<?php

declare(strict_types=1);

namespace App\Http\Requests\Dashboard;

use App\DataTransferObjects\OrganizationData;
use App\Enums\OrganizationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class OrganizationRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:5000'],
            'contact_address' => ['required', 'string', 'max:191'],
            'contact_email' => ['required', 'string', 'email', 'max:191', Rule::unique('organizations', 'contact_email')->ignore($this->route('organization'))],
            'status' => ['required', Rule::enum(OrganizationStatus::class)],
        ];
    }

    public function toDto(): OrganizationData
    {
        return new OrganizationData(
            title: $this->string('title')->value(),
            description: $this->string('description')->value(),
            contact_address: $this->string('contact_address')->value(),
            contact_email: $this->string('contact_email')->value(),
            status: OrganizationStatus::from($this->string('status')->value()),
        );
    }
}
