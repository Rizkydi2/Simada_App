@extends('layouts.app')

@section('title', 'Berita Acara & Surat')

@section('content')
<div class="grid-form">

    <div class="card" style="align-self: start;">
        <div class="card-head"><h2 class="card-title">Pengaturan Dokumen</h2></div>

        <div class="stack" style="gap: 14px;">
            <div class="field">
                <label for="jenis">Jenis Dokumen</label>
                <select class="select" id="jenis">
                    <option>Berita Acara Evaluasi</option>
                    <option>Berita Acara Penetapan</option>
                    <option>Surat Penunjukan</option>
                    <option>Surat Undangan</option>
                </select>
            </div>
            <div class="field">
                <label for="paket">Pilih Paket</label>
                <select class="select" id="paket">
                    <option>PKT-001 - Pembangunan Gedung Sekolah</option>
                    <option>PKT-002 - Rehabilitasi Jalan Raya</option>
                    <option>PKT-003 - Pengadaan Alat Kesehatan</option>
                </select>
            </div>
            <div class="field">
                <label for="nomor">Nomor & Tanggal</label>
                <input class="input" type="text" id="nomor" placeholder="Contoh: 001/BA/X/2026 - 14 Okt 2026">
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-dark"><i class="bi bi-file-earmark-pdf"></i> Cetak PDF</button>
            <button type="button" class="btn"><i class="bi bi-file-earmark-excel"></i> Excel</button>
        </div>
    </div>

    <div class="card">
        <div class="card-head"><h2 class="card-title">Pratinjau Dokumen</h2></div>
        <div class="preview">
            <div>
                <i class="bi bi-file-earmark-text"></i>
                Pratinjau dokumen akan tampil di sini
            </div>
        </div>
    </div>

</div>
@endsection