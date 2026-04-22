<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'deskripsi',
        'materi',
        'file_materi',
        'video_url',
        'urutan',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Relasi dengan course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
