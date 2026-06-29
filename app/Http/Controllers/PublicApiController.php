<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

/**
 * Public read-only API endpoints consumed by the Blade/JS frontend.
 * All responses return explicit field lists — never raw Eloquent models.
 */
class PublicApiController extends Controller
{
    /**
     * All properties with the fields needed by the homepage map and card list.
     */
    public function properties(): JsonResponse
    {
        return response()->json(
            Property::query()->get([
                'id',
                'title',
                'location',
                'description',
                'images_div',
                'price_per_night',
                'capacity',
                'lat',
                'lng',
            ]),
        );
    }

    /**
     * Confirmed reservations.
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
        return response()->json(
            Reservation::where('status', 'confirmed')
                ->get(['property_id', 'check_in', 'check_out', 'status']),
        );
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
