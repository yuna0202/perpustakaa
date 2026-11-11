<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class InitialAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@gajayana.ac.id';

        if (User::where('email', $email)->exists()) {
            return;
        }

        $password = config('library.initial_admin_password');

        if (! is_string($password) || mb_strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_INITIAL_PASSWORD to a password of at least 12 characters.');
        }

        User::create([
            'nama' => 'Admin Perpustakaan',
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
        ]);
    }
}
