<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();

        if (!$user || !$user->role) {
            return view('dashboard');
        }

        $roleDashboard = $user->role->permissions()
            ->where('slug', 'like', 'dashboard.%')
            ->where('slug', '!=', 'dashboard')
            ->first();

        if ($roleDashboard) {
            $viewName = str_replace('dashboard.', 'dashboards.', $roleDashboard->slug);

            if (view()->exists($viewName)) {
                if ($viewName === 'dashboards.admin') {
                    $service = new DashboardService();

                    $todaySnapshot = $service->getTodaySnapshot();
                    $attentionItems = $service->getAttentionItems();
                    $upcomingArrivals = $service->getUpcomingArrivals();
                    $quickStats = $service->getQuickStats();
                    $accommodationSummary = $service->getAccommodationSummary();
                    $reservationSummary = $service->getReservationSummary();
                    $quoteSummary = $service->getQuoteSummary();

                    return view($viewName, compact(
                        'user',
                        'todaySnapshot',
                        'attentionItems',
                        'upcomingArrivals',
                        'quickStats',
                        'accommodationSummary',
                        'reservationSummary',
                        'quoteSummary',
                    ));
                }

                return view($viewName);
            }
        }

        return view('dashboard');
    }
}
