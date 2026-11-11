<?php

namespace App\Mail;

use App\Models\Anggota;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Anggota $anggota) {}

    public function build(): static
    {
        return $this->subject('Hasil verifikasi pendaftaran anggota')->view('emails.registration-rejected');
    }
}
