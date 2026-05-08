<?php

namespace App\Http\Controllers;

use App\Models\Reservation;

class ReservationController extends Controller
{
    public function data($propertyId)
    {
        $reservations = Reservation::where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->get(['property_id', 'check_in', 'check_out']);

        return response()->json($reservations);
    }
}
