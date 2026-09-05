<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentNotification extends Notification
{
    use Queueable;

    /**
     * Inisialisasi notifikasi komentar atau mention baru.
     */
    public function __construct(
        public string $type, // 'mention' atau 'reply'
        public string $senderUsername,
        public string $senderName,
        public int $reportId,
        public string $reportTitle,
        public int $commentId,
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
        $isAi = strtolower($this->senderUsername) === 'sira';

        return [
            'type' => $this->type,
            'is_ai' => $isAi,
            'sender_username' => $this->senderUsername,
            'sender_name' => $this->senderName,
            'report_id' => $this->reportId,
            'report_title' => $this->reportTitle,
            'comment_id' => $this->commentId,
            'snippet' => $this->snippet,
            'url' => route('reports.show', $this->reportId).'#comment-'.$this->commentId,
            'message' => $this->type === 'mention'
                ? "@{$this->senderUsername} menyebut Anda dalam komentar"
                : ($isAi ? 'Asisten @Sira membalas komentar Anda' : "@{$this->senderUsername} membalas komentar Anda"),
        ];
    }
}
