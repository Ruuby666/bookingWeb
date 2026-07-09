<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->property->owner_id || $user->isSuperAdmin();
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->property->owner_id;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->property->owner_id;
    }
}
