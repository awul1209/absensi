<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // 1. Ganti nama kolom 'tipe' menjadi 'status' untuk lebih deskriptif
            $table->renameColumn('tipe', 'status');

            // 2. Ubah kolom 'waktu_absen' menjadi 'tanggal' saja
            $table->renameColumn('waktu_absen', 'tanggal_absen');
            $table->date('tanggal_absen')->change();

            // 3. Tambahkan kolom untuk jam masuk & pulang (bisa null)
            $table->time('jam_masuk')->nullable()->after('tanggal_absen');
            $table->time('jam_pulang')->nullable()->after('jam_masuk');

            // 4. Tambahkan kolom keterangan untuk catatan (misal: surat dokter)
            $table->text('keterangan')->nullable()->after('jam_pulang');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Logika untuk mengembalikan perubahan jika diperlukan
            $table->renameColumn('status', 'tipe');
            $table->renameColumn('tanggal_absen', 'waktu_absen');
            $table->dateTime('waktu_absen')->change();
            $table->dropColumn(['jam_masuk', 'jam_pulang', 'keterangan']);
        });
    }
};