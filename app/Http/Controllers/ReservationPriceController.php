<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationPriceRequest;
use App\Models\Property;
use App\Models\ReservationPrice;
use App\Services\ReservationPriceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationPriceController extends Controller
{
    public function __construct(
        private readonly ReservationPriceService $reservationPriceService,
    ) {}

    public function index()
    {
        $properties = Property::where('owner_id', Auth::id())->get();

        $reservationPrices = ReservationPrice::with('property')
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()))
            ->orderBy('property_id')
            ->get();

        return view('admin.reservation_price', compact('reservationPrices', 'properties'));
    }

    public function getPriceRange(Request $request)
    {
        $startDate  = Carbon::parse(trim(explode('GMT', $request->start_date)[0]))->startOfDay();
        $endDate    = Carbon::parse(trim(explode('GMT', $request->end_date)[0]))->startOfDay();

        $nights = $this->reservationPriceService->getPriceBreakdown(
            $request->property_id,
            $startDate,
            $endDate
        );

        return response()->json($nights);
    }

    public function create(StoreReservationPriceRequest $request)
    {
        $result = $this->reservationPriceService->createPriceRange(
            $request->validated('property_id'),
            Carbon::parse($request->validated('start_date')),
            Carbon::parse($request->validated('end_date')),
            $request->validated('price_per_night')
        );

        return $result['success']
            ? redirect()->back()->with('success', 'Rango de precio creado correctamente.')
            : redirect()->back()->with('error', $result['error'])->withInput();
    }

    public function destroy($id)
    {
        $result = $this->reservationPriceService->deletePriceRange($id);

        return $result['success']
            ? redirect()->back()->with('success', 'Rango de precio eliminado correctamente.')
            : redirect()->back()->with('error', $result['error'])->withInput();
    }
}
