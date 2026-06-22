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
        Schema::create('ujian_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_result_id')->constrained('ujian_results')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('selected_answer')->nullable(); // for single choice MCQ
            $table->boolean('is_correct')->default(false);
            $table->decimal('points', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_answers');
    }
};

