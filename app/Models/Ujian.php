<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujians';

    protected $fillable = [
        'judul',
        'course_id',
        'guru_id',
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

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function sesiUjians(): HasMany
    {
        return $this->hasMany(SesiUjian::class, 'ujian_id');
    }

    public function ujianResults(): HasMany
    {
        return $this->hasMany(UjianResult::class, 'ujian_id');
    }
}
