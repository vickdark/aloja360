<?php

namespace App\Http\Controllers;

use App\Enums\CleaningTaskStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceRequestStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\QuoteStatus;
use App\Enums\ReservationStatus;
use App\Models\Accommodation;
use App\Models\AuditLog;
use App\Models\CleaningTask;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\Reservation;
use App\Models\Usuarios\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Redirige al usuario a su dashboard específico basado en permisos.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\Usuarios\Usuario \$user */
        $user = Auth::user();

        // Validamos que el usuario y su rol existan para evitar errores
        if (!$user || !$user->role) {
            return view('dashboard');
        }

        // 1. Si el usuario tiene un permiso específico de dashboard (ej: dashboard.admin, dashboard.vendedor)
        // Buscamos si tiene algún permiso que empiece por 'dashboard.' y no sea el genérico
        $roleDashboard = $user->role->permissions()
            ->where('slug', 'like', 'dashboard.%')
            ->where('slug', '!=', 'dashboard') // Ajustado para comparar con el slug base
            ->first();

        if ($roleDashboard) {
            // Si tiene un dashboard específico, intentamos cargar esa vista
            // Por convención: dashboard.{rol} -> views/dashboards/{rol}.blade.php
            $viewName = str_replace('dashboard.', 'dashboards.', $roleDashboard->slug);
            
            if (view()->exists($viewName)) {
                if ($roleDashboard->slug === 'dashboard.admin') {
                    return view($viewName, $this->buildAdminDashboardData($user, $request));
                }

                return view($viewName);
            }
        }

        // 2. Dashboard genérico por defecto
        return view('dashboard');
    }

    /**
     * Prepara un overview administrativo con foco en operación, caja y alertas.
     */
    private function buildAdminDashboardData(Usuario $user, Request $request): array
    {
        $today = now()->startOfDay();
        $weekEnd = $today->copy()->addDays(7)->endOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        $activeReservationStatuses = [
            ReservationStatus::Pending->value,
            ReservationStatus::Confirmed->value,
            ReservationStatus::CheckedIn->value,
        ];

        $accommodationStatusCounts = Accommodation::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalAccommodations = (int) $accommodationStatusCounts->sum();
        $occupiedAccommodations = (int) $accommodationStatusCounts->get('occupied', 0);
        $occupancyRate = $totalAccommodations > 0
            ? round(($occupiedAccommodations / $totalAccommodations) * 100, 1)
            : 0;

        $todayCheckIns = Reservation::query()
            ->whereIn('status', $activeReservationStatuses)
            ->whereDate('check_in_date', $today)
            ->count();

        $todayCheckOuts = Reservation::query()
            ->whereIn('status', [
                ReservationStatus::Confirmed->value,
                ReservationStatus::CheckedIn->value,
            ])
            ->whereDate('check_out_date', $today)
            ->count();

        $monthRevenue = $this->sumSignedPayments(
            Payment::query()
                ->where('status', PaymentStatus::Confirmed->value)
                ->whereBetween('payment_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->get(['type', 'amount'])
        );

        $monthSales = (float) Reservation::query()
            ->whereNotIn('status', [
                ReservationStatus::Cancelled->value,
                ReservationStatus::NoShow->value,
            ])
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_amount');

        $monthExpenses = (float) Expense::query()
            ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('amount');

        $activeReservations = Reservation::query()
            ->whereIn('status', $activeReservationStatuses)
            ->with([
                'payments' => fn ($query) => $query
                    ->where('status', PaymentStatus::Confirmed->value)
                    ->select('id', 'reservation_id', 'type', 'amount'),
            ])
            ->get(['id', 'total_amount']);

        $outstandingBalance = (float) $activeReservations->sum(function (Reservation $reservation) {
            return $reservation->outstanding_balance;
        });

        $pendingReservations = Reservation::query()
            ->where('status', ReservationStatus::Pending->value)
            ->count();

        $upcomingReservationsCount = Reservation::query()
            ->whereIn('status', [
                ReservationStatus::Pending->value,
                ReservationStatus::Confirmed->value,
            ])
            ->whereBetween('check_in_date', [$today->toDateString(), $weekEnd->toDateString()])
            ->count();

        $expiringQuotes = Quote::query()
            ->whereIn('status', [
                QuoteStatus::Draft->value,
                QuoteStatus::Sent->value,
            ])
            ->whereBetween('expires_at', [$today, $weekEnd])
            ->count();

        $convertedQuotes = Quote::query()
            ->where('status', QuoteStatus::Converted->value)
            ->count();

        $totalQuotes = Quote::count();
        $quoteConversionRate = $totalQuotes > 0
            ? round(($convertedQuotes / $totalQuotes) * 100, 1)
            : 0;

        $cleaningPending = CleaningTask::query()
            ->whereIn('status', [
                CleaningTaskStatus::Pending->value,
                CleaningTaskStatus::Assigned->value,
                CleaningTaskStatus::InProgress->value,
            ])
            ->count();

        $maintenanceOpen = MaintenanceRequest::query()
            ->whereIn('status', [
                MaintenanceRequestStatus::Reported->value,
                MaintenanceRequestStatus::Scheduled->value,
                MaintenanceRequestStatus::InProgress->value,
            ])
            ->count();

        $criticalMaintenance = MaintenanceRequest::query()
            ->where('priority', MaintenancePriority::Critical->value)
            ->whereIn('status', [
                MaintenanceRequestStatus::Reported->value,
                MaintenanceRequestStatus::Scheduled->value,
                MaintenanceRequestStatus::InProgress->value,
            ])
            ->count();

        $inventoryAlerts = InventoryItem::query()
            ->whereNotNull('reorder_threshold')
            ->whereColumn('current_quantity', '<=', 'reorder_threshold')
            ->count();

        $upcomingReservations = Reservation::query()
            ->with(['accommodation:id,name', 'primaryGuest:id,first_name,last_name'])
            ->whereIn('status', [
                ReservationStatus::Pending->value,
                ReservationStatus::Confirmed->value,
            ])
            ->whereDate('check_in_date', '>=', $today)
            ->orderBy('check_in_date')
            ->limit(5)
            ->get();

        $recentActivity = AuditLog::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit(6)
            ->get();

        $alerts = collect([
            [
                'label' => 'Reservas pendientes de confirmar',
                'value' => $pendingReservations,
                'context' => 'Revisar solicitudes sin confirmación para no perder ventas.',
                'route' => route('reservations.index', ['status' => ReservationStatus::Pending->value]),
                'action' => 'Ver reservas',
                'tone' => $pendingReservations > 0 ? 'warning' : 'success',
            ],
            [
                'label' => 'Cotizaciones por vencer',
                'value' => $expiringQuotes,
                'context' => 'Haz seguimiento comercial antes de que caduquen.',
                'route' => route('quotes.index'),
                'action' => 'Ver cotizaciones',
                'tone' => $expiringQuotes > 0 ? 'warning' : 'success',
            ],
            [
                'label' => 'Mantenimientos críticos abiertos',
                'value' => $criticalMaintenance,
                'context' => 'Prioriza incidencias que pueden sacar unidades de operación.',
                'route' => route('maintenance.index'),
                'action' => 'Ver mantenimiento',
                'tone' => $criticalMaintenance > 0 ? 'danger' : 'success',
            ],
            [
                'label' => 'Inventario bajo mínimo',
                'value' => $inventoryAlerts,
                'context' => 'Reponer insumos evita afectar limpieza y experiencia del huésped.',
                'route' => route('inventory.index'),
                'action' => 'Ver inventario',
                'tone' => $inventoryAlerts > 0 ? 'warning' : 'success',
            ],
        ]);

        $accommodationBreakdown = [
            [
                'label' => 'Disponibles',
                'value' => (int) $accommodationStatusCounts->get('available', 0),
                'class' => 'success',
            ],
            [
                'label' => 'Ocupados',
                'value' => $occupiedAccommodations,
                'class' => 'primary',
            ],
            [
                'label' => 'Limpieza pendiente',
                'value' => (int) $accommodationStatusCounts->get('pending_cleaning', 0),
                'class' => 'warning',
            ],
            [
                'label' => 'Mantenimiento',
                'value' => (int) $accommodationStatusCounts->get('maintenance', 0),
                'class' => 'danger',
            ],
            [
                'label' => 'Bloqueados',
                'value' => (int) $accommodationStatusCounts->get('blocked', 0),
                'class' => 'secondary',
            ],
        ];

        return [
            'pageTitle' => 'Centro de control administrativo',
            'pageSubtitle' => 'Prioriza operación, cobranza y pendientes críticos desde un solo lugar.',
            'sessionSummary' => [
                'user' => $user->name,
                'role' => optional($user->role)->nombre ?? 'Sin rol',
                'ip' => $request->ip(),
                'user_agent' => Str::limit($request->userAgent() ?? 'No disponible', 60),
                'generated_at' => now(),
            ],
            'primaryMetrics' => [
                [
                    'label' => 'Ocupación actual',
                    'value' => number_format($occupancyRate, 1).'%',
                    'meta' => $occupiedAccommodations.' de '.$totalAccommodations.' alojamientos ocupados',
                    'icon' => 'fa-bed',
                    'class' => 'primary',
                ],
                [
                    'label' => 'Check-ins de hoy',
                    'value' => $todayCheckIns,
                    'meta' => 'Entradas previstas para la jornada',
                    'icon' => 'fa-door-open',
                    'class' => 'success',
                ],
                [
                    'label' => 'Ingresos cobrados del mes',
                    'value' => '$'.number_format($monthRevenue, 2),
                    'meta' => 'Pagos confirmados registrados este mes',
                    'icon' => 'fa-wallet',
                    'class' => 'info',
                ],
                [
                    'label' => 'Saldo pendiente',
                    'value' => '$'.number_format($outstandingBalance, 2),
                    'meta' => 'Monto aún por cobrar de reservas activas',
                    'icon' => 'fa-file-invoice-dollar',
                    'class' => 'warning',
                ],
            ],
            'operations' => [
                ['label' => 'Check-outs de hoy', 'value' => $todayCheckOuts],
                ['label' => 'Reservas próximas 7 días', 'value' => $upcomingReservationsCount],
                ['label' => 'Limpiezas pendientes', 'value' => $cleaningPending],
                ['label' => 'Mantenimientos abiertos', 'value' => $maintenanceOpen],
            ],
            'commercial' => [
                ['label' => 'Reservas pendientes', 'value' => $pendingReservations],
                ['label' => 'Cotizaciones por vencer', 'value' => $expiringQuotes],
                ['label' => 'Conversión de cotizaciones', 'value' => number_format($quoteConversionRate, 1).'%'],
                ['label' => 'Ventas generadas este mes', 'value' => '$'.number_format($monthSales, 2)],
            ],
            'finance' => [
                ['label' => 'Ingresos cobrados', 'value' => '$'.number_format($monthRevenue, 2)],
                ['label' => 'Gastos del mes', 'value' => '$'.number_format($monthExpenses, 2)],
                ['label' => 'Resultado neto', 'value' => '$'.number_format($monthRevenue - $monthExpenses, 2)],
                ['label' => 'Saldo pendiente', 'value' => '$'.number_format($outstandingBalance, 2)],
            ],
            'accommodationBreakdown' => $accommodationBreakdown,
            'alerts' => $alerts,
            'upcomingReservations' => $upcomingReservations,
            'recentActivity' => $recentActivity,
            'quickActions' => [
                ['label' => 'Nueva reserva', 'route' => route('reservations.create'), 'icon' => 'fa-calendar-plus', 'class' => 'primary'],
                ['label' => 'Registrar pago', 'route' => route('payments.create'), 'icon' => 'fa-money-bill-wave', 'class' => 'success'],
                ['label' => 'Crear cotización', 'route' => route('quotes.create'), 'icon' => 'fa-file-signature', 'class' => 'info'],
                ['label' => 'Revisar reportes', 'route' => route('reports.index'), 'icon' => 'fa-chart-line', 'class' => 'secondary'],
                ['label' => 'Programar limpieza', 'route' => route('cleaning.create'), 'icon' => 'fa-soap', 'class' => 'warning'],
                ['label' => 'Sincronizar permisos', 'route' => route('permissions.sync'), 'icon' => 'fa-rotate', 'class' => 'dark', 'method' => 'post'],
            ],
        ];
    }

    private function sumSignedPayments($payments): float
    {
        return (float) $payments->sum(function (Payment $payment) {
            $sign = in_array($payment->type?->value, [
                PaymentType::Refund->value,
                PaymentType::DepositReturn->value,
            ], true) ? -1 : 1;

            return $sign * (float) $payment->amount;
        });
    }
}
