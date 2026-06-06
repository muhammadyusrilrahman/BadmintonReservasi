<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\CourtSchedule;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\ReservationStatusLog;
use App\Models\User;
use App\Services\RefundService;
use App\Services\RescheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RescheduleRefundTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private User $otherCustomer;
    private Court $court;
    private CourtSchedule $originalSchedule;
    private CourtSchedule $newSchedule;
    private CourtSchedule $overlappingSchedule;
    private Reservation $reservation;
    private Payment $payment;
    private string $date;
    private string $newDate;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);

        // 2. Create Users
        $this->admin = User::factory()->create(['name' => 'Admin Test']);
        $this->admin->assignRole('admin');

        $this->customer = User::factory()->create(['name' => 'Customer Test']);
        $this->customer->assignRole('customer');

        $this->otherCustomer = User::factory()->create(['name' => 'Other Customer Test']);
        $this->otherCustomer->assignRole('customer');

        // 3. Create Court
        $this->court = Court::create([
            'name'           => 'Lapangan A',
            'type'           => 'synthetic',
            'description'    => 'Lapangan Test',
            'price_per_hour' => 50000,
            'is_active'      => true,
        ]);

        // 4. Set future dates (min tomorrow for reschedule H-1)
        // Let's use Wednesday 3 weeks from now to ensure it's in the future and doesn't clash
        $dateCarbon = Carbon::parse('next Wednesday')->addWeeks(2);
        $this->date = $dateCarbon->format('Y-m-d');
        $this->newDate = $dateCarbon->copy()->addDay()->format('Y-m-d'); // Next day (Thursday)

        $originalDayOfWeek = $dateCarbon->dayOfWeek;
        $newDayOfWeek = $dateCarbon->copy()->addDay()->dayOfWeek;

        // 5. Create Schedules
        // Original slot (e.g. Wednesday 08:00 - 09:00)
        $this->originalSchedule = CourtSchedule::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $originalDayOfWeek,
            'start_time'  => '08:00:00',
            'end_time'    => '09:00:00',
            'price'       => 50000,
            'is_active'   => true,
        ]);

        // Reschedule target slot (e.g. Thursday 08:00 - 09:00)
        $this->newSchedule = CourtSchedule::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $newDayOfWeek,
            'start_time'  => '08:00:00',
            'end_time'    => '09:00:00',
            'price'       => 60000, // Slightly different price to test price adjustment
            'is_active'   => true,
        ]);

        // Overlapping reservation target schedule slot
        $this->overlappingSchedule = CourtSchedule::create([
            'court_id'    => $this->court->id,
            'day_of_week' => $newDayOfWeek,
            'start_time'  => '09:00:00',
            'end_time'    => '10:00:00',
            'price'       => 60000,
            'is_active'   => true,
        ]);

        // 6. Create Confirmed & Paid Reservation for Customer
        $this->reservation = Reservation::create([
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'date'           => $this->date,
            'start_time'     => '08:00:00',
            'end_time'       => '09:00:00',
            'duration_hours' => 1,
            'total_price'    => 50000,
            'status'         => 'confirmed',
            'reschedule_count'=> 0,
        ]);

        $this->payment = Payment::create([
            'reservation_id' => $this->reservation->id,
            'amount'         => 50000,
            'payment_method' => 'transfer',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);
    }

    /**
     * Test reschedule page loads correctly.
     */
    public function test_customer_can_view_reschedule_form(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('customer.reservations.reschedule', $this->reservation));

        $response->assertStatus(200);
        $response->assertSee('Ubah Jadwal Reservasi');
        $response->assertSee($this->court->name);
    }

    /**
     * Test successful reschedule process.
     */
    public function test_customer_can_reschedule_to_available_slot(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.reservations.reschedule.process', $this->reservation), [
                'date'         => $this->newDate,
                'schedule_ids' => [$this->newSchedule->id],
            ]);

        $response->assertRedirect(route('customer.reservations.show', $this->reservation));
        $response->assertSessionHas('success');

        $this->reservation->refresh();
        $this->payment->refresh();

        // Assert reservation details updated
        $this->assertEquals($this->newDate, $this->reservation->date->format('Y-m-d'));
        $this->assertEquals('08:00:00', $this->reservation->start_time);
        $this->assertEquals('09:00:00', $this->reservation->end_time);
        $this->assertEquals(60000, $this->reservation->total_price);
        $this->assertEquals(1, $this->reservation->reschedule_count);
        $this->assertEquals('confirmed', $this->reservation->status);

        // Assert associated payment amount updated
        $this->assertEquals(60000, $this->payment->amount);

        // Assert ReservationStatusLog exists
        $this->assertDatabaseHas('reservation_status_logs', [
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->customer->id,
            'change_type'    => 'reschedule',
            'old_status'     => 'confirmed',
            'new_status'     => 'confirmed',
        ]);
    }

    /**
     * Test cannot reschedule more than once.
     */
    public function test_customer_cannot_reschedule_more_than_once(): void
    {
        // First reschedule (set reschedule_count directly to bypass)
        $this->reservation->update(['reschedule_count' => 1]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.reservations.reschedule.process', $this->reservation), [
                'date'         => $this->newDate,
                'schedule_ids' => [$this->newSchedule->id],
            ]);

        $response->assertSessionHas('error');
        
        $this->reservation->refresh();
        $this->assertEquals($this->date, $this->reservation->date->format('Y-m-d'));
    }

    /**
     * Test cannot reschedule less than H-1.
     */
    public function test_customer_cannot_reschedule_less_than_24_hours(): void
    {
        // Set reservation to today (H-0)
        $this->reservation->update(['date' => today()]);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.reservations.reschedule.process', $this->reservation), [
                'date'         => $this->newDate,
                'schedule_ids' => [$this->newSchedule->id],
            ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test cannot reschedule to occupied slot.
     */
    public function test_customer_cannot_reschedule_to_overlapping_slot(): void
    {
        // Book the target slot for another customer first
        Reservation::create([
            'user_id'        => $this->otherCustomer->id,
            'court_id'       => $this->court->id,
            'date'           => $this->newDate,
            'start_time'     => '08:00:00',
            'end_time'       => '09:00:00',
            'duration_hours' => 1,
            'total_price'    => 60000,
            'status'         => 'confirmed',
        ]);

        // Attempt to reschedule our reservation to that same slot
        $response = $this->actingAs($this->customer)
            ->post(route('customer.reservations.reschedule.process', $this->reservation), [
                'date'         => $this->newDate,
                'schedule_ids' => [$this->newSchedule->id],
            ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test customer can request refund.
     */
    public function test_customer_can_request_refund(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.reservations.refund.request', $this->reservation), [
                'bank_name'      => 'BCA',
                'account_number' => '1234567890',
                'account_name'   => 'Customer Test',
                'reason'         => 'Tiba-tiba ada acara mendadak besok pagi.',
            ]);

        $response->assertRedirect(route('customer.reservations.show', $this->reservation));
        $response->assertSessionHas('success');

        // Verify Refund record created
        $this->assertDatabaseHas('refunds', [
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->customer->id,
            'amount'         => 50000,
            'bank_name'      => 'BCA',
            'account_number' => '1234567890',
            'account_name'   => 'Customer Test',
            'status'         => 'requested',
        ]);

        // Verify Status Log created
        $this->assertDatabaseHas('reservation_status_logs', [
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->customer->id,
            'change_type'    => 'refund_requested',
            'old_status'     => 'confirmed',
            'new_status'     => 'confirmed',
        ]);
    }

    /**
     * Test admin can approve refund request.
     */
    public function test_admin_can_approve_refund(): void
    {
        // 1. Create a refund request
        $refund = Refund::create([
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->customer->id,
            'amount'         => 50000,
            'reason'         => 'Alasan personal',
            'bank_name'      => 'BCA',
            'account_number' => '1234567890',
            'account_name'   => 'Customer Test',
            'status'         => 'requested',
        ]);

        // 2. Admin approves refund
        $response = $this->actingAs($this->admin)
            ->post(route('admin.refunds.approve', $refund), [
                'admin_notes' => 'Persetujuan disetujui. Silakan cek berkala.',
            ]);

        $response->assertRedirect(route('admin.refunds.show', $refund));
        $response->assertSessionHas('success');

        $refund->refresh();
        $this->reservation->refresh();
        $this->payment->refresh();

        // Assert database updates
        $this->assertEquals('approved', $refund->status);
        $this->assertEquals('cancelled', $this->reservation->status);
        $this->assertEquals('refunded', $this->payment->status);
        $this->assertEquals($this->admin->id, $refund->processed_by);
        $this->assertNotNull($refund->processed_at);

        // Verify Status Log
        $this->assertDatabaseHas('reservation_status_logs', [
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->admin->id,
            'change_type'    => 'refund_approved',
            'old_status'     => 'confirmed',
            'new_status'     => 'cancelled',
        ]);
    }

    /**
     * Test admin can reject refund request.
     */
    public function test_admin_can_reject_refund(): void
    {
        // 1. Create refund request
        $refund = Refund::create([
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->customer->id,
            'amount'         => 50000,
            'reason'         => 'Alasan personal',
            'bank_name'      => 'BCA',
            'account_number' => '1234567890',
            'account_name'   => 'Customer Test',
            'status'         => 'requested',
        ]);

        // 2. Admin rejects refund
        $response = $this->actingAs($this->admin)
            ->post(route('admin.refunds.reject', $refund), [
                'admin_notes' => 'Pengajuan ditolak karena melewati batas waktu H-1.',
            ]);

        $response->assertRedirect(route('admin.refunds.show', $refund));
        $response->assertSessionHas('success');

        $refund->refresh();
        $this->reservation->refresh();
        $this->payment->refresh();

        // Assert database updates
        $this->assertEquals('rejected', $refund->status);
        $this->assertEquals('confirmed', $this->reservation->status);
        $this->assertEquals('paid', $this->payment->status);

        // Verify Status Log
        $this->assertDatabaseHas('reservation_status_logs', [
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->admin->id,
            'change_type'    => 'refund_rejected',
            'old_status'     => 'confirmed',
            'new_status'     => 'confirmed',
        ]);
    }

    /**
     * Test admin can complete approved refund.
     */
    public function test_admin_can_complete_approved_refund(): void
    {
        // 1. Create an approved refund request
        $refund = Refund::create([
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->customer->id,
            'amount'         => 50000,
            'reason'         => 'Alasan personal',
            'bank_name'      => 'BCA',
            'account_number' => '1234567890',
            'account_name'   => 'Customer Test',
            'status'         => 'approved',
            'processed_by'   => $this->admin->id,
            'processed_at'   => now(),
        ]);

        // 2. Complete the refund
        $response = $this->actingAs($this->admin)
            ->post(route('admin.refunds.complete', $refund));

        $response->assertRedirect(route('admin.refunds.show', $refund));
        $response->assertSessionHas('success');

        $refund->refresh();

        // Assert database updates
        $this->assertEquals('completed', $refund->status);
        $this->assertNotNull($refund->completed_at);

        // Verify Status Log
        $this->assertDatabaseHas('reservation_status_logs', [
            'reservation_id' => $this->reservation->id,
            'user_id'        => $this->admin->id,
            'change_type'    => 'refund_completed',
        ]);
    }
}
