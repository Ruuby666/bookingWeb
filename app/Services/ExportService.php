<?php

namespace App\Services;

use App\Exports\ConfirmedReservationsExport;
use App\Exports\ConfirmedReservationsStuffExport;
use App\Exports\FacturasExport;
use App\Models\Reservation;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    /**
     * Build a ZIP containing two Excel files (reservations + staff view)
     * and return it as a downloadable response.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadReservationsZip(): BinaryFileResponse
    {
        $file1 = ConfirmedReservationsExport::download()->getFile()->getPathname();
        $file2 = ConfirmedReservationsStuffExport::download()->getFile()->getPathname();

        $date    = Carbon::now()->format('d_m_Y');
        $zipFile = tempnam(sys_get_temp_dir(), 'reservas_zip_') . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE);
        $zip->addFile($file1, "Reservas_{$date}.xlsx");
        $zip->addFile($file2, "Reservas_stuff_{$date}.xlsx");
        $zip->close();

        return response()
            ->download($zipFile, 'Reservas_completas.zip')
            ->deleteFileAfterSend(true);
    }

    /**
     * Mark the given reservation IDs as invoiced and return the invoices Excel download.
     *
     * @param  array       $ids            Reservation IDs to invoice
     * @param  float|null  $invoiceAmount
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadInvoicesExcel(array $ids, ?float $invoiceAmount): BinaryFileResponse
    {
        foreach ($ids as $id) {
            Reservation::markAsInvoiced($id);
        }

        return FacturasExport::download($ids, $invoiceAmount);
    }
}
