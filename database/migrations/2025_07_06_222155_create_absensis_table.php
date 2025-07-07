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

    Schema::create('absensis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
        $table->dateTime('waktu_absen');
        $table->string('tipe'); // 'masuk' atau 'pulang'
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
