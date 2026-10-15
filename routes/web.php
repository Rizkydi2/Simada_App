<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// WF-01 Login & Autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/keluar', [AuthController::class, 'logout'])->name('logout');

// WF-02 Dashboard
Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

// WF-03 & WF-04 Paket Pengadaan
Route::get('/paket', fn () => view('paket.index'))->name('paket.index');
Route::get('/paket/tambah', fn () => view('paket.create'))->name('paket.create');

// WF-05 Tracking Progres (UC-07)
Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
Route::post('/tracking/{id}/update', [TrackingController::class, 'updateTahapan'])->name('tracking.update');

// WF-06 Verifikasi
Route::get('/verifikasi', fn () => view('verifikasi'))->name('verifikasi');

// WF-07 Berita Acara & Surat
Route::get('/dokumen', fn () => view('dokumen'))->name('dokumen');

// WF-08 User & Hak Akses (UC-01)
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');