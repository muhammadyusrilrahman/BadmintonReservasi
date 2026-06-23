<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends BaseController
{
    /**
     * Display the court schedule for staff.
     * Supports date navigation and court filtering.
     */
    public function index(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $courtId = $request->input('court_id');

        $courts = Court::orderBy('name')->get();

        // Build reservation query for selected date
        $reservationsQuery = Reservation::with(['user', 'court', 'payment'])
            ->whereDate('date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed']);

        if ($courtId) {
            $reservationsQuery->where('court_id', $courtId);
        }

        $reservations = $reservationsQuery
            ->orderBy('start_time')
            ->get();

        // Group by court for the schedule grid
        $scheduleByCourtId = $reservations->groupBy('court_id');

        // Build the schedule grid: an array of courts, each with its reservations
        $scheduleGrid = $courts->map(function ($court) use ($scheduleByCourtId) {
            return (object) [
                'court' => $court,
                'reservations' => $scheduleByCourtId->get($court->id, collect()),
            ];
        });

        // If filtered by court, only show that court
        if ($courtId) {
            $scheduleGrid = $scheduleGrid->filter(fn($item) => $item->court->id == $courtId);
        }

        // Stats for the selected date
        $totalReservations = $reservations->count();
        $confirmedCount = $reservations->where('status', 'confirmed')->count();
        $completedCount = $reservations->where('status', 'completed')->count();
        $pendingCount = $reservations->where('status', 'pending')->count();

        return view('staff.schedule.index', [
            'title'             => 'Jadwal Reservasi',
            'date'              => $date,
            'courts'            => $courts,
            'selectedCourt'     => $courtId,
            'scheduleGrid'      => $scheduleGrid,
            'totalReservations' => $totalReservations,
            'confirmedCount'    => $confirmedCount,
            'completedCount'    => $completedCount,
            'pendingCount'      => $pendingCount,
        ]);
    }
}
