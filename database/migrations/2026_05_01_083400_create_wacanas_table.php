<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk menyimpan teks wacana/bacaan panjang (Reading Comprehension).
     * Satu wacana bisa digunakan oleh banyak soal sekaligus.
     */
    public function up(): void
    {
        Schema::create('wacanas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->string('judul'); // Judul wacana, misal: "Bacaan 1 - Ekosistem"
            $table->longText('konten'); // Isi wacana/teks bacaan (support HTML/KaTeX)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wacanas');
    }
};
