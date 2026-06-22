<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'student_id',
        'tanggal',
        'status',
        'keterangan',
        'guru_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi dengan course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relasi dengan student
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relasi dengan guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
