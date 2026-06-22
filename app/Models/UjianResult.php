<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class UjianResult extends Model
{
    use HasFactory;

    protected $table = 'ujian_results';

    protected $fillable = [
        'sesi_ujian_id',
        'student_id',
        'start_time',
        'end_time',
        'score',
        'total_questions',
        'time_taken_minutes',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'decimal:2',
        'time_taken_minutes' => 'integer',
        'status' => 'string',
    ];

    public function sesiUjian(): BelongsTo
    {
        return $this->belongsTo(SesiUjian::class, 'sesi_ujian_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UjianAnswer::class);
    }

    public function ujian(): HasOneThrough
    {
        return $this->hasOneThrough(
            Ujian::class,
            SesiUjian::class,
            'id',
            'id',
            'sesi_ujian_id',
            'ujian_id'
        );
    }
}

