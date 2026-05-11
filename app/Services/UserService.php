<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Find an existing user by email or create a new one.
     *
     * If the user does not exist, it creates a new record using the provided data.
     *
     * @param string $name
     * @param string $email
     * @param string $phone
     * @return User
     */
    public function findOrCreate(string $name, string $email, string $phone): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'name'         => $name,
                'phone_number' => $phone,
                'password'     => 'password', // Should be changed on first login
                'is_admin'     => false,
            ]
        );
    }

    /**
     * Update an existing user profile.
     *
     * If a password is provided, it will be hashed before saving.
     * If not, the password field is ignored.
     *
     * @param int $id
     * @param array $data User data (name, email, phone_number, password optional)
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }
}
