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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mata_pelajaran');
            $table->string('kode_mata_pelajaran')->unique();
            $table->text('deskripsi')->nullable();
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->string('kelas');
            $table->string('jurusan');
            $table->integer('semester');
            $table->integer('sks')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
