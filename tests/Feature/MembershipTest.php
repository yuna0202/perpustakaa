<?php

namespace Tests\Feature;

use App\Mail\RegistrationApproved;
use App\Mail\RegistrationRejected;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\PendingMail;
use Mockery;
use RuntimeException;
use Tests\LibraryTestCase;

class MembershipTest extends LibraryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Storage::fake('local');
    }

    public function test_only_admin_can_see_private_ktm_and_response_is_not_cached(): void
    {
        $member = $this->member();
        Storage::disk('local')->put($member->foto_KTM, 'private-ktm-fixture');
        $url = route('admin.pendaftar.ktm', $member);
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs($member->user)->get($url)->assertForbidden();
        $this->actingAs($this->user())->get($url)->assertForbidden();
        $response = $this->actingAs($this->user('admin'))->get($url)->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_approval_generates_unpredictable_token_and_public_verification_exposes_only_member_fields(): void
    {
        $member = $this->member();
        $admin = $this->user('admin');
        $this->actingAs($admin)->patch(route('admin.pendaftar.review', $member), [
            'status_verifikasi' => 'disetujui',
        ])->assertRedirect();

        $member->refresh();
        $this->assertSame('disetujui', $member->status_verifikasi);
        $this->assertNotNull($member->QRcode);
        $this->assertSame(64, strlen($member->QRcode));
        $this->assertNull($member->alasan_penolakan);
        $this->assertNotNull($member->tanggal_terbit);
        $this->assertSame(now()->year, $member->tanggal_terbit->year);
        Mail::assertSent(RegistrationApproved::class, fn ($mail) => $mail->hasTo($member->user->email));
        $this->post(route('logout'));

        $this->get(route('anggota.verifikasi', $member->QRcode))
            ->assertOk()
            ->assertSee($member->user->nama)
            ->assertSee($member->nim)
            ->assertSee($member->prodi)
            ->assertSee('Tanggal terbit')
            ->assertSee($member->tanggal_terbit->format('d/m/Y'))
            ->assertDontSee($member->user->email)
            ->assertDontSee($member->no_hp)
            ->assertDontSee($member->foto_KTM);
        $this->get(route('anggota.verifikasi', 'not-a-real-token'))->assertNotFound();
    }

    public function test_student_cannot_approve_any_member_including_self(): void
    {
        $self = $this->member();
        $other = $this->member();
        $this->actingAs($self->user);

        foreach ([$self, $other] as $member) {
            $this->patch(route('admin.pendaftar.review', $member), ['status_verifikasi' => 'disetujui'])
                ->assertForbidden();
            $this->assertSame('pending', $member->fresh()->status_verifikasi);
        }
        Mail::assertNothingSent();
    }

    public function test_decision_cannot_be_replayed_or_replaced_and_does_not_send_duplicate_mail(): void
    {
        $member = $this->member();
        $this->actingAs($this->user('admin'));
        $this->patch(route('admin.pendaftar.review', $member), ['status_verifikasi' => 'disetujui'])->assertRedirect();
        $token = $member->fresh()->QRcode;
        $this->patch(route('admin.pendaftar.review', $member), [
            'status_verifikasi' => 'ditolak', 'alasan_penolakan' => 'Tidak berlaku',
        ])->assertSessionHasErrors('status_verifikasi');
        $this->assertSame('disetujui', $member->fresh()->status_verifikasi);
        $this->assertSame($token, $member->fresh()->QRcode);
        Mail::assertSent(RegistrationApproved::class, 1);
        Mail::assertNotSent(RegistrationRejected::class);
    }

    public function test_rejection_requires_reason_sends_result_and_produces_no_card(): void
    {
        $member = $this->member();
        $this->actingAs($this->user('admin'));
        $this->patch(route('admin.pendaftar.review', $member), ['status_verifikasi' => 'ditolak'])
            ->assertSessionHasErrors('alasan_penolakan');
        $this->assertSame('pending', $member->fresh()->status_verifikasi);

        $this->patch(route('admin.pendaftar.review', $member), [
            'status_verifikasi' => 'ditolak', 'alasan_penolakan' => 'Foto KTM tidak terbaca.',
        ])->assertRedirect();
        $member->refresh();
        $this->assertSame('ditolak', $member->status_verifikasi);
        $this->assertNull($member->QRcode);
        Mail::assertSent(RegistrationRejected::class, fn ($mail) => $mail->hasTo($member->user->email));
        $this->actingAs($member->user)->get(route('anggota.kartu'))
            ->assertOk()->assertSee('Foto KTM tidak terbaca.')
            ->assertViewHas('qrCode', null);
    }

    public function test_card_is_scoped_to_authenticated_student_and_only_approved_members_have_qr(): void
    {
        $pending = $this->member();
        $approved = $this->member(['status_verifikasi' => 'disetujui', 'QRcode' => str_repeat('a', 64)]);

        $this->actingAs($pending->user)->get(route('anggota.kartu', ['id_anggota' => $approved->id_anggota]))
            ->assertOk()->assertViewHas('anggota', fn ($value) => $value->is($pending))
            ->assertViewHas('qrCode', null);
        $this->actingAs($approved->user)->get(route('anggota.kartu'))
            ->assertOk()->assertSee($approved->nim)
            ->assertViewHas('qrCode', fn ($value) => str_contains((string) $value, '<svg'));
    }

    public function test_public_verification_does_not_accept_a_pending_or_rejected_token(): void
    {
        $pending = $this->member(['QRcode' => str_repeat('b', 64)]);
        $rejected = $this->member(['status_verifikasi' => 'ditolak', 'QRcode' => str_repeat('c', 64)]);
        $this->get(route('anggota.verifikasi', $pending->QRcode))->assertNotFound();
        $this->get(route('anggota.verifikasi', $rejected->QRcode))->assertNotFound();
    }

    public function test_admin_can_send_stored_decision_but_pending_and_other_roles_cannot(): void
    {
        config(['mail.default' => 'smtp']);
        $member = $this->member(['status_verifikasi' => 'ditolak', 'alasan_penolakan' => 'Foto tidak jelas.']);
        $pending = $this->member();
        $url = route('admin.pendaftar.kirim-hasil', $member);

        $this->post($url)->assertRedirect(route('login'));
        $this->actingAs($member->user)->post($url)->assertForbidden();
        $this->actingAs($this->user('admin'))->post(route('admin.pendaftar.kirim-hasil', $pending))->assertSessionHas('warning');
        Mail::assertNothingSent();
        $this->post($url)->assertSessionHas('success');
        Mail::assertSent(RegistrationRejected::class, fn ($mail) => $mail->hasTo($member->user->email) && $mail->anggota->alasan_penolakan === 'Foto tidak jelas.');
    }

    public function test_log_mailer_does_not_claim_or_attempt_to_send_decision(): void
    {
        config(['mail.default' => 'log']);
        $member = $this->member(['status_verifikasi' => 'disetujui', 'QRcode' => str_repeat('a', 64)]);
        $this->actingAs($this->user('admin'))->post(route('admin.pendaftar.kirim-hasil', $member))
            ->assertSessionHas('warning');
        Mail::assertNothingSent();
    }

    public function test_card_qr_follows_the_lan_address_used_to_open_the_card(): void
    {
        $member = $this->member(['status_verifikasi' => 'disetujui', 'QRcode' => str_repeat('a', 64), 'tanggal_terbit' => '2026-11-09 10:00:00']);
        $this->actingAs($member->user)
            ->get('http://192.168.10.25:8000/anggota/kartu')
            ->assertOk()->assertViewHas('verificationUrl', 'http://192.168.10.25:8000/anggota/verifikasi/'.str_repeat('a', 64))
            ->assertSee('Tanggal terbit')
            ->assertSee('09/11/2026')
            ->assertDontSee('QR saat ini mengarah ke localhost')
            ->assertSee('src="/storage/anggota/foto/foto-uji.jpg"', false);

        $this->get('http://10.20.30.40:8000/anggota/kartu')
            ->assertOk()->assertViewHas('verificationUrl', 'http://10.20.30.40:8000/anggota/verifikasi/'.str_repeat('a', 64));

        Storage::disk('local')->put('active-lan-url', 'http://192.168.10.25:8000');
        config(['membership.lan_url_file' => Storage::disk('local')->path('active-lan-url')]);
        $this->get('http://127.0.0.1:8000/anggota/kartu')
            ->assertOk()->assertViewHas('verificationUrl', 'http://192.168.10.25:8000/anggota/verifikasi/'.str_repeat('a', 64));
    }

    public function test_card_qr_can_keep_a_stable_verification_domain_across_dhcp_changes(): void
    {
        config(['membership.verification_base_url' => 'https://perpus.example.test']);
        $member = $this->member(['status_verifikasi' => 'disetujui', 'QRcode' => str_repeat('q', 64)]);
        $this->actingAs($member->user);

        foreach (['192.168.1.5', '10.10.9.228'] as $address) {
            $this->get("http://{$address}:8000/anggota/kartu")
                ->assertOk()
                ->assertViewHas('verificationUrl', 'https://perpus.example.test/anggota/verifikasi/'.str_repeat('q', 64));
        }
    }

    public function test_applicants_can_be_filtered_by_status_and_searched_by_nim(): void
    {
        $pending = $this->member();
        $approved = $this->member(['status_verifikasi' => 'disetujui', 'QRcode' => str_repeat('d', 64)]);
        $this->actingAs($this->user('admin'));
        $this->get(route('admin.pendaftar.index', ['status' => 'pending', 'q' => $pending->nim]))
            ->assertOk()->assertSee($pending->nim)->assertDontSee($approved->nim);
        $this->get(route('admin.pendaftar.index', ['status' => 'disetujui']))
            ->assertOk()->assertSee($approved->nim)->assertDontSee($pending->nim);
    }

    public function test_mail_failure_after_approval_preserves_decision_and_qr(): void
    {
        $member = $this->member();
        $pendingMail = Mockery::mock(PendingMail::class);
        $pendingMail->shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP temporarily unavailable'));
        Mail::shouldReceive('to')->with($member->user->email)->once()->andReturn($pendingMail);

        $this->actingAs($this->user('admin'))->patch(route('admin.pendaftar.review', $member), [
            'status_verifikasi' => 'disetujui',
        ])->assertRedirect()->assertSessionHas('success')->assertSessionHas('warning');

        $this->assertSame('disetujui', $member->fresh()->status_verifikasi);
        $this->assertSame(64, strlen($member->fresh()->QRcode));
    }
}
