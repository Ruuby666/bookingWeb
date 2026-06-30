<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class StatusReservationController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly ReservationService $reservationService,
    ) {}

    /**
     * Display confirmed and pending reservations.
     */
    public function pending(): View
    {
        ['confirmed' => $reservations, 'pending' => $pending] =
            $this->reservationService->getPendingAndConfirmedForOwner(Auth::id());

        return view('admin.pending', compact('reservations', 'pending'));
    }

    /**
     * Confirm a reservation.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function updateStatus($id)
    {
        $reservation = Reservation::with(['guest', 'property'])
            ->where('id', $id)
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()))
            ->firstOrFail();

        $result = $this->reservationService->confirmReservation($reservation);

        return $result['success']
            ? redirect()->back()->with('success', 'Confirmation sent to the client.')
            : redirect()->back()->with('error', $result['error']);
    }

    /**
     * Show the suggestion email page.
     */
    public function suggestionEmail(Reservation $reservation): View
    {
        if ($reservation->property->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('admin.suggestion', compact('reservation'));
    }
}
