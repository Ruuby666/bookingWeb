<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function createReservation($property, $data, $user)
    {

        $checkIn = Carbon::createFromFormat('d/m/Y', $data['checkIn'])->setTime(0, 0, 0);
        $checkOut = Carbon::createFromFormat('d/m/Y', $data['checkOut'])->setTime(0, 0, 0);

        // Comprobación de solapamiento con reservas confirmadas
        $overlappingReservation = Reservation::where('property_id', $property->id)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut->copy()->subDay()])
                    ->orWhereBetween('check_out', [$checkIn->copy()->addDay(), $checkOut])
                    ->orWhere(function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();

        if ($overlappingReservation) {
            return redirect()->back()->with('error', 'Elija otra fecha hay un solapamiento.');
        }


        $reservation = Reservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'pending',
            'notes' => $data['message'],
            'guests' => $data['guests'],
            'total_price' => $this->calculateTotalPrice($property->id, $checkIn, $checkOut),
        ]);

        $this->updateReservationJson();
        return $reservation;
    }

    private function updateReservationJson()
    {
        $reservations = Reservation::all()->toArray();
        Storage::put('reservations.json', encrypt(json_encode($reservations, JSON_PRETTY_PRINT)));
        error_log("Archivo reservations.json actualizado tras creación de usuario.");
    }

    private function calculateTotalPrice($propertyId, $checkIn, $checkOut)
    {
        $property = Property::find($propertyId);
        $nights = $checkIn->diffInDays($checkOut);

        return $nights * $property->price_per_night;
    }

    
}
