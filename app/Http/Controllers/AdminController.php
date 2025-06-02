<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use  App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmedMail;
use Carbon\Carbon;
use App\Exports\ConfirmedReservationsExport;
use App\Exports\ConfirmedReservationsStuffExport;

class AdminController extends Controller
{
    public function loginFunction(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ]
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {

            if ($user->isAdmin()) {
                Auth::login($user);
                session(['is_admin' => true]);
                return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
            } else {
                return back()->with('error', 'You are not authorized to access this page.');
            }
        }
        return back()->with('error', 'Email or password is incorrect.');
    }

    public function logoutFunction()
    {
        // Elimina la sesión del admin
        session()->forget('is_admin');

        return redirect()->route('index')->with('success', 'Logged out successfully.');
    }

    public function properties()
    {
        if (session('is_admin')) {
            $properties = Property::all();
            return view('admin.admin', compact('properties'));
        }

        return redirect()->route('login');
    }

    public function pending()
    {
        $reservations = Reservation::where('status', 'confirmed')->with('property', 'user')->get();
        $pending = Reservation::where('status', 'pending')->with('property', 'user')->get();

        return view('admin.pending', compact('reservations', 'pending'));
    }

    public function updateStatus($id)
    {
        $reservation = Reservation::with(['user', 'property'])->where('id', $id)->firstOrFail();

        // Verificar solapamiento de fechas con otras reservas confirmadas en la misma propiedad
        $conflictingReservation = Reservation::where('property_id', $reservation->property_id)
            ->where('status', 'confirmed')
            ->where('id', '!=', $reservation->id)
            ->where(function ($query) use ($reservation) {
                $query->whereBetween('check_in', [$reservation->check_in, $reservation->check_out])
                    ->orWhereBetween('check_out', [$reservation->check_in, $reservation->check_out])
                    ->orWhere(function ($query) use ($reservation) {
                        $query->where('check_in', '<=', $reservation->check_in)
                            ->where('check_out', '>=', $reservation->check_out);
                    });
            })
            ->exists();

        if ($conflictingReservation) {
            return redirect()->back()->with('error', 'No se puede confirmar: fechas ya reservadas.');
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        $this->updateReservationJson();

        Mail::to($reservation->user->email)->send(new ReservationConfirmedMail($reservation));

        return redirect()->back()->with('success', 'Confirmación enviada al cliente.');
    }

    private function updateReservationJson()
    {
        $reservations = Reservation::all()->toArray();
        Storage::put('reservations.json', encrypt(json_encode($reservations, JSON_PRETTY_PRINT)));
        error_log("Archivo reservations.json actualizado tras creación de usuario.");
    }

    public function suggestionEmail(Reservation $reservation)
    {
        return view('admin.suggestion', compact('reservation'));
    }

    public function calendar()
    {
        return view('admin.calendar');
    }

    public function getConfirmedReservations(Request $request)
    {
        $propiedad = $request->query('propiedad');

        $query = Reservation::with(['user', 'property'])->where('status', 'confirmed');

        if ($propiedad && $propiedad !== 'todos') {
            $query->whereHas('property', function ($q) use ($propiedad) {
                $q->where('title', $propiedad);
            });
        }

        $reservations = $query->get();

        $events = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'title' => $reservation->user->name . ' en ' . $reservation->property->title,
                'note' => $reservation->notes,
                'user' => $reservation->user,
                'property' => $reservation->property->title,
                'start' => $reservation->check_in,
                'end' => $reservation->check_out,
            ];
        });

        return response()->json($events);
    }

    public function updateTime(Request $request)
    {
        $request->validate([
            'event_id' => 'required|integer|exists:reservations,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $reservation = Reservation::findOrFail($request->event_id);

        $datestart = $reservation->check_in->format('Y-m-d');
        $dateend = $reservation->check_out->format('Y-m-d');

        if ($request->start_time >= $request->end_time) {
            return back()->withErrors(['end_time' => 'La hora de salida debe ser posterior a la de entrada.']);
        }

        $reservation->check_in = Carbon::createFromFormat('Y-m-d H:i', "$datestart {$request->start_time}");
        $reservation->check_out = Carbon::createFromFormat('Y-m-d H:i', "$dateend {$request->end_time}");
        $reservation->save();

        return redirect()->back()->with('success', 'Hora actualizada correctamente.');
    }

    public function exportExcel()
    {
        $file1 = ConfirmedReservationsExport::download()->getFile()->getPathname();
        $file2 = ConfirmedReservationsStuffExport::download()->getFile()->getPathname();

        $date = Carbon::now()->format('d_m_Y');

        // Crear archivo ZIP temporal
        $zipFile = tempnam(sys_get_temp_dir(), 'reservas_zip_') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE);
        $zip->addFile($file1, 'Reservas_'. $date .'.xlsx');
        $zip->addFile($file2, 'Reservas_stuff_'. $date .'.xlsx');
        $zip->close();

        // Descargar el ZIP y eliminarlo después
        return response()->download($zipFile, 'Reservas_completas.zip')->deleteFileAfterSend(true);
    }

    public function exportStuffExcel()
    {
        return ConfirmedReservationsStuffExport::download();
    }
}
