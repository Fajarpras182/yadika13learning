<?php

namespace App\Imports;

use App\Models\Major;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MajorsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Major([
            'code' => $row['code'] ?? $row['kode'] ?? null,
            'name' => $row['name'] ?? $row['nama'] ?? null,
            'description' => $row['description'] ?? $row['deskripsi'] ?? null,
            'is_active' => isset($row['status']) ? in_array(strtolower($row['status']), ['aktif','active','1','true']) : true,
        ]);
    }
}


