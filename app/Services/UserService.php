<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Find an existing user by email, or create a new one from request data.
     *
     * @param  string $name
     * @param  string $email
     * @param  string $phone
     * @return User
     */
    public function findOrCreate(string $name, string $email, string $phone): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name'         => $name,
                'phone_number' => $phone,
                'password'     => 'password', // User should reset on first login
                'is_admin'     => false,
            ]
        );
    }

    /**
     * Update an existing user's profile.
     *
     * @param  int   $id
     * @param  array $data  Keys: name, email, phone_number, password (optional)
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }
}
