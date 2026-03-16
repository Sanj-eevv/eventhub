<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\ResendVerificationEmailAction;
use App\Actions\VerifyEmailAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Inertia\Response;
use Inertia\ResponseFactory;

final class EmailVerificationController extends Controller
{
    public function __construct(
        private readonly VerifyEmailAction $verifyEmailAction,
        private readonly ResendVerificationEmailAction $resendVerificationEmailAction,
        private readonly AuthManager $authManager,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function index(Request $request): RedirectResponse|Response
    {
        /** @var User $user */
        $user = $this->authManager->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
        }

        return $this->inertiaResponse->render('Auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
        ]);
    }

    public function verify(EmailVerificationRequest $verificationRequest): RedirectResponse
    {
        /** @var User $user */
        $user = $verificationRequest->user();

        $this->verifyEmailAction->execute($user);

        return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
    }

    public function resend(): RedirectResponse
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
