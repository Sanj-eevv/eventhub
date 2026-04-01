<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\UpdateSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class DashboardSettingController extends Controller
{
    public function __construct(
        private readonly UpdateSettingsAction $updateSettingsAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function edit(): Response
    {
        $this->authorize('update', Setting::class);

        return $this->inertiaResponse->render('Dashboard/Settings/Edit', [
            'settings' => [
                'ticket_reservation_minutes' => (int) Setting::get('ticket_reservation_minutes', 5),
                'cancellation_cutoff_hours' => (int) Setting::get('cancellation_cutoff_hours', 24),
                'refund_percentage' => (int) Setting::get('refund_percentage', 100),
            ],
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->authorize('update', Setting::class);

        $this->updateSettingsAction->execute($request->validated());

        return $this->redirector->back()->with('toastSuccess', 'Settings saved successfully.');
    }
}
