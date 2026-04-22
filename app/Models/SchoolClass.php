<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'major_id',
        'year',
        'homeroom_teacher',
        'is_active',
        'student_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'student_count' => 'integer',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function students()
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'class_id');
    }
}
