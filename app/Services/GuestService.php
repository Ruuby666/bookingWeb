<?php

namespace App\Services;

use App\Models\Guest;

class GuestService
{
    /**
     * Find an existing guest by email or create a new one.
     *
     * If the guest already exists, their name and phone number are
     * refreshed with the latest values provided, since the same guest
     * may book again with updated contact details.
     */
    public function findOrCreate(string $name, string $email, string $phone): Guest
    {
        $guest = Guest::where('email', $email)->first();

        if ($guest) {
            $guest->update([
                'name' => $name,
                'phone_number' => $phone,
            ]);

            return $guest;
        }

        return Guest::create([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
        ]);
    }
}
