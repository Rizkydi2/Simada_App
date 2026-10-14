<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SIMADA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/simada.css') }}">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="brand-logo"><i class="bi bi-building"></i></div>
        <h1>SIMADA</h1>
        <p>Sistem Informasi Pengadaan</p>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input class="input" type="text" id="username" name="username" placeholder="Masukkan username">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input class="input" type="password" id="password" name="password" placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn btn-dark btn-block" style="margin-top: 10px;">Login</button>
        </form>
    </div>
</body>
</html>