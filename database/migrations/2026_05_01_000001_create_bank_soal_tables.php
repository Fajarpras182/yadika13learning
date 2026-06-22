<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Single Source of Truth - Bank Soal
 *
 * Membuat dua tabel baru yang berfungsi sebagai satu sumber kebenaran
 * untuk semua soal ujian, terpisah dari tabel questions lama.
 *
 * bank_soal  → Header/grup soal milik seorang guru per mata pelajaran.
 * soal       → Butir pertanyaan individual yang terikat ke satu bank_soal.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ----------------------------------------------------------------
        // Tabel bank_soal
        // Setiap guru memiliki satu atau banyak bank soal.
        // Satu bank soal mewakili satu mata pelajaran & kelas tertentu.
        // ----------------------------------------------------------------
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();

            // Guru pemilik bank soal (RBAC: scope data per guru)
            $table->foreignId('guru_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('ID guru pemilik bank soal ini');

            $table->string('nama_mata_pelajaran', 255)
                  ->comment('Nama mata pelajaran, misal: Matematika, Bahasa Indonesia');

            $table->string('kelas', 20)
                  ->comment('Kelas yang dituju, misal: X, XI IPA, XII IPS');

            $table->text('deskripsi')->nullable()
                  ->comment('Keterangan tambahan mengenai bank soal ini');

            $table->timestamps();

            // Index untuk mempercepat query scoping per guru
            $table->index('guru_id');
        });

        // ----------------------------------------------------------------
        // Tabel soal
        // Butir soal individual. Setiap soal WAJIB terikat ke satu bank_soal.
        // Admin & Guru membaca dari tabel yang sama (Single Source of Truth).
        // ----------------------------------------------------------------
        Schema::create('soal', function (Blueprint $table) {
            $table->id();

            // Relasi ke bank_soal (hapus bank → hapus semua soal di dalamnya)
            $table->foreignId('bank_soal_id')
                  ->constrained('bank_soal')
                  ->onDelete('cascade')
                  ->comment('Bank soal induk dari butir soal ini');

            $table->text('teks_pertanyaan')
                  ->comment('Bunyi pertanyaan / soal');

            $table->text('opsi_a');
            $table->text('opsi_b');
            $table->text('opsi_c');
            $table->text('opsi_d');

            // Kunci jawaban: nilai enum dibatasi hanya a-d sesuai spesifikasi
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd'])
                  ->comment('Huruf kunci jawaban yang benar');

            $table->timestamps();

            // Index untuk mempercepat query soal per bank
            $table->index('bank_soal_id');
        });
    }

    public function down(): void
    {
        // Urutan drop: child dulu sebelum parent
        Schema::dropIfExists('soal');
        Schema::dropIfExists('bank_soal');
    }
};
