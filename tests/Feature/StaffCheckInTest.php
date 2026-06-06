<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffCheckInTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $customer;
    private Court $court;
    private Reservation $reservation;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'staff']);
        Role::create(['name' => 'customer']);

        $this->staff = User::factory()->create(['name' => 'Staff Test']);
        $this->staff->assignRole('staff');

        $this->customer = User::factory()->create(['name' => 'Customer Test']);
        $this->customer->assignRole('customer');

        $this->court = Court::create([
            'name'           => 'Lapangan A',
            'type'           => 'synthetic',
            'description'    => 'Lapangan Test',
            'price_per_hour' => 100000,
            'is_active'      => true,
        ]);

        $this->reservation = Reservation::create([
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'date'           => today(),
            'start_time'     => '10:00',
            'end_time'       => '11:00',
            'duration_hours' => 1,
            'total_price'    => 100000,
            'status'         => 'confirmed',
        ]);

        $this->payment = Payment::create([
            'reservation_id' => $this->reservation->id,
            'amount'         => 100000,
            'payment_method' => 'transfer',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);
    }

    /**
     * Test booking_code auto-generated on creating reservation.
     */
    public function test_booking_code_auto_generated(): void
    {
        $this->assertNotNull($this->reservation->booking_code);
        $this->assertStringStartsWith('ADN-', $this->reservation->booking_code);
        $this->assertEquals(9, strlen($this->reservation->booking_code)); // ADN- + 5 chars
    }

    /**
     * Test staff can view check-in index page.
     */
    public function test_staff_can_view_checkin_index(): void
    {
        $response = $this->actingAs($this->staff)
            ->get(route('staff.checkin.index'));

        $response->assertStatus(200);
        $response->assertSee('Check-in Hari Ini');
        $response->assertSee($this->reservation->booking_code);
    }

    /**
     * Test staff can search bookings via AJAX.
     */
    public function test_staff_can_search_bookings(): void
    {
        $response = $this->actingAs($this->staff)
            ->getJson(route('staff.checkin.search', ['q' => $this->reservation->booking_code]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'results')
            ->assertJsonPath('results.0.booking_code', $this->reservation->booking_code);
    }

    /**
     * Test staff can view booking verification page.
     */
    public function test_staff_can_verify_booking(): void
    {
        $response = $this->actingAs($this->staff)
            ->get(route('staff.checkin.verify', $this->reservation->booking_code));

        $response->assertStatus(200);
        $response->assertSee($this->reservation->booking_code);
        $response->assertSee('Siap Check-in');
    }

    /**
     * Test successful check-in process.
     */
    public function test_staff_can_process_checkin(): void
    {
        $response = $this->actingAs($this->staff)
            ->post(route('staff.checkin.process', $this->reservation));

        $response->assertRedirect(route('staff.checkin.verify', $this->reservation->booking_code));
        $response->assertSessionHas('success');

        $this->reservation->refresh();
        $this->assertEquals('completed', $this->reservation->status);
        $this->assertNotNull($this->reservation->checked_in_at);
        $this->assertEquals($this->staff->id, $this->reservation->checked_in_by);
    }

    /**
     * Test check-in fails for unpaid reservation.
     */
    public function test_checkin_fails_for_unpaid_reservation(): void
    {
        $this->payment->update(['status' => 'pending']);

        $response = $this->actingAs($this->staff)
            ->post(route('staff.checkin.process', $this->reservation));

        $response->assertSessionHas('error');
        $this->reservation->refresh();
        $this->assertEquals('confirmed', $this->reservation->status);
    }

    /**
     * Test check-in fails for non-today reservation.
     */
    public function test_checkin_fails_for_non_today_reservation(): void
    {
        $this->reservation->update(['date' => today()->addDay()]);

        $response = $this->actingAs($this->staff)
            ->post(route('staff.checkin.process', $this->reservation));

        $response->assertSessionHas('error');
    }

    /**
     * Test check-in fails for already checked-in reservation.
     */
    public function test_checkin_fails_for_already_checked_in(): void
    {
        // First check-in
        $this->actingAs($this->staff)
            ->post(route('staff.checkin.process', $this->reservation));

        // Second check-in attempt
        $response = $this->actingAs($this->staff)
            ->post(route('staff.checkin.process', $this->reservation));

        $response->assertSessionHas('error');
    }

    /**
     * Test staff can view check-in history.
     */
    public function test_staff_can_view_checkin_history(): void
    {
        // Process check-in first
        $this->actingAs($this->staff)
            ->post(route('staff.checkin.process', $this->reservation));

        $response = $this->actingAs($this->staff)
            ->get(route('staff.checkin.history'));

        $response->assertStatus(200);
        $response->assertSee($this->reservation->booking_code);
    }

    /**
     * Test customer cannot access staff check-in pages.
     */
    public function test_customer_cannot_access_checkin(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('staff.checkin.index'));

        // Middleware redirects non-staff to their dashboard
        $response->assertRedirect();
    }
}
