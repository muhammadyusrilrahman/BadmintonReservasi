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
    public function show(Reservation $reservation): View
    {
        // Ensure customer can only reschedule their own reservation
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$reservation->canReschedule()) {
            abort(403, "Reservasi ini tidak dapat di-reschedule.");
        }

        return view('customer.reschedule', [
            'title'       => 'Reschedule Reservasi #' . $reservation->id,
            'reservation' => $reservation,
            'court'       => $reservation->court,
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
            'date'         => ['required', 'date', 'after_or_equal:today'],
            'schedule_ids' => ['required', 'array', 'min:1'],
            'schedule_ids.*' => ['integer', 'exists:court_schedules,id'],
        ], [], [
            'date'         => 'tanggal baru',
            'schedule_ids' => 'jadwal slot baru',
        ]);

        try {
            $this->rescheduleService->reschedule(
                reservation: $reservation,
                newDate: $request->date,
                newScheduleIds: $request->schedule_ids,
                userId: auth()->id()
            );

            return redirect()
                ->route('customer.reservations.show', $reservation)
                ->with('success', 'Reservasi Anda berhasil di-reschedule ke jadwal baru!');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }
}
