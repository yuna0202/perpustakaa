<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{buku}', [CatalogController::class, 'show'])->whereNumber('buku')->name('katalog.show');
Route::get('/anggota/verifikasi/{token}', [MemberController::class, 'verify'])->where('token', '[A-Za-z0-9]{64}')->name('anggota.verifikasi');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::get('/daftar', [RegistrationController::class, 'create'])->name('daftar');
    Route::post('/daftar', [RegistrationController::class, 'store'])->middleware('throttle:registration')->name('daftar.store');
});
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/anggota/kartu', [MemberController::class, 'card'])->middleware(['auth', 'role:mahasiswa'])->name('anggota.kartu');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/pendaftar', [ApplicantController::class, 'index'])->name('pendaftar.index');
    Route::get('/pendaftar/{anggota}', [ApplicantController::class, 'show'])->name('pendaftar.show');
    Route::patch('/pendaftar/{anggota}', [ApplicantController::class, 'review'])->name('pendaftar.review');
    Route::post('/pendaftar/{anggota}/kirim-hasil', [ApplicantController::class, 'sendDecision'])->middleware('throttle:10,1')->name('pendaftar.kirim-hasil');
    Route::get('/pendaftar/{anggota}/ktm', [ApplicantController::class, 'ktm'])->name('pendaftar.ktm');
    Route::resource('kategori', CategoryController::class)->except('show')->parameters(['kategori' => 'kategori']);
    Route::resource('buku', BookController::class)->parameters(['buku' => 'buku']);
});
