<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\CourtSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CourtScheduleController extends BaseController
{
    /**
     * Display a listing of the schedules for the court.
     */
    public function index(Court $court)
    {
        $schedules = $court->schedules()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('admin.courts.schedules.index', [
            'title' => "Kelola Jadwal: {$court->name}",
            'court' => $court,
            'schedules' => $schedules,
            'days' => [
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                0 => 'Minggu',
            ]
        ]);
    }

    /**
     * Store a newly created schedule or generate bulk slots.
     */
    public function store(Request $request, Court $court)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|array',
            'day_of_week.*' => 'integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price' => 'required|integer|min:0',
            'generate_hourly' => 'nullable|boolean'
        ]);

        $days = $validated['day_of_week'];
        $price = $validated['price'];
        $generateHourly = $request->boolean('generate_hourly', false);

        $start = Carbon::createFromFormat('H:i', $validated['start_time']);
        $end = Carbon::createFromFormat('H:i', $validated['end_time']);

        $slotsCreated = 0;

        foreach ($days as $day) {
            if ($generateHourly) {
                // Generate 1 hour slots
                $current = $start->copy();
                while ($current < $end) {
                    $slotStart = $current->format('H:i:s');
                    $next = $current->copy()->addHour();
                    if ($next > $end) {
                        break;
                    }
                    $slotEnd = $next->format('H:i:s');

                    // Delete existing overlapping slots to prevent duplicates
                    $this->deleteOverlappingSlots($court, $day, $slotStart, $slotEnd);

                    $court->schedules()->create([
                        'day_of_week' => $day,
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                        'price' => $price,
                        'is_active' => true,
                    ]);
                    $slotsCreated++;

                    $current->addHour();
                }
            } else {
                // Just create one big slot (e.g. 06:00 - 18:00)
                $slotStart = $start->format('H:i:s');
                $slotEnd = $end->format('H:i:s');
                
                $this->deleteOverlappingSlots($court, $day, $slotStart, $slotEnd);

                $court->schedules()->create([
                    'day_of_week' => $day,
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                    'price' => $price,
                    'is_active' => true,
                ]);
                $slotsCreated++;
            }
        }

        return redirect()->route('admin.courts.schedules.index', $court->id)
            ->with('success', "Berhasil menambahkan {$slotsCreated} slot jadwal.");
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Court $court, CourtSchedule $schedule)
    {
        if ($schedule->court_id !== $court->id) {
            abort(404);
        }

        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    /**
     * Bulk delete schedules or delete all.
     */
    public function destroyBulk(Request $request, Court $court)
    {
        if ($request->has('delete_all') && $request->delete_all == 1) {
            $court->schedules()->delete();
            return back()->with('success', 'Semua jadwal berhasil dihapus.');
        }

        $request->validate([
            'schedule_ids' => 'required|array',
            'schedule_ids.*' => 'integer|exists:court_schedules,id'
        ]);

        $court->schedules()->whereIn('id', $request->schedule_ids)->delete();

        return back()->with('success', count($request->schedule_ids) . ' jadwal berhasil dihapus.');
    }

    /**
     * Helper to delete overlapping slots for a given day and time range.
     */
    private function deleteOverlappingSlots(Court $court, int $day, string $start, string $end)
    {
        // Two time ranges A and B overlap if (StartA < EndB) and (EndA > StartB)
        $court->schedules()
            ->where('day_of_week', $day)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->delete();
    }
}
