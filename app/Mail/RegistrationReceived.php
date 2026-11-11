<?php

namespace App\Mail;

use App\Models\Anggota;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Anggota $anggota) {}

    public function build(): static
    {
        return $this->subject('Pendaftaran anggota telah diterima')->view('emails.registration-received');
    }
}
