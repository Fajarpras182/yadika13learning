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
        Schema::table('ujian_answers', function (Blueprint $table) {
            $table->unique(['ujian_result_id', 'question_id'], 'ujian_answers_result_question_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujian_answers', function (Blueprint $table) {
            $table->dropUnique('ujian_answers_result_question_unique');
        });
    }
};
