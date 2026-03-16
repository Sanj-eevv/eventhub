<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\LogoutAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

final class LogoutController extends Controller
{
    public function __construct(private readonly LogoutAction $logoutAction, private readonly Redirector $redirector) {}

    public function __invoke(Request $request): RedirectResponse
    {
        $this->logoutAction->execute();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->redirector->route('home');
    }
}
