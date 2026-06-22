<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom konfigurasi lanjutan untuk sesi ujian.
     * - allowed_ip_ranges: JSON array subnet/IP yang diizinkan (Geofencing)
     * - kkm: Kriteria Ketuntasan Minimal untuk auto-remedial
     * - allow_remedial: flag remedial otomatis
     */
    public function up(): void
    {
        Schema::table('sesi_ujians', function (Blueprint $table) {
            $table->json('allowed_ip_ranges')->nullable()->after('id');
            $table->decimal('kkm', 5, 2)->default(75.00)->after('allowed_ip_ranges');
            $table->boolean('allow_remedial')->default(false)->after('kkm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_ujians', function (Blueprint $table) {
            $table->dropColumn(['allowed_ip_ranges', 'kkm', 'allow_remedial']);
        });
    }
};
