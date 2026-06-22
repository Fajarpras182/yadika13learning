<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'judul',
        'deskripsi',
        'instruksi',
        'file_tugas',
        'deadline',
        'bobot_nilai',
        'is_active',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relasi dengan course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi dengan grades
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
