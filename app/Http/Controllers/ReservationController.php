<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

/**
 * Controller responsible for reservation data.
 */
class ReservationController extends Controller
{
    /**
     * Return confirmed reservations for a property as JSON.
     *
     * @param  int  $propertyId  Property ID
     * @return JsonResponse
     */
    public function data($propertyId)
    {
        $reservations = Reservation::where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->get(['property_id', 'check_in', 'check_out']);

        return response()->json($reservations);
    }
}
