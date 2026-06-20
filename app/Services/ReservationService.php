<?php

namespace App\Services;

use App\Mail\ReservationConfirmedMail;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReservationService
{
    /**
     * Create a new pending reservation.
     */
    public function createReservation(Property $property, array $data, Guest $guest): Reservation
    {
        $checkIn = $data['checkIn'];
        $checkOut = $data['checkOut'];

        if ($checkIn->gt($checkOut)) {
            [$checkIn, $checkOut] = [$checkOut, $checkIn];
        }

        return Reservation::create([
            'property_id' => $property->id,
            'guest_id' => $guest->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => 'pending',
            'notes' => $data['message'] ?? null,
            'guests' => $data['adults'] + $data['children'],
            'invoice' => false,
            'total_price' => $data['total_price'],
        ]);
    }

    /**
     * Confirm a reservation after checking for date conflicts.
     */
    public function confirmReservation(Reservation $reservation): array
    {
        $conflict = Reservation::where('property_id', $reservation->property_id)
            ->where('status', 'confirmed')
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($reservation): void {
                $query
                    ->whereBetween('check_in', [$reservation->check_in, $reservation->check_out])
                    ->orWhereBetween('check_out', [$reservation->check_in, $reservation->check_out])
                    ->orWhere(function ($q) use ($reservation): void {
                        $q->where('check_in', '<=', $reservation->check_in)
                            ->where('check_out', '>=', $reservation->check_out);
                    });
            })
            ->exists();

        if ($conflict) {
            return [
                'success' => false,
                'error' => 'Cannot confirm: date range is already booked.',
            ];
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        Mail::to($reservation->guest->email)
            ->send(new ReservationConfirmedMail($reservation));

        return ['success' => true];
    }

    /**
     * Update check-in and check-out times of a reservation.
     */
    public function updateReservationTime(
        Reservation $reservation,
        string $startTime,
        string $endTime,
    ): array {
        $dateStart = $reservation->check_in->format('Y-m-d');
        $dateEnd = $reservation->check_out->format('Y-m-d');

        if ($startTime >= $endTime && $dateStart === $dateEnd) {
            return [
                'success' => false,
                'error' => 'Check-out time must be after check-in time.',
            ];
        }

        $reservation->check_in = Carbon::createFromFormat('Y-m-d H:i', "$dateStart $startTime");
        $reservation->check_out = Carbon::createFromFormat('Y-m-d H:i', "$dateEnd $endTime");

        $reservation->save();

        return ['success' => true];
    }

    /**
     * Find an overlapping confirmed reservation for a property.
     */
    public function findOverlappingReservation(
        int $propertyId,
        Carbon $checkIn,
        Carbon $checkOut,
    ): ?Reservation {
        return Reservation::where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($checkIn, $checkOut): void {
                $query->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })
            ->first();
    }

    /**
     * Get all confirmed reservations for the authenticated owner.
     */
    public function getConfirmedReservationsForOwner(?string $propertyTitle = null)
    {
        $query = Reservation::with(['guest', 'property'])
            ->where('status', 'confirmed')
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()));

        if ($propertyTitle && $propertyTitle !== 'todos') {
            $query->whereHas('property', fn ($q) => $q->where('title', $propertyTitle));
        }

        return $query->get();
    }

    /**
     * Get both confirmed and pending reservations for the owner.
     */
    public function getPendingAndConfirmedForOwner(): array
    {
        $ownerFilter = fn ($q) => $q->where('owner_id', Auth::id());

        $confirmed = Reservation::where('status', 'confirmed')
            ->whereHas('property', $ownerFilter)
            ->with('property', 'guest')
            ->get();

        $pending = Reservation::where('status', 'pending')
            ->whereHas('property', $ownerFilter)
            ->with('property', 'guest')
            ->get();

        return compact('confirmed', 'pending');
    }
}