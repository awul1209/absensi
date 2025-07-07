<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    public function up(): void
    {
        Schema::create('kinerjas', function (Blueprint $table) {
            $table->id();
            // Membuat foreign key ke tabel karyawans
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->text('deskripsi_pekerjaan');
            $table->string('status')->default('Selesai'); // Contoh: Selesai, Dikerjakan, Ditunda
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerjas');
    }
}
