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
    ) {}

    /**
     * Process a booking request end-to-end:
     *  1. Parse & validate dates
     *  2. Check for overlapping confirmed reservations
     *  3. Find or create the guest user
     *  4. Create the pending reservation
     *  5. Send booking notification email
     *
     * @param  Property $property
     * @param  array    $data  Keys: adults, children, guests, name, number, email,
     *                         message, daterange, total_price
     * @return array{success: bool, error?: string, checkIn?: Carbon, checkOut?: Carbon}
     */
    public function process(Property $property, array $data): array
    {
        // --- 1. Parse dates ---
        $dates = explode(' - ', $data['daterange']);

        try {
            $checkIn  = Carbon::createFromFormat(
                'd/m/Y H:i',
                trim($dates[0]) . ' ' . $this->resolveCheckInHour($property)
            );
            $checkOut = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]) . ' 11:00');
        } catch (\Exception) {
            return ['success' => false, 'error' => 'Formato de fecha inválido.'];
        }

        // --- 2. Overlap check ---
        $conflict = $this->reservationService->findOverlappingReservation(
            $property->id,
            $checkIn,
            $checkOut
        );

        if ($conflict) {
            return [
                'success' => false,
                'error'   => 'Select other date range, there is a reservation already from '
                    . $conflict->check_in->format('d/m/Y H:i')
                    . ' to '
                    . $conflict->check_out->format('d/m/Y H:i'),
            ];
        }

        // --- 3. Find or create user ---
        $user = $this->userService->findOrCreate(
            $data['name'],
            $data['email'],
            $data['number']
        );

        // --- 4. Create reservation ---
        $bookingData = array_merge($data, [
            'checkIn'  => $checkIn,
            'checkOut' => $checkOut,
        ]);

        $this->reservationService->createReservation($property, $bookingData, $user);

        // --- 5. Notify owner ---
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
