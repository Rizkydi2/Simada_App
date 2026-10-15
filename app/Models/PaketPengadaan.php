<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaketPengadaan extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'tb_paket_pengadaan';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_paket',
        'nama_paket',
        'opd',
        'metode',
        'pagu',
        'hps',
        'tahapan_sekarang',
    ];

    /**
     * Casting tipe data atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pagu' => 'float',
        'hps' => 'float',
    ];

    /**
     * 5 Daftar Tahapan Pengadaan Standar SIMADA (SDD UC-07 / WF-05).
     *
     * @var array<int, string>
     */
    public const TAHAPAN_LIST = [
        'Perencanaan',
        'Pengumuman',
        'Evaluasi',
        'Penetapan',
        'Selesai',
    ];

    /**
     * Relasi One-to-Many ke model TrackingProgress.
     * Satu paket pengadaan memiliki banyak riwayat catatan tahapan (tb_tracking_progress).
     */
    public function trackingProgress(): HasMany
    {
        return $this->hasMany(TrackingProgress::class, 'paket_id', 'id')->orderBy('created_at', 'asc');
    }
}
