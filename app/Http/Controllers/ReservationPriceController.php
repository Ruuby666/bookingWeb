<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ReservationPrice;
use App\Models\Property;
use Carbon\Carbon;

class ReservationPriceController extends Controller
{
    public function index()
    {
        $properties = Property::where('owner_id', Auth::id())->get();

        $reservationPrices = ReservationPrice::with('property')
            ->whereHas('property', function ($query) {
                $query->where('owner_id', Auth::id());
            })
            ->orderBy('property_id')
            ->get();
        return view('admin.reservation_price', compact('reservationPrices', 'properties'));
    }
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

    public function destroy($id)
    {
        $price = ReservationPrice::where('id', $id)
            ->whereHas('property', function ($query) {
                $query->where('owner_id', Auth::id());
            })
            ->first();

        if (!$price) {
            return redirect()->back()->with('error', 'Rango de precio no encontrado.')->withInput();
        }

        $price->delete();

        return redirect()->back()->with('success', 'Rango de precio eliminado correctamente.');
    }

    public function create(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'price_per_night' => 'required|numeric|min:0',
        ], [
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        $property = Property::where('id', $request->property_id)
            ->where('owner_id', Auth::id())
            ->first();

        if (!$property) {
            return redirect()->back()->with('error', 'No autorizado.');
        }

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Comprobar solapamiento de fechas
        $overlap = ReservationPrice::where('property_id', $request->property_id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'Ya existe un rango de fechas que se solapa con el que intentas crear.')->withInput();
        }

        ReservationPrice::create([
            'property_id' => $request->property_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price_per_night' => $request->price_per_night,
        ]);
        return redirect()->back()->with('success', 'Rango de precio creado correctamente.');
    }
}
