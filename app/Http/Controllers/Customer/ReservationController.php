<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\BaseController;
use App\Models\Court;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;
use App\Services\PromoCodeService;

class ReservationController extends BaseController
{
    public function __construct(
        private readonly ReservationService $reservationService,
        private readonly \App\Services\NotificationService $notificationService,
        private readonly PromoCodeService $promoCodeService
    ) {
    }

    /**
     * Show booking page.
     */
    public function create(): View
    {
        return view('customer.booking', [
            'title'  => 'Booking Lapangan',
            'courts' => Court::active()->orderBy('name')->get(),
        ]);
    }

    /**
     * AJAX: Get available slots for a court on a specific date.
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'court_id' => 'required|exists:courts,id',
            'date'     => 'required|date|after_or_equal:today',
        ]);

        $slots = $this->reservationService->getAvailableSlots(
            (int) $request->court_id,
            $request->date
        );

        return response()->json(['slots' => $slots]);
    }

    /**
     * Process multi-day multi-slot booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'court_id'                   => ['required', 'exists:courts,id'],
            'bookings'                   => ['required', 'array', 'min:1'],
            'bookings.*.date'            => ['required', 'date', 'after_or_equal:today'],
            'bookings.*.schedule_ids'    => ['required', 'array', 'min:1'],
            'bookings.*.schedule_ids.*'  => ['integer', 'exists:court_schedules,id'],
            'payment_method'             => ['required', 'in:transfer,ewallet'],
            'notes'                      => ['nullable', 'string', 'max:500'],
            'promo_code'                 => ['nullable', 'string', 'max:50'],
        ], [], [
            'court_id'                   => 'lapangan',
            'bookings'                   => 'data booking',
            'bookings.*.date'            => 'tanggal',
            'bookings.*.schedule_ids'    => 'jadwal',
            'payment_method'             => 'metode pembayaran',
            'notes'                      => 'catatan',
        ]);

        try {
            $totalReservations = 0;

            foreach ($request->bookings as $booking) {
                $reservations = $this->reservationService->createCustomerBooking(
                    scheduleIds: $booking['schedule_ids'],
                    userId: auth()->id(),
                    date: $booking['date'],
                    courtId: (int) $request->court_id,
                    paymentMethod: $request->payment_method,
                    notes: $request->notes,
                    promoCode: $request->promo_code,
                );

                foreach ($reservations as $reservation) {
                    $this->notificationService->sendBookingSuccess($reservation);
                }

                $totalReservations += count($reservations);
            }

            $dayCount = count($request->bookings);
            $msg = "Berhasil memesan {$totalReservations} slot";
            if ($dayCount > 1) {
                $msg .= " di {$dayCount} hari";
            }
            $msg .= "! Silakan lakukan pembayaran dan upload bukti transfer.";

            return redirect()
                ->route('customer.reservations.index')
                ->with('success', $msg);
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage())->withInput();
        }
    }

    /**
     * List customer's reservations.
     */
    public function index(Request $request): View
    {
        $reservations = Reservation::where('user_id', auth()->id())
            ->with(['court', 'payment'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.reservations.index', [
            'title'        => 'Reservasi Saya',
            'reservations' => $reservations,
        ]);
    }

    /**
     * Show reservation detail.
     */
    public function show(Reservation $reservation): View
    {
        // Ensure customer can only see their own reservations
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $reservation->load(['court', 'payment']);

        return view('customer.reservations.show', [
            'title'       => 'Detail Reservasi #' . $reservation->id,
            'reservation' => $reservation,
        ]);
    }

    /**
     * Upload payment proof.
     */
    public function uploadProof(Request $request, Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:2048'],
        ], [], [
            'payment_proof' => 'bukti pembayaran',
        ]);

        try {
            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            $this->reservationService->uploadPaymentProof($reservation, $path);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
        } catch (Exception $e) {
            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * AJAX: Validate and calculate promo code discount.
     */
    public function applyPromo(Request $request): JsonResponse
    {
        $request->validate([
            'promo_code'    => ['required', 'string', 'max:50'],
            'total_price'   => ['required', 'integer', 'min:0'],
        ]);

        try {
            $result = $this->promoCodeService->validateAndApply(
                $request->promo_code,
                (int) $request->total_price
            );

            return response()->json([
                'success'         => true,
                'message'         => $result['message'],
                'discount'        => $result['discount'],
                'promo_code'      => $result['promo']->code,
                'discount_label'  => $result['promo']->formatted_discount,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
