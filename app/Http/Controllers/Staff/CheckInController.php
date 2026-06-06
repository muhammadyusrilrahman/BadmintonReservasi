<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\CheckInService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CheckInController extends BaseController
{
    public function __construct(private readonly CheckInService $checkInService)
    {
    }

    /**
     * Halaman utama check-in — jadwal hari ini.
     */
    public function index(Request $request)
    {
        $courtId = $request->input('court_id');
        $stats = $this->checkInService->getDailyStats();
        $reservations = $this->checkInService->getTodaySchedule($courtId ? (int) $courtId : null);
        $courts = Court::where('is_active', true)->orderBy('name')->get();

        return view('staff.checkin.index', [
            'title'         => 'Check-in Hari Ini',
            'stats'         => $stats,
            'reservations'  => $reservations,
            'courts'        => $courts,
            'selectedCourt' => $courtId,
        ]);
    }

    /**
     * AJAX search booking.
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = $this->checkInService->searchBooking($query);

        return response()->json([
            'results' => $results->map(fn($r) => [
                'id'            => $r->id,
                'booking_code'  => $r->booking_code,
                'customer_name' => $r->user->name,
                'court_name'    => $r->court->name,
                'date'          => $r->date->format('d/m/Y'),
                'time'          => substr($r->start_time, 0, 5) . ' - ' . substr($r->end_time, 0, 5),
                'status'        => $r->status,
                'status_label'  => $r->status_label,
                'verify_url'    => route('staff.checkin.verify', $r->booking_code),
            ])
        ]);
    }

    /**
     * Halaman verifikasi booking.
     */
    public function verify(string $bookingCode)
    {
        $reservation = $this->checkInService->findByBookingCode($bookingCode);

        if (!$reservation) {
            return redirect()->route('staff.checkin.index')
                ->with('error', 'Booking dengan kode "' . $bookingCode . '" tidak ditemukan.');
        }

        return view('staff.checkin.verify', [
            'title'       => 'Verifikasi Booking',
            'reservation' => $reservation,
        ]);
    }

    /**
     * Proses check-in.
     */
    public function process(Reservation $reservation)
    {
        try {
            $this->checkInService->processCheckIn($reservation, auth()->id());

            return redirect()->route('staff.checkin.verify', $reservation->booking_code)
                ->with('success', 'Check-in berhasil! Customer "' . $reservation->user->name . '" sudah terdaftar.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Riwayat check-in.
     */
    public function history(Request $request)
    {
        $date = $request->input('date');
        $courtId = $request->input('court_id');
        $courts = Court::where('is_active', true)->orderBy('name')->get();
        $history = $this->checkInService->getCheckInHistory($date, $courtId ? (int) $courtId : null);

        return view('staff.checkin.history', [
            'title'         => 'Riwayat Check-in',
            'history'       => $history,
            'courts'        => $courts,
            'selectedDate'  => $date,
            'selectedCourt' => $courtId,
        ]);
    }
}
