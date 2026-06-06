<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\User;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Court $court;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'phone'    => '08123456789',
        ]);
        $this->admin->assignRole('admin');

        $this->customer = User::create([
            'name'     => 'Customer Test',
            'email'    => 'customer@test.com',
            'password' => bcrypt('password'),
            'phone'    => '08198765432',
        ]);
        $this->customer->assignRole('customer');

        $this->court = Court::create([
            'name'           => 'Lapangan A',
            'type'           => 'synthetic',
            'description'    => 'Lapangan sintetis',
            'price_per_hour' => 75000,
            'is_active'      => true,
        ]);
    }

    private function createReservationWithPayment(string $status = 'confirmed', ?string $paymentStatus = 'paid', ?Carbon $date = null): Reservation
    {
        $date = $date ?? today();

        $reservation = Reservation::create([
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'date'           => $date,
            'start_time'     => '08:00',
            'end_time'       => '10:00',
            'duration_hours' => 2,
            'total_price'    => 150000,
            'status'         => $status,
            'notes'          => null,
        ]);

        if ($paymentStatus) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => 150000,
                'payment_method' => 'transfer',
                'status'         => $paymentStatus,
                'paid_at'        => $paymentStatus === 'paid' ? $date : null,
            ]);
        }

        return $reservation;
    }

    // ──────────────────────────────────────
    // Access Control
    // ──────────────────────────────────────

    public function test_admin_can_access_reports_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $response->assertOk();
        $response->assertSee('Laporan');
    }

    public function test_admin_can_access_finance_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.finance.index'));
        $response->assertOk();
        $response->assertSee('Keuangan');
    }

    public function test_customer_cannot_access_reports_page(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.reports.index'));
        // CheckRole middleware redirects non-admin users
        $response->assertRedirect();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.reports.index'));
        $response->assertRedirect(route('login'));
    }

    // ──────────────────────────────────────
    // Dashboard Stats
    // ──────────────────────────────────────

    public function test_dashboard_stats_calculate_correctly(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());
        $this->createReservationWithPayment('cancelled', 'failed', today());

        $service = app(ReportingService::class);
        $stats = $service->getDashboardStats(today()->startOfDay(), today()->endOfDay());

        $this->assertEquals(150000, $stats['net_revenue']);
        $this->assertEquals(150000, $stats['gross_revenue']);
        $this->assertEquals(2, $stats['total_reservations']);
        $this->assertIsFloat($stats['utilization_rate']);
        $this->assertIsFloat($stats['payment_success_rate']);
    }

    public function test_refunded_amount_subtracted_from_net_revenue(): void
    {
        $reservation = $this->createReservationWithPayment('completed', 'paid', today());

        Refund::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $this->customer->id,
            'amount'         => 50000,
            'reason'         => 'Test refund',
            'bank_name'      => 'BCA',
            'account_number' => '123456',
            'account_name'   => 'Test',
            'status'         => 'completed',
            'completed_at'   => now(),
        ]);

        $service = app(ReportingService::class);
        $stats = $service->getDashboardStats(today()->startOfDay(), today()->endOfDay());

        $this->assertEquals(100000, $stats['net_revenue']); // 150k - 50k
    }

    // ──────────────────────────────────────
    // Chart Data
    // ──────────────────────────────────────

    public function test_revenue_trend_returns_valid_structure(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        $trend = $service->getRevenueTrend(today()->subDays(7), today(), 'daily');

        $this->assertArrayHasKey('labels', $trend);
        $this->assertArrayHasKey('data', $trend);
        $this->assertCount(count($trend['labels']), $trend['data']);
        $this->assertContains(150000, $trend['data']);
    }

    public function test_court_occupancy_returns_valid_structure(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        $occupancy = $service->getCourtOccupancy(today()->subDays(7), today());

        $this->assertArrayHasKey('labels', $occupancy);
        $this->assertArrayHasKey('bookings', $occupancy);
        $this->assertArrayHasKey('hours', $occupancy);
        $this->assertContains('Lapangan A', $occupancy['labels']);
    }

    public function test_payment_method_distribution_returns_valid_structure(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        $methods = $service->getPaymentMethodDistribution(today()->subDays(7), today());

        $this->assertArrayHasKey('labels', $methods);
        $this->assertArrayHasKey('data', $methods);
        $this->assertArrayHasKey('amounts', $methods);
    }

    // ──────────────────────────────────────
    // Report Data
    // ──────────────────────────────────────

    public function test_transactions_report_returns_data(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        // Use wide range to capture auto-set created_at timestamps
        $data = $service->getTransactionsReport(today()->subDays(7)->startOfDay(), today()->endOfDay());

        $this->assertNotEmpty($data);
        $first = $data->first();
        $this->assertArrayHasKey('booking', $first);
        $this->assertArrayHasKey('customer', $first);
        $this->assertArrayHasKey('jumlah', $first);
        $this->assertArrayHasKey('status', $first);
    }

    public function test_reservations_report_returns_data(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        $data = $service->getReservationsReport(today()->subDays(7), today());

        $this->assertNotEmpty($data);
        $this->assertEquals('Customer Test', $data->first()['customer']);
    }

    public function test_users_report_returns_data(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        $data = $service->getUsersReport(today()->subDays(7)->startOfDay(), today()->endOfDay());

        $this->assertNotEmpty($data);
        $this->assertEquals('Customer Test', $data->first()['nama']);
    }

    public function test_reviews_report_returns_data(): void
    {
        $reservation = $this->createReservationWithPayment('completed', 'paid', today());

        Review::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'rating'         => 5,
            'comment'        => 'Sangat bagus!',
        ]);

        $service = app(ReportingService::class);
        $data = $service->getReviewsReport(today()->subDays(7)->startOfDay(), today()->endOfDay());

        $this->assertNotEmpty($data);
        $this->assertEquals(5, $data->first()['rating']);
    }

    // ──────────────────────────────────────
    // Date Filter
    // ──────────────────────────────────────

    public function test_date_filter_works_on_reports_page(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'start_date' => today()->subDays(7)->format('Y-m-d'),
            'end_date'   => today()->format('Y-m-d'),
            'period'     => 'daily',
            'type'       => 'transactions',
        ]));

        $response->assertOk();
        $response->assertSee('Customer Test');
    }

    public function test_future_date_range_returns_empty_data(): void
    {
        $this->createReservationWithPayment('confirmed', 'paid', today());

        $service = app(ReportingService::class);
        $data = $service->getTransactionsReport(today()->addYear(), today()->addYear()->addMonth());

        $this->assertEmpty($data);
    }

    // ──────────────────────────────────────
    // Export
    // ──────────────────────────────────────

    public function test_excel_export_requires_valid_type(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export.excel', [
            'type'       => 'invalid_type',
            'start_date' => today()->subDays(7)->format('Y-m-d'),
            'end_date'   => today()->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors('type');
    }

    public function test_pdf_export_requires_valid_type(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export.pdf', [
            'type'       => 'invalid_type',
            'start_date' => today()->subDays(7)->format('Y-m-d'),
            'end_date'   => today()->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors('type');
    }

    public function test_export_requires_date_range(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export.excel', [
            'type' => 'transactions',
        ]));

        $response->assertSessionHasErrors(['start_date', 'end_date']);
    }

    // ──────────────────────────────────────
    // Review Model
    // ──────────────────────────────────────

    public function test_review_belongs_to_reservation(): void
    {
        $reservation = $this->createReservationWithPayment('completed', 'paid');

        $review = Review::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'rating'         => 4,
            'comment'        => 'Good!',
        ]);

        $this->assertEquals($reservation->id, $review->reservation->id);
        $this->assertEquals($this->customer->id, $review->user->id);
        $this->assertEquals($this->court->id, $review->court->id);
    }

    public function test_reservation_has_one_review(): void
    {
        $reservation = $this->createReservationWithPayment('completed', 'paid');

        $review = Review::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'rating'         => 5,
            'comment'        => 'Excellent!',
        ]);

        $this->assertNotNull($reservation->review);
        $this->assertEquals($review->id, $reservation->review->id);
    }

    public function test_review_rating_distribution(): void
    {
        $reservation = $this->createReservationWithPayment('completed', 'paid');

        Review::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'rating'         => 5,
            'comment'        => null,
        ]);

        $service = app(ReportingService::class);
        $distribution = $service->getRatingDistribution(today()->subDays(7)->startOfDay(), today()->endOfDay());

        $this->assertEquals([0, 0, 0, 0, 1], $distribution['data']);
    }

    // ──────────────────────────────────────
    // Report Types
    // ──────────────────────────────────────

    public function test_all_report_types_are_defined(): void
    {
        $types = ReportingService::getReportTypes();

        $this->assertArrayHasKey('transactions', $types);
        $this->assertArrayHasKey('reservations', $types);
        $this->assertArrayHasKey('payments', $types);
        $this->assertArrayHasKey('refunds', $types);
        $this->assertArrayHasKey('users', $types);
        $this->assertArrayHasKey('reviews', $types);
    }

    public function test_headings_defined_for_all_types(): void
    {
        $service = app(ReportingService::class);

        foreach (array_keys(ReportingService::getReportTypes()) as $type) {
            $headings = $service->getHeadingsByType($type);
            $this->assertNotEmpty($headings, "Headings for {$type} should not be empty");
        }
    }

    public function test_switching_report_type_tab(): void
    {
        $this->createReservationWithPayment('completed', 'paid', today());

        foreach (['transactions', 'reservations', 'payments', 'refunds', 'users'] as $type) {
            $response = $this->actingAs($this->admin)->get(route('admin.reports.index', ['type' => $type]));
            $response->assertOk();
        }
    }
}
