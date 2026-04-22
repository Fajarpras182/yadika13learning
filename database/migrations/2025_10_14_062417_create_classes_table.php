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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., X, XI, XII
            $table->foreignId('major_id')->constrained('majors')->onDelete('cascade');
            $table->integer('year')->nullable(); // tahun angkatan
            $table->string('homeroom_teacher')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['name','major_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
