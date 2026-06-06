<?php

namespace App\Services;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Get summary stats for dashboard cards.
     */
    public function getDashboardStats(Carbon $startDate, Carbon $endDate): array
    {
        $netRevenue = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->sum('amount');

        $totalRefunded = Refund::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('amount');

        $totalReservations = Reservation::whereBetween('created_at', [$startDate, $endDate])->count();

        $totalHoursBooked = Reservation::whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('duration_hours');

        // Utilisasi lapangan: jam terpakai / (total courts * jam operasional per hari * jumlah hari)
        $activeCourts = Court::where('is_active', true)->count();
        $operatingHoursPerDay = 14; // 07:00 - 21:00
        $totalDays = max(1, $startDate->diffInDays($endDate) + 1);
        $maxCapacity = $activeCourts * $operatingHoursPerDay * $totalDays;
        $utilizationRate = $maxCapacity > 0 ? round(($totalHoursBooked / $maxCapacity) * 100, 1) : 0;

        $paymentSuccessRate = $this->getPaymentSuccessRate($startDate, $endDate);

        $avgRating = Review::whereBetween('created_at', [$startDate, $endDate])->avg('rating');

        return [
            'net_revenue'         => $netRevenue - $totalRefunded,
            'gross_revenue'       => $netRevenue,
            'total_refunded'      => $totalRefunded,
            'total_reservations'  => $totalReservations,
            'total_hours_booked'  => $totalHoursBooked,
            'utilization_rate'    => $utilizationRate,
            'payment_success_rate' => $paymentSuccessRate,
            'avg_rating'          => round($avgRating ?? 0, 1),
            'active_courts'       => $activeCourts,
        ];
    }

    /**
     * Get revenue trend data for chart.
     * Uses PHP-side grouping for database-agnostic compatibility (MySQL + SQLite).
     */
    public function getRevenueTrend(Carbon $startDate, Carbon $endDate, string $period = 'daily'): array
    {
        $payments = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->select('paid_at', 'amount')
            ->get();

        // Group by period key using Carbon formatting
        $groupedRevenues = $payments->groupBy(function ($payment) use ($period) {
            $date = Carbon::parse($payment->paid_at);
            return match ($period) {
                'weekly'  => $date->format('Y-W'),
                'monthly' => $date->format('Y-m'),
                default   => $date->format('Y-m-d'),
            };
        })->map(fn ($group) => $group->sum('amount'));

        // Build labels for every period in range
        $labels = [];
        $data = [];

        if ($period === 'monthly') {
            $periodObj = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->endOfMonth());
            foreach ($periodObj as $date) {
                $key = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $data[] = (int) ($groupedRevenues[$key] ?? 0);
            }
        } elseif ($period === 'weekly') {
            $periodObj = CarbonPeriod::create($startDate->copy()->startOfWeek(), '1 week', $endDate);
            foreach ($periodObj as $date) {
                $key = $date->format('Y-W');
                $labels[] = 'Minggu ' . $date->weekOfYear;
                $data[] = (int) ($groupedRevenues[$key] ?? 0);
            }
        } else {
            $periodObj = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($periodObj as $date) {
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d/m');
                $data[] = (int) ($groupedRevenues[$key] ?? 0);
            }
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Get court occupancy data for bar chart.
     */
    public function getCourtOccupancy(Carbon $startDate, Carbon $endDate): array
    {
        $courts = Court::where('is_active', true)
            ->withCount(['reservations' => function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['confirmed', 'completed'])
                    ->whereBetween('date', [$startDate, $endDate]);
            }])
            ->withSum(['reservations' => function ($query) use ($startDate, $endDate) {
                $query->whereIn('status', ['confirmed', 'completed'])
                    ->whereBetween('date', [$startDate, $endDate]);
            }], 'duration_hours')
            ->get();

        return [
            'labels' => $courts->pluck('name')->toArray(),
            'bookings' => $courts->pluck('reservations_count')->toArray(),
            'hours' => $courts->pluck('reservations_sum_duration_hours')->map(fn ($v) => (int) ($v ?? 0))->toArray(),
        ];
    }

    /**
     * Get payment method distribution for doughnut chart.
     */
    public function getPaymentMethodDistribution(Carbon $startDate, Carbon $endDate): array
    {
        $methods = Payment::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw("COALESCE(payment_type, payment_method) as method, COUNT(*) as total, SUM(amount) as amount")
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $labelMap = [
            'transfer' => 'Transfer Bank (Manual)',
            'cash'     => 'Tunai',
            'qris'     => 'QRIS',
            'gopay'    => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'bank_transfer' => 'Transfer Bank (Otomatis)',
            'credit_card' => 'Kartu Kredit',
            'cstore'   => 'Gerai Retail',
        ];

        return [
            'labels' => $methods->map(fn ($m) => $labelMap[$m->method] ?? ucfirst(str_replace('_', ' ', $m->method)))->toArray(),
            'data'   => $methods->pluck('total')->toArray(),
            'amounts' => $methods->pluck('amount')->toArray(),
        ];
    }

    /**
     * Get rating distribution for bar chart.
     */
    public function getRatingDistribution(Carbon $startDate, Carbon $endDate): array
    {
        $ratings = Review::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->orderBy('rating')
            ->pluck('total', 'rating');

        $data = [];
        for ($i = 1; $i <= 5; $i++) {
            $data[] = (int) ($ratings[$i] ?? 0);
        }

        return [
            'labels' => ['⭐ 1', '⭐ 2', '⭐ 3', '⭐ 4', '⭐ 5'],
            'data'   => $data,
        ];
    }

    // ──────────────────────────────────────
    // Report Data Collections (for tables & exports)
    // ──────────────────────────────────────

    /**
     * Transactions report data.
     */
    public function getTransactionsReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Payment::with(['reservation.user', 'reservation.court'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get()
            ->map(fn (Payment $p) => [
                'tanggal'     => $p->created_at->format('d/m/Y H:i'),
                'booking'     => $p->reservation?->booking_code ?? '-',
                'customer'    => $p->reservation?->user?->name ?? '-',
                'lapangan'    => $p->reservation?->court?->name ?? '-',
                'metode'      => $p->method_label,
                'jumlah'      => $p->amount,
                'status'      => $p->status_label,
            ]);
    }

    /**
     * Reservations report data.
     */
    public function getReservationsReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Reservation::with(['user', 'court', 'payment'])
            ->whereBetween('date', [$startDate, $endDate])
            ->latest('date')
            ->get()
            ->map(fn (Reservation $r) => [
                'tanggal'     => $r->date->format('d/m/Y'),
                'booking'     => $r->booking_code,
                'customer'    => $r->user?->name ?? '-',
                'lapangan'    => $r->court?->name ?? '-',
                'jam'         => $r->start_time . ' - ' . $r->end_time,
                'durasi'      => $r->duration_hours . ' jam',
                'total'       => $r->total_price,
                'status'      => $r->status_label,
            ]);
    }

    /**
     * Payments report data.
     */
    public function getPaymentsReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Payment::with(['reservation.user', 'reservation.court', 'verifiedBy'])
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->latest('paid_at')
            ->get()
            ->map(fn (Payment $p) => [
                'tanggal_bayar' => $p->paid_at?->format('d/m/Y H:i') ?? '-',
                'booking'       => $p->reservation?->booking_code ?? '-',
                'customer'      => $p->reservation?->user?->name ?? '-',
                'lapangan'      => $p->reservation?->court?->name ?? '-',
                'metode'        => $p->method_label,
                'jumlah'        => $p->amount,
                'verifikator'   => $p->verifiedBy?->name ?? 'Otomatis',
            ]);
    }

    /**
     * Refunds report data.
     */
    public function getRefundsReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Refund::with(['reservation.court', 'user', 'processedBy'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get()
            ->map(fn (Refund $r) => [
                'tanggal'     => $r->created_at->format('d/m/Y'),
                'customer'    => $r->user?->name ?? '-',
                'lapangan'    => $r->reservation?->court?->name ?? '-',
                'jumlah'      => $r->amount,
                'alasan'      => $r->reason,
                'status'      => $r->status_label,
                'diproses'    => $r->processedBy?->name ?? '-',
            ]);
    }

    /**
     * Users report data.
     * Note: Uses PHP-side filtering instead of HAVING for SQLite compatibility.
     */
    public function getUsersReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return User::role('customer')
            ->withCount(['reservations' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['reservations' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', ['confirmed', 'completed']);
            }], 'total_price')
            ->orderByDesc('reservations_sum_total_price')
            ->get()
            ->filter(fn (User $u) => $u->reservations_count > 0)
            ->map(fn (User $u) => [
                'nama'         => $u->name,
                'email'        => $u->email,
                'telepon'      => $u->phone ?? '-',
                'total_booking' => $u->reservations_count,
                'total_belanja' => (int) ($u->reservations_sum_total_price ?? 0),
                'bergabung'    => $u->created_at->format('d/m/Y'),
            ]);
    }

    /**
     * Reviews report data.
     */
    public function getReviewsReport(Carbon $startDate, Carbon $endDate): Collection
    {
        return Review::with(['user', 'court', 'reservation'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get()
            ->map(fn (Review $r) => [
                'tanggal'   => $r->created_at->format('d/m/Y'),
                'customer'  => $r->user?->name ?? '-',
                'lapangan'  => $r->court?->name ?? '-',
                'rating'    => $r->rating,
                'komentar'  => $r->comment ?? '-',
            ]);
    }

    /**
     * Get report data by type.
     */
    public function getReportByType(string $type, Carbon $startDate, Carbon $endDate): Collection
    {
        return match ($type) {
            'transactions' => $this->getTransactionsReport($startDate, $endDate),
            'reservations' => $this->getReservationsReport($startDate, $endDate),
            'payments'     => $this->getPaymentsReport($startDate, $endDate),
            'refunds'      => $this->getRefundsReport($startDate, $endDate),
            'users'        => $this->getUsersReport($startDate, $endDate),
            'reviews'      => $this->getReviewsReport($startDate, $endDate),
            default        => collect(),
        };
    }

    /**
     * Get headings for export by report type.
     */
    public function getHeadingsByType(string $type): array
    {
        return match ($type) {
            'transactions' => ['Tanggal', 'Kode Booking', 'Customer', 'Lapangan', 'Metode Bayar', 'Jumlah (Rp)', 'Status'],
            'reservations' => ['Tanggal', 'Kode Booking', 'Customer', 'Lapangan', 'Jam', 'Durasi', 'Total (Rp)', 'Status'],
            'payments'     => ['Tanggal Bayar', 'Kode Booking', 'Customer', 'Lapangan', 'Metode', 'Jumlah (Rp)', 'Verifikator'],
            'refunds'      => ['Tanggal', 'Customer', 'Lapangan', 'Jumlah (Rp)', 'Alasan', 'Status', 'Diproses Oleh'],
            'users'        => ['Nama', 'Email', 'Telepon', 'Total Booking', 'Total Belanja (Rp)', 'Bergabung'],
            'reviews'      => ['Tanggal', 'Customer', 'Lapangan', 'Rating', 'Komentar'],
            default        => [],
        };
    }

    /**
     * Get report type labels.
     */
    public static function getReportTypes(): array
    {
        return [
            'transactions' => 'Transaksi',
            'reservations' => 'Reservasi',
            'payments'     => 'Pembayaran',
            'refunds'      => 'Refund',
            'users'        => 'Pengguna',
            'reviews'      => 'Review',
        ];
    }

    // ──────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────

    private function getPaymentSuccessRate(Carbon $startDate, Carbon $endDate): float
    {
        $total = Payment::whereBetween('created_at', [$startDate, $endDate])->count();
        if ($total === 0) return 0;

        $paid = Payment::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return round(($paid / $total) * 100, 1);
    }
}
