@extends('layouts.app')

@section('title', 'Tracking Progres')

@section('content')
@php
    $tahapan = [
        ['Perencanaan', 'bi-pencil-square'],
        ['Pengumuman',  'bi-megaphone'],
        ['Evaluasi',    'bi-clipboard-data'],
        ['Penetapan',   'bi-award'],
        ['Selesai',     'bi-check-circle'],
    ];
    $aktif = 2; // index tahapan yang sedang berjalan (0-4)

    $riwayat = [
        ['12 Okt 2026', 'Perencanaan', 'Budi Santoso', 'Dokumen perencanaan disetujui'],
        ['15 Okt 2026', 'Pengumuman',  'Siti Rahma',   'Pengumuman tayang di portal'],
        ['20 Okt 2026', 'Evaluasi',    'Pokja 1',      'Evaluasi dokumen penawaran'],
    ];
@endphp

<div class="card">
    <div class="card-head">
        <h2 class="card-title">Paket: PKT-001 - Pembangunan Gedung Sekolah</h2>
    </div>

    <div class="steps">
        @foreach ($tahapan as $i => [$nama, $icon])
            <div class="step {{ $i < $aktif ? 'done' : ($i === $aktif ? 'active' : '') }}">
                <i class="bi {{ $i < $aktif ? 'bi-check-circle-fill' : $icon }}"></i>
                {{ $nama }}
            </div>
        @endforeach
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Waktu</th><th>Tahapan</th><th>Petugas</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                @foreach ($riwayat as $r)
                    <tr>
                        <td>{{ $r[0] }}</td>
                        <td><span class="badge blue">{{ $r[1] }}</span></td>
                        <td>{{ $r[2] }}</td>
                        <td>{{ $r[3] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="form-actions">
        <button type="button" class="btn btn-dark"><i class="bi bi-arrow-repeat"></i> Update Tahapan</button>
    </div>
</div>
@endsection