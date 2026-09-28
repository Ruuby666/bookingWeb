<?php

namespace App\Services;

use App\Models\Guest;

class GuestService
{
    /**
     * Find an existing guest by email or create a new one.
     *
     * If the guest already exists, their stored name and phone number are
     * kept as-is rather than overwritten with the new submission — the
     * booking form has no way to verify the submitter actually owns that
     * email, so blindly overwriting would let anyone who knows a guest's
     * email silently change their stored contact details.
     */
    public function findOrCreate(string $name, string $email, string $phone): Guest
    {
        $guest = Guest::where('email', $email)->first();

        if ($guest) {
            return $guest;
        }

        return Guest::create([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
        ]);
    }
}
