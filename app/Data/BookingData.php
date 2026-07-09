<?php

namespace App\Data;

use App\Http\Requests\BookingRequest;
use Carbon\Carbon;

/**
 * Data Transfer Object for a booking request.
 *
 * Replaces the implicit array $data that was passed through
 * BookingRequestService, MailService, and ReservationService.
 * Any renamed/missing field now fails at construction time (type error),
 * not silently at runtime when array key is accessed.
 */
readonly class BookingData
{
    public function __construct(
        public int $propertyId,
        public string $name,
        public string $email,
        public string $phone,
        public int $adults,
        public int $children,
        public string $daterange,
        public ?string $message = null,
    ) {}

    public static function fromRequest(BookingRequest $request): self
    {
        $data = $request->validated();

        return new self(
            propertyId: $data['property_id'],
            name: $data['name'],
            email: $data['email'],
            phone: $data['number'],
            adults: $data['adults'],
            children: $data['children'],
            daterange: $data['daterange'],
            message: $data['message'] ?? null,
        );
    }

    /**
     * Total number of guests (adults + children).
     * Avoids repeating this calculation in multiple services.
     */
    public function totalGuests(): int
    {
        return $this->adults + $this->children;
    }

    public function toReservationArray(
        Carbon $checkIn,
        Carbon $checkOut,
        float $totalPrice,
    ): array {
        return [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'message' => $this->message,
            'adults' => $this->adults,
            'children' => $this->children,
            'total_price' => $totalPrice,
        ];
    }
}
