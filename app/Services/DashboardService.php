<?php

namespace App\Services;

use App\Enums\AccommodationStatus;
use App\Enums\CleaningTaskStatus;
use App\Enums\MaintenanceRequestStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\QuoteStatus;
use App\Enums\ReservationStatus;
use App\Models\Accommodation;
use App\Models\CleaningTask;
use App\Models\Guest;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\Reservation;

class DashboardService
{
    public function getTodaySnapshot(): array
    {
        $today = now()->toDateString();

        return [
            'check_ins_today' => Reservation::where('check_in_date', $today)
                ->whereIn('status', [ReservationStatus::Pending, ReservationStatus::Confirmed])
                ->count(),
            'check_outs_today' => Reservation::where('check_out_date', $today)
                ->where('status', ReservationStatus::CheckedIn)
                ->count(),
            'occupied_now' => Accommodation::where('status', AccommodationStatus::Occupied)->count(),
            'active_reservations' => Reservation::whereIn('status', [
                ReservationStatus::Pending,
                ReservationStatus::Confirmed,
                ReservationStatus::CheckedIn,
            ])->count(),
        ];
    }

    public function getAttentionItems(): array
    {
        return [
            'pending_cleanings' => CleaningTask::whereIn('status', [
                CleaningTaskStatus::Pending,
                CleaningTaskStatus::Assigned,
            ])->count(),
            'pending_payments' => Payment::where('status', PaymentStatus::Pending)->count(),
            'open_maintenance' => MaintenanceRequest::whereIn('status', [
                MaintenanceRequestStatus::Reported,
                MaintenanceRequestStatus::Scheduled,
                MaintenanceRequestStatus::InProgress,
            ])->count(),
            'pending_reservations' => Reservation::where('status', ReservationStatus::Pending)->count(),
        ];
    }

    public function getUpcomingArrivals(): array
    {
        $today = now()->toDateString();

        return Reservation::with('accommodation', 'primaryGuest')
            ->whereIn('status', [ReservationStatus::Pending, ReservationStatus::Confirmed])
            ->where('check_in_date', '>=', $today)
            ->orderBy('check_in_date')
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'code' => $r->code,
                'guest_name' => $r->primaryGuest ? $r->primaryGuest->fullName() : 'Sin asignar',
                'accommodation' => $r->accommodation ? $r->accommodation->name : '-',
                'check_in_date' => $r->check_in_date->format('d/m'),
                'status' => $r->status->value,
            ])
            ->toArray();
    }

    public function getQuickStats(): array
    {
        return [
            'total_accommodations' => Accommodation::count(),
            'total_guests' => Guest::count(),
            'total_reservations' => Reservation::count(),
        ];
    }

    public function getAccommodationSummary(): array
    {
        $counts = Accommodation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($counts);

        return [
            'total' => $total,
            'available' => $counts[AccommodationStatus::Available->value] ?? 0,
            'occupied' => $counts[AccommodationStatus::Occupied->value] ?? 0,
            'reserved' => $counts[AccommodationStatus::Reserved->value] ?? 0,
            'pending_cleaning' => $counts[AccommodationStatus::PendingCleaning->value] ?? 0,
            'cleaning' => $counts[AccommodationStatus::Cleaning->value] ?? 0,
            'maintenance' => $counts[AccommodationStatus::Maintenance->value] ?? 0,
            'blocked' => $counts[AccommodationStatus::Blocked->value] ?? 0,
        ];
    }

    public function getReservationSummary(): array
    {
        $counts = Reservation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'pending' => $counts[ReservationStatus::Pending->value] ?? 0,
            'confirmed' => $counts[ReservationStatus::Confirmed->value] ?? 0,
            'checked_in' => $counts[ReservationStatus::CheckedIn->value] ?? 0,
            'checked_out' => $counts[ReservationStatus::CheckedOut->value] ?? 0,
        ];
    }

    public function getQuoteSummary(): array
    {
        $counts = Quote::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'draft' => $counts[QuoteStatus::Draft->value] ?? 0,
            'sent' => $counts[QuoteStatus::Sent->value] ?? 0,
            'accepted' => $counts[QuoteStatus::Accepted->value] ?? 0,
            'rejected' => $counts[QuoteStatus::Rejected->value] ?? 0,
            'expired' => $counts[QuoteStatus::Expired->value] ?? 0,
            'converted' => $counts[QuoteStatus::Converted->value] ?? 0,
        ];
    }
}
