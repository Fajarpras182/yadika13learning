<?php

namespace App\Services\Exam;

use App\Models\Soal;
use App\Models\UjianAnswer;

class OptionRandomizerService
{
    /**
     * Menghasilkan urutan acak opsi (a, b, c, d) untuk satu soal.
     * Urutan ini unik per siswa dan disimpan di ujian_answers.options_order.
     *
     * @param Soal $soal
     * @return array  misal: ['c', 'a', 'd', 'b']
     */
    public function generateRandomOrder(Soal $soal): array
    {
        if (!$soal->is_random_options) {
            return ['a', 'b', 'c', 'd'];
        }

        $options = ['a', 'b', 'c', 'd'];
        shuffle($options);

        return $options;
    }

    /**
     * Menyiapkan seluruh soal untuk satu sesi ujian siswa.
     * Menginisialisasi record UjianAnswer dengan urutan opsi acak.
     *
     * @param int   $ujianResultId  ID dari ujian_results
     * @param array $soalIds        Array ID soal dari bank soal
     * @return void
     */
    public function initializeAnswersWithRandomOptions(int $ujianResultId, array $soalIds): void
    {
        foreach ($soalIds as $soalId) {
            $soal = Soal::find($soalId);
            if (!$soal) continue;

            $randomOrder = $this->generateRandomOrder($soal);

            // Upsert: jika sudah ada (misal dari auto-save sebelumnya), update options_order
            UjianAnswer::updateOrCreate(
                [
                    'ujian_result_id' => $ujianResultId,
                    'question_id'     => $soalId,
                ],
                [
                    'options_order'    => $randomOrder,
                    'selected_answer'  => null,
                    'is_doubtful'      => false,
                    'is_correct'       => false,
                    'points'           => 0,
                ]
            );
        }
    }

    /**
     * Mengambil teks opsi berdasarkan urutan acak yang sudah disimpan.
     * Digunakan di frontend untuk menampilkan soal dengan urutan berbeda per siswa.
     *
     * @param Soal  $soal
     * @param array $optionsOrder  misal: ['c', 'a', 'd', 'b']
     * @return array  Array asosiatif label => teks opsi
     */
    public function getShuffledOptions(Soal $soal, array $optionsOrder): array
    {
        $labels = ['A', 'B', 'C', 'D'];
        $result = [];

        foreach ($optionsOrder as $index => $originalKey) {
            $result[] = [
                'label'        => $labels[$index],      // Label tampilan: A, B, C, D
                'original_key' => $originalKey,         // Key asli: a, b, c, d
                'text'         => $soal->getOpsiByHuruf($originalKey),
            ];
        }

        return $result;
    }
}
