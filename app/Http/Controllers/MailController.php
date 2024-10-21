<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Property;
use Illuminate\Support\Facades\Log;

class MailController extends Controller
{
    public function sendEmail(Request $request)
    {
        print $request;
        $data = [
            'name' => $request->name,
            'number' => $request->number,
            'email' => $request->email,
            'message' => $request->message,
            'daterange' => $request->daterange,
        ];

        // Obtener la propiedad desde la base de datos usando el ID
        $property = Property::find($request->property_id);

        // Añadir la propiedad al array de datos
        $data['property'] = $property;

        $sub = 'New Booking';

        try {
            Mail::to('rubensepulvedareal@gmail.com')->send(new ContactMail($data, $sub));
            return redirect()->back();
        } catch (\Exception $e) {
            return 'Error sending email: ' . $e->getMessage();
        }
    }
}
