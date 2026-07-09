<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\ReservationPrice;
use App\Models\User;

class ReservationPricePolicy
{
    public function create(User $user, Property $property): bool
    {
        return $user->id === $property->owner_id;
    }

    public function view(User $user, ReservationPrice $reservationPrice): bool
    {
        return $user->id === $reservationPrice->property->owner_id || $user->isSuperAdmin();
    }

    public function update(User $user, ReservationPrice $reservationPrice): bool
    {
        return $user->id === $reservationPrice->property->owner_id;
    }

    public function delete(User $user, ReservationPrice $reservationPrice): bool
    {
        return $user->id === $reservationPrice->property->owner_id;
    }
}
