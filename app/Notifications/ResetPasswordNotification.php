<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Inisialisasi notifikasi reset kata sandi.
     */
    public function __construct(public string $token) {}

    /**
     * Gunakan saluran email untuk mengirim tautan reset.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Format email berisi tautan pengaturan ulang kata sandi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $broker = Config::get('auth.defaults.passwords');

        return (new MailMessage)
            ->subject('Atur Ulang Kata Sandi Akun SIRA')
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'url' => $this->resetUrl($notifiable),
                'expires' => Config::get("auth.passwords.{$broker}.expire", 60),
            ]);
    }

    /**
     * Bangun URL halaman reset kata sandi untuk pengguna.
     */
    protected function resetUrl(object $notifiable): string
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
