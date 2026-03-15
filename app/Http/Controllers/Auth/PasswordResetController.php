<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetEmailRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use SensitiveParameter;

final class PasswordResetController extends Controller
{
    public function showPasswordResetRequestForm(Request $request): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    public function sendPasswordResetEmail(SendPasswordResetEmailRequest $sendPasswordResetEmailRequest): RedirectResponse
    {
        $email = $sendPasswordResetEmailRequest->input('email');
        Password::sendResetLink(['email' => $email]);

        return back()->with('status', 'If an account with that email exists, we will send a password reset link.');
    }

    public function showPasswordResetForm(Request $request, #[SensitiveParameter] string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $request->input('email'),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $resetPasswordRequest): RedirectResponse
    {
        $requestData = $resetPasswordRequest->validated();
        $status = Password::reset(
            $requestData,
            function (User $user, #[SensitiveParameter] string $newPassword): void {
                $user->password = Hash::make($newPassword);
                $user->remember_token = Str::random(60);
                $user->save();
                event(new PasswordReset($user));
            },
        );
        if (Password::PASSWORD_RESET === $status) {
            return redirect()->route('auth.login')->with('status', __($status));
        }

        return back()->withInput($resetPasswordRequest->only('email'))->withErrors(['email' => 'Failed to reset your password.']);
    }
}
