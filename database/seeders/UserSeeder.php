<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@smkyadika13.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'nis_nip' => 'ADM001',
            'is_active' => true,
        ]);

        // Guru users
        $gurus = [
            [
                'name' => 'Budi Santoso, S.Pd',
                'email' => 'budi.santoso@smkyadika13.sch.id',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'nis_nip' => 'GUR001',
                'no_hp' => '081234567890',
                'is_active' => true,
            ],
            [
                'name' => 'Siti Nurhaliza, S.Pd',
                'email' => 'siti.nurhaliza@smkyadika13.sch.id',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'nis_nip' => 'GUR002',
                'no_hp' => '081234567891',
                'is_active' => true,
            ],
            [
                'name' => 'Ahmad Wijaya, S.T',
                'email' => 'ahmad.wijaya@smkyadika13.sch.id',
                'password' => Hash::make('guru123'),
                'role' => 'guru',
                'nis_nip' => 'GUR003',
                'no_hp' => '081234567892',
                'is_active' => true,
            ],
        ];

        foreach ($gurus as $guru) {
            User::create($guru);
        }

        // Siswa users
        $siswas = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@smkyadika13.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nis_nip' => 'SIS001',
                'kelas' => 'XII',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Raya No. 123, Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Sari Indira',
                'email' => 'sari.indira@smkyadika13.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nis_nip' => 'SIS002',
                'kelas' => 'XII',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Merdeka No. 456, Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Rizki Ramadhan',
                'email' => 'rizki.ramadhan@smkyadika13.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nis_nip' => 'SIS003',
                'kelas' => 'XI',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567895',
                'alamat' => 'Jl. Sudirman No. 789, Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Dewi Kartika',
                'email' => 'dewi.kartika@smkyadika13.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nis_nip' => 'SIS004',
                'kelas' => 'XI',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567896',
                'alamat' => 'Jl. Gatot Subroto No. 321, Jakarta',
                'is_active' => true,
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@smkyadika13.sch.id',
                'password' => Hash::make('siswa123'),
                'role' => 'siswa',
                'nis_nip' => 'SIS005',
                'kelas' => 'X',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567897',
                'alamat' => 'Jl. Thamrin No. 654, Jakarta',
                'is_active' => true,
            ],
        ];

        foreach ($siswas as $siswa) {
            User::create($siswa);
        }
    }
}
