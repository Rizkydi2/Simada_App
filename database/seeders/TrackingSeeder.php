<?php

namespace Database\Seeders;

use App\Models\PaketPengadaan;
use App\Models\TrackingProgress;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Paket PKT-001: Pembangunan Gedung Sekolah - Dinas Pendidikan (Tahapan: Evaluasi)
        $paket1 = PaketPengadaan::updateOrCreate(
            ['kode_paket' => 'PKT-001'],
            [
                'nama_paket' => 'Pembangunan Gedung Sekolah',
                'opd' => 'Dinas Pendidikan',
                'metode' => 'e-Tendering',
                'pagu' => 1500000000,
                'hps' => 1450000000,
                'tahapan_sekarang' => 'Evaluasi',
            ]
        );

        // Hapus riwayat lama jika seeder dijalankan ulang
        $paket1->trackingProgress()->delete();

        // Riwayat Log untuk PKT-001
        $paket1->trackingProgress()->createMany([
            [
                'tahapan' => 'Perencanaan',
                'peran' => 'PPK',
                'petugas' => 'Budi Santoso',
                'keterangan' => 'Dokumen perencanaan dan spesifikasi teknis disetujui',
                'created_at' => Carbon::now()->subDays(8)->setTime(9, 0, 0),
                'updated_at' => Carbon::now()->subDays(8)->setTime(9, 0, 0),
            ],
            [
                'tahapan' => 'Pengumuman',
                'peran' => 'Pokja Pemilihan',
                'petugas' => 'Siti Rahma',
                'keterangan' => 'Pengumuman lelang resmi ditayangkan di portal SPSE',
                'created_at' => Carbon::now()->subDays(5)->setTime(10, 30, 0),
                'updated_at' => Carbon::now()->subDays(5)->setTime(10, 30, 0),
            ],
            [
                'tahapan' => 'Evaluasi',
                'peran' => 'Pokja Pemilihan',
                'petugas' => 'Pokja 1',
                'keterangan' => 'Evaluasi dokumen penawaran',
                'created_at' => Carbon::now()->subDays(2)->setTime(14, 0, 0),
                'updated_at' => Carbon::now()->subDays(2)->setTime(14, 0, 0),
            ],
        ]);

        // 2. Paket PKT-002: Rehabilitasi Jalan Raya - Dinas PUPR (Tahapan: Pengumuman)
        $paket2 = PaketPengadaan::updateOrCreate(
            ['kode_paket' => 'PKT-002'],
            [
                'nama_paket' => 'Rehabilitasi Jalan Raya',
                'opd' => 'Dinas PUPR',
                'metode' => 'e-Tendering',
                'pagu' => 2500000000,
                'hps' => 2400000000,
                'tahapan_sekarang' => 'Pengumuman',
            ]
        );

        // Hapus riwayat lama jika seeder dijalankan ulang
        $paket2->trackingProgress()->delete();

        // Riwayat Log untuk PKT-002
        $paket2->trackingProgress()->createMany([
            [
                'tahapan' => 'Perencanaan',
                'peran' => 'PPK',
                'petugas' => 'Ir. Ahmad Zaini',
                'keterangan' => 'Penyusunan RUP dan penetapan HPS pekerjaan selesai',
                'created_at' => Carbon::now()->subDays(4)->setTime(8, 30, 0),
                'updated_at' => Carbon::now()->subDays(4)->setTime(8, 30, 0),
            ],
            [
                'tahapan' => 'Pengumuman',
                'peran' => 'Pokja Pemilihan',
                'petugas' => 'Rina Marlina',
                'keterangan' => 'Pengumuman lelang dan pembukaan pendaftaran rekanan',
                'created_at' => Carbon::now()->subDays(1)->setTime(11, 0, 0),
                'updated_at' => Carbon::now()->subDays(1)->setTime(11, 0, 0),
            ],
        ]);
    }
}
