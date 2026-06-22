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
        // Update students' class_id based on kelas and jurusan
        $students = \DB::table('users')->where('role', 'siswa')->get();

        foreach ($students as $student) {
            $majorCode = '';
            if (str_contains($student->jurusan, 'Teknik Informatika')) {
                $majorCode = 'TI';
            } elseif (str_contains($student->jurusan, 'Komputer')) {
                $majorCode = 'TKJ';
            } elseif (str_contains($student->jurusan, 'Akuntansi')) {
                $majorCode = 'AK';
            }

            if ($majorCode) {
                $class = \DB::table('classes')
                    ->join('majors', 'classes.major_id', '=', 'majors.id')
                    ->where('classes.name', $student->kelas)
                    ->where('majors.code', $majorCode)
                    ->select('classes.id')
                    ->first();

                if ($class) {
                    \DB::table('users')->where('id', $student->id)->update(['class_id' => $class->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
