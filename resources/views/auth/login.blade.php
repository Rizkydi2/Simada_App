<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIMADA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/simada.css') }}">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="brand-logo"><i class="bi bi-building"></i></div>
        <h1>SIMADA</h1>
        <p>Sistem Informasi Pengadaan Barang & Jasa</p>

        {{-- Notifikasi Sukses / Logout --}}
        @if (session('success'))
            <div class="result-box" style="margin-bottom: 16px; padding: 12px 16px; font-size: 13px; text-align: left; display: flex; align-items: center; gap: 8px; border-radius: 14px;">
                <i class="bi bi-check-circle-fill" style="font-size: 16px;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Notifikasi Error Validasi / Kredensial Salah --}}
        @if ($errors->any())
            <div class="result-box bad" style="margin-bottom: 16px; padding: 12px 16px; font-size: 13px; text-align: left; border-radius: 14px;">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 16px;"></i>
                    <span>Gagal Masuk:</span>
                </div>
                <ul style="margin: 6px 0 0 20px; font-size: 12px; font-weight: 500;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="field">
                <label for="username">NIP atau Email Kedinasan</label>
                <input class="input" type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Contoh: admin@simada.kebumenkab.go.id" required autofocus>
            </div>
            <div class="field" style="margin-top: 12px;">
                <label for="password">Kata Sandi</label>
                <input class="input" type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
            </div>
            <button type="submit" class="btn btn-dark btn-block" style="margin-top: 18px;">
                <i class="bi bi-box-arrow-in-right"></i> Masuk ke SIMADA
            </button>
        </form>

        <div style="margin-top: 24px; padding: 14px 16px; background: var(--shell); border-radius: 16px; font-size: 12px; text-align: left; color: #444;">
            <div style="font-weight: 600; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-info-circle-fill" style="color: var(--blue);"></i> Kredensial Uji Coba (Seeder):
            </div>
            <div style="line-height: 1.6;">
                <div>• <b>Admin:</b> <code>admin@simada.kebumenkab.go.id</code></div>
                <div>• <b>PPK:</b> <code>budi.santoso@kebumenkab.go.id</code></div>
                <div>• <b>Pokja:</b> <code>siti.rahma@simada.kebumenkab.go.id</code></div>
                <div style="margin-top: 4px; color: var(--muted);">Password semua akun: <code>password123</code></div>
            </div>
        </div>
    </div>
</body>
</html>