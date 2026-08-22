<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        if (! auth()->user()->hasRole('admin')
            && ! (auth()->user()->role
                && auth()->user()->role->permissions()
                    ->where('slug', 'reports.index')->exists())
        ) {
            abort(403, 'No tienes permiso para ver reportes.');
        }

        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $accommodationId = $request->query('accommodation_id');

        $report = new ReportService();
        $kpis = $report->getKPIs($dateFrom, $dateTo, $accommodationId);
        $gross = $report->getGrossSummary($dateFrom, $dateTo, $accommodationId);
        $cleaning = $report->getCleaningSummary($dateFrom, $dateTo, $accommodationId);
        $maintenance = $report->getMaintenanceSummary($dateFrom, $dateTo, $accommodationId);
        $paymentsSummary = $report->getPaymentsSummary($dateFrom, $dateTo, $accommodationId);
        $accommodations = Accommodation::orderBy('name')->get();

        return view('reports.index', compact(
            'kpis',
            'gross',
            'cleaning',
            'maintenance',
            'paymentsSummary',
            'dateFrom',
            'dateTo',
            'accommodationId',
            'accommodations'
        ));
    }

    public function data(Request $request)
    {
        $dateFrom = $request->query('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', now()->toDateString());
        $accommodationId = $request->query('accommodation_id');

        $report = new ReportService();

        return response()->json([
            'monthly_trend' => $report->getMonthlyTrend($dateFrom, $dateTo, $accommodationId),
            'income_by_method' => $report->getIncomeByMethod($dateFrom, $dateTo, $accommodationId),
            'top_accommodations' => $report->getTopAccommodations($dateFrom, $dateTo, $accommodationId),
            'reservation_status' => $report->getReservationStatusDistribution($dateFrom, $dateTo, $accommodationId),
            'recent_transactions' => $report->getRecentTransactions($dateFrom, $dateTo, $accommodationId),
            'daily_revenue' => $report->getDailyRevenue($dateFrom, $dateTo, $accommodationId),
        ]);
    }
}
