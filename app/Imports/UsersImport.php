<?php

namespace App\Imports;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $email = $row['email'] ?? null;
        if (!$email) {
            return null; // Skip rows without email
        }

        $kelas = $row['kelas'] ?? null;
        $classId = null;

        // Find the class_id based on kelas name
        if ($kelas) {
            $schoolClass = SchoolClass::where('name', $kelas)->first();
            if ($schoolClass) {
                $classId = $schoolClass->id;
            }
        }

        $attributes = [
            'name' => $row['name'] ?? $row['nama'] ?? null,
            'password' => isset($row['password']) ? Hash::make($row['password']) : Hash::make('password123'),
            'role' => $row['role'] ?? 'guru', // Default to 'guru' for teacher imports
            'nis_nip' => $row['nis_nip'] ?? null,
            'kelas' => $kelas,
            'class_id' => $classId,
            'jurusan' => $row['jurusan'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'is_active' => isset($row['status']) ? in_array(strtolower($row['status']), ['aktif','active','1','true']) : true,
        ];

        return User::updateOrCreate(['email' => $email], $attributes);
    }
}


