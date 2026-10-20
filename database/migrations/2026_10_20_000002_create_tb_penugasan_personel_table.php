<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sesuai SDD Seksi 5.2 (Tabel Relasi Penugasan Paket - Tenaga Ahli untuk deteksi benturan).
     */
    public function up(): void
    {
        Schema::create('tb_penugasan_personel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('tb_paket_pengadaan')->cascadeOnDelete();
            $table->foreignId('tenaga_ahli_id')->constrained('tb_tenaga_ahli')->cascadeOnDelete();
            $table->string('posisi', 100);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status', 30)->default('Aktif'); // 'Aktif', 'Selesai'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penugasan_personel');
    }
};
