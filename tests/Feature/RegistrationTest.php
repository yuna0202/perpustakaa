<?php

namespace Tests\Feature;

use App\Mail\NewRegistrationNotification;
use App\Mail\RegistrationReceived;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\PendingMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Mockery;
use Tests\LibraryTestCase;

class RegistrationTest extends LibraryTestCase
{
    protected function registration(array $attributes = []): array
    {
        $this->withSession(['registration_captcha' => ['answer' => 7, 'issued_at' => time()]]);

        return parent::registration(array_merge(['captcha' => 7], $attributes));
    }

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
        Mail::fake();
    }

    public function test_registration_creates_linked_pending_member_with_hashed_password_and_private_ktm(): void
    {
        $admin = $this->user('admin');
        $this->post(route('daftar.store'), $this->registration([
            'role' => 'admin',
            'status_verifikasi' => 'disetujui',
            'QRcode' => 'attacker-token',
        ]))->assertRedirect(route('anggota.kartu'));

        $user = User::where('email', 'nadia@example.test')->firstOrFail();
        $member = $user->anggota;
        $this->assertAuthenticatedAs($user);
        $this->assertSame('mahasiswa', $user->role);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertNotSame('Password123!', $user->password);
        $this->assertSame('pending', $member->status_verifikasi);
        $this->assertSame('Fakultas Teknik dan Informatika', $member->fakultas);
        $this->assertNull($member->angkatan);
        $this->assertNull($member->QRcode);
        $this->assertStringNotContainsString('portrait.jpg', $member->foto_stnghBadan);
        $this->assertStringNotContainsString('ktm.png', $member->foto_KTM);
        Storage::disk('public')->assertExists($member->foto_stnghBadan);
        Storage::disk('local')->assertExists($member->foto_KTM);
        Storage::disk('public')->assertMissing($member->foto_KTM);
        Mail::assertSent(RegistrationReceived::class, fn ($mail) => $mail->hasTo($user->email));
        Mail::assertSent(NewRegistrationNotification::class, fn ($mail) => $mail->hasTo($admin->email));
    }

    public function test_duplicate_nim_and_email_are_rejected_without_partial_registration_or_files(): void
    {
        $existing = $this->member(['nim' => '24510099']);
        $this->post(route('daftar.store'), $this->registration(['email' => $existing->user->email]))
            ->assertSessionHasErrors(['email', 'nim']);

        $this->assertDatabaseCount('t_user', 1);
        $this->assertDatabaseCount('t_anggota', 1);
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertSame([], Storage::disk('local')->allFiles());
        Mail::assertNothingSent();
    }

    public function test_registration_rejects_non_image_and_oversize_photos(): void
    {
        $this->post(route('daftar.store'), $this->registration([
            'foto_stnghBadan' => UploadedFile::fake()->create('script.jpg', 10, 'text/plain'),
            'foto_KTM' => UploadedFile::fake()->image('large.png')->size(2049),
        ]))->assertSessionHasErrors(['foto_stnghBadan', 'foto_KTM']);

        $this->assertDatabaseCount('t_user', 0);
        $this->assertDatabaseCount('t_anggota', 0);
        Mail::assertNothingSent();
    }

    public function test_registration_rejects_incomplete_and_invalid_fields(): void
    {
        $this->post(route('daftar.store'), [])->assertSessionHasErrors([
            'nama', 'nim', 'email', 'password', 'prodi', 'captcha', 'no_hp', 'foto_stnghBadan', 'foto_KTM',
        ]);
        $this->assertDatabaseCount('t_user', 0);
    }

    public function test_invalid_program_password_and_captcha_cannot_create_member(): void
    {
        $this->post(route('daftar.store'), $this->registration([
            'prodi' => 'Program tidak dikenal', 'password' => 'password', 'captcha' => 8,
        ]))->assertSessionHasErrors(['prodi', 'password', 'captcha']);

        $this->assertDatabaseCount('t_user', 0);
        $this->assertDatabaseCount('t_anggota', 0);
    }

    public function test_captcha_is_single_use_and_is_refreshed_after_other_validation_errors(): void
    {
        $this->post(route('daftar.store'), $this->registration(['no_hp' => 'salah']))
            ->assertSessionHasErrors('no_hp');
        $this->post(route('daftar.store'), parent::registration(['captcha' => 7]))
            ->assertSessionHasErrors('captcha');

        $this->get(route('daftar'))->assertOk();
        $answer = session('registration_captcha.answer');
        $this->assertIsInt($answer);
        $this->post(route('daftar.store'), parent::registration(['captcha' => $answer]))
            ->assertRedirect(route('anggota.kartu'));
        $this->assertDatabaseHas('t_anggota', ['nim' => '24510099']);
    }

    public function test_faculty_is_derived_from_program_even_when_client_sends_another(): void
    {
        $this->post(route('daftar.store'), $this->registration([
            'prodi' => 'Akuntansi', 'fakultas' => 'Fakultas Teknik dan Informatika',
        ]))->assertRedirect(route('anggota.kartu'));

        $this->assertDatabaseHas('t_anggota', [
            'nim' => '24510099', 'prodi' => 'Akuntansi', 'fakultas' => 'Fakultas Ekonomi dan Bisnis',
        ]);
    }

    public function test_program_name_cannot_exceed_mysql_column_width(): void
    {
        $this->post(route('daftar.store'), $this->registration(['prodi' => str_repeat('A', 101)]))
            ->assertSessionHasErrors('prodi');
        $this->assertDatabaseCount('t_user', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_member_insert_failure_rolls_back_account_and_cleans_uploaded_files(): void
    {
        Anggota::creating(static function (): void {
            throw new RuntimeException('Simulated member insert failure');
        });

        try {
            $this->post(route('daftar.store'), $this->registration());
            $this->assertDatabaseCount('t_user', 0);
            $this->assertDatabaseCount('t_anggota', 0);
            $this->assertSame([], Storage::disk('public')->allFiles());
            $this->assertSame([], Storage::disk('local')->allFiles());
            Mail::assertNothingSent();
        } finally {
            Anggota::flushEventListeners();
        }
    }

    public function test_mail_transport_failure_keeps_registration_and_shows_warning(): void
    {
        $pendingMail = Mockery::mock(PendingMail::class);
        $pendingMail->shouldReceive('send')->once()->andThrow(new RuntimeException('SMTP temporarily unavailable'));
        Mail::shouldReceive('to')->with('nadia@example.test')->once()->andReturn($pendingMail);

        $this->post(route('daftar.store'), $this->registration())
            ->assertRedirect(route('anggota.kartu'))->assertSessionHas('success')->assertSessionHas('warning');
        $this->assertDatabaseHas('t_user', ['email' => 'nadia@example.test']);
        $this->assertDatabaseHas('t_anggota', ['nim' => '24510099', 'status_verifikasi' => 'pending']);
        $this->assertAuthenticated();
    }

    public function test_registration_ip_limit_applies_even_to_invalid_submissions(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('daftar.store'), [])->assertSessionHasErrors('nama');
        }
        $this->post(route('daftar.store'), [])->assertStatus(429);
        $this->assertDatabaseCount('t_user', 0);
    }
}
