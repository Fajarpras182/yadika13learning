<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujians';

    protected $fillable = [
        'judul',
        'course_id',
        'guru_id',
        'bank_soal_id',   // FK ke bank soal (Single Source of Truth)
        'class_ids',
        'tanggal_ujian',
        'durasi_menit',
        'bobot_nilai',
        'soal_acak',
        'jawaban_acak',
        'tampilkan_hasil',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'class_ids' => 'array',
        'tanggal_ujian' => 'datetime',
        'durasi_menit' => 'integer',
        'bobot_nilai' => 'integer',
        'soal_acak' => 'boolean',
        'jawaban_acak' => 'boolean',
        'tampilkan_hasil' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /**
     * Bank soal yang menjadi sumber soal untuk ujian ini.
     * Admin memilih bank_soal_id saat membuat ujian.
     */
    public function bankSoal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    /**
     * Semua soal yang terkait langsung dengan ujian ini.
     * 
     * Soal terikat ke ujian melalui field ujian_id di table questions.
     * Ketika soal ditambahkan ke ujian, ujian_id akan di-set secara otomatis.
     * Dengan demikian:
     *   - $ujian->questions()->count() → jumlah soal yang di-assign ke ujian ini ✅
     *   - $ujian->questions → koleksi soal untuk ujian ini ✅
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'course_id', 'course_id');
    }

    public function sesiUjians(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'ujian_id');
    }

    public function results(): HasManyThrough
    {
        return $this->hasManyThrough(UjianResult::class, SesiUjian::class, 'ujian_id', 'sesi_ujian_id', 'id', 'id');
    }
}
