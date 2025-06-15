<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservationPrice;
use App\Models\Property;
use Carbon\Carbon;

class ReservationPriceController extends Controller
{
    public function getPriceRange(Request $request)
    {
        $startRaw = $request->start_date;
        $endRaw = $request->end_date;
        $startClean = trim(explode('GMT', $startRaw)[0]);
        $endClean = trim(explode('GMT', $endRaw)[0]);
        $startDate = Carbon::parse($startClean)->startOfDay();
        $endDate = Carbon::parse($endClean)->startOfDay();

        $propertyId = $request->property_id;

        $property = Property::findOrFail($propertyId);
        $defaultPrice = $property->price_per_night;

        $nights = [];

        while ($startDate->lt($endDate)) {
            $currentDate = $startDate->copy();

            $price = ReservationPrice::where('property_id', $propertyId)
                ->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->value('price_per_night');
            

            $nights[] = [
                'date' => $currentDate->toDateString(),
                'price' => $price ?? $defaultPrice,
            ];

            $startDate->addDay();
        }

        return response()->json($nights);
    }
}
