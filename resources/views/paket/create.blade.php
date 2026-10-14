@extends('layouts.app')

@section('title', 'Form Paket Pengadaan')

@section('content')
<div class="card">
    <div class="card-head"><h2 class="card-title">Data Paket</h2></div>

    <form>
        <div class="form-grid">
            <div class="field">
                <label for="jenis">Jenis Pengadaan</label>
                <select class="select" id="jenis">
                    <option>e-Tendering</option>
                    <option>e-Penunjukan Langsung</option>
                    <option>e-Purchasing</option>
                </select>
            </div>
            <div class="field">
                <label for="nama">Nama Paket</label>
                <input class="input" type="text" id="nama" placeholder="Masukkan nama paket">
            </div>
            <div class="field">
                <label for="opd">OPD</label>
                <select class="select" id="opd">
                    <option>Dinas Pendidikan</option>
                    <option>Dinas Kesehatan</option>
                    <option>Dinas PUPR</option>
                    <option>Dinas Kominfo</option>
                    <option>Dinas Perhubungan</option>
                </select>
            </div>
            <div class="field">
                <label for="pagu">Pagu</label>
                <input class="input" type="number" id="pagu" placeholder="Rp 0">
            </div>
            <div class="field">
                <label for="penyedia">Penyedia</label>
                <input class="input" type="text" id="penyedia" placeholder="Nama penyedia">
            </div>
            <div class="field">
                <label for="tenaga">Tenaga Ahli</label>
                <input class="input" type="text" id="tenaga" placeholder="Nama tenaga ahli">
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('verifikasi') }}" class="btn"><i class="bi bi-shield-check"></i> Cek Crosscheck & SKP</a>
        </div>

        <div class="form-actions">
            <a href="{{ route('paket.index') }}" class="btn btn-green"><i class="bi bi-check-lg"></i> Simpan</a>
            <a href="{{ route('paket.index') }}" class="btn">Batal</a>
        </div>
    </form>
</div>
@endsection