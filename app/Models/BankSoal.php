<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model BankSoal
 *
 * Merepresentasikan satu kumpulan soal yang dimiliki oleh seorang Guru
 * untuk mata pelajaran dan kelas tertentu.
 *
 * Single Source of Truth: Admin dan Guru membaca dari tabel ini yang sama.
 * Guru hanya bisa mengakses baris di mana guru_id = auth()->id().
 * Admin bisa membaca semua baris tanpa batasan.
 *
 * @property int    $id
 * @property int    $guru_id
 * @property string $nama_mata_pelajaran
 * @property string $kelas
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read User $guru
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Soal> $soals
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Ujian> $ujians
 */
class BankSoal extends Model
{
    use HasFactory;

    protected $table = 'bank_soal';

    protected $fillable = [
        'guru_id',
        'nama_mata_pelajaran',
        'kelas',
        'deskripsi',
    ];

    // ----------------------------------------------------------------
    // Relasi
    // ----------------------------------------------------------------

    /**
     * Guru pemilik bank soal ini.
     * BankSoal belongsTo User (role: guru).
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Seluruh butir soal di dalam bank soal ini.
     * BankSoal hasMany Soal.
     */
    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'bank_soal_id');
    }

    /**
     * Ujian-ujian yang menggunakan bank soal ini.
     * BankSoal hasMany Ujian.
     */
    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class, 'bank_soal_id');
    }

    /**
     * Wacana/teks bacaan yang ada di bank soal ini.
     * BankSoal hasMany Wacana.
     */
    public function wacanas(): HasMany
    {
        return $this->hasMany(\App\Models\Wacana::class, 'bank_soal_id');
    }

    // ----------------------------------------------------------------
    // Helper / Accessor
    // ----------------------------------------------------------------

    /**
     * Jumlah soal yang tersedia di bank ini.
     */
    public function getTotalSoalAttribute(): int
    {
        return $this->soals()->count();
    }
}
