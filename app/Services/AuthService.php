<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Attempt to log in a user and verify admin privileges.
     *
     * @param  string  $email
     * @param  string  $password
     * @return array{success: bool, error?: string}
     */
    public function attemptAdminLogin(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ['success' => false, 'error' => 'Email or password is incorrect.'];
        }

        if (! $user->isAdmin()) {
            return ['success' => false, 'error' => 'You are not authorized to access this page.'];
        }

        Auth::login($user);

        return ['success' => true];
    }

    /**
     * Log out the currently authenticated admin.
     */
    public function logoutAdmin(): void
    {
        Auth::logout();

    }
}
