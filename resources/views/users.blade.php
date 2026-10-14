@extends('layouts.app')

@section('title', 'User & Hak Akses')

@section('content')
@php
    $users = [
        ['admin',   'Administrator',  'Admin'],
        ['budi.s',  'Budi Santoso',   'PPK'],
        ['pokja01', 'Pokja Satu',     'Pokja'],
        ['siti.r',  'Siti Rahma',     'Pejabat Pengadaan'],
        ['andi.k',  'Andi Kurniawan', 'PA/KPA'],
    ];
    $roles = ['Admin', 'PPK', 'Pokja', 'Pejabat Pengadaan', 'PA/KPA'];
@endphp

<div class="card">
    <div class="toolbar" style="margin-top: 0;">
        <div class="search" style="background: #fafafa; border: 1px solid var(--line);">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari user">
        </div>
        <button type="button" class="btn btn-dark"><i class="bi bi-plus-lg"></i> Tambah User</button>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Username</th><th>Nama</th><th>Role</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td><b>{{ $u[0] }}</b></td>
                        <td>{{ $u[1] }}</td>
                        <td><span class="badge blue">{{ $u[2] }}</span></td>
                        <td>
                            <div class="td-actions">
                                <a href="#" class="btn-icon" title="Ubah"><i class="bi bi-pencil"></i></a>
                                <a href="#" class="btn-icon" title="Hapus"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="chips">
        <b style="margin-right: 6px;">Role:</b>
        @foreach ($roles as $r)
            <span class="chip">{{ $r }}</span>
        @endforeach
    </div>
</div>
@endsection