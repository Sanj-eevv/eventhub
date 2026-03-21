<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\ResetPasswordAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;
use SensitiveParameter;

final class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly ResetPasswordAction $resetPasswordAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function create(Request $request, #[SensitiveParameter] string $token): Response
    {
        return $this->inertiaResponse->render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->input('email'),
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = $this->resetPasswordAction->execute($request->validated());

        if (PasswordBroker::PASSWORD_RESET === $status) {
            return $this->redirector->route('auth.login')->with('status', __($status));
        }

        return $this->redirector->back()->withInput($request->only('email'))->withErrors(['email' => 'Failed to reset your password.']);
    }
}
