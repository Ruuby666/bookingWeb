<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Reservation;

class ConfirmedReservationsExport
{
    public static function download()
    {
        $reservations = Reservation::with(['user', 'property'])
            ->where('status', 'confirmed')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['ID', 'Usuario', 'Propiedad', 'Check-in', 'Check-out', 'Notas']
        ], null, 'A1');

        $row = 2;
        foreach ($reservations as $reservation) {
            $sheet->fromArray([
                $reservation->id,
                $reservation->user->name,
                $reservation->property->title,
                $reservation->check_in,
                $reservation->check_out,
                $reservation->notes,
            ], null, 'A' . $row++);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'reservas_confirmadas.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}