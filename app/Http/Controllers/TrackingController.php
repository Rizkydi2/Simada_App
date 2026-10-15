<?php

namespace App\Http\Controllers;

use App\Models\PaketPengadaan;
use App\Models\TrackingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TrackingController extends Controller
{
    /**
     * Menampilkan halaman tracking progres pengadaan (UC-07).
     * Mendukung pemilihan paket via dropdown, pencarian kode/nama paket,
     * serta visualisasi 5 tahapan stepper dan riwayat perubahan tahapan.
     */
    public function index(Request $request)
    {
        $semuaPaket = PaketPengadaan::orderBy('kode_paket', 'asc')->get();

        $paket = null;

        // 1. Jika ada parameter 'paket_id' dari dropdown atau link
        if ($request->filled('paket_id')) {
            $paket = PaketPengadaan::find($request->query('paket_id'));
        }

        // 2. Jika ada query pencarian 'q'
        if (!$paket && $request->filled('q')) {
            $keyword = trim($request->query('q'));
            $paket = PaketPengadaan::where('kode_paket', 'like', "%{$keyword}%")
                ->orWhere('nama_paket', 'like', "%{$keyword}%")
                ->first();
        }

        // 3. Fallback ke paket pertama jika ada
        if (!$paket) {
            $paket = $semuaPaket->first();
        }

        // 5 Tahapan Standar sesuai SDD (WF-05 / UC-07)
        $tahapanList = [
            ['nama' => 'Perencanaan', 'icon' => 'bi-pencil-square'],
            ['nama' => 'Pengumuman',  'icon' => 'bi-megaphone'],
            ['nama' => 'Evaluasi',    'icon' => 'bi-clipboard-data'],
            ['nama' => 'Penetapan',   'icon' => 'bi-award'],
            ['nama' => 'Selesai',     'icon' => 'bi-check-circle'],
        ];

        $aktifIndex = 0;
        $riwayat = collect();

        if ($paket) {
            // Ambil riwayat urut berdasarkan waktu pembuatan (kronologis)
            $riwayat = $paket->trackingProgress()->orderBy('created_at', 'asc')->get();

            // Hitung indeks tahapan aktif (0-4)
            foreach ($tahapanList as $index => $item) {
                if (strcasecmp($item['nama'], $paket->tahapan_sekarang) === 0) {
                    $aktifIndex = $index;
                    break;
                }
            }
        }

        return view('tracking', compact('semuaPaket', 'paket', 'riwayat', 'tahapanList', 'aktifIndex'));
    }

    /**
     * Memperbarui tahapan sekarang pada tb_paket_pengadaan
     * dan menambahkan catatan baru pada tb_tracking_progress di dalam DB::transaction.
     * Dilengkapi validasi otorisasi peran aktor (Extension 4a & TC-UC07-03)
     * dan validasi larangan urutan tahapan mundur (Extension 6a & TC-UC07-04).
     */
    public function updateTahapan(Request $request, $id)
    {
        $paket = PaketPengadaan::findOrFail($id);

        // 1. Validasi Hak Akses Peran Aktor (UC-07 Extension 4a & TC-UC07-03)
        // Peran berwenang: Pokja Pemilihan (tender) dan Pejabat Pengadaan (non-tender).
        // Peran pemantau (PPK, PA/KPA, Admin Sistem) ditolak dengan HTTP 403 Forbidden.
        $peran = trim($request->input('peran', 'Pokja Pemilihan'));
        $authorizedRoles = ['Pokja Pemilihan', 'Pejabat Pengadaan'];

        if (!in_array($peran, $authorizedRoles)) {
            abort(403, 'Akses Ditolak (403 Forbidden) karena ' . $peran . ' hanya berwenang memantau.');
        }

        // 2. Validasi Input Form
        $validated = $request->validate([
            'tahapan' => 'required|string|in:' . implode(',', PaketPengadaan::TAHAPAN_LIST),
            'petugas' => 'nullable|string|max:255',
            'nama' => 'nullable|string|max:255',
            'keterangan' => 'required|string|max:1000',
        ], [
            'tahapan.required' => 'Tahapan pengadaan wajib dipilih.',
            'tahapan.in' => 'Tahapan pengadaan yang dipilih tidak valid.',
            'keterangan.required' => 'Keterangan atau catatan tahapan wajib diisi.',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        $petugas = trim($request->input('petugas', $request->input('nama', '')));
        if (empty($petugas)) {
            throw ValidationException::withMessages([
                'petugas' => 'Nama petugas / Pokja wajib diisi.',
            ]);
        }

        // 3. Validasi Urutan Tahapan Mundur (UC-07 Extension 6a & TC-UC07-04)
        $tahapanList = PaketPengadaan::TAHAPAN_LIST;
        $currentIndex = array_search($paket->tahapan_sekarang, $tahapanList);
        $newIndex = array_search($validated['tahapan'], $tahapanList);

        if ($currentIndex !== false && $newIndex !== false && $newIndex < $currentIndex) {
            throw ValidationException::withMessages([
                'tahapan' => 'Tahapan tidak boleh dimundurkan. Tahapan saat ini adalah ' . $paket->tahapan_sekarang . '.',
            ]);
        }

        // 4. Eksekusi Perubahan di Database Secara Atomik (DB::transaction)
        DB::transaction(function () use ($paket, $validated, $peran, $petugas) {
            // Perbarui status tahapan_sekarang di tb_paket_pengadaan
            $paket->update([
                'tahapan_sekarang' => $validated['tahapan'],
            ]);

            // Tambahkan baris riwayat tahapan baru di tb_tracking_progress
            $paket->trackingProgress()->create([
                'tahapan' => $validated['tahapan'],
                'peran' => $peran,
                'petugas' => $petugas,
                'keterangan' => $validated['keterangan'],
            ]);
        });

        return redirect()->route('tracking', ['paket_id' => $id])
            ->with('success', 'Tahapan paket pengadaan berhasil diperbarui menjadi: ' . $validated['tahapan']);
    }
}
