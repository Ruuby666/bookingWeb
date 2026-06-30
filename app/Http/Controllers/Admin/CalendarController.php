<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReservationTimeRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function __construct(
        private readonly ReservationService $reservationService,
    ) {}

    /**
     * Display the reservation calendar.
     */
    public function calendar(): View
    {
        $properties = Property::where('owner_id', Auth::id())->get();

        return view('admin.calendar', compact('properties'));
    }

    /**
     * Return confirmed reservations as JSON.
     *
     * @return JsonResponse
     */
    public function getConfirmedReservations(Request $request)
    {
        $propiedad = $request->query('propiedad');

        $reservations = $this->reservationService
            ->getConfirmedReservationsForOwner(Auth::id(), $propiedad);

        /** @var \Illuminate\Support\Collection<int, Reservation> $reservations */
        $events = $reservations->map(fn($r) => [
            'id' => $r->id,
            'title' => $r->guest->name . ' in ' . $r->property->title,
            'note' => $r->notes,
            'guest' => $r->guest,
            'property' => $r->property->title,
            'start' => $r->check_in,
            'end' => $r->check_out,
        ]);

        return response()->json($events);
    }

    /**
     * Update reservation times.
     *
     * @return RedirectResponse
     */
    public function updateTime(UpdateReservationTimeRequest $request)
    {
        $reservation = Reservation::where('id', $request->validated('event_id'))
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->firstOrFail();

        $result = $this->reservationService->updateReservationTime(
            $reservation,
            $request->validated('start_time'),
            $request->validated('end_time'),
        );

        return $result['success']
            ? redirect()->back()->with('success', 'Reservation time updated successfully.')
            : redirect()->back()->with('error', $result['error']);
    }
}
