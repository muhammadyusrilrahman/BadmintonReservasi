<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BadmintonReportExport;
use App\Http\Controllers\BaseController;
use App\Services\ReportingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends BaseController
{
    public function __construct(private readonly ReportingService $reportingService)
    {
    }

    /**
     * Halaman Keuangan — Grafik analytics & ringkasan keuangan.
     */
    public function finance(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        $period = $request->input('period', 'daily');

        // Dashboard stats
        $stats = $this->reportingService->getDashboardStats($startDate, $endDate);

        // Chart data
        $revenueTrend = $this->reportingService->getRevenueTrend($startDate, $endDate, $period);
        $courtOccupancy = $this->reportingService->getCourtOccupancy($startDate, $endDate);
        $paymentMethods = $this->reportingService->getPaymentMethodDistribution($startDate, $endDate);
        $ratingDistribution = $this->reportingService->getRatingDistribution($startDate, $endDate);

        // Recent transactions (last 10)
        $recentTransactions = $this->reportingService->getTransactionsReport($startDate, $endDate)->take(10);

        // Refund summary
        $refundSummary = $this->reportingService->getRefundsReport($startDate, $endDate);

        return view('admin.finance.index', [
            'title'              => 'Keuangan',
            'stats'              => $stats,
            'revenueTrend'       => $revenueTrend,
            'courtOccupancy'     => $courtOccupancy,
            'paymentMethods'     => $paymentMethods,
            'ratingDistribution' => $ratingDistribution,
            'recentTransactions' => $recentTransactions,
            'refundSummary'      => $refundSummary,
            'startDate'          => $startDate,
            'endDate'            => $endDate,
            'period'             => $period,
        ]);
    }

    /**
     * Halaman Laporan — Tabel data & export Excel/PDF.
     */
    public function index(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfDay();

        // Report types for tabs
        $reportTypes = ReportingService::getReportTypes();
        $activeType = $request->input('type', 'transactions');
        $reportData = $this->reportingService->getReportByType($activeType, $startDate, $endDate);
        $headings = $this->reportingService->getHeadingsByType($activeType);

        return view('admin.reports.index', [
            'title'       => 'Laporan',
            'reportTypes' => $reportTypes,
            'activeType'  => $activeType,
            'reportData'  => $reportData,
            'headings'    => $headings,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
        ]);
    }

    /**
     * Export report as Excel.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:transactions,reservations,payments,refunds,users,reviews',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $type = $request->input('type');
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $typeLabels = ReportingService::getReportTypes();
        $fileName = 'Laporan_' . ($typeLabels[$type] ?? $type) . '_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';

        return Excel::download(new BadmintonReportExport($type, $startDate, $endDate), $fileName);
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'type'       => 'required|in:transactions,reservations,payments,refunds,users,reviews',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $type = $request->input('type');
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $typeLabels = ReportingService::getReportTypes();
        $typeLabel = $typeLabels[$type] ?? $type;

        $data = $this->reportingService->getReportByType($type, $startDate, $endDate);
        $headings = $this->reportingService->getHeadingsByType($type);
        $stats = $this->reportingService->getDashboardStats($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'typeLabel' => $typeLabel,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'data'      => $data,
            'headings'  => $headings,
            'stats'     => $stats,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $fileName = 'Laporan_' . $typeLabel . '_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}

