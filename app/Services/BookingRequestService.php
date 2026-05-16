<?php

namespace App\Services;

use App\Models\Property;
use Carbon\Carbon;

class BookingRequestService
{
    public function __construct(
        private readonly ReservationService $reservationService,
        private readonly UserService $userService,
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
    public function process(Property $property, array $data): array{
        // --- 1. Parse dates ---
        $dates = explode(' - ', $data['daterange']);

        try {
            $checkIn = Carbon::createFromFormat(
                'd/m/Y H:i',
                trim($dates[0]).' '.$this->resolveCheckInHour($property)
            );
            $checkOut = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]).' 11:00');
        } catch (\Exception) {
            return ['success' => false, 'error' => 'Formato de fecha inválido.'];
        }

        // --- 2. Validate minimum nights ---
        $nights = (int) round($checkIn->diffInDays($checkOut));

        if ($nights < $property->min_nights) {
            return [
                'success' => false,
                'error' => "This property requires a minimum of {$property->min_nights} nights. You selected {$nights} nights.",
            ];
        }

        // --- 3. Overlap check ---
        $conflict = $this->reservationService->findOverlappingReservation(
            $property->id,
            $checkIn,
            $checkOut
        );

        if ($conflict) {
            return [
                'success' => false,
                'error' => 'Select other date range, there is a reservation already from '
                    .$conflict->check_in->format('d/m/Y H:i')
                    .' to '
                    .$conflict->check_out->format('d/m/Y H:i'),
            ];
        }

        // --- 4. Calculate total price in the backend ---
        // Ignore frontend price and recalculate here to prevent manipulation
        $breakdown = $this->reservationPriceService->getPriceBreakdown(
            $property->id,
            $checkIn,
            $checkOut
        );

        $totalPrice = array_sum(array_column($breakdown, 'price'));

        // --- 5. Find or create user ---
        $user = $this->userService->findOrCreate(
            $data['name'],
            $data['email'],
            $data['number']
        );

        // --- 6. Create reservation ---
        $bookingData = array_merge($data, [
            'checkIn'     => $checkIn,
            'checkOut'    => $checkOut,
            'total_price' => $totalPrice, 
        ]);

        $this->reservationService->createReservation($property, $bookingData, $user);

        // --- 7. Notify owner ---
        $this->mailService->sendBookingNotification(
            array_merge($bookingData, ['property' => $property])
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
