<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmailBase
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda - ASIMPENAS')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Selamat datang di **ASIMPENAS** (Aplikasi Seleksi Mitra dan Penawaran Komoditas) - Perum BULOG Kantor Cabang Surakarta.')
            ->line('')
            ->line('Terima kasih telah mendaftar sebagai Mitra Pangan. Untuk melanjutkan proses pendaftaran dan mengakses dashboard Anda, silakan verifikasi alamat email dengan mengklik tombol di bawah ini:')
            ->action('Verifikasi Email Saya', $verificationUrl)
            ->line('')
            ->line('Link verifikasi ini akan **kadaluarsa dalam 60 menit**. Jika tombol tidak berfungsi, Anda dapat menyalin dan menempel URL di bawah ini ke browser Anda.')
            ->line('')
            ->line('Jika Anda tidak membuat akun di ASIMPENAS, Anda dapat mengabaikan email ini dan tidak diperlukan tindakan lebih lanjut.')
            ->line('')
            ->line('---')
            ->line('**Butuh Bantuan?**')
            ->line('Hubungi kami:')
            ->line('📧 Email: bulog.bumn.ska@gmail.com')
            ->line('📞 Telepon: (0271) 716498')
            ->line('📍 Alamat: Jl. L.U. Adi Sucipto No. 17, Blulukan, Colomadu, Karanganyar, Jawa Tengah 57174')
            ->salutation('Hormat kami,')
            ->salutation('**Tim ASIMPENAS**')
            ->salutation('Perum BULOG Kantor Cabang Surakarta');
    }
}
