<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add created_by and guru_id to questions table
 * 
 * Adds missing columns to support backward compatibility
 * with the old GuruController bank-soal methods.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Add guru_id (references the teacher who created the question)
            $table->foreignId('guru_id')
                  ->nullable()
                  ->after('course_id')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Guru yang membuat soal ini');

            // Add created_by for ownership tracking
            $table->foreignId('created_by')
                  ->nullable()
                  ->after('guru_id')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('User yang membuat soal ini');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->dropForeign(['guru_id']);
            $table->dropColumn('guru_id');
        });
    }
};
