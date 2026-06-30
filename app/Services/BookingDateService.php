<?php

namespace App\Services;

use App\Models\Property;
use Carbon\Carbon;
use InvalidArgumentException;

class BookingDateService
{
    /**
     * Parse the booking date range into Carbon instances.
     *
     * @return array{
     *     checkIn: Carbon,
     *     checkOut: Carbon
     * }
     */
    public function parse(Property $property, string $dateRange): array
    {
        $dates = explode(' - ', $dateRange);

        if (count($dates) !== 2) {
            throw new InvalidArgumentException('Invalid date format.');
        }

        try {
            $checkIn = Carbon::createFromFormat(
                'd/m/Y H:i',
                trim($dates[0]) . ' ' . $this->resolveCheckInHour($property),
            );

            $checkOut = Carbon::createFromFormat(
                'd/m/Y H:i',
                trim($dates[1]) . ' 11:00',
            );
        } catch (\Exception) {
            throw new InvalidArgumentException('Invalid date format.');
        }

        return [
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
        ];
    }

    /**
     * Determine the check-in hour depending on the property type.
     */
    private function resolveCheckInHour(Property $property): string
    {
        return str_contains($property->title, 'Casa')
            || str_contains($property->title, 'Villa')
            ? '15:00'
            : '14:00';
    }
}
