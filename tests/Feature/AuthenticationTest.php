<?php

namespace Tests\Feature;

use Tests\LibraryTestCase;

class AuthenticationTest extends LibraryTestCase
{
    public function test_guests_are_redirected_from_every_protected_area(): void
    {
        foreach (['/admin', '/admin/pendaftar', '/admin/kategori', '/admin/buku', '/anggota/kartu'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
    }

    public function test_student_cannot_access_or_mutate_admin_resources(): void
    {
        $this->actingAs($this->user());

        foreach (['/admin', '/admin/pendaftar', '/admin/kategori', '/admin/buku'] as $path) {
            $this->get($path)->assertForbidden();
        }

        $this->post(route('admin.kategori.store'), ['nama_kategori' => 'Ilegal'])->assertForbidden();
        $this->post(route('admin.buku.store'), [])->assertForbidden();
        $this->assertDatabaseCount('t_kategori', 0);
    }

    public function test_login_redirects_each_role_and_logout_invalidates_authentication(): void
    {
        $student = $this->user();
        $this->post(route('login.store'), ['email' => $student->email, 'password' => 'Password123!'])
            ->assertRedirect(route('anggota.kartu'));
        $this->assertAuthenticatedAs($student);
        $this->post(route('logout'))->assertRedirect();
        $this->assertGuest();

        $admin = $this->user('admin');
        $this->post(route('login.store'), ['email' => $admin->email, 'password' => 'Password123!'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->get(route('anggota.kartu'))->assertForbidden();
    }

    public function test_invalid_credentials_are_rejected_and_eventually_rate_limited(): void
    {
        $user = $this->user();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
