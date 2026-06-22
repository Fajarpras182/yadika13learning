<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom wacana_id dan is_random_options ke tabel soal.
     */
    public function up(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            // FK ke wacana (nullable: soal bisa berdiri sendiri tanpa wacana)
            $table->foreignId('wacana_id')->nullable()->after('bank_soal_id')
                  ->constrained('wacanas')->onDelete('set null');

            // Flag untuk mengacak urutan opsi A/B/C/D per siswa
            $table->boolean('is_random_options')->default(true)->after('jawaban_benar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soal', function (Blueprint $table) {
            $table->dropForeign(['wacana_id']);
            $table->dropColumn(['wacana_id', 'is_random_options']);
        });
    }
};
