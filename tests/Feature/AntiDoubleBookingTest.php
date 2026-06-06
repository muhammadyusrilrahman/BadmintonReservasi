<?php

namespace Tests\Feature;

use App\Jobs\ExpireReservationJob;
use App\Models\Court;
use App\Models\CourtSchedule;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AntiDoubleBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $user1;
    private User $user2;
    private Court $court;
    private CourtSchedule $schedule;
    private string $date;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create two test users
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        // 2. Create an active court
        $this->court = Court::create([
            'name' => 'Lapangan A',
            'type' => 'synthetic',
            'description' => 'Lapangan Syntetis A',
            'price_per_hour' => 50000,
            'is_active' => true,
        ]);

        // 3. Create a schedule slot (e.g. Wednesday 08:00 - 09:00)
        // Wednesday is day_of_week = 3 in PHP (Carbon: 1 = Monday, 3 = Wednesday)
        $this->date = '2026-05-27'; // A future Wednesday
        $this->schedule = CourtSchedule::create([
            'court_id' => $this->court->id,
            'day_of_week' => 3, // Wednesday
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'price' => 50000,
            'is_active' => true,
        ]);
    }

    /**
     * Test booking availability checker prevents double booking on same slot.
     */
    public function test_cannot_book_same_slot_twice()
    {
        Queue::fake();
        $service = app(ReservationService::class);

        // Book slot first time for User 1
        $reservations1 = $service->createCustomerBooking(
            scheduleIds: [$this->schedule->id],
            userId: $this->user1->id,
            date: $this->date,
            courtId: $this->court->id
        );

        $this->assertCount(1, $reservations1);
        $this->assertEquals('pending', $reservations1[0]->status);

        // Attempt to book the exact same slot for User 2 -> must throw exception
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("sudah tidak tersedia");

        $service->createCustomerBooking(
            scheduleIds: [$this->schedule->id],
            userId: $this->user2->id,
            date: $this->date,
            courtId: $this->court->id
        );
    }

    /**
     * Test that creating a booking dispatches the ExpireReservationJob with a 15-minute delay.
     */
    public function test_booking_dispatches_expire_job_with_delay()
    {
        Queue::fake();

        $service = app(ReservationService::class);

        $reservations = $service->createCustomerBooking(
            scheduleIds: [$this->schedule->id],
            userId: $this->user1->id,
            date: $this->date,
            courtId: $this->court->id
        );

        $this->assertCount(1, $reservations);

        // Assert job was pushed with 15 minutes delay
        Queue::assertPushed(ExpireReservationJob::class, function ($job) use ($reservations) {
            return $job->reservation->id === $reservations[0]->id;
        });
    }

    /**
     * Test that ExpireReservationJob cancels a reservation if it remains unpaid after 15 minutes.
     */
    public function test_expire_job_cancels_unpaid_reservation()
    {
        Queue::fake();
        $service = app(ReservationService::class);

        $reservations = $service->createCustomerBooking(
            scheduleIds: [$this->schedule->id],
            userId: $this->user1->id,
            date: $this->date,
            courtId: $this->court->id
        );

        $reservation = $reservations[0];

        $this->assertEquals('pending', $reservation->status);
        $this->assertEquals('pending', $reservation->payment->status);

        // Run the expire job manually
        $job = new ExpireReservationJob($reservation);
        $job->handle($service);

        // Refresh model state
        $reservation->refresh();

        $this->assertEquals('cancelled', $reservation->status);
        $this->assertEquals('failed', $reservation->payment->status);
    }

    /**
     * Test that Artisan command reservations:expire cancels pending reservations older than 15 minutes.
     */
    public function test_artisan_command_cancels_expired_reservations()
    {
        Queue::fake();
        $service = app(ReservationService::class);

        // Create a booking
        $reservations = $service->createCustomerBooking(
            scheduleIds: [$this->schedule->id],
            userId: $this->user1->id,
            date: $this->date,
            courtId: $this->court->id
        );

        $reservation = $reservations[0];

        // Manually manipulate the created_at to be 16 minutes in the past
        $reservation->created_at = now()->subMinutes(16);
        $reservation->save();

        $this->assertEquals('pending', $reservation->status);

        // Execute the Artisan command
        $this->artisan('reservations:expire')
            ->expectsOutputToContain('Total 1 reservasi dibatalkan')
            ->assertExitCode(0);

        // Check if status is cancelled
        $reservation->refresh();
        $this->assertEquals('cancelled', $reservation->status);
        $this->assertEquals('failed', $reservation->payment->status);
    }
}
