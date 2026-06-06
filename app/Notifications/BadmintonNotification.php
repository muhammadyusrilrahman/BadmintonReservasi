<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadmintonNotification extends Notification
{
    use Queueable;

    protected string $type;
    protected string $title;
    protected string $message;
    protected string $url;
    protected ?int $reservationId;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $type, string $title, string $message, string $url, ?int $reservationId = null)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->reservationId = $reservationId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'           => $this->type, // booking_success, payment_success, payment_failed, refund_approved, reschedule_approved, schedule_changed
            'title'          => $this->title,
            'message'        => $this->message,
            'url'            => $this->url,
            'reservation_id' => $this->reservationId,
        ];
    }
}
