<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Illuminate\Support\Facades\Response;
use App\Models\Reservation;

class FacturasExport
{
    public static function download(array $ids, $invoiceAmount)
    {
        $reservations = Reservation::with(['user', 'property'])
            ->whereIn('id', $ids)
            ->orderBy('check_in')
            ->get();

        $spreadsheet = new Spreadsheet();
        $facturaNumber = intval($invoiceAmount);
        if ($facturaNumber < 10) {
            $facturaNumber = 0 . $facturaNumber;
        }



        $sheetIndex = 0;

        foreach ($reservations as $reservation) {
            if ($sheetIndex === 0) {
                $sheet = $spreadsheet->getActiveSheet();
            } else {
                $sheet = $spreadsheet->createSheet();
            }

            $address = array_map('trim', explode(',', $reservation->property->location));
            if ($reservation->property->title == 'El Galeon') {
                $sheet->setCellValue('B2', 'TONIRETOOS SL');
                $sheet->setCellValue('B3', 'B35632223');
                $sheet->setCellValue('B4', 'APARTAMENTO EL GALEON');
                $sheet->setCellValue('B5', 'VALLE DE LA DEGOLLADA 63');
                $sheet->setCellValue('B6', 'LA DEGOLLADA, 35570 YAIZA');
            } else {
                $sheet->setCellValue('B2', 'OSCAR SEPULVEDA GUTIERREZ');
                $sheet->setCellValue('B3', '45532610Q');
                $sheet->setCellValue('B4', strtoupper($reservation->property->title));
                $sheet->setCellValue('B5', strtoupper($address[0] ?? ''));
                $sheet->setCellValue('B6', strtoupper($address[1] ?? ''));
            }
            $sheet->setTitle("Factura {$facturaNumber}");



            $sheet->setCellValue('B7', 'LANZAROTE');
            $sheet->setCellValue('B10', 'Factura: ' . $facturaNumber . '/' . date('y'));

            $headers = ['ENTRADA', 'SALIDA', 'DIAS',  'NOMBRE CLIENTE', 'IGIC', 'IMPORTE BASE IMPONIBLE', 'IMPORTE TOTAL'];
            $sheet->fromArray($headers, null, 'C30');

            // Estilo para cabecera (negrita y centrado)
            $headerStyle = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ];

            $sheet->getStyle('B2:M60')->applyFromArray(
                [
                    'font' => ['size' => 40]
                ]
            );

            $sheet->getStyle('C30:I30')->applyFromArray($headerStyle);

            $userName = $reservation->user->name ?? '';
            $checkIn = \Carbon\Carbon::parse($reservation->check_in)->format('d.m.Y');
            $checkOut = \Carbon\Carbon::parse($reservation->check_out)->format('d.m.Y');
            $days = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut);
            $baseImponible = $reservation->total_price ?? 'N/A';
            $igic = $baseImponible * 0.07;
            $importeTotal = $baseImponible + $igic;

            $data = [
                $checkIn,
                $checkOut,
                $days,
                $userName,
                number_format($igic, 2, '.') . ' €',
                number_format($baseImponible, 2, '.') . ' €',
                number_format($importeTotal, 2, '.') . ' €',
            ];

            $sheet->fromArray($data, null, 'C31');

            $sheet->getStyle('C31:I31')->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            foreach (range('C', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $sheet->getPageSetup()
                ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
                ->setFitToPage(true)
                ->setFitToWidth(1)
                ->setFitToHeight(0);

            $sheet->getPageSetup()->setHorizontalCentered(true);

            $facturaNumber++;
            $sheetIndex++;
        }

        $filename = 'Facturas' . date('d.m.Y') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        (new Xlsx($spreadsheet))->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
