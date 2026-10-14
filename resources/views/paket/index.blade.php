@extends('layouts.app')

@section('title', 'Paket Pengadaan')

@section('content')
@php
    $jenisList = [
        'tender'     => 'e-Tendering',
        'langsung'   => 'e-Penunjukan Langsung',
        'purchasing' => 'e-Purchasing',
    ];
    $jenis = request('jenis', 'tender');

    $paket = [
        ['PKT-001', 'Pembangunan Gedung Sekolah', 'Dinas Pendidikan', 'Evaluasi', 'tender'],
        ['PKT-002', 'Rehabilitasi Jalan Raya', 'Dinas PUPR', 'Pengumuman', 'tender'],
        ['PKT-003', 'Pengadaan Alat Kesehatan', 'Dinas Kesehatan', 'Penetapan', 'langsung'],
        ['PKT-004', 'Jasa Konsultan Perencanaan', 'Dinas PUPR', 'Perencanaan', 'langsung'],
        ['PKT-005', 'Pengadaan Laptop Kantor', 'Dinas Kominfo', 'Selesai', 'purchasing'],
        ['PKT-006', 'Pengadaan ATK', 'Dinas Perhubungan', 'Selesai', 'purchasing'],
    ];
    $warna = ['Perencanaan' => 'gray', 'Pengumuman' => 'amber', 'Evaluasi' => 'blue', 'Penetapan' => 'blue', 'Selesai' => 'green'];
    $rows = collect($paket)->where(4, $jenis);
@endphp

<div class="card">
    <div class="tabs">
        @foreach ($jenisList as $key => $label)
            <a href="{{ route('paket.index', ['jenis' => $key]) }}" class="tab {{ $jenis === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="toolbar">
        <div class="search" style="background: #fafafa; border: 1px solid var(--line);">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari paket">
        </div>
        <a href="{{ route('paket.create') }}" class="btn btn-dark"><i class="bi bi-plus-lg"></i> Tambah Paket</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Kode</th><th>Nama Paket</th><th>OPD</th><th>Tahapan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $p)
                    <tr>
                        <td><b>{{ $p[0] }}</b></td>
                        <td>{{ $p[1] }}</td>
                        <td>{{ $p[2] }}</td>
                        <td><span class="badge {{ $warna[$p[3]] }}">{{ $p[3] }}</span></td>
                        <td>
                            <div class="td-actions">
                                <a href="{{ route('tracking') }}" class="btn-icon" title="Lihat progres"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn-icon" title="Ubah"><i class="bi bi-pencil"></i></a>
                                <a href="#" class="btn-icon" title="Hapus"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; color: var(--muted);">Belum ada paket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection