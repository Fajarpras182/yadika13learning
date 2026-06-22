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
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'class_id')) {
                $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            }
            if (!Schema::hasColumn('courses', 'major_id')) {
                $table->foreignId('major_id')->nullable()->constrained('majors')->onDelete('cascade');
            }

            // Drop old columns
            if (Schema::hasColumn('courses', 'kelas')) {
                $table->dropColumn(['kelas']);
            }
            if (Schema::hasColumn('courses', 'jurusan')) {
                $table->dropColumn(['jurusan']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add back old columns
            $table->string('kelas');
            $table->string('jurusan');

            // Drop foreign key columns
            $table->dropForeign(['class_id']);
            $table->dropForeign(['major_id']);
            $table->dropColumn(['class_id', 'major_id']);
        });
    }
};
