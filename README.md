<h1 align="center">SIMADA</h1>
<p align="center"><b>Sistem Informasi Pengadaan Barang dan Jasa</b><br>Pemerintah Kabupaten Kebumen</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-%E2%89%A5%208.1-777BB4?logo=php&logoColor=white" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" alt="Laravel 10">
  <img src="https://img.shields.io/badge/MySQL-simada__app-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/versi-1.0%20(prototipe)-orange" alt="Versi 1.0 prototipe">
</p>

---

## Tentang SIMADA

**SIMADA** adalah aplikasi web milik Pemerintah Kabupaten Kebumen yang berfungsi sebagai sarana **koordinasi dan pengendalian alur kerja pengadaan barang/jasa** bagi PA/KPA, PPK, Pokja Pemilihan, dan Pejabat Pengadaan di lingkungan Bagian PBJ Setda Kebumen.

Aplikasi ini dibangun ulang untuk menggantikan SIMADA lama yang ditutup oleh Dinas Kominfo karena celah keamanan teknis. SIMADA berjalan **berdampingan dengan SPSE LKPP** dan melengkapi hal-hal yang belum didukung SPSE, yaitu:

- Berita Acara Penetapan/Pengumuman Pemenang
- Surat Pemberitahuan Selesai PBJ
- *Crosscheck* penugasan tenaga ahli lintas paket aktif
- Validasi Sisa Kemampuan Paket (SKP) penyedia

Kebutuhan sistem bersumber dari KAK Belanja Jasa Konversi Aplikasi/Sistem Informasi Pengembangan SIMADA Bagian PBJ Setda Kebumen TA 2025.

> **Catatan versi:** repositori ini berisi **SIMADA v1.0 (prototipe)**. Sebagian modul sudah berjalan penuh dengan basis data, sebagian lagi masih berupa antarmuka dengan data contoh. Rinciannya ada di bagian [Status Fitur](#status-fitur).

---

## Fitur dan Status

| Modul | Rujukan SDD | Status |
| --- | --- | --- |
| Login & Autentikasi (NIP atau email) | WF-01 | ✅ Berjalan, terhubung basis data |
| Manajemen Pengguna & Hak Akses (RBAC) | UC-01, WF-08 | ✅ Berjalan, terhubung basis data |
| Pelacakan Progres Tahapan Pengadaan | UC-07, WF-05 | ✅ Berjalan, terhubung basis data |
| Dashboard Pagu vs Realisasi | UC-09, WF-02 | 🟡 Antarmuka dengan data contoh |
| Manajemen Paket Pengadaan (e-Tendering, e-Pengadaan Langsung, e-Purchasing) | UC-02, WF-03, WF-04 | 🟡 Antarmuka dengan data contoh, formulir belum menyimpan ke basis data |
| Crosscheck Personel & Validasi SKP | UC-03, UC-04, WF-06 | 🟡 Antarmuka saja, logika belum dibuat |
| Generator Berita Acara & Surat Selesai PBJ | UC-05, UC-06, WF-07 | 🟡 Antarmuka saja, belum membentuk dokumen |
| Ekspor laporan PDF/Excel | UC-10 | ⬜ Belum diimplementasikan |
| Notifikasi otomatis WhatsApp/Email | UC-08 | ⬜ Belum diimplementasikan (*nice to have*) |

### Yang sudah berfungsi

**Login & autentikasi**
- Masuk dengan **NIP atau email** dan kata sandi (hash bcrypt).
- Akun berstatus *Nonaktif* ditolak.
- Sesi berlaku 120 menit, ID sesi diperbarui setelah login, form dilindungi token CSRF.

**Manajemen pengguna & hak akses (khusus Admin Sistem)**
- Tambah, ubah, hapus, serta cari/filter pengguna.
- Lima peran RBAC dan status akun *Aktif/Nonaktif*.
- Akun Admin Sistem terakhir tidak dapat dihapus, diubah perannya, atau dinonaktifkan.

**Pelacakan progres tahapan**
- *Stepper* 5 tahapan: **Perencanaan → Pengumuman → Evaluasi → Penetapan → Selesai**.
- Riwayat perubahan tahapan per paket, pencarian berdasarkan kode/nama paket.
- Hanya **Pokja Pemilihan** dan **Pejabat Pengadaan** yang boleh memperbarui tahapan.
- Tahapan tidak boleh mundur, sama, atau melompat. Pembaruan berjalan dalam transaksi database.

---

## Peran Pengguna (RBAC)

| Peran | Menu yang tampil | Hak khusus |
| --- | --- | --- |
| **Admin Sistem** | Semua menu, termasuk User & Hak Akses | Mengelola akun pengguna |
| **PPK** | Dashboard, Paket Pengadaan, Tracking Progres, Berita Acara & Surat | Memantau tahapan |
| **PA/KPA** | Dashboard, Paket Pengadaan, Tracking Progres, Berita Acara & Surat | Memantau tahapan |
| **Pokja Pemilihan** | Dashboard, Paket Pengadaan, Tracking Progres, Verifikasi, Berita Acara & Surat | Memperbarui tahapan paket (tender) |
| **Pejabat Pengadaan** | Dashboard, Paket Pengadaan, Tracking Progres, Verifikasi, Berita Acara & Surat | Memperbarui tahapan paket (non-tender) |

Akses di luar wewenang peran dijawab dengan **HTTP 403**.

---

## Teknologi

| Komponen | Teknologi |
| --- | --- |
| Bahasa & framework | PHP ≥ 8.1, Laravel 10 (arsitektur MVC) |
| Basis data | MySQL (database `simada_app`), dikelola lewat phpMyAdmin |
| Antarmuka | Blade Template, CSS kustom (`public/css/simada.css`), Bootstrap Icons 1.11.3, font Inter (CDN) |
| Autentikasi | Session Laravel, Laravel Sanctum (bawaan) |
| Pengujian | PHPUnit 10 |
| Gaya kode | EditorConfig, Laravel Pint |
| Lingkungan pengembangan | XAMPP (Apache, MySQL, PHP), Composer, Artisan |

Penerapan konsep OOP: *inheritance* (seluruh controller turunan `Controller`), *encapsulation* lewat konstanta kelas (`User::ROLES`, `User::STATUSES`, `PaketPengadaan::TAHAPAN_LIST`), ORM Eloquent dengan relasi `hasMany`/`belongsTo`, dan *dependency injection* pada method controller.

---

## Instalasi (Lingkungan Lokal)

### Prasyarat

- [XAMPP](https://www.apachefriends.org/) dengan **PHP 8.1 atau lebih baru** (Apache dan MySQL)
- [Composer](https://getcomposer.org/)
- [Git](https://git-scm.com/)

### Langkah-langkah

1. **Jalankan Apache dan MySQL** lewat XAMPP Control Panel.

2. **Clone repositori**
   ```bash
   git clone https://github.com/Rizkydi2/Simada_App.git
   cd Simada_App
   ```

3. **Buat database kosong** bernama `simada_app` lewat phpMyAdmin (`http://localhost/phpmyadmin`).

4. **Salin file environment**
   ```bash
   cp .env.example .env
   ```
   Di Windows (CMD): `copy .env.example .env`.
   Isian database pada `.env.example` sudah mengarah ke `simada_app` dengan pengguna `root` tanpa kata sandi (default XAMPP). Sesuaikan jika konfigurasi MySQL kamu berbeda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=simada_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Pasang dependensi dan buat app key**
   ```bash
   composer install
   php artisan key:generate
   ```

6. **Buat tabel dan isi data awal**
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan aplikasi**
   ```bash
   php artisan serve
   ```
   Buka **http://127.0.0.1:8000** di peramban. Kamu akan diarahkan ke halaman login.

---

## Akun Contoh

`UserSeeder` membuat lima akun, satu untuk tiap peran. Kata sandi awal semuanya **`password123`**.

| Peran | Nama | Email |
| --- | --- | --- |
| Admin Sistem | Admin Sistem UKPBJ | `admin@simada.kebumenkab.go.id` |
| PPK | Budi Santoso | `budi.santoso@kebumenkab.go.id` |
| Pokja Pemilihan | Siti Rahma | `siti.rahma@simada.kebumenkab.go.id` |
| Pejabat Pengadaan | Hendra Wijaya | `hendra.wijaya@kebumenkab.go.id` |
| PA/KPA | Drs. Ahmad Fauzi | `ahmad.fauzi@kebumenkab.go.id` |

> ⚠️ Akun dan kata sandi ini hanya untuk pengembangan dan demonstrasi. **Wajib diganti sebelum aplikasi dipakai di produksi.**

Seeder juga menyiapkan contoh paket pengadaan (mis. `PKT-001` Pembangunan Gedung Sekolah dan `PKT-002` Rehabilitasi Jalan Raya) beserta riwayat tahapannya, serta data contoh tenaga ahli.

---

## Rute Utama

| Rute | Fungsi |
| --- | --- |
| `GET /login`, `POST /login` | Halaman dan proses login |
| `GET /keluar` | Logout |
| `GET /dashboard` | Dashboard |
| `GET /paket`, `GET /paket/tambah` | Daftar dan formulir paket pengadaan |
| `GET /tracking`, `POST /tracking/{id}/update` | Pelacakan dan pembaruan tahapan |
| `GET /verifikasi` | Verifikasi personel dan SKP |
| `GET /dokumen` | Berita Acara & Surat |
| `GET /users`, `POST /users`, `PUT /users/{id}`, `DELETE /users/{id}` | Manajemen pengguna |

Daftar lengkap bisa dilihat dengan `php artisan route:list`.

---

## Struktur Proyek

```
Simada_App/
├── app/
│   ├── Http/Controllers/     # AuthController, UserController, TrackingController
│   └── Models/               # User, PaketPengadaan, TrackingProgress, TenagaAhli, PenugasanPersonel
├── database/
│   ├── migrations/           # users, tb_paket_pengadaan, tb_tracking_progress,
│   │                         # tb_tenaga_ahli, tb_penugasan_personel
│   └── seeders/              # UserSeeder, TrackingSeeder, TenagaAhliSeeder
├── public/css/simada.css     # Gaya antarmuka kustom
├── resources/views/          # Blade: login, dashboard, paket, tracking, verifikasi, dokumen, users
├── routes/web.php            # Definisi rute web
└── tests/Feature/            # AuthenticationTest, TrackingProgressTest, UserManagementTest
```

---

## Model Data

| Tabel | Isi |
| --- | --- |
| `users` | Akun pengguna: `name`, `nip` (unik), `email` (unik), `password`, `role`, `opd`, `status` |
| `tb_paket_pengadaan` | `kode_paket` (unik), `nama_paket`, `opd`, `metode`, `pagu`, `hps`, `tahapan_sekarang` |
| `tb_tracking_progress` | Riwayat tahapan per paket (relasi 1:N ke `tb_paket_pengadaan`) |
| `tb_tenaga_ahli` | Data tenaga ahli penyedia (`nik` unik, keahlian, status) |
| `tb_penugasan_personel` | Penugasan tenaga ahli ke paket beserta periode (relasi M:N paket–tenaga ahli) |

Entitas lain pada ERD di SDD (`tb_roles`, `tb_opd`, `tb_penyedia`, `tb_berita_acara`, `tb_surat_pemberitahuan`) masih berupa rencana dan belum memiliki migrasi.

---

## Pengujian

```bash
php artisan test
```

Pengujian fitur berada di `tests/Feature` (autentikasi, manajemen pengguna, dan pelacakan progres).

> ⚠️ Pengujian memakai database yang diatur di `.env` dan **menghapus pengguna selain 5 akun seeder**. Jangan dijalankan pada database yang berisi data penting. Sebaiknya gunakan database terpisah untuk pengujian.

---

## Dokumentasi

| Dokumen | Keterangan |
| --- | --- |
| SDD (Software Design Document) | Arsitektur MVC, use case, ERD, wireframe WF-01 s.d. WF-08, estimasi FPA dan COCOMO II |
| SCD (System Description Document) | Deskripsi sistem, teknologi, akses dan otorisasi, panduan pemecahan masalah |
| User Manual | Panduan pengguna per peran: Admin Sistem, PA/KPA, PPK, Pejabat Pengadaan, Pokja Pemilihan |

---

## Pemecahan Masalah Singkat

| Gejala | Penyebab dan solusi |
| --- | --- |
| `Access denied` / `Unknown database simada_app` | MySQL belum jalan, database belum dibuat, atau isian `DB_*` di `.env` salah |
| `Base table or view not found` | Migrasi belum dijalankan: `php artisan migrate` |
| Login akun contoh gagal / halaman tracking kosong | Seeder belum dijalankan: `php artisan db:seed` |
| `No application encryption key` (HTTP 500) | Jalankan `php artisan key:generate` |
| HTTP 419 Page Expired | Token CSRF atau sesi kedaluwarsa, muat ulang halaman |
| Perubahan `.env` tidak terbaca | Jalankan `php artisan config:clear` |
| Error aplikasi | Cek `storage/logs/laravel.log` |

---

## Tim Pengembang

**Tim SIMADA**

- Rizky Adinugraha Imansyah
- Adrian Sultan Taher
- Muhammad Rifky Balweel

**Product Owner:** Agus Hanafi, S.ST, MT, PPK Bagian PBJ Setda Kabupaten Kebumen

**Pengelola infrastruktur:** Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Kebumen

---

## Lisensi

Dibangun di atas [Laravel](https://laravel.com), yang dirilis di bawah [lisensi MIT](https://opensource.org/licenses/MIT).
