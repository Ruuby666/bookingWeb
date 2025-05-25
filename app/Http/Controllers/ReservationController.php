<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Property;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function createReservation($property, $data, $user)
    {
        $reservation = Reservation::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
            'check_in' => $data['checkIn'],
            'check_out' => $data['checkOut'],
            'status' => 'pending',
            'notes' => $data['message'],
            'guests' => $data['guests'],
            'total_price' => $this->calculateTotalPrice($property->id, $data['checkIn'], $data['checkOut']),
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
        $nights = (new \Carbon\Carbon($checkIn))->diffInDays(new \Carbon\Carbon($checkOut));

        return $nights * $property->price_per_night;
    }

    public function getConfirmedReservations()
    {
        $reservations = Reservation::with(['user', 'property'])->where('status', 'confirmed')->get();

        $events = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'title' => 'Reserva de ' . $reservation->user->name . ' en ' . $reservation->property->title,
                'description' => $reservation->notes,
                'user' => $reservation->user->name,
                'property' => $reservation->property->title,
                'start' => $reservation->check_in,
                'end' => $reservation->check_out,
            ];
        });

        return response()->json($events);
    }
}
