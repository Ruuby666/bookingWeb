<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UpdateReservationTimeRequest;
use App\Models\Reservation;
use App\Services\AuthService;
use App\Services\ExportService;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly ReservationService $reservationService,
        private readonly ExportService $exportService,
    ) {}

    public function loginFunction(AdminLoginRequest $request)
    {
        $result = $this->authService->attemptAdminLogin(
            $request->validated('email'),
            $request->validated('password')
        );

        if (! $result['success']) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
    }

    public function logoutFunction()
    {
        $this->authService->logoutAdmin();

        return redirect()->route('index')->with('success', 'Logged out successfully.');
    }

    public function properties()
    {
        if (! session('is_admin')) {
            return redirect()->route('login');
        }

        $properties = \App\Models\Property::where('owner_id', Auth::id())->get();

        return view('admin.admin', compact('properties'));
    }

    public function pending()
    {
        ['confirmed' => $reservations, 'pending' => $pending] =
            $this->reservationService->getPendingAndConfirmedForOwner();

        return view('admin.pending', compact('reservations', 'pending'));
    }

    public function updateStatus($id)
    {
        $reservation = Reservation::with(['user', 'property'])
            ->where('id', $id)
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()))
            ->firstOrFail();

        $result = $this->reservationService->confirmReservation($reservation);

        return $result['success']
            ? redirect()->back()->with('success', 'Confirmación enviada al cliente.')
            : redirect()->back()->with('error', $result['error']);
    }

    public function suggestionEmail(Reservation $reservation)
    {
        if ($reservation->property->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('admin.suggestion', compact('reservation'));
    }

    public function calendar()
    {
        return view('admin.calendar');
    }

    public function getConfirmedReservations(\Illuminate\Http\Request $request)
    {
        $propiedad    = $request->query('propiedad');
        $reservations = $this->reservationService->getConfirmedReservationsForOwner($propiedad);

        $events = $reservations->map(fn ($r) => [
            'id'       => $r->id,
            'title'    => $r->user->name . ' en ' . $r->property->title,
            'note'     => $r->notes,
            'user'     => $r->user,
            'property' => $r->property->title,
            'start'    => $r->check_in,
            'end'      => $r->check_out,
        ]);

        return response()->json($events);
    }

    public function updateTime(UpdateReservationTimeRequest $request)
    {
        $reservation = Reservation::where('id', $request->validated('event_id'))
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()))
            ->firstOrFail();

        $result = $this->reservationService->updateReservationTime(
            $reservation,
            $request->validated('start_time'),
            $request->validated('end_time')
        );

        return $result['success']
            ? redirect()->back()->with('success', 'Hora actualizada correctamente.')
            : redirect()->back()->with('error', $result['error']);
    }

    public function exportExcel()
    {
        return $this->exportService->downloadReservationsZip();
    }

    public function exportfacturaExcel(\Illuminate\Http\Request $request)
    {
        return $this->exportService->downloadInvoicesExcel(
            $request->input('ids'),
            $request->input('invoice_amount')
        );
    }
}
