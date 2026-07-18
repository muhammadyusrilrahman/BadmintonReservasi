<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Models\Reservation;
use App\Services\RescheduleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class RescheduleController extends BaseController
{
    public function __construct(private readonly RescheduleService $rescheduleService)
    {
    }

    /**
     * Show reschedule form.
     */
    public function show(Reservation $reservation): View|RedirectResponse
    {
        // Ensure customer can only reschedule their own reservation
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$reservation->canReschedule()) {
            return redirect()->route('customer.reservations.show', $reservation)
                ->withError('Reservasi ini sudah tidak dapat di-reschedule (maksimal 1 kali atau batas waktu sudah lewat).');
        }

        // Load session reservations jika ini bagian dari session booking
        $sessionReservations = collect();
        if ($reservation->booking_session_id) {
            $sessionReservations = Reservation::where('booking_session_id', $reservation->booking_session_id)
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();
        }

        $sessionData = ($sessionReservations->count() > 0 ? $sessionReservations : collect([$reservation]))->map(function($r) {
            return [
                'id' => $r->id,
                'original_date_ymd' => $r->date->format('Y-m-d'),
                'formatted_original_date' => $r->date->translatedFormat('l, d F Y'),
                'start_time' => $r->start_time,
                'end_time' => $r->end_time,
                'total_price' => $r->total_price,
                'duration_hours' => $r->duration_hours
            ];
        });

        return view('customer.reschedule', [
            'title'               => 'Reschedule Reservasi #' . $reservation->id,
            'reservation'         => $reservation,
            'court'               => $reservation->court,
            'sessionReservations' => $sessionReservations,
            'sessionData'         => $sessionData,
        ]);
    }

    /**
     * Process reschedule.
     */
    public function process(Request $request, Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reschedules' => ['required', 'array', 'min:1'],
            'reschedules.*.reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'reschedules.*.date' => ['required', 'date', 'after_or_equal:today'],
            'reschedules.*.schedule_ids' => ['required', 'array', 'min:1'],
            'reschedules.*.schedule_ids.*' => ['integer', 'exists:court_schedules,id'],
        ], [], [
            'reschedules' => 'data reschedule',
            'reschedules.*.date' => 'tanggal baru',
            'reschedules.*.schedule_ids' => 'jadwal slot baru',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                foreach ($request->input('reschedules') as $rescheduleData) {
                    $targetReservation = Reservation::findOrFail($rescheduleData['reservation_id']);
                    
                    if ($targetReservation->user_id !== auth()->id()) {
                        continue;
                    }

                    $this->rescheduleService->reschedule(
                        reservation: $targetReservation,
                        newDate: $rescheduleData['date'],
                        newScheduleIds: $rescheduleData['schedule_ids'],
                        userId: auth()->id()
                    );
                }
            });

            return redirect()
                ->route('customer.reservations.show', $reservation)
                ->with('success', 'Reservasi Anda berhasil di-reschedule ke jadwal baru!');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }
}
