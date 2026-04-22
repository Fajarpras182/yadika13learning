<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_jawaban',
        'jawaban_text',
        'nilai',
        'feedback',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    // Relasi dengan assignment
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Relasi dengan student
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
