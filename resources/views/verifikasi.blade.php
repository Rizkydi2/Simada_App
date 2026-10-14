@extends('layouts.app')

@section('title', 'Verifikasi Crosscheck & SKP')

@section('content')
<div class="grid-2">

    {{-- Crosscheck Personel --}}
    <div class="card">
        <div class="card-head"><h2 class="card-title">Crosscheck Personel</h2></div>

        <div class="field">
            <label for="tenaga">Nama Tenaga Ahli</label>
            <input class="input" type="text" id="tenaga" placeholder="Masukkan nama tenaga ahli">
        </div>
        <div class="form-actions" style="margin: 14px 0 18px;">
            <button type="button" class="btn btn-dark btn-sm"><i class="bi bi-search"></i> Cek</button>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Paket</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>PKT-001 Gedung Sekolah</td><td><span class="badge">Aktif</span></td></tr>
                    <tr><td>PKT-004 Konsultan Perencanaan</td><td><span class="badge red">Bentrok</span></td></tr>
                    <tr><td>PKT-006 Pengadaan ATK</td><td><span class="badge gray">Selesai</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Validasi SKP --}}
    <div class="card">
        <div class="card-head"><h2 class="card-title">Validasi SKP</h2></div>

        <div class="field">
            <label for="penyedia">Penyedia</label>
            <input class="input" type="text" id="penyedia" placeholder="Masukkan nama penyedia">
        </div>

        <div class="result-box">
            <i class="bi bi-check-circle" style="font-size: 26px; display: block; margin-bottom: 6px;"></i>
            Hasil SKP: Layak
        </div>

        <button type="button" class="btn btn-dark"><i class="bi bi-patch-check"></i> Validasi</button>
    </div>

</div>
@endsection