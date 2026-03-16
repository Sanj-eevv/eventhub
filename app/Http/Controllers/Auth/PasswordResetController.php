<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\ResetPasswordAction;
use App\Actions\SendPasswordResetEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetEmailRequest;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;
use SensitiveParameter;

final class PasswordResetController extends Controller
{
    public function __construct(
        private readonly SendPasswordResetEmailAction $sendPasswordResetEmailAction,
        private readonly ResetPasswordAction $resetPasswordAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function showPasswordResetRequestForm(Request $request): Response
    {
        return $this->inertiaResponse->render('Auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    public function sendPasswordResetEmail(SendPasswordResetEmailRequest $sendPasswordResetEmailRequest): RedirectResponse
    {
        $this->sendPasswordResetEmailAction->execute($sendPasswordResetEmailRequest->input('email'));

        return $this->redirector->back()->with('status', 'If an account with that email exists, we will send a password reset link.');
    }

    public function showPasswordResetForm(Request $request, #[SensitiveParameter] string $token): Response
    {
        return $this->inertiaResponse->render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->input('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): RedirectResponse
    {
        $status = $this->resetPasswordAction->execute($resetPasswordRequest->validated());

        if (PasswordBroker::PASSWORD_RESET === $status) {
            return $this->redirector->route('auth.login')->with('status', __($status));
        }

        return $this->redirector->back()->withInput($resetPasswordRequest->only('email'))->withErrors(['email' => 'Failed to reset your password.']);
    }
}
