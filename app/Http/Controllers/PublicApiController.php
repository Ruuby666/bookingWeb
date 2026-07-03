<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Public read-only API endpoints consumed by the Blade/JS frontend.
 * All responses return explicit field lists — never raw Eloquent models.
 */
class PublicApiController extends Controller
{
    /**
     * All properties with the fields needed by the homepage map and card list.
     * Cached for 1 hour to reduce database queries on every datepicker interaction.
     */
    public function properties(): JsonResponse
    {
        $properties = Cache::remember(
            'properties_list',
            now()->addHour(),
            function () {
                return Property::query()->get([
                    'id',
                    'title',
                    'location',
                    'description',
                    'images_div',
                    'price_per_night',
                    'capacity',
                    'lat',
                    'lng',
                ]);
            },
        );

        return response()->json($properties);
    }

    /**
     * Confirmed reservations.
     * Cached for 10 minutes to reduce database queries on every datepicker interaction.
     *
     * Returns `status` explicitly so frontend JS can filter without guessing
     * whether the endpoint already filtered for confirmed-only.
     *
     * Frontend contract (date-picker.js and date-range.blade.php):
     *   reservation.property_id  — int
     *   reservation.check_in     — datetime string
     *   reservation.check_out    — datetime string
     *   reservation.status       — 'confirmed'
     */
    public function reservations(): JsonResponse
    {
        $reservations = Cache::remember(
            'reservations_confirmed',
            now()->addMinutes(10),
            function () {
                return Reservation::where('status', 'confirmed')
                    ->get(['property_id', 'check_in', 'check_out', 'status']);
            },
        );

        return response()->json($reservations);
    }

    /**
     * Property → first image name mapping used by the homepage JS.
     */
    public function images(): JsonResponse
    {
        $images = Property::query()->get(['id', 'images_div'])
            ->mapWithKeys(fn ($p) => [$p->id => $p->images_div]);

        return response()->json($images);
    }
}
