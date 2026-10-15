<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_tracking_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')
                ->constrained('tb_paket_pengadaan')
                ->onDelete('cascade');
            $table->string('tahapan');
            $table->string('peran')->nullable();
            $table->string('petugas');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_tracking_progress');
    }
};
