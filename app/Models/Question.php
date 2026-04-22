<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'course_id',
        'pertanyaan',
        'jawaban_a',
        'jawaban_b',
        'jawaban_c',
        'jawaban_d',
        'jawaban_e',
        'kunci_jawaban',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ujian_id' => 'integer',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function answers()
    {
        return $this->hasMany(UjianAnswer::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(UjianAnswer::class);
    }
}
