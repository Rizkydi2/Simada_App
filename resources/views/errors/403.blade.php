@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="card" style="text-align: center; padding: 48px 24px; max-width: 600px; margin: 40px auto; border-radius: var(--radius); box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);">
    <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--red-soft); color: var(--red); display: grid; place-items: center; font-size: 36px; margin: 0 auto 20px;">
        <i class="bi bi-shield-x"></i>
    </div>

    <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 8px;">Akses Ditolak (403 Forbidden)</h2>

    <div class="result-box bad" style="margin: 20px 0; font-size: 14px; font-weight: 600; text-align: center; padding: 16px;">
        {{ $exception->getMessage() ?: 'Akses Ditolak (403 Forbidden) karena Anda hanya berwenang memantau.' }}
    </div>

    <p style="color: var(--muted); font-size: 13px; line-height: 1.6; margin-bottom: 28px;">
        Sesuai spesifikasi SDD/SRS SIMADA (UC-07 & UAT-01), pembaruan tahapan paket pengadaan hanya dapat dilakukan oleh pejabat/petugas berwenang (<strong>Pokja Pemilihan</strong> atau <strong>Pejabat Pengadaan</strong>). Peran pemantau (PPK, PA/KPA, Admin) hanya berhak melihat progres.
    </p>

    <div style="display: flex; justify-content: center; gap: 12px;">
        <a href="{{ route('tracking') }}" class="btn btn-dark">
            <i class="bi bi-arrow-left"></i> Kembali ke Tracking Progres
        </a>
    </div>
</div>
@endsection
