<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan kolom untuk foto referensi wajah di tabel karyawans
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('foto_referensi')->nullable()->after('foto_wajah');
        });

        // Memodifikasi tabel absensis secara keseluruhan
        Schema::table('absensis', function (Blueprint $table) {
            // Hapus kolom lama jika ada
            if (Schema::hasColumn('absensis', 'jam_masuk')) {
                $table->dropColumn('jam_masuk');
            }
            if (Schema::hasColumn('absensis', 'jam_pulang')) {
                $table->dropColumn('jam_pulang');
            }
            if (Schema::hasColumn('absensis', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            
            // Ubah nama kolom tanggal
            $table->renameColumn('tanggal_absen', 'tanggal');

            // Tambahkan kolom-kolom baru yang lebih detail
            $table->time('jam_masuk')->nullable();
            $table->string('status_masuk')->default('tepat_waktu'); // 'tepat_waktu', 'terlambat'
            $table->string('foto_masuk')->nullable();
            $table->decimal('latitude_masuk', 10, 7)->nullable();
            $table->decimal('longitude_masuk', 11, 7)->nullable();
            $table->integer('keterlambatan_menit')->default(0);

            $table->time('jam_pulang')->nullable();
            $table->string('foto_pulang')->nullable();
            $table->decimal('latitude_pulang', 10, 7)->nullable();
            $table->decimal('longitude_pulang', 11, 7)->nullable();
        });
    }

    public function down(): void
    {
        // Logic untuk mengembalikan perubahan (opsional)
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn('foto_referensi');
        });
        // ... down logic untuk tabel absensis
    }
};
