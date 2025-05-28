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
            'property_id' => 'required|exists:properties,id',
            'guests' => 'required|integer|min:1|max:' . $property->capacity,
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
            'daterange' => 'required|string|regex:/\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}/',
        ], [
            'property_id.required' => 'La propiedad es obligatoria.',
            'property_id.exists' => 'La propiedad seleccionada no existe.',
            'guests.required' => 'El número de invitados es obligatorio.',
            'guests.integer' => 'El número de invitados debe ser un número entero.',
            'guests.min' => 'Debe haber al menos un invitado.',
            'guests.max' => 'El número máximo de invitados permitido es ' . $property->capacity . '.',
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'number.required' => 'El número de teléfono es obligatorio.',
            'number.string' => 'El número de teléfono debe ser texto.',
            'number.max' => 'El número de teléfono no puede tener más de 20 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'message.string' => 'El mensaje debe ser texto.',
            'message.max' => 'El mensaje no puede tener más de 1000 caracteres.',
            'daterange.required' => 'El rango de fechas es obligatorio.',
            'daterange.regex' => 'El rango de fechas debe tener el formato DD/MM/AAAA - DD/MM/AAAA.',
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
        try {
            $data['checkOut'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]) . ' 11:00');

            if (str_contains($property->title, 'Casa') || str_contains($property->title, 'Villa')) {
                $checkinHour = '15:00';
            } else {
                $checkinHour = '14:00';
            }
            $data['checkIn'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]) . ' ' . $checkinHour);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['daterange' => 'Formato de fecha inválido.'])->withInput();
        }


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
