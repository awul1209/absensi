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
        Schema::create('pengajuan_sakit_izin', function (Blueprint $table) {
            $table->id();

            // ============ PERUBAHAN DI SINI UNTUK NIK SEBAGAI FOREIGN KEY ============
            $table->string('karyawan_id_nik'); // Ganti nama kolom menjadi lebih spesifik, misal 'karyawan_id_nik'
            // Tambahkan foreign key constraint yang mereferensikan kolom 'nik' di tabel 'karyawans'
            // Pastikan kolom 'nik' di tabel 'karyawans' bertipe string dan memiliki indeks UNIQUE.
            $table->foreign('karyawan_id_nik')->references('nik')->on('karyawans')->onDelete('cascade');
            // =========================================================================

            $table->enum('tipe_pengajuan', ['sakit', 'izin', 'cuti'])->default('izin');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->text('alasan');
            $table->string('dokumen_bukti_path')->nullable();
            $table->enum('status_persetujuan', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('disetujui_oleh_admin_id')->nullable();
            $table->foreign('disetujui_oleh_admin_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_sakit_izin');
    }
};