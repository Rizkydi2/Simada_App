<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingProgress extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'tb_tracking_progress';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'paket_id',
        'tahapan',
        'peran',
        'petugas',
        'keterangan',
    ];

    /**
     * Relasi Many-to-One (BelongsTo) ke model PaketPengadaan.
     * Setiap entri log tracking progress merujuk ke satu paket pengadaan.
     */
    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketPengadaan::class, 'paket_id', 'id');
    }
}
