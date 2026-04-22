<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\User;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get guru users
        $guru1 = User::where('email', 'budi.santoso@smkyadika13.sch.id')->first();
        $guru2 = User::where('email', 'siti.nurhaliza@smkyadika13.sch.id')->first();
        $guru3 = User::where('email', 'ahmad.wijaya@smkyadika13.sch.id')->first();

        // Get classes and majors
        $classXII = \App\Models\SchoolClass::where('name', 'XII TKJ 1')->first();
        $classXI = \App\Models\SchoolClass::where('name', 'XI Teknik Informatika')->first();
        $classX = \App\Models\SchoolClass::where('name', 'X Teknik Informatika')->first();
        $majorTKJ = \App\Models\Major::where('code', 'TKJ')->first();
        $majorAKL = \App\Models\Major::where('code', 'AK')->first();

        // Create courses
        $courses = [
            [
                'nama_mata_pelajaran' => 'Pemrograman Web',
                'kode_mata_pelajaran' => 'PW001',
                'deskripsi' => 'Mata pelajaran yang membahas tentang pengembangan aplikasi web menggunakan HTML, CSS, JavaScript, dan PHP.',
                'guru_id' => $guru1->id,
                'class_id' => $classXII->id,
                'major_id' => $majorTKJ->id,
                'semester' => 1,
                'sks' => 4,
            ],
            [
                'nama_mata_pelajaran' => 'Matematika B',
                'kode_mata_pelajaran' => 'MTKB',
                'deskripsi' => 'Mata pelajaran yang membahas tentang matematika tingkat lanjut.',
                'guru_id' => $guru1->id,
                'class_id' => $classXII->id,
                'major_id' => $majorTKJ->id,
                'semester' => 1,
                'sks' => 3,
            ],
            [
                'nama_mata_pelajaran' => 'Basis Data',
                'kode_mata_pelajaran' => 'BD001',
                'deskripsi' => 'Mata pelajaran yang membahas tentang konsep basis data, SQL, dan manajemen data.',
                'guru_id' => $guru2->id,
                'class_id' => $classXII->id,
                'major_id' => $majorTKJ->id,
                'semester' => 2,
                'sks' => 3,
            ],
            [
                'nama_mata_pelajaran' => 'Jaringan Komputer',
                'kode_mata_pelajaran' => 'JK001',
                'deskripsi' => 'Mata pelajaran yang membahas tentang konsep jaringan komputer, protokol, dan konfigurasi jaringan.',
                'guru_id' => $guru3->id,
                'class_id' => $classXII->id,
                'major_id' => $majorTKJ->id,
                'semester' => 1,
                'sks' => 3,
            ],
        ];

        foreach ($courses as $courseData) {
            $course = Course::create($courseData);

            // Create lessons for each course
            if ($course->nama_mata_pelajaran === 'Pemrograman Web') {
                $lessons = [
                    [
                        'judul' => 'Pengenalan HTML',
                        'deskripsi' => 'Materi dasar tentang HTML dan struktur dokumen web',
                        'materi' => 'HTML (HyperText Markup Language) adalah bahasa markup yang digunakan untuk membuat halaman web. HTML terdiri dari elemen-elemen yang ditandai dengan tag-tag...',
                        'urutan' => 1,
                        'is_published' => true,
                    ],
                    [
                        'judul' => 'CSS dan Styling',
                        'deskripsi' => 'Materi tentang CSS untuk styling halaman web',
                        'materi' => 'CSS (Cascading Style Sheets) adalah bahasa stylesheet yang digunakan untuk mengatur tampilan dan format dokumen HTML...',
                        'urutan' => 2,
                        'is_published' => true,
                    ],
                    [
                        'judul' => 'JavaScript Dasar',
                        'deskripsi' => 'Materi tentang JavaScript untuk interaktivitas web',
                        'materi' => 'JavaScript adalah bahasa pemrograman yang digunakan untuk membuat halaman web menjadi interaktif...',
                        'urutan' => 3,
                        'is_published' => false,
                    ],
                ];

                foreach ($lessons as $lessonData) {
                    $course->lessons()->create($lessonData);
                }

                // Create assignments
                $assignments = [
                    [
                        'judul' => 'Tugas HTML - Membuat Halaman Profil',
                        'deskripsi' => 'Buat halaman profil sederhana menggunakan HTML',
                        'instruksi' => 'Buat halaman HTML yang berisi profil diri Anda dengan minimal 5 elemen HTML yang berbeda.',
                        'deadline' => now()->addDays(7),
                        'bobot_nilai' => 25,
                    ],
                    [
                        'judul' => 'Tugas CSS - Styling Halaman',
                        'deskripsi' => 'Terapkan CSS pada halaman HTML yang sudah dibuat',
                        'instruksi' => 'Tambahkan CSS pada halaman profil yang sudah dibuat dengan minimal 10 properti CSS yang berbeda.',
                        'deadline' => now()->addDays(14),
                        'bobot_nilai' => 30,
                    ],
                ];

                foreach ($assignments as $assignmentData) {
                    $course->assignments()->create($assignmentData);
                }
            }

            // Enroll students to courses based on their class
            $students = User::where('role', 'siswa')->where('class_id', $course->class_id)->get();
            foreach ($students as $student) {
                $course->students()->attach($student->id);
            }
        }
    }
}
