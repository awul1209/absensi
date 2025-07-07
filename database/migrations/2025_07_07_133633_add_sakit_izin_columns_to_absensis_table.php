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
    Schema::table('absensis', function (Blueprint $table) {
        $table->text('keterangan_sakit_izin')->nullable()->after('status_masuk');
        $table->string('dokumen_bukti')->nullable()->after('keterangan_sakit_izin');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            //
        });
    }
};
