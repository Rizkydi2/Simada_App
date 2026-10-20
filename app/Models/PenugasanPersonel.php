<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanPersonel extends Model
{
    use HasFactory;

    protected $table = 'tb_penugasan_personel';

    protected $fillable = [
        'paket_id',
        'tenaga_ahli_id',
        'posisi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi ke Paket Pengadaan.
     */
    public function paket(): BelongsTo
    {
        return $this->belongsTo(PaketPengadaan::class, 'paket_id');
    }

    /**
     * Relasi ke Profil Tenaga Ahli.
     */
    public function tenagaAhli(): BelongsTo
    {
        return $this->belongsTo(TenagaAhli::class, 'tenaga_ahli_id');
    }
}
