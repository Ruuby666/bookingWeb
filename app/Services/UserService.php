<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Update an existing user profile.
     *
     * If a password is provided, it will be hashed before saving.
     * If not, the password field is ignored.
     *
     * @param  array  $data  User data (name, email, phone_number, password optional)
     */
    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user;
    }
}
