<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportInvoicesRequest;
use App\Services\ExportService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly ExportService $exportService,
    ) {}

    /**
     * Download reservations Excel file.
     *
     * @return BinaryFileResponse
     */
    public function exportExcel()
    {
        return $this->exportService->downloadReservationsZip(
            Auth::user(),
        );
    }

    /**
     * Download invoices Excel file.
     *
     * @return BinaryFileResponse
     */
    public function exportInvoiceExcel(ExportInvoicesRequest $request)
    {
        $validated = $request->validated();

        return $this->exportService->downloadInvoicesExcel(
            Auth::user(),
            $validated['ids'] ?? [],
            $validated['invoice_amount'] ?? null,
        );
    }
}
