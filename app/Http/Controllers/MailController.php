<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        $sub = 'New Booking';

        try {
            Mail::to('rubensepulvedareal@gmail.com')->send(new ContactMail($data, $sub));
            return 'Email sent successfully!';
        } catch (\Exception $e) {
            return 'Error sending email: ' . $e->getMessage();
        }
    }
}
