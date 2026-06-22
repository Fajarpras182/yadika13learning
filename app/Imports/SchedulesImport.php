<?php

namespace App\Imports;

use App\Models\Schedule;
use App\Models\Course;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchedulesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $courseId = null;
        if (!empty($row['course']) || !empty($row['mata_pelajaran'])) {
            $name = $row['course'] ?? $row['mata_pelajaran'];
            $courseId = Course::where('nama_mata_pelajaran', $name)->value('id');
        }
        $classId = null;
        if (!empty($row['class']) || !empty($row['kelas'])) {
            $name = $row['class'] ?? $row['kelas'];
            $classId = SchoolClass::where('name', $name)->value('id');
        }

        return new Schedule([
            'course_id' => $courseId,
            'class_id' => $classId,
            'day' => $row['day'] ?? $row['hari'] ?? null,
            'start_time' => $row['start_time'] ?? $row['mulai'] ?? null,
            'end_time' => $row['end_time'] ?? $row['selesai'] ?? null,
            'room' => $row['room'] ?? $row['ruang'] ?? null,
            'is_active' => isset($row['status']) ? in_array(strtolower($row['status']), ['aktif','active','1','true']) : true,
        ]);
    }
}


