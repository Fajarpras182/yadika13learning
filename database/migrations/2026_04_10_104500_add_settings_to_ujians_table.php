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
        Schema::table('ujians', function (Blueprint $table) {
            if (!Schema::hasColumn('ujians', 'class_ids')) {
                $table->json('class_ids')->nullable()->after('guru_id');
            }
            if (!Schema::hasColumn('ujians', 'bobot_nilai')) {
                $table->unsignedInteger('bobot_nilai')->default(100)->after('durasi_menit');
            }
            if (!Schema::hasColumn('ujians', 'soal_acak')) {
                $table->boolean('soal_acak')->default(false)->after('bobot_nilai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn(['class_ids', 'bobot_nilai', 'soal_acak']);
        });
    }
};
