<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed 5 data pengguna awal yang mewakili masing-masing peran RBAC sesuai SDD dan SRS SIMADA:
     * - Admin Sistem: Admin Sistem UKPBJ
     * - PPK: Budi Santoso (Dinas Pendidikan)
     * - Pokja Pemilihan: Siti Rahma (Bagian PBJ Setda Kebumen)
     * - Pejabat Pengadaan: Hendra Wijaya (Dinas PUPR)
     * - PA/KPA: Drs. Ahmad Fauzi (Bappeda)
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Sistem UKPBJ',
                'nip' => '198501012010011001',
                'email' => 'admin@simada.kebumenkab.go.id',
                'password' => Hash::make('password123'),
                'role' => 'Admin Sistem',
                'opd' => 'Bagian PBJ Setda Kebumen',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Budi Santoso',
                'nip' => '197903152005011002',
                'email' => 'budi.santoso@kebumenkab.go.id',
                'password' => Hash::make('password123'),
                'role' => 'PPK',
                'opd' => 'Dinas Pendidikan',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Siti Rahma',
                'nip' => '198811202012022003',
                'email' => 'siti.rahma@simada.kebumenkab.go.id',
                'password' => Hash::make('password123'),
                'role' => 'Pokja Pemilihan',
                'opd' => 'Bagian PBJ Setda Kebumen',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Hendra Wijaya',
                'nip' => '198207042008011004',
                'email' => 'hendra.wijaya@kebumenkab.go.id',
                'password' => Hash::make('password123'),
                'role' => 'Pejabat Pengadaan',
                'opd' => 'Dinas PUPR',
                'status' => 'Aktif',
            ],
            [
                'name' => 'Drs. Ahmad Fauzi',
                'nip' => '196805121994031005',
                'email' => 'ahmad.fauzi@kebumenkab.go.id',
                'password' => Hash::make('password123'),
                'role' => 'PA/KPA',
                'opd' => 'Bappeda',
                'status' => 'Aktif',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
