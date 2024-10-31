<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $user = $this->createUser($request);
        }

        $data = [
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

        print_r($data);

        $sub = 'New Booking';

        try {
            Mail::to('rubensepulvedareal@gmail.com')->send(new ContactMail($data, $sub));

            $this->createReservation($property, $data, $user);

            return redirect()->back();
        } catch (\Exception $e) {
            return 'Error sending email: ' . $e->getMessage();
        }
    }

    private function createReservation($propertytoreserve, $data, $user)
    {
        $reservation =  Reservation::create([
            'property_id' => $propertytoreserve->id,
            'user_id' =>  $user->id,
            'check_in' =>  $data['checkIn'],
            'check_out' =>  $data['checkOut'],
            'status' => 'pending',
            'notes' =>  $data['message'],
            'guests' =>  2,
            'total_price' => $this->calculateTotalPrice($propertytoreserve->id, $data['checkIn'], $data['checkOut']),
        ]);

        $this->updateReservationJson();
        return $reservation;
    }

    private function updateReservationJson()
    {
        $reservations = Reservation::all()->toArray();
        Storage::put('reservations.json', json_encode($reservations, JSON_PRETTY_PRINT));
        error_log("Archivo reservations.json actualizado tras creación de usuario.");
    }

    private function calculateTotalPrice($propertyId, $checkIn, $checkOut)
    {
        $property = Property::find($propertyId);
        $nights = (new \Carbon\Carbon($checkIn))->diffInDays(new \Carbon\Carbon($checkOut));

        return $nights * $property->price_per_night;
    }

    private function createUser(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'password' => Hash::make('default_password'),
        ]);

        $this->updateUsersJson();

        return $user;
    }

    private function updateUsersJson()
    {
        $users = User::all()->toArray();
        Storage::put('users.json', json_encode($users, JSON_PRETTY_PRINT));
        error_log("Archivo users.json actualizado tras creación de usuario.");
    }
}
