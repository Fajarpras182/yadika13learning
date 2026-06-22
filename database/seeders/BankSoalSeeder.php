<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankSoal;
use App\Models\Soal;
use App\Models\User;

/**
 * BankSoalSeeder
 *
 * Mengisi data contoh untuk tabel bank_soal dan soal.
 * Jalankan: php artisan db:seed --class=BankSoalSeeder
 */
class BankSoalSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil guru yang sudah ada, atau buat baru
        $guru1 = User::where('role', 'guru')->first();
        $guru2 = User::where('role', 'guru')->skip(1)->first();

        if (! $guru1) {
            $this->command->warn('Tidak ada user dengan role guru. Seeder dilewati.');
            return;
        }

        // ================================================================
        // Bank Soal 1 — Matematika Kelas X (milik guru1)
        // ================================================================
        $bankMatematika = BankSoal::create([
            'guru_id'              => $guru1->id,
            'nama_mata_pelajaran'  => 'Matematika',
            'kelas'                => 'X',
            'deskripsi'            => 'Bank soal Matematika untuk kelas X semester ganjil',
        ]);

        $soalMatematika = [
            [
                'teks_pertanyaan' => 'Hasil dari 2 + 2 × 5 adalah ...',
                'opsi_a'          => '20',
                'opsi_b'          => '12',
                'opsi_c'          => '10',
                'opsi_d'          => '14',
                'jawaban_benar'   => 'b',
            ],
            [
                'teks_pertanyaan' => 'Nilai dari √144 adalah ...',
                'opsi_a'          => '11',
                'opsi_b'          => '12',
                'opsi_c'          => '13',
                'opsi_d'          => '14',
                'jawaban_benar'   => 'b',
            ],
            [
                'teks_pertanyaan' => 'Jika 3x = 15, maka nilai x adalah ...',
                'opsi_a'          => '3',
                'opsi_b'          => '4',
                'opsi_c'          => '5',
                'opsi_d'          => '6',
                'jawaban_benar'   => 'c',
            ],
        ];

        foreach ($soalMatematika as $soal) {
            Soal::create(array_merge($soal, ['bank_soal_id' => $bankMatematika->id]));
        }

        // ================================================================
        // Bank Soal 2 — Bahasa Indonesia Kelas XI (milik guru1)
        // ================================================================
        $bankBahasaIndo = BankSoal::create([
            'guru_id'             => $guru1->id,
            'nama_mata_pelajaran' => 'Bahasa Indonesia',
            'kelas'               => 'XI',
            'deskripsi'           => 'Bank soal Bahasa Indonesia kelas XI',
        ]);

        Soal::create([
            'bank_soal_id'    => $bankBahasaIndo->id,
            'teks_pertanyaan' => 'Kata "pedagogi" berasal dari bahasa ...',
            'opsi_a'          => 'Latin',
            'opsi_b'          => 'Yunani',
            'opsi_c'          => 'Arab',
            'opsi_d'          => 'Belanda',
            'jawaban_benar'   => 'b',
        ]);

        // ================================================================
        // Bank Soal 3 — Fisika Kelas XII (milik guru2, jika ada)
        // ================================================================
        if ($guru2) {
            $bankFisika = BankSoal::create([
                'guru_id'             => $guru2->id,
                'nama_mata_pelajaran' => 'Fisika',
                'kelas'               => 'XII',
                'deskripsi'           => 'Bank soal Fisika kelas XII IPA',
            ]);

            Soal::create([
                'bank_soal_id'    => $bankFisika->id,
                'teks_pertanyaan' => 'Satuan SI untuk gaya adalah ...',
                'opsi_a'          => 'Watt',
                'opsi_b'          => 'Joule',
                'opsi_c'          => 'Newton',
                'opsi_d'          => 'Pascal',
                'jawaban_benar'   => 'c',
            ]);
        }

        $this->command->info('Bank Soal Seeder selesai dijalankan.');
    }
}
