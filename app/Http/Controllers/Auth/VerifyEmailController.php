<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\VerifyEmailAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;

final class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly VerifyEmailAction $verifyEmailAction,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
    ) {}

    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->verifyEmailAction->execute($user);

        return $this->redirector->intended($this->urlGenerator->route('home', absolute: false));
    }
}
