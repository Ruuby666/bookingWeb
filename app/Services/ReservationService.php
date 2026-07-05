<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Events\ReservationConfirmed;
use App\Models\Guest;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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

        $reservation = Reservation::create([
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

        // Invalidate reservations cache on new booking
        Cache::forget('reservations_confirmed');

        return $reservation;
    }

    /**
     * Confirm a reservation after checking for date conflicts.
     */
    public function confirmReservation(Reservation $reservation): array
    {
        $conflict = $this->findOverlappingReservation(
            $reservation->property_id,
            $reservation->check_in,
            $reservation->check_out,
            $reservation->id,
        );

        if ($conflict) {
            return [
                'success' => false,
                'error' => 'Cannot confirm: date range is already booked.',
            ];
        }

        $reservation->status = ReservationStatus::Confirmed;
        $reservation->save();

        // Invalidate cache when a reservation is confirmed
        Cache::forget('reservations_confirmed');

        event(new ReservationConfirmed($reservation));

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
     *
     * Uses a half-open interval [check_in, check_out): a check-out that lands
     * exactly on another reservation's check-in is same-day turnover, not a
     * conflict. This is the single source of truth for overlap checks — both
     * booking validation and confirmation must use it.
     */
    public function findOverlappingReservation(
        int $propertyId,
        Carbon $checkIn,
        Carbon $checkOut,
        ?int $excludeReservationId = null,
    ): ?Reservation {
        return Reservation::where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->when(
                $excludeReservationId,
                fn ($query) => $query->where('id', '!=', $excludeReservationId),
            )
            ->where(function ($query) use ($checkIn, $checkOut): void {
                $query->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            })
            ->first();
    }

    /**
     * Get all confirmed reservations for the authenticated owner.
     */
    public function getConfirmedReservationsForOwner(int $ownerId, ?string $propertyTitle = null)
    {
        $query = Reservation::with(['guest', 'property'])
            ->where('status', 'confirmed')
            ->whereHas('property', fn ($q) => $q->where('owner_id', $ownerId));

        if ($propertyTitle && $propertyTitle !== 'todos') {
            $query->whereHas('property', fn ($q) => $q->where('title', $propertyTitle));
        }

        return $query->get();
    }

    /**
     * Get both confirmed and pending reservations for the owner.
     */
    public function getPendingAndConfirmedForOwner(int $ownerId): array
    {
        $ownerFilter = fn ($q) => $q->where('owner_id', $ownerId);

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
