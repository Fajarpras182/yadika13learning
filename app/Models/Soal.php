<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Soal
 *
 * Merepresentasikan satu butir pertanyaan pilihan ganda.
 * Selalu terikat ke satu BankSoal (Single Source of Truth).
 *
 * Data ini TIDAK diduplikasi. Admin dan Guru membaca dari tabel yang sama.
 *
 * @property int    $id
 * @property int    $bank_soal_id
 * @property string $teks_pertanyaan
 * @property string $opsi_a
 * @property string $opsi_b
 * @property string $opsi_c
 * @property string $opsi_d
 * @property string $jawaban_benar  — nilai: 'a' | 'b' | 'c' | 'd'
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read BankSoal $bankSoal
 */
class Soal extends Model
{
    use HasFactory;

    protected $table = 'soal';

    protected $fillable = [
        'bank_soal_id',
        'wacana_id',
        'teks_pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'jawaban_benar',
        'is_random_options',
    ];

    protected $casts = [
        'is_random_options' => 'boolean',
    ];

    /**
     * Sembunyikan jawaban_benar saat serialisasi ke JSON
     * (misal: dikirim ke frontend saat ujian berlangsung).
     * Uncomment jika diperlukan.
     *
     * protected $hidden = ['jawaban_benar'];
     */

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Bank soal induk dari butir soal ini.
     */
    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    /**
     * Wacana (teks bacaan panjang) yang terkait dengan soal ini.
     * Nullable: soal bisa berdiri sendiri tanpa wacana.
     */
    public function wacana(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Wacana::class, 'wacana_id');
    }

    // ----------------------------------------------------------------
    // Helper / Accessor
    // ----------------------------------------------------------------

    /**
     * Kembalikan label huruf kunci jawaban dalam huruf besar.
     * Berguna untuk tampilan.
     */
    public function getJawabanBenarLabelAttribute(): string
    {
        return strtoupper($this->jawaban_benar);
    }

    /**
     * Kembalikan teks opsi jawaban berdasarkan huruf pilihan.
     * Contoh: $soal->getOpsiByHuruf('a') → isi opsi_a
     */
    public function getOpsiByHuruf(string $huruf): string
    {
        $kolom = 'opsi_' . strtolower($huruf);

        return $this->{$kolom} ?? '';
    }
}
