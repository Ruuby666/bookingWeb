<?php

namespace App\Services;

use App\Models\Property;
use Carbon\Carbon;

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
     * Process a booking request end-to-end:
     *  1. Parse & validate dates
     *  2. Check for overlapping confirmed reservations
     *  3. Calculate total price in the backend
     *  4. Find or create the guest user
     *  5. Create the pending reservation
     *  6. Send booking notification email
     *
     * @param  array  $data  Keys: adults, children, guests, name, number, email,
     *                       message, daterange, total_price
     * @return array{success: bool, error?: string, checkIn?: Carbon, checkOut?: Carbon}
     */
    public function process(Property $property, array $data): array
    {
        // --- 1. Parse dates ---

        $dates = $this->bookingDateService->parse(
            $property,
            $data['daterange']
        );

        $checkIn = $dates['checkIn'];
        $checkOut = $dates['checkOut'];

        // --- 2. Validate minimum nights and Overlap ---

        $this->bookingValidationService->validate(
            $property,
            $checkIn,
            $checkOut,
        );

        // --- 4. Calculate total price in the backend ---
        // Ignore frontend price and recalculate here to prevent manipulation
        $breakdown = $this->reservationPriceService->getPriceBreakdown(
            $property->id,
            $checkIn,
            $checkOut,
        );

        $totalPrice = array_sum(array_column($breakdown, 'price'));

        // --- 5. Find or create guest ---
        $guest = $this->guestService->findOrCreate(
            $data['name'],
            $data['email'],
            $data['number'],
        );

        // --- 6. Create reservation ---
        $bookingData = array_merge($data, [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'total_price' => $totalPrice,
        ]);

        $this->reservationService->createReservation($property, $bookingData, $guest);

        // --- 7. Notify owner ---
        $this->mailService->sendBookingNotification(
            array_merge($bookingData, ['property' => $property]),
        );

        return ['success' => true, 'checkIn' => $checkIn, 'checkOut' => $checkOut];
    }

    /**
     * Determine the check-in hour based on the property type.
     */
    private function resolveCheckInHour(Property $property): string
    {
        return str_contains($property->title, 'Casa') || str_contains($property->title, 'Villa')
            ? '15:00'
            : '14:00';
    }
}
