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
        // Menambahkan Index ke tabel ujian_results
        Schema::table('ujian_results', function (Blueprint $table) {
            $table->index('student_id');
            $table->index('sesi_ujian_id');
            $table->index('score'); // Berguna untuk query ranking/leaderboard
        });

        // Menambahkan Index ke tabel ujian_answers
        Schema::table('ujian_answers', function (Blueprint $table) {
            $table->index('question_id');
            $table->index('is_correct'); // Mempercepat query perhitungan nilai manual
        });
        
        // Catatan Enterprise: Jika ujian_answers menampung jutaan baris,
        // pertimbangkan implementasi Table Partitioning by RANGE (misal per bulan/tahun ajaran).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian_results', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['sesi_ujian_id']);
            $table->dropIndex(['score']);
        });

        Schema::table('ujian_answers', function (Blueprint $table) {
            $table->dropIndex(['question_id']);
            $table->dropIndex(['is_correct']);
        });
    }
};
