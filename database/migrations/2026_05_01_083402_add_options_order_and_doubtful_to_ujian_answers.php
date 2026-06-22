<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom options_order (JSON) dan is_doubtful ke ujian_answers.
     * - options_order: menyimpan urutan acak opsi per siswa agar konsisten saat refresh
     * - is_doubtful: fitur "Tandai Ragu-Ragu" (tombol kuning)
     */
    public function up(): void
    {
        Schema::table('ujian_answers', function (Blueprint $table) {
            // Urutan acak opsi per siswa, misal: ["c","a","d","b"]
            $table->json('options_order')->nullable()->after('selected_answer');

            // Fitur Tandai Ragu-Ragu
            $table->boolean('is_doubtful')->default(false)->after('options_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian_answers', function (Blueprint $table) {
            $table->dropColumn(['options_order', 'is_doubtful']);
        });
    }
};
