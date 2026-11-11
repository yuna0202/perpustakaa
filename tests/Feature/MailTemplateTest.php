<?php

namespace Tests\Feature;

use App\Mail\NewRegistrationNotification;
use App\Mail\RegistrationApproved;
use App\Mail\RegistrationReceived;
use App\Mail\RegistrationRejected;
use Tests\LibraryTestCase;

class MailTemplateTest extends LibraryTestCase
{
    public function test_all_membership_email_templates_render_member_data_and_working_route_links(): void
    {
        $member = $this->member(['alasan_penolakan' => 'Foto KTM perlu diperjelas.']);

        foreach ([RegistrationReceived::class, RegistrationApproved::class, RegistrationRejected::class, NewRegistrationNotification::class] as $mailClass) {
            $html = (new $mailClass($member))->render();
            $this->assertStringContainsString($member->user->nama, $html);
            $this->assertStringNotContainsString($member->user->password, $html);
            $this->assertStringNotContainsString($member->foto_KTM, $html);
        }

        $this->assertStringContainsString(route('anggota.kartu'), (new RegistrationApproved($member))->render());
        $this->assertStringContainsString(route('admin.pendaftar.show', $member), (new NewRegistrationNotification($member))->render());
        $this->assertStringContainsString($member->alasan_penolakan, (new RegistrationRejected($member))->render());
    }
}
