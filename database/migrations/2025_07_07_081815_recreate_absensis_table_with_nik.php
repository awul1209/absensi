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
        // Hapus tabel lama jika ada
        Schema::dropIfExists('absensis');

        // Buat tabel baru dengan struktur yang benar
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            // PERBAIKAN: karyawan_id sekarang menyimpan NIK (string)
            $table->string('karyawan_id'); 
            $table->date('tanggal');
            $table->string('status')->default('Hadir');
            $table->timestamps();
            
            $table->time('jam_masuk')->nullable();
            $table->string('status_masuk')->default('tepat_waktu');
            $table->string('foto_masuk')->nullable();
            $table->decimal('latitude_masuk', 10, 7)->nullable();
            $table->decimal('longitude_masuk', 11, 7)->nullable();
            $table->integer('keterlambatan_menit')->default(0);

            $table->time('jam_pulang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->decimal('latitude_pulang', 10, 7)->nullable();
            $table->decimal('longitude_pulang', 11, 7)->nullable();

            // Tambahkan foreign key constraint jika NIK di tabel karyawans adalah unik
            // $table->foreign('karyawan_id')->references('nik')->on('karyawans')->onDelete('cascade');
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
