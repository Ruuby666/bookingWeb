<?php

namespace App\Exports;

use App\Models\Reservation;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConfirmedReservationsExport
{
    public static function download()
    {
        $reservations = Reservation::with(['user', 'property'])
            ->where('status', 'confirmed')
            ->orderBy('check_in')
            ->get()
            ->groupBy(fn($r) => $r->property->title);

        $spreadsheet = new Spreadsheet;
        $sheetIndex = 0;

        foreach ($reservations as $propertyTitle => $propertyReservations) {
            $sheet = ($sheetIndex === 0)
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();

            $spreadsheet->setActiveSheetIndex($sheetIndex++);
            $sheet->setTitle(substr($propertyTitle, 0, 31));

            $row = 1;

            // Título principal
            $sheet->setCellValue("A{$row}", "RESERVA {$propertyTitle}");
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row += 2;

            $lastMonth = null;
            $prevReservation = null;

            foreach ($propertyReservations as $reservation) {
                $userName = $reservation->user->name ?? '';
                $email = $reservation->user->email ?? '';

                $checkInDate = Carbon::parse($reservation->check_in);
                $checkOutDate = Carbon::parse($reservation->check_out);

                $checkIn = $checkInDate->format('d.m.Y');
                $checkOut = $checkOutDate->format('d.m.Y');

                $checkInHour = $checkInDate->format('H:i');
                $checkOutHour = $checkOutDate->format('H:i');

                $checkInMonth = $checkInDate->month;

                $month = ucfirst($checkInDate->locale('es')->isoFormat('MMMM'));

                $guests = $reservation->guests ?? 'N/A';
                $id = $reservation->id;
                $totalPrice = $reservation->total_price ?? 'N/A';
                $notes = $reservation->notes ?? '';
                $arrivalHour = $checkInHour;
                $departureHour = $checkOutHour;

                // Mostrar el mes solo si ha cambiado
                if ($month !== $lastMonth) {
                    $sheet->setCellValue("A{$row}", strtoupper($month));
                    $lastMonth = $month;

                    // Solo si hay una reserva anterior
                    if ($prevReservation) {
                        $prevCheckOut = Carbon::parse($prevReservation->check_out);
                        if ($prevCheckOut->month === $checkInMonth) {
                            $prevName = $prevReservation->user->name ?? '';
                            $prevCheckOutFormatted = $prevCheckOut->format('d.m.Y');
                            $row++;
                            $sheet->setCellValue("B{$row}", "Hasta {$prevCheckOutFormatted} {$prevName}");
                        }
                    }
                    $row++;
                }

                // Línea de fechas
                $sheet->setCellValue("B{$row}", "{$checkIn} - {$checkOut} {$userName}");
                $row++;

                $sheet->setCellValue("B{$row}", "{$userName}");
                $row++;

                $sheet->setCellValue("B{$row}", "{$email}");
                $row++;

                // Número de huéspedes
                $sheet->setCellValue("B{$row}", "{$guests} personas");
                $row++;

                // ID de la reserva
                $sheet->setCellValue("B{$row}", "ID reserva: {$id}");
                $row++;

                // Precio total
                $sheet->setCellValue("B{$row}", "Total: {$totalPrice}");
                $row++;

                // Notas
                $sheet->setCellValue("B{$row}", "Observaciones: {$notes}");
                $row++;

                // Dias de llegada y salida
                $sheet->setCellValue("B{$row}", "Día Llegada: {$checkIn}, Día Salida: {$checkOut}");
                $row++;

                // Horas de llegada y salida
                $sheet->setCellValue("B{$row}", "Llegada: {$arrivalHour}, Salida: {$departureHour}");
                $row++;

                // Espacio entre reservas
                $row++;

                $prevReservation = $reservation;
            }
        }

        // Guardar archivo temporalmente
        $filename = 'Reservas_actualizado_' . date('d.m.Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        (new Xlsx($spreadsheet))->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
