<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Reservation\StoreAdminReservationRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Court;
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
     * List all reservations.
     */
    public function index(Request $request): View
    {
        $reservations = $this->reservationService->getPaginatedFiltered(
            perPage: 15,
            search:  $request->string('search')->trim()->value() ?: null,
            date:    $request->input('date') ?: null,
            status:  $request->input('status') ?: null,
            courtId: $request->input('court_id') ? (int) $request->input('court_id') : null,
        );

        return view('admin.reservations.index', [
            'title'        => 'Kelola Reservasi',
            'reservations' => $reservations,
            'courts'       => Court::active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show offline booking form.
     */
    public function create(): View
    {
        return view('admin.reservations.create', [
            'title'  => 'Tambah Reservasi Manual',
            'users'  => User::orderBy('name')->get(),
            'courts' => Court::active()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store offline booking.
     */
    public function store(StoreAdminReservationRequest $request): RedirectResponse
    {
        try {
            $this->reservationService->createAdminOfflineBooking($request->validated());
            return $this->redirectWithSuccess('admin.reservations.index', 'Reservasi manual berhasil dibuat. Status pembayaran otomatis Lunas (Tunai).');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }

    /**
     * Show reservation details and payment verification.
     */
    public function show(Reservation $reservation): View
    {
        $reservation->load(['user', 'court', 'payment.verifiedBy']);

        return view('admin.reservations.show', [
            'title'       => 'Detail Reservasi #' . $reservation->id,
            'reservation' => $reservation,
        ]);
    }

    /**
     * Verify payment.
     */
    public function verifyPayment(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate(['status' => 'required|in:paid,failed']);

        try {
            $this->reservationService->verifyPayment($reservation, $request->input('status'));
            
            $msg = $request->input('status') === 'paid' 
                ? 'Pembayaran berhasil diverifikasi. Pesanan dikonfirmasi.' 
                : 'Pembayaran ditolak. Pesanan dibatalkan.';
                
            return $this->redirectWithSuccess('admin.reservations.show', $msg, $reservation);
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Cancel reservation manually.
     */
    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        try {
            $this->reservationService->cancelReservation($reservation);
            return $this->redirectWithSuccess('admin.reservations.show', 'Reservasi berhasil dibatalkan.', $reservation);
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }
}
