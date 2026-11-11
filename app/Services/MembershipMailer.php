<?php

namespace App\Services;

use App\Mail\NewRegistrationNotification;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationReceived;
use App\Mail\RegistrationRejected;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MembershipMailer
{
    public function registration(Anggota $anggota): bool
    {
        $sent = $this->send($anggota->user->email, new RegistrationReceived($anggota));

        foreach (User::where('role', 'admin')->select('id_user', 'email')->cursor() as $admin) {
            // Evaluate every delivery even when an earlier recipient failed.
            $adminSent = $this->send($admin->email, new NewRegistrationNotification($anggota));
            $sent = $adminSent && $sent;
        }

        return $sent;
    }

    public function decision(Anggota $anggota): bool
    {
        $mail = $anggota->status_verifikasi === 'disetujui'
            ? new RegistrationApproved($anggota)
            : new RegistrationRejected($anggota);

        return $this->send($anggota->user->email, $mail);
    }

    private function send(string $recipient, Mailable $mail): bool
    {
        try {
            Mail::to($recipient)->send($mail);

            return true;
        } catch (Throwable $exception) {
            // SMTP exceptions may include credentials or personal data; record only class names.
            Log::warning('Pengiriman email keanggotaan gagal.', [
                'mail_class' => $mail::class,
                'exception_class' => $exception::class,
            ]);

            return false;
        }
    }
}
