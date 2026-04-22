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
        Schema::create('background_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('background_type')->default('image'); // image or color
            $table->string('background_image')->nullable();
            $table->string('background_color')->default('#f8f9fa');
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_settings');
    }
};
