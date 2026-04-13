<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\UpdateSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class SettingController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly UpdateSettingsAction $updateSettingsAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function edit(): Response
    {
        $this->authorize('update', Setting::class);

        return $this->inertiaResponse->render('Dashboard/Settings/Edit', [
            'settings' => new SettingResource($this->settingsService->get()),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $this->authorize('update', Setting::class);

        ($this->updateSettingsAction)($request->toDto());

        return $this->redirector->back()->with('toast_success', 'Settings saved successfully.');
    }
}
