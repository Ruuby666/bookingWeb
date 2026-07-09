<?php

namespace App\Exports;

use App\Exports\Concerns\BuildsConfirmedReservationsSpreadsheet;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConfirmedReservationsExport
{
    use BuildsConfirmedReservationsSpreadsheet;

    public static function download(User $user)
    {
        $spreadsheet = self::buildConfirmedReservationsSpreadsheet($user, fullDetails: true);

        $filename = 'Reservas_actualizado_' . date('d.m.Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        (new Xlsx($spreadsheet))->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
