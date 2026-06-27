<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kata sandi akun SIPUS berhasil diubah')
            ->line('Kata sandi akun SIPUS Anda baru saja berhasil diubah.')
            ->line('Jika perubahan ini bukan dilakukan oleh Anda, segera hubungi petugas perpustakaan dan lakukan reset kata sandi.');
    }
}
