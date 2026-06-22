<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Wacana (Reading Comprehension Passage)
 *
 * Merepresentasikan teks bacaan panjang yang dapat digunakan
 * oleh beberapa soal sekaligus (soal berantai).
 */
class Wacana extends Model
{
    use HasFactory;

    protected $table = 'wacanas';

    protected $fillable = [
        'bank_soal_id',
        'judul',
        'konten',
    ];

    /**
     * Bank soal induk dari wacana ini.
     */
    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    /**
     * Soal-soal yang terhubung ke wacana ini.
     */
    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'wacana_id');
    }
}
