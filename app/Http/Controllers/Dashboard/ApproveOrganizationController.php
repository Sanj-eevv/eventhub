<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\ConfirmOrganizationStatusAction;
use App\Enums\OrganizationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ConfirmOrganizationStatusRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class ApproveOrganizationController extends Controller
{
    public function __construct(
        private readonly ConfirmOrganizationStatusAction $confirmOrganizationStatusAction,
        private readonly Redirector $redirector,
    ) {}

    public function __invoke(ConfirmOrganizationStatusRequest $request, Organization $organization): RedirectResponse
    {
        $this->authorize('approve', $organization);

        $this->confirmOrganizationStatusAction->execute(
            $organization,
            OrganizationStatus::Approved,
            $request->boolean('send_notification', true),
            $request->user(),
        );

        return $this->redirector->back()->with('toast_success', 'Organization approved successfully.');
    }
}
