<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Reservation\StoreAdminReservationRequest;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class ReservationController extends BaseController
{
    public function __construct(private readonly ReservationService $reservationService)
    {
    }

    /**
     * Form tambah reservasi manual (kasir).
     */
    public function create(): View
    {
        return view('kasir.reservations.create', [
            'title'  => 'Tambah Reservasi Manual',
            'users'  => User::orderBy('name')->get(),
            'courts' => Court::active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Simpan reservasi manual (kasir) — status langsung confirmed, lunas tunai.
     */
    public function store(StoreAdminReservationRequest $request): RedirectResponse
    {
        try {
            $this->reservationService->createAdminOfflineBooking($request->validated());
            return $this->redirectWithSuccess('kasir.today.index', 'Reservasi manual berhasil dibuat. Status pembayaran otomatis Lunas (Tunai).');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }

    /**
     * Detail reservasi + tombol konfirmasi pembayaran (kasir).
     */
    public function show(Reservation $reservation): View
    {
        $reservation->load(['user', 'court', 'payment.verifiedBy']);

        // Load semua reservasi dalam sesi yang sama (jika ada)
        $sessionReservations = collect();
        $sessionPayment = null;

        if ($reservation->booking_session_id) {
            $sessionReservations = Reservation::where('booking_session_id', $reservation->booking_session_id)
                ->with(['court'])
                ->orderBy('date')
                ->orderBy('start_time')
                ->get();

            $sessionPayment = Payment::where('booking_session_id', $reservation->booking_session_id)
                ->with(['verifiedBy'])
                ->first();
        }

        return view('kasir.reservations.show', [
            'title'               => 'Detail Reservasi #' . $reservation->id,
            'reservation'         => $reservation,
            'sessionReservations' => $sessionReservations,
            'sessionPayment'      => $sessionPayment,
        ]);
    }

    /**
     * Verifikasi pembayaran oleh kasir.
     * Jika ada booking_session_id, semua reservasi dalam sesi dikonfirmasi sekaligus.
     */
    public function verifyPayment(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate(['status' => 'required|in:paid,failed']);

        try {
            $this->reservationService->verifyPayment($reservation, $request->input('status'));

            $msg = $request->input('status') === 'paid'
                ? 'Pembayaran berhasil diverifikasi. Pesanan dikonfirmasi.'
                : 'Pembayaran ditolak. Pesanan dibatalkan.';

            return $this->redirectWithSuccess('kasir.reservations.show', $msg, $reservation);
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }
}
