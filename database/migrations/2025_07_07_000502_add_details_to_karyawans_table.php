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
        Schema::table('karyawans', function (Blueprint $table) {
            // Menambahkan kolom-kolom baru setelah kolom 'posisi'
            $table->string('nik')->unique()->nullable()->after('posisi');
            $table->year('tahun_masuk')->nullable()->after('nik');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('tahun_masuk');
            $table->text('alamat')->nullable()->after('jenis_kelamin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['nik', 'tahun_masuk', 'jenis_kelamin', 'alamat']);
        });
    }
};
