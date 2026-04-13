<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\VerifyEmailAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;

final class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly VerifyEmailAction $verifyEmailAction,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
    ) {}

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $this->authManager->user();

        ($this->verifyEmailAction)($user);

        return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
    }
}
