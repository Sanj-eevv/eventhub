<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\SendPasswordResetEmailAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPasswordResetEmailRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Response;
use Inertia\ResponseFactory;

final class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly SendPasswordResetEmailAction $sendPasswordResetEmailAction,
        private readonly Redirector $redirector,
        private readonly ResponseFactory $inertiaResponse,
    ) {}

    public function create(Request $request): Response
    {
        return $this->inertiaResponse->render('Auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    public function store(SendPasswordResetEmailRequest $request): RedirectResponse
    {
        ($this->sendPasswordResetEmailAction)($request->string('email')->toString());

        return $this->redirector->back()->with('status', 'If an account with that email exists, we will send a password reset link.');
    }
}
