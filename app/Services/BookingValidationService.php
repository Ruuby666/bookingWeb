<?php

namespace App\Services;

use App\Models\Property;
use Carbon\Carbon;
use InvalidArgumentException;

class BookingValidationService
{
    public function __construct(
        private readonly ReservationService $reservationService,
    ) {}

    /**
     * Validate that the booking satisfies the property's minimum nights.
     */
    public function validateMinimumNights(
        Property $property,
        Carbon $checkIn,
        Carbon $checkOut,
    ): void {
        $nights = (int) round($checkIn->diffInDays($checkOut));

        if ($nights < $property->min_nights) {
            throw new InvalidArgumentException(
                "This property requires a minimum of {$property->min_nights} nights. You selected {$nights} nights.",
            );
        }
    }

    /**
     * Validate that no confirmed reservation overlaps.
     */
    public function validateAvailability(
        Property $property,
        Carbon $checkIn,
        Carbon $checkOut,
    ): void {
        $conflict = $this->reservationService->findOverlappingReservation(
            $property->id,
            $checkIn,
            $checkOut,
        );

        if ($conflict) {
            throw new InvalidArgumentException(
                'Select other date range, there is a reservation already from '
                . $conflict->check_in->format('d/m/Y H:i')
                . ' to '
                . $conflict->check_out->format('d/m/Y H:i'),
            );
        }
    }

    /**
     * Run every booking validation.
     */
    public function validate(
        Property $property,
        Carbon $checkIn,
        Carbon $checkOut,
    ): void {
        $this->validateMinimumNights(
            $property,
            $checkIn,
            $checkOut,
        );

        $this->validateAvailability(
            $property,
            $checkIn,
            $checkOut,
        );
    }
}
