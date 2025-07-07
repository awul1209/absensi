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
        Schema::create('karyawans', function (Blueprint $table) {
                 $table->id();
                // Hubungkan dengan tabel users
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('nama_lengkap');
                $table->string('posisi');
                // Kita bisa hapus email & password dari sini karena sudah ada di tabel users
                $table->string('nomor_telepon')->nullable();
                $table->string('foto_wajah')->nullable(); // Path ke file foto wajah
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
