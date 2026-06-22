<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Pivot — Ujian ↔ Bank Soal
 *
 * Mengganti kolom course_id pada tabel ujians dengan bank_soal_id
 * sehingga setiap ujian langsung mengacu ke satu bank soal tertentu.
 *
 * Pendekatan: menambahkan kolom baru (nullable) agar tidak merusak
 * data lama, sehingga rollback tetap aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            // Tambahkan bank_soal_id setelah kolom yang ada
            // Nullable agar data lama tidak langsung error
            $table->foreignId('bank_soal_id')
                  ->nullable()
                  ->after('guru_id')
                  ->constrained('bank_soal')
                  ->onDelete('set null')
                  ->comment('Bank soal yang digunakan sebagai sumber soal ujian ini');

            // Kolom waktu_mulai & durasi_menit sesuai spesifikasi baru
            // (kolom lama tanggal_ujian & durasi_menit sudah ada, ini hanya alias)
            // Tidak menambah kolom duplikat; durasi_menit sudah ada di tabel ujians.
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropForeign(['bank_soal_id']);
            $table->dropColumn('bank_soal_id');
        });
    }
};
