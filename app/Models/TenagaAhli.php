<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenagaAhli extends Model
{
    use HasFactory;

    protected $table = 'tb_tenaga_ahli';

    protected $fillable = [
        'nik',
        'nama',
        'keahlian',
        'penyedia',
        'telepon',
        'email',
        'status',
    ];

    /**
     * Relasi One-to-Many ke PenugasanPersonel (M:N ke Paket Pengadaan).
     */
    public function penugasan(): HasMany
    {
        return $this->hasMany(PenugasanPersonel::class, 'tenaga_ahli_id');
    }
}
