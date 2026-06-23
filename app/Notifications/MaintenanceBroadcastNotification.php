<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceBroadcastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $maintenanceType; // system, court
    protected string $title;
    protected string $messageContent;
    protected string $scheduledAt;
    protected string $duration;
    protected ?string $courtName;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string $maintenanceType,
        string $title,
        string $messageContent,
        string $scheduledAt,
        string $duration,
        ?string $courtName = null
    ) {
        $this->maintenanceType = $maintenanceType;
        $this->title = $title;
        $this->messageContent = $messageContent;
        $this->scheduledAt = $scheduledAt;
        $this->duration = $duration;
        $this->courtName = $courtName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'maintenance',
            'maintenance_type' => $this->maintenanceType,
            'title'            => $this->title,
            'message'          => $this->messageContent,
            'scheduled_at'     => $this->scheduledAt,
            'duration'         => $this->duration,
            'court_name'       => $this->courtName,
            'url'              => route('customer.dashboard'),
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Kami ingin menginformasikan terkait kegiatan pemeliharaan (maintenance) yang akan datang:");

        if ($this->maintenanceType === 'court') {
            $mail->line("Jenis Maintenance: Pemeliharaan Lapangan ({$this->courtName})");
        } else {
            $mail->line("Jenis Maintenance: Pemeliharaan Sistem Utama");
        }

        $mail->line("Waktu Mulai: {$this->scheduledAt}")
            ->line("Estimasi Durasi: {$this->duration}")
            ->line("Detail:")
            ->line($this->messageContent)
            ->line("Mohon maaf atas ketidaknyamanan yang ditimbulkan.")
            ->action('Kunjungi Dashboard', route('customer.dashboard'));

        return $mail;
    }
}
