@extends('layouts.app')

@section('title', 'Tracking Progres')

@section('content')
@php
    $badgeColors = [
        'Perencanaan' => 'gray',
        'Pengumuman'  => 'amber',
        'Evaluasi'    => 'blue',
        'Penetapan'   => 'blue',
        'Selesai'     => 'green',
    ];
@endphp

@if (session('success'))
    <div class="result-box" style="margin-bottom: 20px; text-align: left; display: flex; align-items: center; justify-content: space-between; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill" style="font-size: 20px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="result-box bad" style="margin-bottom: 20px; text-align: left; border-radius: 16px;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 18px;"></i>
            <strong>Gagal memperbarui tahapan:</strong>
        </div>
        <ul style="margin-left: 28px; font-weight: 500; font-size: 13px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-head">
        <div>
            <h2 class="card-title">
                Paket: {{ $paket ? ($paket->kode_paket . ' - ' . $paket->nama_paket) : 'Belum Ada Paket' }}
            </h2>
            @if ($paket)
                <div style="display: flex; gap: 14px; margin-top: 6px; font-size: 13px; color: var(--muted); flex-wrap: wrap;">
                    <span><i class="bi bi-building"></i> OPD: <b style="color: var(--text);">{{ $paket->opd }}</b></span>
                    <span><i class="bi bi-diagram-3"></i> Metode: <b style="color: var(--text);">{{ $paket->metode }}</b></span>
                    <span><i class="bi bi-cash-stack"></i> Pagu: <b style="color: var(--text);">Rp {{ number_format($paket->pagu, 0, ',', '.') }}</b></span>
                    <span><i class="bi bi-calculator"></i> HPS: <b style="color: var(--text);">Rp {{ number_format($paket->hps, 0, ',', '.') }}</b></span>
                    <span><i class="bi bi-flag"></i> Status: <span class="badge {{ $badgeColors[$paket->tahapan_sekarang] ?? 'blue' }}">{{ $paket->tahapan_sekarang }}</span></span>
                </div>
            @endif
        </div>

        <div class="filters">
            <form method="GET" action="{{ route('tracking') }}" id="formPilihPaket" style="display: flex; align-items: center; gap: 8px;">
                <label for="selectPaket" style="font-size: 12px; font-weight: 600; color: var(--muted);">Pilih Paket:</label>
                <select id="selectPaket" name="paket_id" class="select-pill" onchange="this.form.submit()" style="cursor: pointer; min-width: 280px; font-weight: 500;">
                    @forelse ($semuaPaket as $item)
                        <option value="{{ $item->id }}" {{ $paket && $paket->id === $item->id ? 'selected' : '' }}>
                            {{ $item->kode_paket }} - {{ $item->nama_paket }} ({{ $item->tahapan_sekarang }})
                        </option>
                    @empty
                        <option value="">(Belum ada data paket)</option>
                    @endforelse
                </select>
            </form>
        </div>
    </div>

    @if ($paket)
        <!-- Stepper 5 Tahapan (Perencanaan, Pengumuman, Evaluasi, Penetapan, Selesai) -->
        <div class="steps">
            @php
                $isAllCompleted = strcasecmp($paket->tahapan_sekarang, 'Selesai') === 0;
            @endphp
            @foreach ($tahapanList as $i => $step)
                @php
                    $isDone = $isAllCompleted ? true : ($i < $aktifIndex);
                    $isActive = !$isAllCompleted && ($i === $aktifIndex);
                @endphp
                <div class="step {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                    <i class="bi {{ $isDone ? 'bi-check-circle-fill' : $step['icon'] }}"></i>
                    {{ $step['nama'] }}
                </div>
            @endforeach
        </div>

        <!-- Tabel Riwayat Progres Tahapan -->
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Tahapan</th>
                        <th>Petugas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr>
                            <td>{{ $r->created_at ? $r->created_at->format('d M Y, H:i') : '-' }}</td>
                            <td><span class="badge {{ $badgeColors[$r->tahapan] ?? 'blue' }}">{{ $r->tahapan }}</span></td>
                            <td>
                                <b>{{ $r->petugas }}</b>
                                @if ($r->peran)
                                    <div style="font-size: 11px; color: var(--muted); margin-top: 2px;">{{ $r->peran }}</div>
                                @endif
                            </td>
                            <td>{{ $r->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--muted); padding: 24px;">
                                Belum ada catatan riwayat tracking untuk paket ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @php
            $currentRole = Auth::check() ? Auth::user()->role : null;
            $isSelesai = $paket->tahapan_sekarang === 'Selesai';
            $isEksekutor = is_null($currentRole) || in_array($currentRole, ['Pokja Pemilihan', 'Pejabat Pengadaan']);
            $canUpdateTahapan = $isEksekutor && !$isSelesai;
        @endphp

        <div class="form-actions">
            @if ($isSelesai)
                <div style="display: inline-flex; align-items: center; gap: 8px; color: var(--green-dark); font-size: 13px; padding: 10px 18px; background: var(--green-soft); border-radius: 999px;">
                    <i class="bi bi-check-circle-fill"></i>
                    Seluruh tahapan paket pengadaan telah <b>Selesai (Tuntas)</b>. Tidak ada tahapan lanjutan.
                </div>
            @elseif ($canUpdateTahapan)
                <button type="button" class="btn btn-dark" onclick="openUpdateModal()">
                    <i class="bi bi-arrow-repeat"></i> Update Tahapan
                </button>
            @else
                <div style="display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-size: 13px; padding: 10px 18px; background: var(--shell); border-radius: 999px;">
                    <i class="bi bi-shield-lock" style="color: var(--amber);"></i>
                    Peran Anda sebagai <b>{{ $currentRole }}</b> berwenang sebagai <u>Pemantau Progres</u> (hanya Pokja Pemilihan & Pejabat Pengadaan yang dapat memperbarui tahapan).
                </div>
            @endif
        </div>

        @if ($canUpdateTahapan)
        <!-- Modal Update Tahapan -->
        <div id="modalUpdateTahapan" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.45); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
            <div class="card" style="width: 100%; max-width: 520px; box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.35); border-radius: var(--radius); position: relative;">
                <div class="card-head" style="margin-bottom: 20px;">
                    <div>
                        <h3 class="card-title" style="font-size: 20px;">Update Tahapan Pengadaan</h3>
                        <small style="color: var(--muted);">{{ $paket->kode_paket }} - {{ $paket->nama_paket }}</small>
                    </div>
                    <button type="button" class="btn-icon" onclick="closeUpdateModal()" title="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('tracking.update', $paket->id) }}">
                    @csrf
                    <div class="stack" style="gap: 16px;">
                        <div class="field">
                            <label for="tahapan">Tahapan <span style="color: var(--red);">*</span></label>
                            <select class="select" id="tahapan" name="tahapan" required>
                                @foreach ($tahapanList as $i => $item)
                                    @if ($i === $aktifIndex + 1)
                                        <option value="{{ $item['nama'] }}" selected>
                                            {{ $item['nama'] }}
                                        </option>
                                    @elseif ($i > $aktifIndex + 1)
                                        <option value="{{ $item['nama'] }}" disabled style="color: var(--muted);">
                                            {{ $item['nama'] }} (terkunci)
                                        </option>
                                    @elseif ($i <= $aktifIndex)
                                        <option value="{{ $item['nama'] }}" disabled style="color: var(--muted);">
                                            {{ $item['nama'] }} (selesai)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <small style="color: var(--muted); font-size: 12px;">Sesuai tata kelola PBJ, tahapan wajib dilalui secara berurutan dan tidak boleh dilompati.</small>
                        </div>

                        @if (Auth::check())
                            <input type="hidden" name="peran" value="{{ Auth::user()->role }}">
                            <input type="hidden" name="petugas" value="{{ Auth::user()->name }}">
                            <div class="field">
                                <label>Petugas & Peran Aktor (Terotentikasi)</label>
                                <div style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: #fafafa; border: 1px solid var(--line); border-radius: 14px;">
                                    <i class="bi bi-person-badge-fill" style="font-size: 20px; color: var(--dark);"></i>
                                    <div>
                                        <b>{{ Auth::user()->name }}</b>
                                        <span class="badge blue" style="margin-left: 8px;">{{ Auth::user()->role }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="field">
                                <label for="peran">Peran <span style="color: var(--red);">*</span></label>
                                <select class="select" id="peran" name="peran" required>
                                    <option value="Pokja Pemilihan" {{ old('peran', 'Pokja Pemilihan') === 'Pokja Pemilihan' ? 'selected' : '' }}>Pokja Pemilihan (Berwenang)</option>
                                    <option value="Pejabat Pengadaan" {{ old('peran') === 'Pejabat Pengadaan' ? 'selected' : '' }}>Pejabat Pengadaan (Berwenang)</option>
                                    <option value="PPK" {{ old('peran') === 'PPK' ? 'selected' : '' }}>PPK (Peran Pemantau)</option>
                                    <option value="PA/KPA" {{ old('peran') === 'PA/KPA' ? 'selected' : '' }}>PA/KPA (Peran Pemantau)</option>
                                    <option value="Admin Sistem" {{ old('peran') === 'Admin Sistem' ? 'selected' : '' }}>Admin Sistem (Peran Pemantau)</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="petugas">Nama <span style="color: var(--red);">*</span></label>
                                <input class="input" type="text" id="petugas" name="petugas" placeholder="Contoh: Pokja Pemilihan 1" value="{{ old('petugas') }}" required>
                            </div>
                        @endif

                        <div class="field">
                            <label for="keterangan">Keterangan <span style="color: var(--red);">*</span></label>
                            <textarea class="input" id="keterangan" name="keterangan" rows="3" style="height: auto; padding: 12px 16px; resize: vertical;" placeholder="Tuliskan keterangan detail proses atau hasil evaluasi tahapan..." required>{{ old('keterangan') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 24px; justify-content: flex-end;">
                        <button type="button" class="btn" onclick="closeUpdateModal()">Batal</button>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check-lg"></i> Simpan Tahapan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @else
        <div style="text-align: center; padding: 48px 20px; color: var(--muted);">
            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
            <p style="font-size: 16px; font-weight: 500;">Belum ada data paket pengadaan di database.</p>
            <p style="font-size: 13px; margin-top: 4px;">Silakan jalankan seeder atau tambah paket pengadaan baru.</p>
        </div>
    @endif
</div>

<script>
    function openUpdateModal() {
        const modal = document.getElementById('modalUpdateTahapan');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    function closeUpdateModal() {
        const modal = document.getElementById('modalUpdateTahapan');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openUpdateModal();
        });
    @endif

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUpdateModal();
        }
    });

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('modalUpdateTahapan');
        if (e.target === modal) {
            closeUpdateModal();
        }
    });
</script>
@endsection