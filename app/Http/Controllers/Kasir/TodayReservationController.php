<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Models\Reservation;
use App\Models\Court;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TodayReservationController extends BaseController
{
    /**
     * Display a listing of today's reservations.
     */
    public function index(Request $request)
    {
        $selectedCourt = $request->input('court_id');
        $status = $request->input('status');

        $query = Reservation::with(['user', 'court', 'payment'])
            ->whereDate('date', Carbon::today())
            ->orderBy('start_time');

        if ($selectedCourt) {
            $query->where('court_id', $selectedCourt);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reservations = $query->get();
        $courts = Court::where('is_active', true)->get();

        // Metrics
        $totalReservations = $reservations->count();
        $totalPaid = $reservations->where('status', 'confirmed')->count(); // Or where payment status is paid
        $totalPending = $reservations->where('status', 'pending')->count();

        return view('kasir.today.index', [
            'title'             => 'Reservasi Hari Ini',
            'reservations'      => $reservations,
            'courts'            => $courts,
            'selectedCourt'     => $selectedCourt,
            'status'            => $status,
            'totalReservations' => $totalReservations,
            'totalPaid'         => $totalPaid,
            'totalPending'      => $totalPending,
        ]);
    }
}
