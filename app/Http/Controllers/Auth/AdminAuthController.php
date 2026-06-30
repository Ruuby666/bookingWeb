<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;

class AdminAuthController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * Handle admin login.
     *
     * @return RedirectResponse
     */
    public function loginFunction(AdminLoginRequest $request)
    {
        $result = $this->authService->attemptAdminLogin(
            $request->validated('email'),
            $request->validated('password'),
        );

        if (! $result['success']) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
    }

    /**
     * Log out the authenticated admin.
     *
     * @return RedirectResponse
     */
    public function logoutFunction()
    {
        $this->authService->logoutAdmin();

        return redirect()->route('index')->with('success', 'Logged out successfully.');
    }
}
