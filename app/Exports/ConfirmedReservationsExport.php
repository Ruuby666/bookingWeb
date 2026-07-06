<?php

namespace App\Exports;

use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConfirmedReservationsExport
{
    public static function download(User $user)
    {
        $query = Reservation::with(['guest', 'property'])
            ->where('status', 'confirmed');

        // If the app config prevents super-admins from exporting all reservations,
        // apply owner scoping even when the user is a super-admin.
        if (! $user->is_super_admin || ! config('exports.super_admin_can_export_all')) {
            $query->whereHas('property', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        }

        $reservations = $query
            ->orderBy('check_in')
            ->get()
            ->groupBy(fn ($r) => $r->property->title);

        $spreadsheet = new Spreadsheet;
        $sheetIndex = 0;

        foreach ($reservations as $propertyTitle => $propertyReservations) {
            $sheet = ($sheetIndex === 0)
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();

            $spreadsheet->setActiveSheetIndex($sheetIndex++);
            $sheet->setTitle(substr($propertyTitle, 0, 31));

            $row = 1;

            // Main title
            $sheet->setCellValue("A{$row}", "RESERVA {$propertyTitle}");
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row += 2;

            $lastMonth = null;
            $prevReservation = null;

            foreach ($propertyReservations as $reservation) {
                $userName = $reservation->guest->name ?? '';
                $email = $reservation->guest->email ?? '';

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

                // Show the month only if it has changed
                if ($month !== $lastMonth) {
                    $sheet->setCellValue("A{$row}", strtoupper($month));
                    $lastMonth = $month;

                    // Only if there is a previous reservation
                    if ($prevReservation) {
                        $prevCheckOut = Carbon::parse($prevReservation->check_out);
                        if ($prevCheckOut->month === $checkInMonth) {
                            $prevName = $prevReservation->guest->name ?? '';
                            $prevCheckOutFormatted = $prevCheckOut->format('d.m.Y');
                            $row++;
                            $sheet->setCellValue("B{$row}", "Hasta {$prevCheckOutFormatted} {$prevName}");
                        }
                    }
                    $row++;
                }

                // Date line
                $sheet->setCellValue("B{$row}", "{$checkIn} - {$checkOut} {$userName}");
                $row++;

                $sheet->setCellValue("B{$row}", "{$userName}");
                $row++;

                $sheet->setCellValue("B{$row}", "{$email}");
                $row++;

                // Number of guests
                $sheet->setCellValue("B{$row}", "{$guests} personas");
                $row++;

                // Reservation ID
                $sheet->setCellValue("B{$row}", "ID reserva: {$id}");
                $row++;

                // Total price
                $sheet->setCellValue("B{$row}", "Total: {$totalPrice}");
                $row++;

                // Notes
                $sheet->setCellValue("B{$row}", "Observaciones: {$notes}");
                $row++;

                // Check-in and check-out days
                $sheet->setCellValue("B{$row}", "Día Llegada: {$checkIn}, Día Salida: {$checkOut}");
                $row++;

                // Check-in and check-out times
                $sheet->setCellValue("B{$row}", "Llegada: {$arrivalHour}, Salida: {$departureHour}");
                $row++;

                // Space between reservations
                $row++;

                $prevReservation = $reservation;
            }
        }

        // Save file temporarily
        $filename = 'Reservas_actualizado_' . date('d.m.Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        (new Xlsx($spreadsheet))->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
