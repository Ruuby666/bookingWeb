<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservation;
use App\Mail\ReservationSuggestionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\UserController;
use App\Models\Property;

class MailController extends Controller
{
    protected $userController;
    protected $reservationController;


    public function __construct(UserController $userController, ReservationController $reservationController)
    {
        $this->userController = $userController;
        $this->reservationController = $reservationController;
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        $property = Property::find($request->property_id);

        $validator = Validator::make($request->all(), [
            'guests' => 'required|integer|min:1|max:' . $property->capacity,
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
            'daterange' => 'required|string|regex:/\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}/',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $user = $this->userController->store($request);
        }

        $data = [
            'guests' => $request->guests,
            'name' => $request->name,
            'number' => $request->number,
            'email' => $request->email,
            'message' => $request->message,
            'daterange' => $request->daterange,
        ];
        
        $dates = explode(' - ', $request->daterange);
        $data['checkIn'] = trim($dates[0]);
        $data['checkOut'] = trim($dates[1]);


        $data['property'] = $property;

        $sub = 'New Booking';

        try {
            Mail::to(config('mail.mailers.smtp.username'))->send(new ContactMail($data, $sub));
            $this->reservationController->createReservation($property, $data, $user);

            return redirect()->route('properties.show', ['property' => $property])
                ->with('success', 'Correo enviado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('properties.show', ['property' => $property])
                ->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }

    public function sendSuggestion(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $reservation = Reservation::with(['user', 'property'])->where('id', $id)->firstOrFail();
        $note = $request->input('note');

        Mail::to($reservation->user->email)->send(new ReservationSuggestionMail($reservation, $note));

        return redirect()->route('admin.reservations.pending')->with('success', 'Sugerencia enviada al cliente.');
    }
}
