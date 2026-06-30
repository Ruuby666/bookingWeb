<?php

namespace App\Services;

use App\Events\BookingCreated;
use App\Models\Property;
use Carbon\Carbon;
use InvalidArgumentException;

class BookingRequestService
{
    public function __construct(
        private readonly ReservationService $reservationService,
        private readonly GuestService $guestService,
        private readonly BookingDateService $bookingDateService,
        private readonly BookingValidationService $bookingValidationService,
        private readonly ReservationPriceService $reservationPriceService,
    ) {}

    /**
     * Process a booking request.
     *
     * @return array{
     *     success: bool,
     *     error?: string,
     *     checkIn?: Carbon,
     *     checkOut?: Carbon
     * }
     */
    public function process(Property $property, array $data): array
    {
        try {
            // Parse booking dates
            $dates = $this->bookingDateService->parse(
                $property,
                $data['daterange'],
            );

            $checkIn = $dates['checkIn'];
            $checkOut = $dates['checkOut'];

            // Validate booking rules
            $this->bookingValidationService->validate(
                $property,
                $checkIn,
                $checkOut,
            );

            // Calculate total price securely
            $totalPrice = $this->reservationPriceService->calculateTotal(
                $property->id,
                $checkIn,
                $checkOut,
            );

            // Find or create guest
            $guest = $this->guestService->findOrCreate(
                $data['name'],
                $data['email'],
                $data['number'],
            );

            // Booking payload
            $bookingData = array_merge($data, [
                'checkIn' => $checkIn,
                'checkOut' => $checkOut,
                'total_price' => $totalPrice,
            ]);

            // Create reservation
            $reservation = $this->reservationService->createReservation(
                $property,
                $bookingData,
                $guest,
            );

            // Dispatch booking event
            event(new BookingCreated($reservation));

            return [
                'success' => true,
                'checkIn' => $checkIn,
                'checkOut' => $checkOut,
            ];
        } catch (InvalidArgumentException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
