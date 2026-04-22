<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoursesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $guruId = null;
        if (!empty($row['guru'])) {
            $guruId = User::where('role','guru')->where('name', $row['guru'])->value('id');
        }

        return new Course([
            'nama_mata_pelajaran' => $row['nama'] ?? $row['mata_pelajaran'] ?? null,
            'kode_mata_pelajaran' => $row['kode'] ?? null,
            'deskripsi' => $row['deskripsi'] ?? null,
            'guru_id' => $guruId,
            'kelas' => $row['kelas'] ?? null,
            'jurusan' => $row['jurusan'] ?? null,
            'semester' => $row['semester'] ?? null,
            'sks' => $row['sks'] ?? 2,
            'is_active' => isset($row['status']) ? in_array(strtolower($row['status']), ['aktif','active','1','true']) : true,
        ]);
    }
}


