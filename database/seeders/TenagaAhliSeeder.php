<?php

namespace Database\Seeders;

use App\Models\PaketPengadaan;
use App\Models\PenugasanPersonel;
use App\Models\TenagaAhli;
use Illuminate\Database\Seeder;

class TenagaAhliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sesuai SDD Seksi 5.2 (Entitas Tenaga Ahli & Penugasan Personel).
     */
    public function run(): void
    {
        $ahli1 = TenagaAhli::updateOrCreate(
            ['nik' => '3305011504800001'],
            [
                'nama' => 'Ir. Bambang Triyono, M.T.',
                'keahlian' => 'Ahli Struktur Bangunan Gedung',
                'penyedia' => 'PT Karya Kebumen Mandiri',
                'telepon' => '081234567890',
                'email' => 'bambang.triyono@karyakebumen.co.id',
                'status' => 'Ditugaskan',
            ]
        );

        $ahli2 = TenagaAhli::updateOrCreate(
            ['nik' => '3305022008850002'],
            [
                'nama' => 'Dian Prasetyo, S.T.',
                'keahlian' => 'Ahli K3 Konstruksi & Jalan',
                'penyedia' => 'CV Lawet Perkasa',
                'telepon' => '081398765432',
                'email' => 'dian.prasetyo@lawetperkasa.co.id',
                'status' => 'Ditugaskan',
            ]
        );

        $ahli3 = TenagaAhli::updateOrCreate(
            ['nik' => '3305031102900003'],
            [
                'nama' => 'Siti Nurhaliza, S.T., M.Sc.',
                'keahlian' => 'Ahli Manajemen Proyek TI',
                'penyedia' => 'PT Kebumen Digital Solusindo',
                'telepon' => '081567890123',
                'email' => 'siti.nurhaliza@kebumendigital.co.id',
                'status' => 'Tersedia',
            ]
        );

        // Penugasan awal ke paket jika paket sudah ada
        $pkt1 = PaketPengadaan::where('kode_paket', 'PKT-001')->first();
        if ($pkt1) {
            PenugasanPersonel::updateOrCreate(
                [
                    'paket_id' => $pkt1->id,
                    'tenaga_ahli_id' => $ahli1->id,
                ],
                [
                    'posisi' => 'Tenaga Ahli Struktur',
                    'tanggal_mulai' => '2026-10-01',
                    'tanggal_selesai' => '2026-12-31',
                    'status' => 'Aktif',
                ]
            );
        }

        $pkt2 = PaketPengadaan::where('kode_paket', 'PKT-002')->first();
        if ($pkt2) {
            PenugasanPersonel::updateOrCreate(
                [
                    'paket_id' => $pkt2->id,
                    'tenaga_ahli_id' => $ahli2->id,
                ],
                [
                    'posisi' => 'Ahli K3 Konstruksi',
                    'tanggal_mulai' => '2026-10-15',
                    'tanggal_selesai' => '2026-11-30',
                    'status' => 'Aktif',
                ]
            );
        }
    }
}
