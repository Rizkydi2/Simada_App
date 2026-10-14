@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $opd = [
        ['Dinas Pendidikan', 12.5, 10.8, 86],
        ['Dinas Kesehatan',  9.2,  7.1,  77],
        ['Dinas PUPR',       15.0, 13.4, 89],
        ['Dinas Kominfo',    6.3,  4.9,  78],
        ['Dinas Perhubungan', 5.2, 3.5,  67],
    ];
    $terbaru = [
        ['Gedung Sekolah Baru', 'Dinas Pendidikan', 'Rp 4,2 M', 'Evaluasi', 'blue', 'bi-building', 't1'],
        ['Alat Kesehatan Puskesmas', 'Dinas Kesehatan', 'Rp 1,8 M', 'Pengumuman', 'amber', 'bi-heart-pulse', 't2'],
        ['Jembatan Kali Baru', 'Dinas PUPR', 'Rp 7,5 M', 'Penetapan', 'green', 'bi-bricks', 't3'],
        ['Server Data Center', 'Dinas Kominfo', 'Rp 2,1 M', 'Perencanaan', 'gray', 'bi-hdd-rack', 't4'],
    ];
@endphp

<div class="grid-main">
    <div class="stack">

        {{-- Overview --}}
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">Overview</h2>
                <div class="filters">
                    <select class="select-pill">
                        <option>Semua OPD</option>
                        @foreach ($opd as $o)<option>{{ $o[0] }}</option>@endforeach
                    </select>
                    <select class="select-pill">
                        <option>2026</option>
                        <option>2025</option>
                    </select>
                    <button class="btn btn-dark btn-sm" type="button"><i class="bi bi-download"></i> Ekspor</button>
                </div>
            </div>

            <div class="stats">
                <div class="stat first">
                    <div class="stat-label"><i class="bi bi-wallet2"></i> Total Pagu</div>
                    <div class="stat-value">Rp 48,2 M</div>
                    <span class="delta up"><i class="bi bi-arrow-up"></i> 12,4%</span>
                    <small>vs periode lalu</small>
                </div>
                <div class="stat">
                    <div class="stat-label"><i class="bi bi-file-earmark-check"></i> Total Kontrak</div>
                    <div class="stat-value">Rp 39,7 M</div>
                    <span class="delta up"><i class="bi bi-arrow-up"></i> 8,1%</span>
                    <small>vs periode lalu</small>
                </div>
                <div class="stat">
                    <div class="stat-label"><i class="bi bi-pie-chart"></i> Serapan</div>
                    <div class="stat-value">82,4%</div>
                    <span class="delta down"><i class="bi bi-arrow-down"></i> 2,3%</span>
                    <small>vs periode lalu</small>
                </div>
            </div>
        </div>

        {{-- Grafik Pagu vs Kontrak --}}
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">Grafik Pagu vs Kontrak</h2>
                <div class="legend">
                    <span><i style="background:#d6d6da"></i>Pagu</span>
                    <span><i style="background:var(--green)"></i>Kontrak</span>
                </div>
            </div>
            <div class="chart">
                @foreach ($opd as $o)
                    <div class="chart-col">
                        <div class="bars">
                            <div class="bar" style="height: {{ $o[1] / 15 * 100 }}%"></div>
                            <div class="bar green" style="height: {{ $o[2] / 15 * 100 }}%" data-tip="Rp {{ number_format($o[2], 1, ',', '.') }} M"></div>
                        </div>
                        <div class="chart-label">{{ str_replace('Dinas ', '', $o[0]) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tabel OPD --}}
        <div class="card">
            <div class="card-head"><h2 class="card-title">Rekap per OPD</h2></div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>OPD</th><th>Pagu</th><th>Kontrak</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($opd as $o)
                            <tr>
                                <td><b>{{ $o[0] }}</b></td>
                                <td>Rp {{ number_format($o[1], 1, ',', '.') }} M</td>
                                <td>Rp {{ number_format($o[2], 1, ',', '.') }} M</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Panel kanan --}}
    <div class="card" style="align-self: start;">
        <div class="card-head"><h2 class="card-title">Paket Terbaru</h2></div>
        @foreach ($terbaru as $t)
            <div class="list-item">
                <div class="thumb {{ $t[6] }}"><i class="bi {{ $t[5] }}"></i></div>
                <div class="list-body">
                    <b>{{ $t[0] }}</b>
                    <small>{{ $t[1] }}</small>
                </div>
                <div class="list-side">
                    <span>{{ $t[2] }}</span>
                    <span class="badge {{ $t[4] }}">{{ $t[3] }}</span>
                </div>
            </div>
        @endforeach
        <a href="{{ route('paket.index') }}" class="btn btn-block" style="margin-top: 14px;">Semua paket</a>
    </div>
</div>
@endsection