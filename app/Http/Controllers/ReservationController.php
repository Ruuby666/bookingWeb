<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function createReservation($property, $data, $user)
    {

        $checkIn = $data['checkIn'];
        $checkOut = $data['checkOut'];

        if ($checkIn->gt($checkOut)) {
            [$checkIn, $checkOut] = [$checkOut, $checkIn];
            $data['checkIn'] = $checkIn;
            $data['checkOut'] = $checkOut;
        }

        $reservation = Reservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'pending',
            'notes' => $data['message'],
            'guests' => $data['guests'],
            'invoice' => false,
            'total_price' => $data['total_price']
        ]);

        return $reservation;
    }

    public function data($propertyId){
        $reservations = Reservation::where('property_id' , $propertyId)
            ->where('status', 'confirmed')
            ->get();

        return response()->json($reservations);
    }

}
