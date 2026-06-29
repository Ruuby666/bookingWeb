<?php

namespace App\Services;

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
        private readonly MailService $mailService,
        private readonly ReservationPriceService $reservationPriceService,
    ) {}

    /**
     * Process a booking request.
     *
     * @param array $data
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
            $this->reservationService->createReservation(
                $property,
                $bookingData,
                $guest,
            );

            // Notify property owner
            $this->mailService->sendBookingNotification(
                array_merge(
                    $bookingData,
                    ['property' => $property],
                ),
            );

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
