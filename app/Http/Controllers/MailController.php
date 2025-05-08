<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\UserController;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
            'guests'=> $request->guests,
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
            Mail::to('rubensepulvedareal@gmail.com')->send(new ContactMail($data, $sub));
            $this->reservationController->createReservation($property, $data, $user);

            return redirect()->back();
        } catch (\Exception $e) {
            return 'Error sending email: ' . $e->getMessage();
        }
    }
}
