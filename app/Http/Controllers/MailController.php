<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\Requests\SendSuggestionRequest;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\BookingRequestService;
use App\Services\MailService;

class MailController extends Controller
{
    public function __construct(
        private readonly BookingRequestService $bookingRequestService,
        private readonly MailService $mailService,
    ) {}

    public function sendEmail(BookingRequest $request)
    {
        $property = Property::findOrFail($request->validated('property_id'));

        $result = $this->bookingRequestService->process($property, $request->validated());

        if (! $result['success']) {
            return redirect()->back()->with('error', $result['error'])->withInput();
        }

        return redirect()
            ->route('properties.show', ['property' => $property])
            ->with('success', 'Email sent successfully! We will contact you shortly.');
    }

    public function sendSuggestion(SendSuggestionRequest $request, $id)
    {
        $reservation = Reservation::with(['user', 'property'])->findOrFail($id);

        $this->mailService->sendSuggestionToGuest($reservation, $request->validated('note'));

        return redirect()
            ->route('admin.reservations.pending')
            ->with('success', 'Sugerencia enviada al cliente.');
    }
}
