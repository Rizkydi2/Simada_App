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
    $userRole = Auth::check() ? Auth::user()->role : null;

    $menu = [
        ['dashboard',    'Dashboard',            'bi-grid-1x2',          'dashboard', true],
        ['paket.index',  'Paket Pengadaan',      'bi-box-seam',          'paket.*',   true],
        ['tracking',     'Tracking Progres',     'bi-signpost-split',    'tracking',  true],
        ['verifikasi',   'Verifikasi',           'bi-patch-check',       'verifikasi', is_null($userRole) || in_array($userRole, ['Admin Sistem', 'Pokja Pemilihan', 'Pejabat Pengadaan'])],
        ['dokumen',      'Berita Acara & Surat', 'bi-file-earmark-text', 'dokumen',    true],
        ['users',        'User & Hak Akses',     'bi-people',            'users',      is_null($userRole) || $userRole === 'Admin Sistem'],
    ];
@endphp

<div class="shell">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo"><i class="bi bi-building"></i></div>
            SIMADA
        </div>
        <nav class="nav">
            @foreach ($menu as [$route, $label, $icon, $pattern, $visible])
                @if ($visible)
                    <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}">
                        <i class="bi {{ $icon }}"></i> {{ $label }}
                    </a>
                @endif
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
            <div class="user-pill" style="height: auto; padding: 4px 16px 4px 6px;">
                @auth
                    <div class="avatar" title="{{ Auth::user()->name }}">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div style="display: flex; flex-direction: column; line-height: 1.2; text-align: left;">
                        <span style="font-size: 13px; font-weight: 600;">{{ Auth::user()->name }}</span>
                        <small style="color: var(--muted); font-size: 11px;">{{ Auth::user()->role }}</small>
                    </div>
                    <span class="sep" style="margin: 0 4px;">|</span>
                    <a href="{{ route('logout') }}" title="Keluar dari sesi akun">Keluar</a>
                @else
                    <div class="avatar">G</div>
                    <span>Tamu</span>
                    <span class="sep" style="margin: 0 4px;">|</span>
                    <a href="{{ route('login') }}" style="color: var(--blue);">Masuk</a>
                @endauth
            </div>
        </header>

        @yield('content')
    </main>
</div>
</body>
</html>