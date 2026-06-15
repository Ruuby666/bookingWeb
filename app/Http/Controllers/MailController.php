<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\Requests\SendSuggestionRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\BookingRequestService;
use App\Services\MailService;
use Illuminate\Http\RedirectResponse;

/**
 * Controller responsible for email and booking request operations.
 */
class MailController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly BookingRequestService $bookingRequestService,
        private readonly MailService $mailService,
    ) {}

    /**
     * Process a booking request and send confirmation email.
     *
     * @param  BookingRequest  $request  Booking request data
     * @return RedirectResponse
     */
    public function sendEmail(BookingRequest $request)
    {
        $property = Property::findOrFail($request->validated('property_id'));

        $result = $this->bookingRequestService->process(
            $property,
            $request->validated(),
        );

        if (! $result['success']) {
            return redirect()
                ->back()
                ->with('error', $result['error'])
                ->withInput();
        }

        return redirect()
            ->route('property.show', ['id' => $property->id])
            ->with('success', 'Email sent successfully! We will contact you shortly.');
    }

    /**
     * Send a reservation suggestion to the guest.
     *
     * @param  SendSuggestionRequest  $request  Suggestion data
     * @param  int  $id  Reservation ID
     * @return RedirectResponse
     */
    public function sendSuggestion(SendSuggestionRequest $request, $id)
    {
        $reservation = Reservation::with(['user', 'property'])
            ->findOrFail($id);

        $this->mailService->sendSuggestionToGuest(
            $reservation,
            $request->validated('note'),
        );

        return redirect()
            ->route('admin.reservations.pending')
            ->with('success', 'Suggestion sent to the client.');
    }
}
