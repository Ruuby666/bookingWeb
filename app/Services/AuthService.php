<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Service responsible for authentication logic.
 */
class AuthService
{
    /**
     * A precomputed bcrypt hash with no matching password, used to keep
     * Hash::check()'s cost constant whether or not the email exists —
     * otherwise a nonexistent email short-circuits before hashing and
     * responds measurably faster, letting an attacker enumerate admin
     * emails by timing the login endpoint.
     */
    private const DUMMY_HASH = '$2y$12$CjWdbAaTEb2zP/gz0co/3..iGzLlk.fjbAdJrYvl4kwHTZgJB/oCG';

    /**
     * Attempt to log in a user and verify admin privileges.
     *
     * @param  string  $email  User email
     * @param  string  $password  User password
     * @return array{success: bool, error?: string}
     */
    public function attemptAdminLogin(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        $validPassword = Hash::check($password, $user->password ?? self::DUMMY_HASH);

        if (! $user || ! $validPassword) {
            return [
                'success' => false,
                'error' => 'Email or password is incorrect.',
            ];
        }

        if (! $user->isAdmin()) {
            return [
                'success' => false,
                'error' => 'You are not authorized to access this page.',
            ];
        }

        Auth::login($user);

        return ['success' => true];
    }

    /**
     * Log out the authenticated admin user.
     */
    public function logoutAdmin(): void
    {
        Auth::logout();
    }
}
