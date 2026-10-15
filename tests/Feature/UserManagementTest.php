<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    /**
     * Set up awal sebelum setiap pengujian:
     * Bersihkan data uji tambahan dan re-seed 5 data pengguna awal.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan data user uji selain 5 akun dasar agar pengujian terisolasi
        User::whereNotIn('email', [
            'admin@simada.kebumenkab.go.id',
            'budi.santoso@kebumenkab.go.id',
            'siti.rahma@simada.kebumenkab.go.id',
            'hendra.wijaya@kebumenkab.go.id',
            'ahmad.fauzi@kebumenkab.go.id',
        ])->delete();

        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    /**
     * TC-UC01-01: Menampilkan halaman daftar pengguna dan 5 peran RBAC (WF-08 / Main Course Step 2).
     */
    public function test_halaman_users_dapat_diakses_dan_menampilkan_daftar_pengguna(): void
    {
        $response = $this->get('/users');

        $response->assertStatus(200);
        $response->assertSee('Inventaris Akun & Hak Akses Pengguna', false);
        $response->assertSee('Admin Sistem UKPBJ');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Rahma');
        $response->assertSee('Hendra Wijaya');
        $response->assertSee('Drs. Ahmad Fauzi');

        // Pastikan badge 5 peran RBAC muncul
        $response->assertSee('Admin Sistem');
        $response->assertSee('PPK');
        $response->assertSee('Pokja Pemilihan');
        $response->assertSee('Pejabat Pengadaan');
        $response->assertSee('PA/KPA');
    }

    /**
     * TC-UC01-02: Fitur filter pengguna berdasarkan peran (Role RBAC).
     */
    public function test_filter_pengguna_berdasarkan_role(): void
    {
        $response = $this->get('/users?role=PPK');

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Hendra Wijaya');
        $response->assertDontSee('Drs. Ahmad Fauzi');
    }

    /**
     * TC-UC01-03: Fitur pencarian pengguna berdasarkan kata kunci (NIP/Nama/Email/OPD).
     */
    public function test_pencarian_pengguna_berdasarkan_keyword(): void
    {
        // Cari berdasarkan NIP Siti Rahma (198811202012022003)
        $response = $this->get('/users?search=198811202012022003');

        $response->assertStatus(200);
        $response->assertSee('Siti Rahma');
        $response->assertDontSee('Budi Santoso');
    }

    /**
     * TC-UC01-04: Menambahkan pengguna baru ke sistem (Main Course Step 3-7).
     * Memverifikasi penyimpanan data, hash password, dan penugasan peran.
     */
    public function test_tambah_user_baru_berhasil_dan_password_dihash(): void
    {
        $payload = [
            'name' => 'Rina Marlina, S.T.',
            'nip' => '199005052015022001',
            'email' => 'rina.marlina@kebumenkab.go.id',
            'password' => 'rahasia123',
            'role' => 'Pokja Pemilihan',
            'opd' => 'Bagian PBJ Setda Kebumen',
            'status' => 'Aktif',
        ];

        $response = $this->post('/users', $payload);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Rina Marlina, S.T.',
            'nip' => '199005052015022001',
            'email' => 'rina.marlina@kebumenkab.go.id',
            'role' => 'Pokja Pemilihan',
            'opd' => 'Bagian PBJ Setda Kebumen',
            'status' => 'Aktif',
        ]);

        $createdUser = User::where('email', 'rina.marlina@kebumenkab.go.id')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('rahasia123', $createdUser->password));
    }

    /**
     * TC-UC01-05: Validasi keunikan NIP dan Email saat pendaftaran user baru (Extension 5a/5b).
     */
    public function test_validasi_tambah_user_email_dan_nip_harus_unik(): void
    {
        $payload = [
            'name' => 'Duplikat User',
            'nip' => '197903152005011002', // NIP Budi Santoso
            'email' => 'budi.santoso@kebumenkab.go.id', // Email Budi Santoso
            'password' => 'password123',
            'role' => 'PPK',
            'opd' => 'Dinas Pendidikan',
            'status' => 'Aktif',
        ];

        $response = $this->post('/users', $payload);

        $response->assertSessionHasErrors(['email', 'nip']);
    }

    /**
     * TC-UC01-06: Memperbarui data pengguna dan hak akses peran RBAC (Extension 3a).
     */
    public function test_update_user_dan_hak_akses_berhasil(): void
    {
        $user = User::where('email', 'hendra.wijaya@kebumenkab.go.id')->first();
        $this->assertNotNull($user);

        $payload = [
            'name' => 'Hendra Wijaya, S.T., M.Eng.',
            'nip' => $user->nip,
            'email' => $user->email,
            'role' => 'PPK', // Mutasi peran menjadi PPK
            'opd' => 'Dinas Perhubungan',
            'status' => 'Aktif',
        ];

        $response = $this->put('/users/' . $user->id, $payload);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Hendra Wijaya, S.T., M.Eng.', $user->name);
        $this->assertEquals('PPK', $user->role);
        $this->assertEquals('Dinas Perhubungan', $user->opd);
    }

    /**
     * TC-UC01-07: Memperbarui kata sandi pengguna secara opsional saat update.
     */
    public function test_update_user_dengan_password_baru(): void
    {
        $user = User::where('email', 'budi.santoso@kebumenkab.go.id')->first();
        $this->assertNotNull($user);

        $payload = [
            'name' => $user->name,
            'nip' => $user->nip,
            'email' => $user->email,
            'password' => 'passwordBaru456',
            'role' => $user->role,
            'opd' => $user->opd,
            'status' => $user->status,
        ];

        $response = $this->put('/users/' . $user->id, $payload);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('passwordBaru456', $user->password));
    }

    /**
     * TC-UC01-08: Menghapus pengguna dari sistem.
     */
    public function test_hapus_user_berhasil(): void
    {
        $tempUser = User::create([
            'name' => 'Staf Sementara',
            'nip' => '199501012020011099',
            'email' => 'staf.sementara@kebumenkab.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Pokja Pemilihan',
            'opd' => 'Bagian PBJ Setda Kebumen',
            'status' => 'Aktif',
        ]);

        $response = $this->delete('/users/' . $tempUser->id);

        $response->assertRedirect('/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $tempUser->id,
        ]);
    }

    /**
     * TC-UC01-09: Proteksi keamanan agar tidak dapat menghapus akun Admin Sistem terakhir.
     */
    public function test_proteksi_tidak_dapat_menghapus_admin_sistem_terakhir(): void
    {
        // Pastikan hanya ada 1 Admin Sistem di database
        User::where('role', 'Admin Sistem')
            ->where('email', '!=', 'admin@simada.kebumenkab.go.id')
            ->delete();

        $admin = User::where('role', 'Admin Sistem')->first();
        $this->assertNotNull($admin);

        $response = $this->delete('/users/' . $admin->id);

        $response->assertRedirect('/users');
        $response->assertSessionHas('error');

        // Pastikan Admin Sistem tetap ada di database
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'Admin Sistem',
        ]);
    }

    /**
     * TC-UC01-10: Proteksi keamanan agar tidak dapat mengubah peran atau menonaktifkan Admin Sistem terakhir.
     */
    public function test_proteksi_tidak_dapat_mengubah_role_atau_menonaktifkan_admin_sistem_terakhir(): void
    {
        // Pastikan hanya ada 1 Admin Sistem aktif
        User::where('role', 'Admin Sistem')
            ->where('email', '!=', 'admin@simada.kebumenkab.go.id')
            ->delete();

        $admin = User::where('role', 'Admin Sistem')->first();
        $this->assertNotNull($admin);

        // Coba ganti peran Admin Sistem terakhir menjadi PPK
        $payload = [
            'name' => $admin->name,
            'nip' => $admin->nip,
            'email' => $admin->email,
            'role' => 'PPK',
            'opd' => $admin->opd,
            'status' => 'Aktif',
        ];

        $response = $this->put('/users/' . $admin->id, $payload);

        $response->assertRedirect('/users');
        $response->assertSessionHas('error');

        $admin->refresh();
        $this->assertEquals('Admin Sistem', $admin->role);
    }

    /**
     * TC-UC01-11: Pembatasan hak akses modul User & Hak Akses bagi peran non-Admin (UAT-01).
     * Prosedur: Login menggunakan akun PPK, kemudian coba mengakses URL /users secara paksa.
     * Hasil yang Diharapkan: Sistem menolak akses dengan HTTP 403 Forbidden.
     */
    public function test_non_admin_dilarang_mengakses_manajemen_pengguna_uat_01(): void
    {
        $ppk = User::where('email', 'budi.santoso@kebumenkab.go.id')->first();
        $this->assertNotNull($ppk);

        $response = $this->actingAs($ppk)->get('/users');

        $response->assertStatus(403);
        $response->assertSee('Hanya Admin Sistem yang berwenang');
    }
}
