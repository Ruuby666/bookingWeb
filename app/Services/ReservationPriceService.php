<?php

namespace App\Services;

use App\Models\Property;
use App\Models\ReservationPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationPriceService
{
    /**
     * Calculate the nightly price breakdown for a date range on a given property.
     *
     * @param  int    $propertyId
     * @param  Carbon $startDate  Inclusive start (check-in night)
     * @param  Carbon $endDate    Exclusive end   (check-out day)
     * @return array<int, array{date: string, price: float}>
     */
    public function getPriceBreakdown(int $propertyId, Carbon $startDate, Carbon $endDate): array
    {
        $property     = Property::findOrFail($propertyId);
        $defaultPrice = $property->price_per_night;

        $nights  = [];
        $current = $startDate->copy()->startOfDay();
        $end     = $endDate->copy()->startOfDay();

        while ($current->lt($end)) {
            $price = ReservationPrice::where('property_id', $propertyId)
                ->where('start_date', '<=', $current)
                ->where('end_date', '>=', $current)
                ->value('price_per_night');

            $nights[] = [
                'date'  => $current->toDateString(),
                'price' => $price ?? $defaultPrice,
            ];

            $current->addDay();
        }

        return $nights;
    }

    /**
     * Create a custom price range for a property, checking for overlaps first.
     *
     * @param  int    $propertyId
     * @param  Carbon $startDate
     * @param  Carbon $endDate
     * @param  float  $pricePerNight
     * @return array{success: bool, error?: string, model?: ReservationPrice}
     */
    public function createPriceRange(
        int $propertyId,
        Carbon $startDate,
        Carbon $endDate,
        float $pricePerNight
    ): array {
        $property = Property::where('id', $propertyId)
            ->where('owner_id', Auth::id())
            ->first();

        if (! $property) {
            return ['success' => false, 'error' => 'No autorizado.'];
        }

        $overlap = ReservationPrice::where('property_id', $propertyId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlap) {
            return [
                'success' => false,
                'error'   => 'Ya existe un rango de fechas que se solapa con el que intentas crear.',
            ];
        }

        $model = ReservationPrice::create([
            'property_id'    => $propertyId,
            'start_date'     => $startDate->startOfDay(),
            'end_date'       => $endDate->endOfDay(),
            'price_per_night' => $pricePerNight,
        ]);

        return ['success' => true, 'model' => $model];
    }

    /**
     * Delete a price range owned by the authenticated user.
     *
     * @param  int $id
     * @return array{success: bool, error?: string}
     */
    public function deletePriceRange(int $id): array
    {
        $price = ReservationPrice::where('id', $id)
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()))
            ->first();

        if (! $price) {
            return ['success' => false, 'error' => 'Rango de precio no encontrado.'];
        }

        $price->delete();

        return ['success' => true];
    }
}
