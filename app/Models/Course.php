<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mata_pelajaran',
        'kode_mata_pelajaran',
        'deskripsi',
        'guru_id',
        'class_id',
        'major_id',
        'semester',
        'sks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi dengan guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Relasi dengan kelas
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Relasi dengan jurusan
    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    // Relasi dengan siswa
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_student', 'course_id', 'student_id');
    }

    // Relasi dengan lessons
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Relasi dengan assignments
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }



    // Relasi dengan attendances
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relasi dengan schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
