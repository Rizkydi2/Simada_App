@extends('layouts.app')

@section('title', 'User & Hak Akses')

@section('content')
@php
    $roleBadges = [
        'Admin Sistem'      => 'red',
        'PPK'               => 'blue',
        'Pokja Pemilihan'   => 'amber',
        'Pejabat Pengadaan' => 'green',
        'PA/KPA'            => 'gray',
    ];
@endphp

{{-- Notifikasi Sukses --}}
@if (session('success'))
    <div class="result-box" style="margin-bottom: 20px; text-align: left; display: flex; align-items: center; justify-content: space-between; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

{{-- Notifikasi Galat Operasi --}}
@if (session('error'))
    <div class="result-box bad" style="margin-bottom: 20px; text-align: left; display: flex; align-items: center; justify-content: space-between; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 20px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

{{-- Notifikasi Validasi Input --}}
@if ($errors->any())
    <div class="result-box bad" style="margin-bottom: 20px; text-align: left; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
            <i class="bi bi-exclamation-circle-fill" style="font-size: 18px;"></i>
            <strong>Terjadi kesalahan validasi data:</strong>
        </div>
        <ul style="margin-left: 28px; font-weight: 500; font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Ringkasan Statistik Pengguna --}}
<div class="stats" style="margin-bottom: 20px;">
    <div class="stat first">
        <div class="stat-label"><i class="bi bi-people-fill" style="color: var(--blue);"></i> Total Pengguna</div>
        <div class="stat-value">{{ $totalUsers ?? 0 }}</div>
        <small>Terdaftar di basis data SIMADA</small>
    </div>
    <div class="stat">
        <div class="stat-label"><i class="bi bi-check-circle-fill" style="color: var(--green);"></i> Akun Aktif</div>
        <div class="stat-value">{{ $activeUsers ?? 0 }}</div>
        <small><span class="delta up"><i class="bi bi-shield-check"></i> Siap Login</span></small>
    </div>
    <div class="stat">
        <div class="stat-label"><i class="bi bi-slash-circle-fill" style="color: var(--muted);"></i> Akun Nonaktif</div>
        <div class="stat-value">{{ $inactiveUsers ?? 0 }}</div>
        <small><span class="delta down"><i class="bi bi-lock"></i> Akses Ditutup</span></small>
    </div>
</div>

{{-- Kartu Utama Tabel Pengguna & Toolbar --}}
<div class="card">
    <div class="card-head">
        <div>
            <h2 class="card-title">Inventaris Akun & Hak Akses Pengguna</h2>
            <small style="color: var(--muted);">Pengaturan peran aparatur pengadaan berbasis Role-Based Access Control (RBAC)</small>
        </div>
        <button type="button" class="btn btn-dark" onclick="openAddUserModal()">
            <i class="bi bi-plus-lg"></i> Tambah User Baru
        </button>
    </div>

    {{-- Filter Peran & Pencarian Pengguna --}}
    <div class="toolbar" style="margin-top: 0; margin-bottom: 20px;">
        <form method="GET" action="{{ route('users') }}" style="display: flex; gap: 10px; flex-wrap: wrap; width: 100%; align-items: center;">
            <div class="search" style="background: #fafafa; border: 1px solid var(--line); flex: 1; min-width: 260px;">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, NIP, email, atau OPD...">
            </div>

            <select name="role" class="select-pill" onchange="this.form.submit()" style="cursor: pointer;">
                <option value="">-- Semua Peran RBAC --</option>
                @foreach ($roles as $r)
                    <option value="{{ $r }}" {{ ($selectedRole ?? '') === $r ? 'selected' : '' }}>
                        {{ $r }} ({{ $roleCounts[$r] ?? 0 }})
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-dark">
                <i class="bi bi-funnel"></i> Terapkan
            </button>

            @if (!empty($search) || !empty($selectedRole))
                <a href="{{ route('users') }}" class="btn btn-sm" title="Reset Filter">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Tabel Pengguna dari Database MySQL --}}
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>NIP</th>
                    <th>Email Kedinasan</th>
                    <th>Peran (Role RBAC)</th>
                    <th>OPD / Unit Kerja</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr>
                        <td>
                            <b>{{ $u->name }}</b>
                        </td>
                        <td>
                            <code>{{ $u->nip ?? '-' }}</code>
                        </td>
                        <td>
                            <span style="color: var(--muted); font-size: 13px;">{{ $u->email }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $roleBadges[$u->role] ?? 'blue' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td>
                            <span>{{ $u->opd ?? '-' }}</span>
                        </td>
                        <td>
                            @if ($u->status === 'Aktif')
                                <span class="badge">{{ $u->status }}</span>
                            @else
                                <span class="badge red">{{ $u->status }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="td-actions" style="justify-content: center;">
                                <button type="button" class="btn-icon" title="Edit Pengguna & Hak Akses"
                                    onclick='openEditUserModal(@json($u))'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('users.destroy', $u->id) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna {{ addslashes($u->name) }}?');"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon" title="Hapus Pengguna" style="color: var(--red);">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--muted); padding: 36px 20px;">
                            <i class="bi bi-person-x" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                            Data pengguna tidak ditemukan. Silakan sesuaikan kata kunci pencarian atau tambah pengguna baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Kartu Ringkasan 5 Peran RBAC (WF-08) --}}
<div class="card">
    <div class="chips">
        <b style="margin-right: 6px;"><i class="bi bi-shield-lock"></i> 5 Peran RBAC SIMADA:</b>
        @foreach ($roles as $r)
            <a href="{{ route('users', ['role' => $r]) }}" class="chip" style="{{ ($selectedRole ?? '') === $r ? 'background: var(--dark); color: #fff;' : '' }}">
                <span class="badge {{ $roleBadges[$r] ?? 'blue' }}" style="padding: 2px 8px; font-size: 11px; margin-right: 4px;">●</span>
                {{ $r }} ({{ $roleCounts[$r] ?? 0 }})
            </a>
        @endforeach
    </div>
</div>

{{-- Modal Popup "Tambah User Baru" --}}
<div id="modalAddUser" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div class="card" style="width: 100%; max-width: 580px; box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.35); border-radius: var(--radius); position: relative; max-height: 90vh; overflow-y: auto;">
        <div class="card-head" style="margin-bottom: 20px;">
            <div>
                <h3 class="card-title" style="font-size: 20px;"><i class="bi bi-person-plus-fill"></i> Tambah User Baru</h3>
                <small style="color: var(--muted);">Pendaftaran akun aparatur pengadaan dan penetapan peran RBAC</small>
            </div>
            <button type="button" class="btn-icon" onclick="closeAddUserModal()" title="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="stack" style="gap: 16px;">
                <div class="field">
                    <label for="add_name">Nama Lengkap & Gelar <span style="color: var(--red);">*</span></label>
                    <input class="input" type="text" id="add_name" name="name" value="{{ old('name') }}" placeholder="Contoh: Nama Lengkap Pengguna, S.T." required>
                </div>

                <div class="form-grid" style="gap: 16px;">
                    <div class="field">
                        <label for="add_nip">NIP (Nomor Induk Pegawai)</label>
                        <input class="input" type="text" id="add_nip" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 197903152005011002" maxlength="30">
                    </div>

                    <div class="field">
                        <label for="add_email">Email Kedinasan <span style="color: var(--red);">*</span></label>
                        <input class="input" type="email" id="add_email" name="email" value="{{ old('email') }}" placeholder="nama@kebumenkab.go.id" required>
                    </div>
                </div>

                <div class="field">
                    <label for="add_password">Kata Sandi Awal <span style="color: var(--red);">*</span></label>
                    <input class="input" type="password" id="add_password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                </div>

                <div class="form-grid" style="gap: 16px;">
                    <div class="field">
                        <label for="add_role">Peran (Role RBAC) <span style="color: var(--red);">*</span></label>
                        <select class="select" id="add_role" name="role" required>
                            @foreach ($roles as $r)
                                <option value="{{ $r }}" {{ old('role', 'Pokja Pemilihan') === $r ? 'selected' : '' }}>
                                    {{ $r }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="add_status">Status Akun <span style="color: var(--red);">*</span></label>
                        <select class="select" id="add_status" name="status" required>
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}" {{ old('status', 'Aktif') === $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="add_opd">Organisasi Perangkat Daerah (OPD)</label>
                    <input class="input" type="text" id="add_opd" name="opd" value="{{ old('opd') }}" placeholder="Contoh: Dinas Pendidikan / Bagian PBJ Setda Kebumen">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; justify-content: flex-end;">
                <button type="button" class="btn" onclick="closeAddUserModal()">Batal</button>
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check-lg"></i> Simpan User Baru
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Popup "Edit User & Hak Akses" --}}
<div id="modalEditUser" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div class="card" style="width: 100%; max-width: 580px; box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.35); border-radius: var(--radius); position: relative; max-height: 90vh; overflow-y: auto;">
        <div class="card-head" style="margin-bottom: 20px;">
            <div>
                <h3 class="card-title" style="font-size: 20px;"><i class="bi bi-pencil-square"></i> Edit User & Hak Akses</h3>
                <small style="color: var(--muted);">Perbarui data profil, peran wewenang (RBAC), atau reset kata sandi</small>
            </div>
            <button type="button" class="btn-icon" onclick="closeEditUserModal()" title="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditUser" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="stack" style="gap: 16px;">
                <div class="field">
                    <label for="edit_name">Nama Lengkap & Gelar <span style="color: var(--red);">*</span></label>
                    <input class="input" type="text" id="edit_name" name="name" required>
                </div>

                <div class="form-grid" style="gap: 16px;">
                    <div class="field">
                        <label for="edit_nip">NIP (Nomor Induk Pegawai)</label>
                        <input class="input" type="text" id="edit_nip" name="nip" maxlength="30">
                    </div>

                    <div class="field">
                        <label for="edit_email">Email Kedinasan <span style="color: var(--red);">*</span></label>
                        <input class="input" type="email" id="edit_email" name="email" required>
                    </div>
                </div>

                <div class="field">
                    <label for="edit_password">Kata Sandi Baru (Opsional)</label>
                    <input class="input" type="password" id="edit_password" name="password" placeholder="Kosongkan jika tidak ingin mengubah kata sandi" minlength="6">
                    <small style="color: var(--muted); font-size: 12px;">Hanya isi jika ingin mereset kata sandi akun pengguna ini.</small>
                </div>

                <div class="form-grid" style="gap: 16px;">
                    <div class="field">
                        <label for="edit_role">Peran (Role RBAC) <span style="color: var(--red);">*</span></label>
                        <select class="select" id="edit_role" name="role" required>
                            @foreach ($roles as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="edit_status">Status Akun <span style="color: var(--red);">*</span></label>
                        <select class="select" id="edit_status" name="status" required>
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="edit_opd">Organisasi Perangkat Daerah (OPD)</label>
                    <input class="input" type="text" id="edit_opd" name="opd" placeholder="Contoh: Dinas Pendidikan / Bappeda">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px; justify-content: flex-end;">
                <button type="button" class="btn" onclick="closeEditUserModal()">Batal</button>
                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddUserModal() {
        const modal = document.getElementById('modalAddUser');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeAddUserModal() {
        const modal = document.getElementById('modalAddUser');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function openEditUserModal(user) {
        const form = document.getElementById('formEditUser');
        form.action = "{{ url('users') }}/" + user.id;

        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_nip').value = user.nip || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_role').value = user.role || 'Pokja Pemilihan';
        document.getElementById('edit_opd').value = user.opd || '';
        document.getElementById('edit_status').value = user.status || 'Aktif';

        const modal = document.getElementById('modalEditUser');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeEditUserModal() {
        const modal = document.getElementById('modalEditUser');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Tutup modal jika tombol Escape ditekan
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddUserModal();
            closeEditUserModal();
        }
    });

    // Tutup modal jika area luar (backdrop) diklik
    window.addEventListener('click', function(e) {
        const modalAdd = document.getElementById('modalAddUser');
        const modalEdit = document.getElementById('modalEditUser');
        if (e.target === modalAdd) {
            closeAddUserModal();
        }
        if (e.target === modalEdit) {
            closeEditUserModal();
        }
    });
</script>
@endsection