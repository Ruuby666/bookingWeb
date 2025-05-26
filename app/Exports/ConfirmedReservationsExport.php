<?php
namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConfirmedReservationsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Reservation::with(['user', 'property'])
            ->where('status', 'confirmed')
            ->get()
            ->map(function ($reservation) {
                return [
                    'ID' => $reservation->id,
                    'Usuario' => $reservation->user->name,
                    'Propiedad' => $reservation->property->title,
                    'Check-in' => $reservation->check_in,
                    'Check-out' => $reservation->check_out,
                    'Notas' => $reservation->notes,
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Usuario', 'Propiedad', 'Check-in', 'Check-out', 'Notas'];
    }
}