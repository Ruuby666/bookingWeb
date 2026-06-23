<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function properties(): JsonResponse
    {
        return response()->json(
            Property::all(['id', 'title', 'location', 'description', 'images_div', 'price_per_night', 'capacity', 'lat', 'lng'])
        );
    }

    public function reservations(): JsonResponse
    {
        return response()->json(
            Reservation::where('status', 'confirmed')
                ->get(['property_id', 'check_in', 'check_out'])
        );
    }

    public function images(): JsonResponse
    {
        $images = Property::all(['id', 'images_div'])
            ->mapWithKeys(fn($p) => [$p->id => $p->images_div]);
        return response()->json($images);
    }
}
