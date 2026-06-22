<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\Ujian;
use App\Models\Question;
use App\Models\SesiUjian;
use Illuminate\Support\Facades\Hash;

class SesiUjianSeeder extends Seeder
{
    public function run(): void
    {
        // Create Major if not exists
        $major = Major::firstOrCreate(
            ['name' => 'IPA'],
            ['code' => 'IPA001', 'description' => 'Ilmu Pengetahuan Alam']
        );

        // Create Classes
        $kelas10A = SchoolClass::firstOrCreate(
            ['name' => '10 IPA 1'],
            ['major_id' => $major->id, 'student_count' => 0]
        );
        $kelas10B = SchoolClass::firstOrCreate(
            ['name' => '10 IPA 2'],
            ['major_id' => $major->id, 'student_count' => 0]
        );

        // Create Guru
        $guru = User::firstOrCreate(
            ['email' => 'guru@smkyadika.test'],
            [
                'name' => 'Guru Matematika',
                'nis_nip' => 'G001',
                'role' => 'guru',
                'password' => Hash::make('password'),
                'class_id' => null,
            ]
        );

        // Create Course
        $course = Course::firstOrCreate(
            ['nama_mata_pelajaran' => 'Matematika'],
            [
                'guru_id' => $guru->id,
                'kode_mata_pelajaran' => 'MTK001',
                'deskripsi' => 'Pelajaran Matematika Kelas 10',
                'class_id' => $kelas10A->id,
                'major_id' => $major->id,
                'semester' => 1,
                'sks' => 2,
                'is_active' => true,
            ]
        );

        // Create Ujian
        $ujian = Ujian::firstOrCreate(
            ['judul' => 'Ujian Tengah Semester Matematika'],
            [
                'course_id' => $course->id,
                'guru_id' => $guru->id,
                'class_ids' => json_encode([$kelas10A->id, $kelas10B->id]),
                'tanggal_ujian' => now()->addDays(3),
                'durasi_menit' => 90,
                'bobot_nilai' => 30,
                'soal_acak' => true,
                'jawaban_acak' => false,
                'tampilkan_hasil' => true,
                'is_active' => true,
            ]
        );

        // Create Bank Soal Questions (ujian_id null)
        $questionsData = [
            [
                'course_id' => $course->id,
                'pertanyaan' => '2 + 2 = ?',
                'jawaban_a' => '3', 'jawaban_b' => '4', 'jawaban_c' => '5', 'jawaban_d' => '6', 'jawaban_e' => '1',
                'kunci_jawaban' => 'b'
            ],
            [
                'course_id' => $course->id,
                'pertanyaan' => 'Luas persegi panjang?',
                'jawaban_a' => 'p x l', 'jawaban_b' => 'p + l', 'jawaban_c' => 'p - l', 'jawaban_d' => 'p / l', 'jawaban_e' => '√(p x l)',
                'kunci_jawaban' => 'a'
            ],
            // Add 3 more...
            [
                'course_id' => $course->id,
                'pertanyaan' => 'Apa ibukota Indonesia?',
                'jawaban_a' => 'Bandung', 'jawaban_b' => 'Jakarta', 'jawaban_c' => 'Surabaya', 'jawaban_d' => 'Medan', 'jawaban_e' => 'Makassar',
                'kunci_jawaban' => 'b'
            ],
            [
                'course_id' => $course->id,
                'pertanyaan' => '√9 = ?',
                'jawaban_a' => '2', 'jawaban_b' => '3', 'jawaban_c' => '4', 'jawaban_d' => '1', 'jawaban_e' => '0',
                'kunci_jawaban' => 'b'
            ],
            [
                'course_id' => $course->id,
                'pertanyaan' => 'Persamaan linear?',
                'jawaban_a' => '2x + 3 = 7', 'jawaban_b' => 'x^2 + 2x + 1 = 0', 'jawaban_c' => 'sin(x)', 'jawaban_d' => 'log(x)', 'jawaban_e' => 'e^x',
                'kunci_jawaban' => 'a'
            ],
        ];

        foreach ($questionsData as $q) {
            Question::firstOrCreate(
                ['pertanyaan' => $q['pertanyaan']],
                $q + ['is_active' => true]
            );
        }

        // Update questions to assign to ujian (set ujian_id)
        $bankQuestions = Question::where('course_id', $course->id)->whereNull('ujian_id')->take(5)->get();
        $bankQuestions->each(function ($q) use ($ujian) {
            $q->update(['ujian_id' => $ujian->id]);
        });

        // Create Students
        $siswaData = [
            ['name' => 'Siswa A', 'email' => 'siswaA@test.test', 'nis_nip' => '12345', 'class_id' => $kelas10A->id, 'jenis_kelamin' => 'L'],
            ['name' => 'Siswa B', 'email' => 'siswaB@test.test', 'nis_nip' => '12346', 'class_id' => $kelas10A->id, 'jenis_kelamin' => 'P'],
            ['name' => 'Siswa C', 'email' => 'siswaC@test.test', 'nis_nip' => '12347', 'class_id' => $kelas10A->id, 'jenis_kelamin' => 'L'],
            ['name' => 'Siswa D', 'email' => 'siswaD@test.test', 'nis_nip' => '12348', 'class_id' => $kelas10B->id, 'jenis_kelamin' => 'P'],
            ['name' => 'Siswa E', 'email' => 'siswaE@test.test', 'nis_nip' => '12349', 'class_id' => $kelas10B->id, 'jenis_kelamin' => 'L'],
            ['name' => 'Siswa F', 'email' => 'siswaF@test.test', 'nis_nip' => '12350', 'class_id' => $kelas10B->id, 'jenis_kelamin' => 'P'],
        ];

        $studentIds = [];
        foreach ($siswaData as $data) {
            $siswa = User::firstOrCreate(
                ['email' => $data['email']],
                $data + [
                    'role' => 'siswa',
                    'password' => Hash::make('password'),
                ]
            );
            $studentIds[] = $siswa->id;
            $kelas10A->increment('student_count');
            $kelas10B->increment('student_count');
        }

        // Create Sesi Ujian
        $sesiPagi = SesiUjian::create([
            'nama_sesi' => 'Sesi Pagi',
            'ujian_id' => $ujian->id,
            'waktu_mulai' => now()->addDays(3)->setTime(8, 0),
            'waktu_selesai' => now()->addDays(3)->setTime(9, 30),
            'is_active' => true,
        ]);


        $sesiSiang = SesiUjian::create([
            'nama_sesi' => 'Sesi Siang',
            'ujian_id' => $ujian->id,
            'waktu_mulai' => now()->addDays(3)->setTime(13, 0),
            'waktu_selesai' => now()->addDays(3)->setTime(14, 30),
            'is_active' => true,
        ]);


        // Assign students to sesi (3 per sesi)
        $sesiPagi->students()->attach([$studentIds[0], $studentIds[1], $studentIds[2]]);
        $sesiSiang->students()->attach([$studentIds[3], $studentIds[4], $studentIds[5]]);

        $this->command->info('✅ SesiUjian demo data created!');
        $this->command->info("Guru: {$guru->email}/password");
        $this->command->info("Siswa: siswaA-F@test.test/password");
        $this->command->info("Test: http://127.0.0.1:8000/guru/sesi-ujian");
    }
}
?>

