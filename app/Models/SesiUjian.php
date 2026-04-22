<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiUjian extends Model
{
    use HasFactory;

    protected $table = 'sesi_ujians';

    protected $fillable = [
        'nama_sesi',
        'ujian_id',
        'waktu_mulai',
        'waktu_selesai',
        'is_active',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'sesi_ujian_student', 'sesi_ujian_id', 'student_id')
            ->where('role', 'siswa');
    }

    public function ujianResults(): HasMany
    {
        return $this->hasMany(UjianResult::class, 'sesi_ujian_id');
    }
}

