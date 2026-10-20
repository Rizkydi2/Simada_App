<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sesuai SDD Seksi 5.2 (Entitas Tenaga Ahli) dan SRS FR-07 (Crosscheck Personel).
     */
    public function up(): void
    {
        Schema::create('tb_tenaga_ahli', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique();
            $table->string('nama', 255);
            $table->string('keahlian', 100);
            $table->string('penyedia', 255)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('status', 30)->default('Tersedia'); // 'Tersedia', 'Ditugaskan', 'Nonaktif'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tenaga_ahli');
    }
};
