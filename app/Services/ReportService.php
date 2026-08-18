<?php

namespace App\Services;

use App\Enums\CleaningTaskStatus;
use App\Enums\MaintenanceRequestStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ReservationStatus;
use App\Models\CleaningTask;
use App\Models\Expense;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getKPIs(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $dateFrom = Carbon::parse($dateFrom)->startOfDay();
        $dateTo = Carbon::parse($dateTo)->endOfDay();

        $incomeQuery = Payment::where('status', PaymentStatus::Confirmed)
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        $expenseQuery = Expense::whereBetween('expense_date', [$dateFrom, $dateTo]);

        $reservationQuery = Reservation::whereBetween('check_in_date', [$dateFrom, $dateTo])
            ->whereNotIn('status', [ReservationStatus::Cancelled]);

        if ($accommodationId) {
            $incomeQuery->whereHas('reservation', fn ($q) => $q->where('accommodation_id', $accommodationId));
            $expenseQuery->where('accommodation_id', $accommodationId);
            $reservationQuery->where('accommodation_id', $accommodationId);
        }

        $totalIncome = (clone $incomeQuery)
            ->whereNotIn('type', [PaymentType::Refund, PaymentType::DepositReturn])
            ->sum('amount');

        $totalRefunds = (clone $incomeQuery)
            ->whereIn('type', [PaymentType::Refund, PaymentType::DepositReturn])
            ->sum('amount');

        $totalExpenses = (clone $expenseQuery)->sum('amount');

        $netBalance = round($totalIncome - $totalRefunds - $totalExpenses, 2);

        $totalReservations = (clone $reservationQuery)->count();

        $nightsCount = (clone $reservationQuery)->sum('nights_count');

        $daysInPeriod = $dateFrom->diffInDays($dateTo) + 1;

        $totalAccommodations = \App\Models\Accommodation::count();

        $occupancyRate = $totalAccommodations > 0 && $daysInPeriod > 0
            ? round(($nightsCount / ($totalAccommodations * $daysInPeriod)) * 100, 1)
            : 0;

        $avgDailyRevenue = $daysInPeriod > 0
            ? round($totalIncome / $daysInPeriod, 2)
            : 0;

        return [
            'total_income' => round($totalIncome, 2),
            'total_refunds' => round($totalRefunds, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_balance' => $netBalance,
            'total_reservations' => $totalReservations,
            'nights_count' => $nightsCount,
            'occupancy_rate' => $occupancyRate,
            'avg_daily_revenue' => $avgDailyRevenue,
            'days_in_period' => $daysInPeriod,
        ];
    }

    public function getMonthlyTrend(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $start = Carbon::parse($dateFrom)->startOfMonth();
        $end = Carbon::parse($dateTo)->endOfMonth();

        $incomeQuery = Payment::where('status', PaymentStatus::Confirmed)
            ->whereNotIn('type', [PaymentType::Refund, PaymentType::DepositReturn])
            ->whereBetween('payment_date', [$start, $end]);

        $expenseQuery = Expense::whereBetween('expense_date', [$start, $end]);

        if ($accommodationId) {
            $incomeQuery->whereHas('reservation', fn ($q) => $q->where('accommodation_id', $accommodationId));
            $expenseQuery->where('accommodation_id', $accommodationId);
        }

        $incomeByMonth = (clone $incomeQuery)
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $expenseByMonth = (clone $expenseQuery)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $months = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $key = $current->format('Y-m');
            $months[$key] = [
                'month' => $current->format('M Y'),
                'income' => round($incomeByMonth[$key] ?? 0, 2),
                'expenses' => round($expenseByMonth[$key] ?? 0, 2),
            ];
            $current->addMonth();
        }

        return array_values($months);
    }

    public function getExpensesByCategory(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $query = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category_name, expense_categories.color as category_color, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.name', 'expense_categories.color');

        if ($accommodationId) {
            $query->where('expenses.accommodation_id', $accommodationId);
        }

        $results = $query->get()->toArray();

        $colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#6b7280', '#14b8a6'];

        return array_map(fn ($item, $i) => [
            'name' => $item['category_name'],
            'total' => round($item['total'], 2),
            'color' => $item['category_color'] ?? $colors[$i % count($colors)],
        ], $results, array_keys($results));
    }

    public function getIncomeByMethod(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $query = Payment::where('status', PaymentStatus::Confirmed)
            ->whereNotIn('type', [PaymentType::Refund, PaymentType::DepositReturn])
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->selectRaw('method, SUM(amount) as total')
            ->groupBy('method');

        if ($accommodationId) {
            $query->whereHas('reservation', fn ($q) => $q->where('accommodation_id', $accommodationId));
        }

        $results = $query->get();

        return $results->map(fn ($item) => [
            'method' => $item->method->label(),
            'total' => round($item->total, 2),
        ])->toArray();
    }

    public function getTopAccommodations(string $dateFrom, string $dateTo, ?int $accommodationId = null, int $limit = 5): array
    {
        $query = Payment::where('payments.status', PaymentStatus::Confirmed)
            ->whereNotIn('payments.type', [PaymentType::Refund, PaymentType::DepositReturn])
            ->whereBetween('payments.payment_date', [$dateFrom, $dateTo])
            ->join('reservations', 'payments.reservation_id', '=', 'reservations.id')
            ->join('accommodations', 'reservations.accommodation_id', '=', 'accommodations.id')
            ->selectRaw('accommodations.name as name, SUM(payments.amount) as total')
            ->groupBy('accommodations.name')
            ->orderByDesc('total')
            ->limit($limit);

        if ($accommodationId) {
            $query->where('accommodations.id', $accommodationId);
        }

        return $query->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'total' => round($item->total, 2),
            ])
            ->toArray();
    }

    public function getReservationStatusDistribution(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $query = Reservation::whereBetween('check_in_date', [$dateFrom, $dateTo]);

        if ($accommodationId) {
            $query->where('accommodation_id', $accommodationId);
        }

        $counts = (clone $query)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusColors = [
            'pending' => '#f59e0b',
            'confirmed' => '#3b82f6',
            'checked_in' => '#22c55e',
            'checked_out' => '#6b7280',
            'cancelled' => '#ef4444',
            'no_show' => '#8b5cf6',
        ];

        return array_map(fn ($status) => [
            'status' => $status,
            'total' => $counts[$status] ?? 0,
            'color' => $statusColors[$status] ?? '#6b7280',
        ], array_keys($statusColors));
    }

    public function getDailyRevenue(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $start = Carbon::parse($dateFrom)->startOfDay();
        $end = Carbon::parse($dateTo)->endOfDay();

        $query = Payment::where('status', PaymentStatus::Confirmed)
            ->whereNotIn('type', [PaymentType::Refund, PaymentType::DepositReturn])
            ->whereBetween('payment_date', [$start, $end])
            ->selectRaw("DATE(payment_date) as day, SUM(amount) as total")
            ->groupBy('day')
            ->orderBy('day');

        if ($accommodationId) {
            $query->whereHas('reservation', fn ($q) => $q->where('accommodation_id', $accommodationId));
        }

        $results = $query->pluck('total', 'day')->toArray();

        $days = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $key = $current->toDateString();
            $days[] = [
                'date' => $current->format('d/m'),
                'total' => round($results[$key] ?? 0, 2),
            ];
            $current->addDay();
        }

        return $days;
    }

    public function getRecentTransactions(string $dateFrom, string $dateTo, ?int $accommodationId = null, int $limit = 10): array
    {
        $paymentsQuery = Payment::with('reservation.accommodation')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->where('status', PaymentStatus::Confirmed);

        $expensesQuery = Expense::with('accommodation', 'expenseCategory')
            ->whereBetween('expense_date', [$dateFrom, $dateTo]);

        if ($accommodationId) {
            $paymentsQuery->whereHas('reservation', fn ($q) => $q->where('accommodation_id', $accommodationId));
            $expensesQuery->where('accommodation_id', $accommodationId);
        }

        $payments = (clone $paymentsQuery)
            ->get()
            ->map(fn ($p) => [
                'type' => 'income',
                'type_label' => $p->type->label(),
                'date' => $p->payment_date->format('d/m/Y'),
                'concept' => $p->reference ?? 'Pago ' . $p->code,
                'accommodation' => $p->reservation?->accommodation?->name ?? '-',
                'amount' => round($p->amount, 2),
                'status' => $p->status->label(),
            ]);

        $expenses = (clone $expensesQuery)
            ->get()
            ->map(fn ($e) => [
                'type' => 'expense',
                'type_label' => $e->expenseCategory?->name ?? 'Gasto',
                'date' => $e->expense_date->format('d/m/Y'),
                'concept' => $e->title,
                'accommodation' => $e->accommodation?->name ?? '-',
                'amount' => round($e->amount, 2),
                'status' => $e->is_approved ? 'Aprobado' : 'Pendiente',
            ]);

        return $payments->concat($expenses)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function getCleaningSummary(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $taskQuery = CleaningTask::whereBetween('scheduled_at', [$dateFrom, $dateTo]);
        $feeQuery = Reservation::whereBetween('check_in_date', [$dateFrom, $dateTo])
            ->whereNotIn('status', [ReservationStatus::Cancelled]);
        $expenseQuery = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->whereHas('expenseCategory', fn ($q) => $q->where('slug', 'cleaning'));

        if ($accommodationId) {
            $taskQuery->where('accommodation_id', $accommodationId);
            $feeQuery->where('accommodation_id', $accommodationId);
            $expenseQuery->where('accommodation_id', $accommodationId);
        }

        $taskCounts = (clone $taskQuery)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalTasks = array_sum($taskCounts);

        $totalFeesCollected = (clone $feeQuery)->sum('cleaning_fee');

        $totalCleaningExpenses = (clone $expenseQuery)->sum('amount');

        $allStatuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $statusColors = [
            'pending' => '#f59e0b',
            'assigned' => '#3b82f6',
            'in_progress' => '#06b6d4',
            'completed' => '#22c55e',
            'cancelled' => '#6b7280',
        ];

        return [
            'total_tasks' => $totalTasks,
            'total_fees_collected' => round($totalFeesCollected, 2),
            'total_expenses' => round($totalCleaningExpenses, 2),
            'net_cleaning' => round($totalFeesCollected - $totalCleaningExpenses, 2),
            'by_status' => array_map(fn ($s) => [
                'status' => $s,
                'total' => $taskCounts[$s] ?? 0,
                'color' => $statusColors[$s],
            ], $allStatuses),
        ];
    }

    public function getMaintenanceSummary(string $dateFrom, string $dateTo, ?int $accommodationId = null): array
    {
        $query = MaintenanceRequest::whereBetween('reported_at', [$dateFrom, $dateTo]);

        $expenseQuery = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->whereHas('expenseCategory', fn ($q) => $q->where('slug', 'maintenance'));

        if ($accommodationId) {
            $query->where('accommodation_id', $accommodationId);
            $expenseQuery->where('accommodation_id', $accommodationId);
        }

        $taskCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalTasks = array_sum($taskCounts);

        $totalEstimated = (clone $query)->sum('estimated_cost');
        $totalActual = (clone $query)->sum('actual_cost');

        $totalMaintenanceExpenses = (clone $expenseQuery)->sum('amount');

        $allStatuses = ['reported', 'scheduled', 'in_progress', 'completed', 'cancelled'];
        $statusColors = [
            'reported' => '#ef4444',
            'scheduled' => '#f59e0b',
            'in_progress' => '#3b82f6',
            'completed' => '#22c55e',
            'cancelled' => '#6b7280',
        ];

        return [
            'total_tasks' => $totalTasks,
            'total_estimated' => round($totalEstimated, 2),
            'total_actual' => round($totalActual, 2),
            'total_expenses' => round($totalMaintenanceExpenses, 2),
            'by_status' => array_map(fn ($s) => [
                'status' => $s,
                'total' => $taskCounts[$s] ?? 0,
                'color' => $statusColors[$s],
            ], $allStatuses),
        ];
    }
}
