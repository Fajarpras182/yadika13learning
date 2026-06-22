<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel arsip: skema identik dengan ujian_answers + ujian_results.
     * Data dari semester/tahun ajaran lama dipindahkan ke sini
     * agar tabel utama tetap ringan dan query operasional tetap cepat.
     */
    public function up(): void
    {
        // Arsip Jawaban
        Schema::create('ujian_answers_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id')->index(); // ID asli dari ujian_answers
            $table->unsignedBigInteger('ujian_result_id')->index();
            $table->unsignedBigInteger('question_id')->index();
            $table->string('selected_answer')->nullable();
            $table->json('options_order')->nullable();
            $table->boolean('is_doubtful')->default(false);
            $table->boolean('is_correct')->default(false);
            $table->decimal('points', 5, 2)->default(0);
            $table->timestamps();
            $table->timestamp('archived_at')->useCurrent();
        });

        // Arsip Hasil Ujian
        Schema::create('ujian_results_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id')->index(); // ID asli dari ujian_results
            $table->unsignedBigInteger('sesi_ujian_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->decimal('score', 5, 2)->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('time_taken_minutes')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            $table->timestamp('archived_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_answers_archives');
        Schema::dropIfExists('ujian_results_archives');
    }
};
