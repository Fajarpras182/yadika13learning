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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'guru', 'siswa'])->default('siswa')->after('email');
            $table->string('nis_nip')->nullable()->after('role');
            $table->string('kelas')->nullable()->after('nis_nip');
            $table->string('jurusan')->nullable()->after('kelas');
            $table->string('no_hp')->nullable()->after('jurusan');
            $table->text('alamat')->nullable()->after('no_hp');
            $table->boolean('is_active')->default(true)->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nis_nip', 'kelas', 'jurusan', 'no_hp', 'alamat', 'is_active']);
        });
    }
};
