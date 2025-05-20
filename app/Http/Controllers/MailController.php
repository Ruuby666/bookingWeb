<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reservation;
use App\Mail\ReservationSuggestionMail;
use Illuminate\Support\Facades\Mail;
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

        $property = Property::find($request->property_id);
        $data['property'] = $property;

        $sub = 'New Booking';

        try {
            Mail::to(config('mail.mailers.smtp.username'))->send(new ContactMail($data, $sub));
            $this->reservationController->createReservation($property, $data, $user);

            return redirect()->back()->with('success', 'Correo enviado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
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
