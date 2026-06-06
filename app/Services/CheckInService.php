<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CheckInService
{
    /**
     * Ambil jadwal hari ini, opsional filter by court.
     */
    public function getTodaySchedule(?int $courtId = null): Collection
    {
        return Reservation::with(['user', 'court', 'payment'])
            ->whereDate('date', Carbon::today())
            ->whereIn('status', ['confirmed', 'completed'])
            ->when($courtId, fn($q) => $q->where('court_id', $courtId))
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Cari booking berdasarkan booking_code, nama, atau email customer.
     */
    public function searchBooking(string $query): Collection
    {
        return Reservation::with(['user', 'court', 'payment'])
            ->where(function ($q) use ($query) {
                $q->where('booking_code', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%")
                                ->orWhere('email', 'like', "%{$query}%");
                  });
            })
            ->whereIn('status', ['confirmed', 'completed', 'pending'])
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->limit(20)
            ->get();
    }

    /**
     * Cari reservasi berdasarkan booking_code.
     */
    public function findByBookingCode(string $code): ?Reservation
    {
        return Reservation::with(['user', 'court', 'payment', 'checkedInBy'])
            ->where('booking_code', $code)
            ->first();
    }

    /**
     * Proses check-in reservasi.
     *
     * @throws InvalidArgumentException
     */
    public function processCheckIn(Reservation $reservation, int $staffId): Reservation
    {
        if ($reservation->status !== 'confirmed') {
            throw new InvalidArgumentException('Hanya reservasi yang sudah dikonfirmasi yang dapat di-check-in.');
        }

        if (!$reservation->payment || $reservation->payment->status !== 'paid') {
            throw new InvalidArgumentException('Pembayaran belum terverifikasi. Check-in tidak dapat diproses.');
        }

        if (!$reservation->date->isToday()) {
            throw new InvalidArgumentException('Check-in hanya dapat dilakukan pada tanggal reservasi (hari ini).');
        }

        if ($reservation->checked_in_at !== null) {
            throw new InvalidArgumentException('Reservasi ini sudah di-check-in sebelumnya.');
        }

        return DB::transaction(function () use ($reservation, $staffId) {
            $reservation->update([
                'status'        => 'completed',
                'checked_in_at' => now(),
                'checked_in_by' => $staffId,
            ]);

            return $reservation->fresh(['user', 'court', 'payment', 'checkedInBy']);
        });
    }

    /**
     * Ambil riwayat check-in dengan pagination.
     */
    public function getCheckInHistory(?string $date = null, ?int $courtId = null): LengthAwarePaginator
    {
        return Reservation::with(['user', 'court', 'checkedInBy'])
            ->checkedIn()
            ->when($date, fn($q) => $q->whereDate('date', $date))
            ->when($courtId, fn($q) => $q->where('court_id', $courtId))
            ->orderByDesc('checked_in_at')
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Statistik harian untuk dashboard check-in.
     */
    public function getDailyStats(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::today();

        $reservations = Reservation::whereDate('date', $date)
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();

        return [
            'total_booking'    => $reservations->count(),
            'checked_in'       => $reservations->where('status', 'completed')->whereNotNull('checked_in_at')->count(),
            'waiting_checkin'  => $reservations->where('status', 'confirmed')->count(),
            'total_revenue'    => $reservations->sum('total_price'),
        ];
    }
}
