<?php

namespace Tests\Feature;

use App\Models\PaketPengadaan;
use App\Models\TrackingProgress;
use Tests\TestCase;

class TrackingProgressTest extends TestCase
{
    /**
     * Set up awal sebelum setiap pengujian:
     * Pastikan data paket awal di-seed sesuai spesifikasi SDD.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'TrackingSeeder']);
    }

    /**
     * Test relasi Eloquent antara PaketPengadaan dan TrackingProgress.
     */
    public function test_eloquent_relationships(): void
    {
        $paket = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        $this->assertNotNull($paket);
        $this->assertTrue($paket->trackingProgress()->exists());

        $firstLog = $paket->trackingProgress->first();
        $this->assertInstanceOf(TrackingProgress::class, $firstLog);
        $this->assertEquals($paket->id, $firstLog->paket->id);
    }

    /**
     * TC-UC07-01: Menampilkan progres & riwayat tahapan paket (Main Course Step 1-3)
     * Prosedur: Buka /tracking, lalu ganti pilihan paket pada dropdown dari PKT-001 ke PKT-002.
     * Hasil yang Diharapkan: Indikator stepper dan tabel histori langsung berubah sesuai data paket di MySQL.
     */
    public function test_tc_uc07_01_menampilkan_progres_dan_riwayat_tahapan(): void
    {
        // 1. Buka halaman /tracking (default PKT-001)
        $response1 = $this->get('/tracking');
        $response1->assertStatus(200);
        $response1->assertSee('PKT-001');
        $response1->assertSee('Pembangunan Gedung Sekolah');
        $response1->assertSee('Evaluasi dokumen penawaran');

        // 2. Ganti pilihan paket ke PKT-002 via query paket_id
        $paket2 = PaketPengadaan::where('kode_paket', 'PKT-002')->first();
        $this->assertNotNull($paket2);

        $response2 = $this->get('/tracking?paket_id=' . $paket2->id);
        $response2->assertStatus(200);
        $response2->assertSee('PKT-002');
        $response2->assertSee('Rehabilitasi Jalan Raya');
        $response2->assertSee('Dinas PUPR');
        $response2->assertSee('Pengumuman lelang dan pembukaan pendaftaran rekanan');
    }

    /**
     * TC-UC07-02: Memperbarui tahapan paket oleh petugas berwenang (UAT-03 & Main Course Step 4-9)
     * Prosedur: Klik [Update Tahapan] pada PKT-001, pilih Tahapan: Penetapan,
     * Peran: Pokja Pemilihan, Nama: Pokja Pemilihan 1, Keterangan: Penetapan pemenang selesai, klik Simpan Tahapan.
     * Hasil yang Diharapkan: Muncul notifikasi hijau berhasil, kotak stepper Evaluasi berubah menjadi centang hijau (✔),
     * kotak Penetapan aktif berwarna hitam, dan baris baru muncul di tabel histori & database.
     */
    public function test_tc_uc07_02_memperbarui_tahapan_oleh_petugas_berwenang(): void
    {
        $paket = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        $this->assertNotNull($paket);
        $this->assertEquals('Evaluasi', $paket->tahapan_sekarang);

        $postData = [
            'tahapan' => 'Penetapan',
            'peran' => 'Pokja Pemilihan',
            'petugas' => 'Pokja Pemilihan 1',
            'keterangan' => 'Penetapan pemenang selesai',
        ];

        $response = $this->post('/tracking/' . $paket->id . '/update', $postData);

        // Redirect kembali ke /tracking dengan paket_id dan flash message
        $response->assertRedirect('/tracking?paket_id=' . $paket->id);
        $response->assertSessionHas('success');

        // Verifikasi database MySQL (tb_paket_pengadaan)
        $this->assertDatabaseHas('tb_paket_pengadaan', [
            'id' => $paket->id,
            'tahapan_sekarang' => 'Penetapan',
        ]);

        // Verifikasi baris log baru di tb_tracking_progress
        $this->assertDatabaseHas('tb_tracking_progress', [
            'paket_id' => $paket->id,
            'tahapan' => 'Penetapan',
            'peran' => 'Pokja Pemilihan',
            'petugas' => 'Pokja Pemilihan 1',
            'keterangan' => 'Penetapan pemenang selesai',
        ]);

        // Verifikasi tampilan halaman setelah diperbarui: stepper dan tabel histori
        $pageResponse = $this->get('/tracking?paket_id=' . $paket->id);
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Penetapan pemenang selesai');
        $pageResponse->assertSee('Pokja Pemilihan 1');
    }

    /**
     * TC-UC07-03: Pembatasan hak akses peran pemantau (Extension 4a UC-07 & UAT-01)
     * Prosedur: Klik [Update Tahapan], pilih Peran Aktor: PPK, isi nama dan keterangan, lalu klik Simpan Tahapan.
     * Hasil yang Diharapkan: Sistem menolak penyimpanan dan menampilkan pesan peringatan
     * Akses Ditolak (403 Forbidden) karena PPK hanya berwenang memantau.
     */
    public function test_tc_uc07_03_pembatasan_hak_akses_peran_pemantau(): void
    {
        $paket = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        $this->assertNotNull($paket);

        $postData = [
            'tahapan' => 'Penetapan',
            'peran' => 'PPK',
            'petugas' => 'PPK Dinas Pendidikan',
            'keterangan' => 'Mencoba penetapan pemenang lelang',
        ];

        $response = $this->post('/tracking/' . $paket->id . '/update', $postData);

        // Status code 403 Forbidden
        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak (403 Forbidden)');
        $response->assertSee('hanya berwenang memantau');

        // Pastikan tidak ada data yang berubah di database
        $this->assertDatabaseMissing('tb_tracking_progress', [
            'paket_id' => $paket->id,
            'peran' => 'PPK',
            'keterangan' => 'Mencoba penetapan pemenang lelang',
        ]);
        $this->assertDatabaseHas('tb_paket_pengadaan', [
            'id' => $paket->id,
            'tahapan_sekarang' => 'Evaluasi',
        ]);
    }

    /**
     * TC-UC07-04: Validasi urutan tahapan mundur (Extension 6a UC-07)
     * Prosedur: Pada paket yang sudah di tahap Penetapan, coba ubah tahapan mundur ke Perencanaan
     * dengan peran Pokja Pemilihan.
     * Hasil yang Diharapkan: Sistem menolak perubahan dan menampilkan pesan validasi
     * bahwa tahapan tidak boleh dimundurkan.
     */
    public function test_tc_uc07_04_validasi_urutan_tahapan_mundur(): void
    {
        $paket = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        $this->assertNotNull($paket);

        // 1. Ubah paket ke tahapan 'Penetapan' terlebih dahulu
        $paket->update(['tahapan_sekarang' => 'Penetapan']);
        $paket->trackingProgress()->create([
            'tahapan' => 'Penetapan',
            'peran' => 'Pokja Pemilihan',
            'petugas' => 'Pokja Pemilihan 1',
            'keterangan' => 'Penetapan pemenang selesai',
        ]);

        // 2. Coba ubah mundur ke 'Perencanaan' dengan peran Pokja Pemilihan
        $postData = [
            'tahapan' => 'Perencanaan',
            'peran' => 'Pokja Pemilihan',
            'petugas' => 'Pokja Pemilihan 1',
            'keterangan' => 'Mencoba memundurkan tahapan ke Perencanaan',
        ];

        $response = $this->post('/tracking/' . $paket->id . '/update', $postData);

        // Sistem menolak dan mengembalikan error validasi pada field 'tahapan'
        $response->assertSessionHasErrors(['tahapan']);

        // Pastikan tahapan di tb_paket_pengadaan tetap 'Penetapan' (tidak mundur)
        $this->assertDatabaseHas('tb_paket_pengadaan', [
            'id' => $paket->id,
            'tahapan_sekarang' => 'Penetapan',
        ]);

        // Pastikan log mundur tidak dicatat
        $this->assertDatabaseMissing('tb_tracking_progress', [
            'paket_id' => $paket->id,
            'tahapan' => 'Perencanaan',
            'keterangan' => 'Mencoba memundurkan tahapan ke Perencanaan',
        ]);
    }

    /**
     * TC-UC07-05: Sesi login peran pemantau (PPK) otomatis menyembunyikan tombol aksi dan menolak update tahapan.
     */
    public function test_tc_uc07_05_sesi_login_ppk_otomatis_tidak_bisa_update_tahapan(): void
    {
        $ppk = \App\Models\User::firstOrCreate(
            ['email' => 'budi.santoso@kebumenkab.go.id'],
            [
                'name' => 'Budi Santoso',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'PPK',
                'status' => 'Aktif',
            ]
        );

        $paket = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        $this->assertNotNull($paket);

        // 1. Pada tampilan, tombol Update Tahapan tidak ada untuk peran PPK
        $viewResponse = $this->actingAs($ppk)->get('/tracking?paket_id=' . $paket->id);
        $viewResponse->assertStatus(200);
        $viewResponse->assertDontSee('Update Tahapan');

        // 2. Jika PPK memaksa request POST /tracking/{id}/update, sistem tolak dengan 403 Forbidden
        $postData = [
            'tahapan' => 'Penetapan',
            'keterangan' => 'Mencoba paksa update tahapan oleh PPK',
        ];
        $postResponse = $this->actingAs($ppk)->post('/tracking/' . $paket->id . '/update', $postData);
        $postResponse->assertStatus(403);
    }

    /**
     * TC-UC07-06: Validasi larangan melompati tahapan pengadaan (Extension 6a UC-07).
     * Prosedur: Paket berada pada tahap Evaluasi (index 2). Pokja mencoba melompat langsung ke Selesai (index 4)
     * tanpa melalui Penetapan (index 3).
     * Hasil yang Diharapkan: Sistem menolak pembaruan dan menampilkan pesan galat bahwa tahapan
     * harus dilalui secara berurutan dan tidak boleh melompati Penetapan.
     */
    public function test_tc_uc07_06_validasi_larangan_melompati_tahapan(): void
    {
        $paket = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        $this->assertNotNull($paket);
        $this->assertEquals('Evaluasi', $paket->tahapan_sekarang);

        // Pokja mencoba lompat langsung ke 'Selesai' melewati 'Penetapan'
        $postData = [
            'tahapan' => 'Selesai',
            'peran' => 'Pokja Pemilihan',
            'petugas' => 'Siti Rahma',
            'keterangan' => 'Mencoba lompat langsung ke Selesai tanpa Penetapan',
        ];

        $response = $this->post('/tracking/' . $paket->id . '/update', $postData);

        // Sistem menolak dan mengembalikan error validasi pada field 'tahapan'
        $response->assertSessionHasErrors(['tahapan']);

        // Pastikan status di database tetap 'Evaluasi'
        $paket->refresh();
        $this->assertEquals('Evaluasi', $paket->tahapan_sekarang);

        // Pastikan tidak ada log 'Selesai' yang tersimpan
        $this->assertDatabaseMissing('tb_tracking_progress', [
            'paket_id' => $paket->id,
            'tahapan' => 'Selesai',
        ]);
    }
}
