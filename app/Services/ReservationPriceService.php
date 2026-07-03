<?php

namespace App\Services;

use App\Models\Property;
use App\Models\ReservationPrice;
use Carbon\Carbon;

class ReservationPriceService
{
    /**
     * Get the nightly price breakdown for a property within a date range.
     *
     * Fetches all applicable price ranges in a single query and maps them
     * to each night, falling back to the property's default price if no
     * override exists. This prevents N+1 queries (30 nights = 30 queries).
     *
     * @param  Carbon  $startDate  Check-in date (inclusive)
     * @param  Carbon  $endDate  Check-out date (exclusive)
     * @return array<int, array{date: string, price: float}>
     */
    public function getPriceBreakdown(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $property = Property::findOrFail($propertyId);

        // 1 query: fetch all price ranges that overlap with the period
        $priceRanges = ReservationPrice::where('property_id', $propertyId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get();

        $nights = [];
        $current = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        while ($current->lt($end)) {
            // Find matching range for this night using in-memory collection
            $matchingRange = $priceRanges->first(
                fn ($r) => $r->start_date <= $current && $r->end_date >= $current,
            );

            $nights[] = [
                'date' => $current->toDateString(),
                'price' => $matchingRange?->price_per_night ?? $property->price_per_night,
            ];

            $current->addDay();
        }

        return $nights;
    }

    /**
     * Calculate the total reservation price for a date range.
     */
    public function calculateTotal(
        int $propertyId,
        Carbon $startDate,
        Carbon $endDate,
    ): float {
        $breakdown = $this->getPriceBreakdown(
            $propertyId,
            $startDate,
            $endDate,
        );

        return (float) array_sum(
            array_column($breakdown, 'price'),
        );
    }

    /**
     * Create a custom price range for a property.
     *
     * Validates ownership and prevents overlapping date ranges.
     *
     * @return array{success: bool, error?: string, model?: ReservationPrice}
     */
    public function createPriceRange(int $propertyId, Carbon $startDate, Carbon $endDate, float $pricePerNight, int $ownerId): array
    {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', $ownerId)
            ->first();

        if (! $property) {
            return ['success' => false, 'error' => 'Unauthorized access.'];
        }

        $overlap = ReservationPrice::where('property_id', $propertyId)
            ->where(function ($query) use ($startDate, $endDate): void {
                $query
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate): void {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlap) {
            return [
                'success' => false,
                'error' => 'A price range already exists that overlaps with the selected dates.',
            ];
        }

        $model = ReservationPrice::create([
            'property_id' => $propertyId,
            'start_date' => $startDate->startOfDay(),
            'end_date' => $endDate->endOfDay(),
            'price_per_night' => $pricePerNight,
        ]);

        return ['success' => true, 'model' => $model];
    }

    /**
     * Delete a price range if it belongs to the authenticated owner.
     *
     * @return array{success: bool, error?: string}
     */
    public function deletePriceRange(int $id, int $ownerId): array
    {
        $price = ReservationPrice::where('id', $id)
            ->whereHas('property', fn ($q) => $q->where('owner_id', $ownerId))
            ->first();

        if (! $price) {
            return ['success' => false, 'error' => 'Price range not found.'];
        }

        $price->delete();

        return ['success' => true];
    }
}
