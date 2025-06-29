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
use Carbon\Carbon;

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
        $property = Property::find($request->property_id);

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'adults' => 'required|integer|min:1|max:' . $property->capacity,
            'children' => 'required|integer|min:0|max:' . ($property->capacity - 1),
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'verification_email' => 'required|same:email|email|max:255',
            'message' => 'nullable|string|max:1000',
            'daterange' => 'required|string|regex:/\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}/',
        ], [
            'adults.required' => 'El número de adultos es obligatorio.',
            'adults.min' => 'Debe haber al menos 1 adulto.',
            'adults.max' => 'El número de adultos no puede exceder ' . $property->capacity . '.',
            'children.required' => 'El número de niños es obligatorio.',
            'children.min' => 'El número de niños no puede ser negativo.',
            'children.max' => 'El número de niños no puede exceder ' . ($property->capacity - 1) . '.',
            'verification_email.same' => 'El correo de verificación debe coincidir con el correo electrónico.',
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'number.required' => 'El número de teléfono es obligatorio.',
            'number.string' => 'El número de teléfono debe ser texto.',
            'number.max' => 'El número de teléfono no puede tener más de 20 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
            'verification_email.required' => 'La verificación del correo es obligatoria.',
            'verification_email.email' => 'Debe ser un correo electrónico válido.',
            'message.string' => 'El mensaje debe ser texto.',
            'message.max' => 'El mensaje no puede tener más de 500 caracteres.',
            'daterange.required' => 'El rango de fechas es obligatorio.',
            'daterange.regex' => 'El rango de fechas debe tener el formato DD/MM/AAAA - DD/MM/AAAA.',
        ]);

        // Validación adicional para el total de huéspedes
        $totalGuests = $request->adults + $request->children;
        if ($totalGuests > $property->capacity) {
            return redirect()->back()
                ->withErrors(['adults' => 'El número total de personas (' . $totalGuests . ') no puede exceder la capacidad de la propiedad (' . $property->capacity . ').'])
                ->withInput();
        }

        // Validación de fechas
        $dates = explode(' - ', $request->daterange);
        try {
            $checkOut = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]) . ' 11:00');

            if (str_contains($property->title, 'Casa') || str_contains($property->title, 'Villa')) {
                $checkinHour = '15:00';
            } else {
                $checkinHour = '14:00';
            }
            $checkIn = Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]) . ' ' . $checkinHour);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['daterange' => 'Formato de fecha inválido.'])->withInput();
        }

        // Verificar que el correo de verificación coincida con el correo ingresado
        if ($request->email !== $request->verification_email) {
            return redirect()->back()->with('error', "The verification email doesn't match the email you entered.");
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $user = $this->userController->store($request);
        }

        $data = [
            'adults' => $request->adults,
            'children' => $request->children,
            'guests' => $totalGuests,
            'name' => $request->name,
            'number' => $request->number,
            'email' => $request->email,
            'message' => $request->message,
            'daterange' => $request->daterange,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'total_price' => $request->total_price,
            'property' => $property,
        ];


        $sub = 'New Booking';

        try {
            Mail::to(config('mail.mailers.smtp.username'))->send(new ContactMail($data, $sub));
            $this->reservationController->createReservation($property, $data, $user);

            return redirect()->route('properties.show', ['property' => $property])
                ->with('success', 'Email sent successfully! We will contact you shortly.');
        } catch (\Exception $e) {
            return redirect()->route('properties.show', ['property' => $property])
                ->with('error', 'Error sending the email: ' . $e->getMessage());
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
