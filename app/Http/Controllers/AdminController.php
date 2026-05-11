<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UpdateReservationTimeRequest;
use App\Models\Reservation;
use App\Models\Property;
use App\Services\AuthService;
use App\Services\ExportService;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsible for admin operations.
 */
class AdminController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly AuthService $authService,
        private readonly ReservationService $reservationService,
        private readonly ExportService $exportService,
    ) {}

    /**
     * Handle admin login.
     *
     * @param AdminLoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Log out the authenticated admin.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutFunction()
    {
        $this->authService->logoutAdmin();

        return redirect()->route('index')->with('success', 'Logged out successfully.');
    }

    /**
     * Display properties owned by the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function properties()
    {
        $properties = Property::where('owner_id', Auth::id())->get();

        return view('admin.admin', compact('properties'));
    }

    /**
     * Display confirmed and pending reservations.
     *
     * @return \Illuminate\View\View
     */
    public function pending()
    {
        ['confirmed' => $reservations, 'pending' => $pending] =
            $this->reservationService->getPendingAndConfirmedForOwner();

        return view('admin.pending', compact('reservations', 'pending'));
    }

    /**
     * Confirm a reservation.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id)
    {
        $reservation = Reservation::with(['user', 'property'])
            ->where('id', $id)
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->firstOrFail();

        $result = $this->reservationService->confirmReservation($reservation);

        return $result['success']
            ? redirect()->back()->with('success', 'Confirmation sent to the client.')
            : redirect()->back()->with('error', $result['error']);
    }

    /**
     * Show the suggestion email page.
     *
     * @param Reservation $reservation
     * @return \Illuminate\View\View
     */
    public function suggestionEmail(Reservation $reservation)
    {
        if ($reservation->property->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('admin.suggestion', compact('reservation'));
    }

    /**
     * Display the reservation calendar.
     *
     * @return \Illuminate\View\View
     */
    public function calendar()
    {
        $properties = Property::where('owner_id', Auth::id())->get();

        return view('admin.calendar', compact('properties'));
    }

    /**
     * Return confirmed reservations as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfirmedReservations(\Illuminate\Http\Request $request)
    {
        $propiedad = $request->query('propiedad');

        $reservations = $this->reservationService
            ->getConfirmedReservationsForOwner($propiedad);

        $events = $reservations->map(fn($r) => [
            'id'       => $r->id,
            'title'    => $r->user->name . ' in ' . $r->property->title,
            'note'     => $r->notes,
            'user'     => $r->user,
            'property' => $r->property->title,
            'start'    => $r->check_in,
            'end'      => $r->check_out,
        ]);

        return response()->json($events);
    }

    /**
     * Update reservation times.
     *
     * @param UpdateReservationTimeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTime(UpdateReservationTimeRequest $request)
    {
        $reservation = Reservation::where('id', $request->validated('event_id'))
            ->whereHas('property', fn($q) => $q->where('owner_id', Auth::id()))
            ->firstOrFail();

        $result = $this->reservationService->updateReservationTime(
            $reservation,
            $request->validated('start_time'),
            $request->validated('end_time')
        );

        return $result['success']
            ? redirect()->back()->with('success', 'Reservation time updated successfully.')
            : redirect()->back()->with('error', $result['error']);
    }

    /**
     * Download reservations Excel file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        return $this->exportService->downloadReservationsZip();
    }

    /**
     * Download invoices Excel file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportfacturaExcel(\Illuminate\Http\Request $request)
    {
        return $this->exportService->downloadInvoicesExcel(
            $request->input('ids'),
            $request->input('invoice_amount')
        );
    }
}
