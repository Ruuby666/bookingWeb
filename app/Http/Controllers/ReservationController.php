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

        $checkIn = $data['checkIn'];
        $checkOut = $data['checkOut'];

        if ($checkIn->gt($checkOut)) {
            [$checkIn, $checkOut] = [$checkOut, $checkIn];
            $data['checkIn'] = $checkIn;
            $data['checkOut'] = $checkOut;
        }

        // Comprobación de solapamiento con reservas confirmadas
        $overlappingReservation = Reservation::where('property_id', $property->id)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in', '<', $checkOut)
                        ->where('check_out', '>', $checkIn);
                });
            })
            ->exists();

        if ($overlappingReservation) {
            return redirect()->back()->with('error', 'Select other date range, there is a reservation already from ' . $overlappingReservation->check_in->format('d/m/Y H:i') . ' to ' . $overlappingReservation->check_out->format('d/m/Y H:i'));
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
        $checkIn = $checkIn->copy()->startOfDay();
        $checkOut = $checkOut->copy()->startOfDay();
        $nights = $checkIn->diffInDays($checkOut);

        return $nights * $property->price_per_night;
    }
}
