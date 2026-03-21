<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\ResendVerificationEmailAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;

final class ResendEmailVerificationController extends Controller
{
    public function __construct(
        private readonly ResendVerificationEmailAction $resendVerificationEmailAction,
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
    ) {}

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $this->authManager->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
        }

        $this->resendVerificationEmailAction->execute($user);

        return $this->redirector->back()->with('status', 'verification-link-sent');
    }
}
