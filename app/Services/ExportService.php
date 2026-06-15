<?php

namespace App\Services;

use App\Exports\ConfirmedReservationsExport;
use App\Exports\ConfirmedReservationsStuffExport;
use App\Exports\FacturasExport;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Service responsible for exporting reservation and invoice data.
 */
class ExportService
{
    /**
     * Generate a ZIP file containing two Excel exports:
     * - Confirmed reservations
     * - Staff view reservations
     */
    public function downloadReservationsZip(User $user): BinaryFileResponse
    {
        $file1 = ConfirmedReservationsExport::download($user)
            ->getFile()
            ->getPathname();

        $file2 = ConfirmedReservationsStuffExport::download($user)
            ->getFile()
            ->getPathname();

        $date = Carbon::now()->format('d_m_Y');
        $zipFile = tempnam(sys_get_temp_dir(), 'reservas_zip_') . '.zip';

        $zip = new \ZipArchive;
        $zip->open($zipFile, \ZipArchive::CREATE);
        $zip->addFile($file1, "Reservas_{$date}.xlsx");
        $zip->addFile($file2, "Reservas_stuff_{$date}.xlsx");
        $zip->close();

        return response()
            ->download($zipFile, 'Reservas_completas.zip')
            ->deleteFileAfterSend(true);
    }

    /**
     * Mark reservations as invoiced and generate an Excel invoice file.
     *
     * @param  array  $ids  List of reservation IDs
     * @param  float|null  $invoiceAmount  Optional invoice amount
     */
    public function downloadInvoicesExcel(User $user, array $ids, ?float $invoiceAmount): BinaryFileResponse
    {
        $response = FacturasExport::download($user, $ids, $invoiceAmount);

        $query = Reservation::whereIn('id', $ids);

        if (! $user->is_super_admin) {
            $query->whereHas('property', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        }

        $query->update([
            'invoice' => true,
        ]);

        return $response;
    }
}
