<?php

namespace App\Services;

use App\Mail\ReservationConfirmedMail;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReservationService
{
    /**
     * Create a new pending reservation.
     *
     * Ensures check-in and check-out dates are in correct order
     * and stores the reservation in "pending" state.
     *
     * @param Property $property
     * @param array $data Reservation data
     * @param User $user Authenticated user
     * @return Reservation
     */
    public function createReservation(Property $property, array $data, User $user): Reservation
    {
        $checkIn = $data['checkIn'];
        $checkOut = $data['checkOut'];

        if ($checkIn->gt($checkOut)) {
            [$checkIn, $checkOut] = [$checkOut, $checkIn];
        }

        return Reservation::create([
            'property_id' => $property->id,
            'user_id'     => $user->id,
            'check_in'    => $checkIn,
            'check_out'   => $checkOut,
            'status'      => 'pending',
            'notes'       => $data['message'] ?? null,
            'guests'      => $data['adults'] + $data['children'],
            'invoice'     => false,
            'total_price' => $data['total_price'],
        ]);
    }

    /**
     * Confirm a reservation after checking for date conflicts.
     *
     * If no conflicts exist, the reservation is marked as confirmed
     * and a confirmation email is sent to the user.
     *
     * @param Reservation $reservation
     * @return array{success: bool, error?: string}
     */
    public function confirmReservation(Reservation $reservation): array
    {
        $conflict = Reservation::where('property_id', $reservation->property_id)
            ->where('status', 'confirmed')
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($reservation) {
                $query
                    ->whereBetween('check_in', [$reservation->check_in, $reservation->check_out])
                    ->orWhereBetween('check_out', [$reservation->check_in, $reservation->check_out])
                    ->orWhere(function ($q) use ($reservation) {
                        $q->where('check_in', '<=', $reservation->check_in)
                          ->where('check_out', '>=', $reservation->check_out);
                    });
            })
            ->exists();

        if ($conflict) {
            return [
                'success' => false,
                'error' => 'Cannot confirm: date range is already booked.'
            ];
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        Mail::to($reservation->user->email)
            ->send(new ReservationConfirmedMail($reservation));

        return ['success' => true];
    }

    /**
     * Update check-in and check-out times of a reservation.
     *
     * Ensures that time validation is correct when both dates
     * belong to the same day.
     *
     * @param Reservation $reservation
     * @param string $startTime Format H:i
     * @param string $endTime Format H:i
     * @return array{success: bool, error?: string}
     */
    public function updateReservationTime(
        Reservation $reservation,
        string $startTime,
        string $endTime
    ): array {
        $dateStart = $reservation->check_in->format('Y-m-d');
        $dateEnd   = $reservation->check_out->format('Y-m-d');

        if ($startTime >= $endTime && $dateStart === $dateEnd) {
            return [
                'success' => false,
                'error' => 'Check-out time must be after check-in time.'
            ];
        }

        $reservation->check_in = Carbon::createFromFormat('Y-m-d H:i', "$dateStart $startTime");
        $reservation->check_out = Carbon::createFromFormat('Y-m-d H:i', "$dateEnd $endTime");

        $reservation->save();

        return ['success' => true];
    }

    /**
     * Find an overlapping confirmed reservation for a property.
     *
     * @param int $propertyId
     * @param Carbon $checkIn
     * @param Carbon $checkOut
     * @return Reservation|null
     */
    public function findOverlappingReservation(
        int $propertyId,
        Carbon $checkIn,
        Carbon $checkOut
    ): ?Reservation {
        return Reservation::where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
            })
            ->first();
    }

    /**
     * Get all confirmed reservations for the authenticated owner.
     *
     * Optionally filters by property title.
     *
     * @param string|null $propertyTitle
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConfirmedReservationsForOwner(?string $propertyTitle = null)
    {
        $query = Reservation::with(['user', 'property'])
            ->where('status', 'confirmed')
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()));

        if ($propertyTitle && $propertyTitle !== 'todos') {
            $query->whereHas('property', fn ($q) => $q->where('title', $propertyTitle));
        }

        return $query->get();
    }

    /**
     * Get both confirmed and pending reservations for the owner.
     *
     * @return array{confirmed: \Illuminate\Database\Eloquent\Collection, pending: \Illuminate\Database\Eloquent\Collection}
     */
    public function getPendingAndConfirmedForOwner(): array
    {
        $ownerFilter = fn ($q) => $q->where('owner_id', Auth::id());

        $confirmed = Reservation::where('status', 'confirmed')
            ->whereHas('property', $ownerFilter)
            ->with('property', 'user')
            ->get();

        $pending = Reservation::where('status', 'pending')
            ->whereHas('property', $ownerFilter)
            ->with('property', 'user')
            ->get();

        return compact('confirmed', 'pending');
    }
}
