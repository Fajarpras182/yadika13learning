<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            if (!Schema::hasColumn('ujians', 'jawaban_acak')) {
                $table->boolean('jawaban_acak')->default(false)->after('soal_acak');
            }
            if (!Schema::hasColumn('ujians', 'tampilkan_hasil')) {
                $table->boolean('tampilkan_hasil')->default(true)->after('jawaban_acak');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn(['jawaban_acak', 'tampilkan_hasil']);
        });
    }
};
