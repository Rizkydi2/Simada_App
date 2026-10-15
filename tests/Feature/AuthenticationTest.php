<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    /**
     * TC-WF01-01: Halaman login dapat diakses dengan status 200.
     */
    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIMADA');
        $response->assertSee('NIP atau Email Kedinasan');
    }

    /**
     * TC-WF01-02: Pengguna berhasil login menggunakan Email dan Password valid.
     */
    public function test_login_berhasil_dengan_email(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin@simada.kebumenkab.go.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals('Admin Sistem UKPBJ', Auth::user()->name);
    }

    /**
     * TC-WF01-03: Pengguna berhasil login menggunakan NIP dan Password valid.
     */
    public function test_login_berhasil_dengan_nip(): void
    {
        $response = $this->post('/login', [
            'username' => '197903152005011002', // NIP Budi Santoso
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals('Budi Santoso', Auth::user()->name);
    }

    /**
     * TC-WF01-04: Sistem menolak login jika kata sandi salah (Extension 1a).
     */
    public function test_login_gagal_jika_password_salah(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin@simada.kebumenkab.go.id',
            'password' => 'passwordSalah',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertFalse(Auth::check());
    }

    /**
     * TC-WF01-05: Sistem menolak login jika akun berstatus Nonaktif (Extension 3b).
     */
    public function test_login_gagal_jika_akun_nonaktif(): void
    {
        $nonaktifUser = User::create([
            'name' => 'Akun Dinonaktifkan',
            'nip' => '199999999999999999',
            'email' => 'nonaktif@kebumenkab.go.id',
            'password' => Hash::make('password123'),
            'role' => 'PPK',
            'opd' => 'Dinas Pendidikan',
            'status' => 'Nonaktif',
        ]);

        $response = $this->post('/login', [
            'username' => 'nonaktif@kebumenkab.go.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertFalse(Auth::check());

        $nonaktifUser->delete();
    }

    /**
     * TC-WF01-06: Pengguna berhasil logout dan sesi diakhiri.
     */
    public function test_logout_berhasil(): void
    {
        $user = User::where('email', 'admin@simada.kebumenkab.go.id')->first();
        $this->actingAs($user);

        $response = $this->get('/keluar');

        $response->assertRedirect('/login');
        $response->assertSessionHas('success');
        $this->assertFalse(Auth::check());
    }
}
