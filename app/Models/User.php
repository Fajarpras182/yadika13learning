<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\SesiUjian;
use App\Models\UjianResult;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nis_nip',
        'jenis_kelamin',
        'kelas',
        'jurusan',
        'no_hp',
        'alamat',
        'is_active',
        'photo',
        'agama',
        'class_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'kelas' => 'array',
    ];

    // Relasi untuk guru
    public function courses()
    {
        return $this->hasMany(Course::class, 'guru_id');
    }

    // Relasi untuk siswa
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id');
    }

    // Relasi untuk nilai
    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    // Relasi untuk absensi sebagai siswa
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    // Relasi untuk absensi sebagai guru
    public function teacherAttendances()
    {
        return $this->hasMany(Attendance::class, 'guru_id');
    }

    // Relasi untuk kelas
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    // Relasi Sesi Ujian
    public function sesiUjians()
    {
        return $this->belongsToMany(SesiUjian::class, 'sesi_ujian_student', 'student_id', 'sesi_ujian_id');
    }

    // Relasi Ujian Results
    public function ujianResults()
    {
        return $this->hasMany(UjianResult::class, 'student_id');
    }

    // Helper methods untuk role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGuru()
    {
        return $this->role === 'guru';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }
}

