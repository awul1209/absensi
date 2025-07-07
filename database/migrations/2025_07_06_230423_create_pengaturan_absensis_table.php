<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_absensi', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_kantor');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->integer('radius'); // Dalam meter
            $table->time('jam_masuk_mulai');
            $table->time('jam_masuk_selesai');
            $table->time('batas_terlambat');
            $table->time('jam_pulang_mulai');
            $table->time('jam_pulang_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_absensi');
    }
};
