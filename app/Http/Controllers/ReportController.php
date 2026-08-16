<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Si hay autorización para 'reports.index', la verificamos, pero como no hay un modelo específico,
        // validamos si el usuario tiene el permiso manualmente.
        if (!auth()->user()->hasRole('admin') && !(auth()->user()->role && auth()->user()->role->permissions()->where('slug', 'reports.index')->exists())) {
            abort(403, 'No tienes permiso para ver reportes.');
        }

        // Lógica básica de reporte para mostrar en la vista
        $totalReservations = Reservation::count();
        $totalIncome = Payment::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $balance = $totalIncome - $totalExpenses;

        return view('reports.index', compact('totalReservations', 'totalIncome', 'totalExpenses', 'balance'));
    }
}
