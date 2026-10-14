<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIMADA') - SIMADA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/simada.css') }}">
</head>
<body>
@php
    $menu = [
        ['dashboard',    'Dashboard',            'bi-grid-1x2',          'dashboard'],
        ['paket.index',  'Paket Pengadaan',      'bi-box-seam',          'paket.*'],
        ['tracking',     'Tracking Progres',     'bi-signpost-split',    'tracking'],
        ['verifikasi',   'Verifikasi',           'bi-patch-check',       'verifikasi'],
        ['dokumen',      'Berita Acara & Surat', 'bi-file-earmark-text', 'dokumen'],
        ['users',        'User & Hak Akses',     'bi-people',            'users'],
    ];
@endphp

<div class="shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo"><i class="bi bi-building"></i></div>
            SIMADA
        </div>
        <nav class="nav">
            @foreach ($menu as [$route, $label, $icon, $pattern])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i> {{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <h1>@yield('title')</h1>
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="Cari sesuatu...">
            </div>
            <button class="icon-btn" type="button"><i class="bi bi-bell"></i></button>
            <div class="user-pill">
                <div class="avatar">P</div>
                Pengguna <span class="sep">|</span>
                <a href="{{ route('logout') }}">Keluar</a>
            </div>
        </header>

        @yield('content')
    </main>
</div>
</body>
</html>