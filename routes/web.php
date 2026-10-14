<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// WF-01 Login
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', fn () => redirect()->route('dashboard'))->name('login.submit');
Route::get('/keluar', fn () => redirect()->route('login'))->name('logout');

// WF-02 Dashboard
Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

// WF-03 & WF-04 Paket Pengadaan
Route::get('/paket', fn () => view('paket.index'))->name('paket.index');
Route::get('/paket/tambah', fn () => view('paket.create'))->name('paket.create');

// WF-05 Tracking Progres
Route::get('/tracking', fn () => view('tracking'))->name('tracking');

// WF-06 Verifikasi
Route::get('/verifikasi', fn () => view('verifikasi'))->name('verifikasi');

// WF-07 Berita Acara & Surat
Route::get('/dokumen', fn () => view('dokumen'))->name('dokumen');

// WF-08 User & Hak Akses
Route::get('/users', fn () => view('users'))->name('users');