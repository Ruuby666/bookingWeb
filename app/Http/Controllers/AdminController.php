<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\UpdateReservationTimeRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\AuthService;
use App\Services\ExportService;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     * @return RedirectResponse
     */
    public function loginFunction(AdminLoginRequest $request)
    {
        $result = $this->authService->attemptAdminLogin(
            $request->validated('email'),
            $request->validated('password'),
        );

        if (! $result['success']) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
    }

    /**
     * Log out the authenticated admin.
     *
     * @return RedirectResponse
     */
    public function logoutFunction()
    {
        $this->authService->logoutAdmin();

        return redirect()->route('index')->with('success', 'Logged out successfully.');
    }

    /**
     * Display properties owned by the authenticated user.
     *
     * @return View
     */
    public function properties()
    {
        $properties = Property::where('owner_id', Auth::id())->get();

        return view('admin.admin', compact('properties'));
    }

    /**
     * Display confirmed and pending reservations.
     *
     * @return View
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
     * @param  int  $id
     * @return RedirectResponse
     */
    public function updateStatus($id)
    {
        $reservation = Reservation::with(['user', 'property'])
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
     *
     * @return View
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
     * @return View
     */
    public function calendar()
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
            ->getConfirmedReservationsForOwner($propiedad);

        $events = $reservations->map(fn ($r) => [
            'id' => $r->id,
            'title' => $r->user->name . ' in ' . $r->property->title,
            'note' => $r->notes,
            'user' => $r->user,
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
            ->whereHas('property', fn ($q) => $q->where('owner_id', Auth::id()))
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

    /**
     * Download reservations Excel file.
     *
     * @return BinaryFileResponse
     */
    public function exportExcel()
    {
        return $this->exportService->downloadReservationsZip();
    }

    /**
     * Download invoices Excel file.
     *
     * @return BinaryFileResponse
     */
    public function exportfacturaExcel(Request $request)
    {
        return $this->exportService->downloadInvoicesExcel(
            $request->input('ids'),
            $request->input('invoice_amount'),
        );
    }
}
