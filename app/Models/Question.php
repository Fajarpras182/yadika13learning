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
        'guru_id',       // ID guru pemilik soal — digunakan untuk filter RBAC
        'created_by',    // Alias/fallback untuk guru_id (legacy support)
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
        'ujian_id'  => 'integer',
        'guru_id'   => 'integer',
    ];

    // Pastikan guru_id selalu sinkron dengan created_by saat data disimpan
    protected static function booted()
    {
        static::creating(function ($question) {
            // Jika guru_id ada tapi created_by tidak, isi created_by
            if ($question->guru_id && ! $question->created_by) {
                $question->created_by = $question->guru_id;
            }
            // Jika created_by ada tapi guru_id tidak, isi guru_id
            if ($question->created_by && ! $question->guru_id) {
                $question->guru_id = $question->created_by;
            }
        });
    }

    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Guru pemilik soal ini (berdasarkan guru_id).
     */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
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
