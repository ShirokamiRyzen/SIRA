<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportMentionNotification extends Notification
{
    use Queueable;

    /**
     * Inisialisasi notifikasi saat akun ditandai / dimention dalam postingan laporan.
     */
    public function __construct(
        public string $senderUsername,
        public string $senderName,
        public int $reportId,
        public string $reportTitle,
        public string $snippet,
    ) {}

    /**
     * Gunakan saluran penyimpanan database lokal.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Format payload array yang disimpan ke tabel notifications.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'post_mention',
            'is_ai' => false,
            'sender_username' => $this->senderUsername,
            'sender_name' => $this->senderName,
            'report_id' => $this->reportId,
            'report_title' => $this->reportTitle,
            'snippet' => $this->snippet,
            'url' => route('reports.show', $this->reportId),
            'message' => "@{$this->senderUsername} menandai/menyebut Anda dalam postingan laporan",
        ];
    }
}
