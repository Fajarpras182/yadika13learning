<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Major;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $majorId = null;
        if (!empty($row['major']) || !empty($row['jurusan'])) {
            $name = $row['major'] ?? $row['jurusan'];
            $majorId = Major::where('name', $name)->value('id');
        }

        return new SchoolClass([
            'name' => $row['name'] ?? $row['nama'] ?? null,
            'major_id' => $majorId,
            'year' => $row['year'] ?? $row['angkatan'] ?? null,
            'homeroom_teacher' => $row['homeroom_teacher'] ?? $row['wali_kelas'] ?? null,
            'is_active' => isset($row['status']) ? in_array(strtolower($row['status']), ['aktif','active','1','true']) : true,
        ]);
    }
}


