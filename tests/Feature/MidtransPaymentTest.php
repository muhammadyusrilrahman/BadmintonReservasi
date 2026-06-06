<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\CourtSchedule;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private Court $court;
    private CourtSchedule $schedule;
    private Reservation $reservation;
    private Payment $payment;
    private string $serverKey = 'test-midtrans-server-key';

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        // Configure Midtrans server key for testing environment
        config(['services.midtrans.server_key' => $this->serverKey]);
        config(['services.midtrans.is_production' => false]);
        config(['services.midtrans.client_key' => 'test-midtrans-client-key']);

        // Create testing users with verified email and customer role
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->user->assignRole('customer');

        $this->otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->otherUser->assignRole('customer');

        // Create court and schedule
        $this->court = Court::create([
            'name' => 'Lapangan A',
            'type' => 'synthetic',
            'price_per_hour' => 50000,
            'is_active' => true,
        ]);

        $this->schedule = CourtSchedule::create([
            'court_id' => $this->court->id,
            'day_of_week' => 3, // Wednesday
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'price' => 50000,
            'is_active' => true,
        ]);

        // Create reservation
        $this->reservation = Reservation::create([
            'user_id' => $this->user->id,
            'court_id' => $this->court->id,
            'date' => '2026-05-27',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'duration_hours' => 1,
            'total_price' => 50000,
            'status' => 'pending',
        ]);

        // Create payment
        $this->payment = Payment::create([
            'reservation_id' => $this->reservation->id,
            'amount' => 50000,
            'payment_method' => 'transfer',
            'status' => 'pending',
        ]);
    }

    /**
     * Test successful Snap token generation.
     */
    public function test_can_generate_midtrans_snap_token()
    {
        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'mock-snap-token-12345',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v1/payment/mock-snap-token-12345'
            ], 201)
        ]);

        $response = $this->actingAs($this->user)
            ->postJson(route('customer.reservations.snap-token', $this->reservation));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'snap_token' => 'mock-snap-token-12345',
                'client_key' => 'test-midtrans-client-key'
            ]);

        // Verify Snap token saved in DB
        $this->payment->refresh();
        $this->assertEquals('mock-snap-token-12345', $this->payment->snap_token);
    }

    /**
     * Test that another customer cannot retrieve or generate Snap token.
     */
    public function test_unauthorized_user_cannot_generate_snap_token()
    {
        $response = $this->actingAs($this->otherUser)
            ->postJson(route('customer.reservations.snap-token', $this->reservation));

        $response->assertStatus(403);
    }

    /**
     * Test successful payment webhook processing (settlement).
     */
    public function test_payment_webhook_settlement_updates_status_to_paid_and_confirmed()
    {
        $orderId = 'RSV-' . $this->reservation->id . '-PMT-' . $this->payment->id . '-' . time();
        $statusCode = '200';
        $grossAmount = '50000';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-tx-id-999',
            'signature_key' => $signatureKey,
        ];

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Status pembayaran berhasil diperbarui.'
            ]);

        $this->payment->refresh();
        $this->reservation->refresh();

        $this->assertEquals('paid', $this->payment->status);
        $this->assertEquals('confirmed', $this->reservation->status);
        $this->assertEquals('qris', $this->payment->payment_type);
        $this->assertEquals('midtrans-tx-id-999', $this->payment->midtrans_transaction_id);
        $this->assertNotNull($this->payment->paid_at);
    }

    /**
     * Test failed payment webhook processing (cancel / deny / expire).
     */
    public function test_payment_webhook_failure_updates_status_to_failed_and_cancelled()
    {
        $orderId = 'RSV-' . $this->reservation->id . '-PMT-' . $this->payment->id . '-' . time();
        $statusCode = '202';
        $grossAmount = '50000';
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'cancel',
            'payment_type' => 'gopay',
            'transaction_id' => 'midtrans-tx-id-777',
            'signature_key' => $signatureKey,
        ];

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(200);

        $this->payment->refresh();
        $this->reservation->refresh();

        $this->assertEquals('failed', $this->payment->status);
        $this->assertEquals('cancelled', $this->reservation->status);
    }

    /**
     * Test webhook signature validation rejects forged request.
     */
    public function test_payment_webhook_with_invalid_signature_is_rejected()
    {
        $orderId = 'RSV-' . $this->reservation->id . '-PMT-' . $this->payment->id . '-' . time();
        $statusCode = '200';
        $grossAmount = '50000';
        $forgedSignatureKey = 'forged-signature-string';

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'midtrans-tx-id-999',
            'signature_key' => $forgedSignatureKey,
        ];

        $response = $this->postJson('/payment/callback', $payload);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Signature key tidak valid.'
            ]);

        $this->payment->refresh();
        $this->reservation->refresh();

        // Assert payment remains pending
        $this->assertEquals('pending', $this->payment->status);
        $this->assertEquals('pending', $this->reservation->status);
    }
}
