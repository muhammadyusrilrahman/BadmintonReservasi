<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\NotificationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Court $court;
    private Reservation $reservation;
    private NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'customer']);

        // 2. Create Users
        $this->admin = User::factory()->create(['name' => 'Admin Test', 'email' => 'admin@test.com']);
        $this->admin->assignRole('admin');

        $this->customer = User::factory()->create(['name' => 'Customer Test', 'email' => 'customer@test.com']);
        $this->customer->assignRole('customer');

        // 3. Create Court
        $this->court = Court::create([
            'name'           => 'Lapangan A',
            'type'           => 'synthetic',
            'description'    => 'Lapangan Test',
            'price_per_hour' => 50000,
            'is_active'      => true,
        ]);

        // 4. Create Reservation
        $this->reservation = Reservation::create([
            'user_id'        => $this->customer->id,
            'court_id'       => $this->court->id,
            'date'           => today()->addDay()->format('Y-m-d'),
            'start_time'     => '08:00:00',
            'end_time'       => '09:00:00',
            'duration_hours' => 1,
            'total_price'    => 50000,
            'status'         => 'pending',
        ]);

        $this->notificationService = new NotificationService();
    }

    /**
     * Test activity log is recorded on state-changing POST/PUT/DELETE requests.
     */
    public function test_activity_log_records_on_state_changing_request(): void
    {
        // Call a state-changing POST route (e.g. mark notification read-all)
        $response = $this->actingAs($this->customer)
            ->post(route('customer.notifications.read-all'));

        $response->assertStatus(302); // Standard redirect back or redirect to previous page

        // Assert record exists in user_activity_logs
        $this->assertDatabaseHas('user_activity_logs', [
            'user_id' => $this->customer->id,
            'method'  => 'POST',
            'activity'=> 'Membuat data baru di path: customer/notifications/read-all',
        ]);
    }

    /**
     * Test login event triggers activity log.
     */
    public function test_login_event_records_activity(): void
    {
        // Clear existing activity logs to isolate
        UserActivityLog::truncate();

        // Dispatch Login event
        Event::dispatch(new Login('web', $this->customer, false));

        // Assert user login log is created
        $this->assertDatabaseHas('user_activity_logs', [
            'user_id'  => $this->customer->id,
            'activity' => 'User login ke dalam sistem 🔑',
        ]);
    }

    /**
     * Test logout event triggers activity log.
     */
    public function test_logout_event_records_activity(): void
    {
        // Clear existing activity logs to isolate
        UserActivityLog::truncate();

        // Dispatch Logout event
        Event::dispatch(new Logout('web', $this->customer));

        // Assert user logout log is created
        $this->assertDatabaseHas('user_activity_logs', [
            'user_id'  => $this->customer->id,
            'activity' => 'User logout dari sistem 🔒',
        ]);
    }

    /**
     * Test admin can view and filter activity logs monitoring page.
     */
    public function test_admin_can_view_activity_logs_page(): void
    {
        // Generate some logs
        UserActivityLog::create([
            'user_id'    => $this->customer->id,
            'activity'   => 'Membuat reservasi booking baru',
            'method'     => 'POST',
            'url'        => 'http://localhost/customer/booking',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Sistem Log Aktivitas');
        $response->assertSee('Membuat reservasi booking baru');
    }

    /**
     * Test search and filter on admin activity logs page.
     */
    public function test_admin_can_filter_activity_logs(): void
    {
        UserActivityLog::create([
            'user_id'    => $this->customer->id,
            'activity'   => 'Aktivitas Khusus 123',
            'method'     => 'POST',
            'url'        => 'http://localhost',
            'ip_address' => '192.168.1.1',
            'created_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id'    => $this->customer->id,
            'activity'   => 'Aktivitas Lainnya',
            'method'     => 'DELETE',
            'url'        => 'http://localhost',
            'ip_address' => '192.168.1.2',
            'created_at' => now(),
        ]);

        // Filter search
        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index', ['search' => 'Khusus']));
        $response->assertSee('Aktivitas Khusus 123');
        $response->assertDontSee('Aktivitas Lainnya');

        // Filter method
        $response = $this->actingAs($this->admin)
            ->get(route('admin.activity-logs.index', ['method' => 'DELETE']));
        $response->assertSee('Aktivitas Lainnya');
        $response->assertDontSee('Aktivitas Khusus 123');
    }

    /**
     * Test customer cannot view activity logs monitoring page.
     */
    public function test_customer_cannot_view_activity_logs_page(): void
    {
        $response = $this->actingAs($this->customer)
            ->get(route('admin.activity-logs.index'));

        $response->assertRedirect(); // Denied/redirected by CheckRole middleware
    }

    /**
     * Test notification is sent on booking success.
     */
    public function test_notification_sent_on_booking_success(): void
    {
        $this->customer->notifications()->delete();

        $this->notificationService->sendBookingSuccess($this->reservation);

        $this->assertEquals(1, $this->customer->unreadNotifications()->count());
        $notification = $this->customer->unreadNotifications()->first();
        $this->assertEquals('booking_success', $notification->data['type']);
        $this->assertStringContainsString('Pemesanan Berhasil!', $notification->data['title']);
    }

    /**
     * Test notification is sent on payment success.
     */
    public function test_notification_sent_on_payment_success(): void
    {
        $this->customer->notifications()->delete();

        $this->notificationService->sendPaymentSuccess($this->reservation);

        $this->assertEquals(1, $this->customer->unreadNotifications()->count());
        $notification = $this->customer->unreadNotifications()->first();
        $this->assertEquals('payment_success', $notification->data['type']);
        $this->assertStringContainsString('Pembayaran Berhasil!', $notification->data['title']);
    }

    /**
     * Test notification is sent on payment failed.
     */
    public function test_notification_sent_on_payment_failed(): void
    {
        $this->customer->notifications()->delete();

        $this->notificationService->sendPaymentFailed($this->reservation);

        $this->assertEquals(1, $this->customer->unreadNotifications()->count());
        $notification = $this->customer->unreadNotifications()->first();
        $this->assertEquals('payment_failed', $notification->data['type']);
    }

    /**
     * Test notification is sent on refund approved.
     */
    public function test_notification_sent_on_refund_approved(): void
    {
        $this->customer->notifications()->delete();

        $this->notificationService->sendRefundApproved($this->reservation);

        $this->assertEquals(1, $this->customer->unreadNotifications()->count());
        $notification = $this->customer->unreadNotifications()->first();
        $this->assertEquals('refund_approved', $notification->data['type']);
    }

    /**
     * Test notification is sent on reschedule approved.
     */
    public function test_notification_sent_on_reschedule_approved(): void
    {
        $this->customer->notifications()->delete();

        $this->notificationService->sendRescheduleApproved($this->reservation);

        $this->assertEquals(1, $this->customer->unreadNotifications()->count());
        $notification = $this->customer->unreadNotifications()->first();
        $this->assertEquals('reschedule_approved', $notification->data['type']);
    }

    /**
     * Test notification is sent on schedule changed.
     */
    public function test_notification_sent_on_schedule_changed(): void
    {
        $this->customer->notifications()->delete();

        $this->notificationService->sendScheduleChanged($this->reservation, '08:00 - 09:00', '10:00 - 11:00');

        $this->assertEquals(1, $this->customer->unreadNotifications()->count());
        $notification = $this->customer->unreadNotifications()->first();
        $this->assertEquals('schedule_changed', $notification->data['type']);
        $this->assertStringContainsString('Jadwal Reservasi Berubah', $notification->data['title']);
        $this->assertStringContainsString('08:00 - 09:00', $notification->data['message']);
        $this->assertStringContainsString('10:00 - 11:00', $notification->data['message']);
    }

    /**
     * Test customer can mark specific notification as read.
     */
    public function test_customer_can_mark_notification_as_read(): void
    {
        $this->customer->notifications()->delete();
        $this->notificationService->sendBookingSuccess($this->reservation);

        $notification = $this->customer->unreadNotifications()->first();

        $response = $this->actingAs($this->customer)
            ->post(route('customer.notifications.read', $notification->id));

        $response->assertRedirect($notification->data['url']);
        $this->assertEquals(0, $this->customer->unreadNotifications()->count());
        $this->assertEquals(1, $this->customer->notifications()->whereNotNull('read_at')->count());
    }

    /**
     * Test customer can mark all notifications as read.
     */
    public function test_customer_can_mark_all_notifications_as_read(): void
    {
        $this->customer->notifications()->delete();
        $this->notificationService->sendBookingSuccess($this->reservation);
        $this->notificationService->sendPaymentSuccess($this->reservation);

        $this->assertEquals(2, $this->customer->unreadNotifications()->count());

        $response = $this->actingAs($this->customer)
            ->postJson(route('customer.notifications.read-all'));

        $response->assertStatus(200);
        $this->assertEquals(0, $this->customer->unreadNotifications()->count());
    }
}
