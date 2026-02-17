<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class EmailVerificationController extends Controller
{
    public function index(Request $request): RedirectResponse|Response
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard.index', absolute: false));
        }
        return Inertia::render('Auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
        ]);
    }

    public function verify(EmailVerificationRequest $verificationRequest): RedirectResponse
    {
        $verificationRequest->fulfill();
        return redirect()->intended(route('dashboard.index', absolute: false));
    }

    public function resend(): RedirectResponse
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard.index', absolute: false));
        }
        Auth::user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }
}
