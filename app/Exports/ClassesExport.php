<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows->map(function ($row) {
            return [
                'name' => $row->name,
                'major' => $row->major->name ?? '',
                'homeroom_teacher' => $row->homeroom_teacher,
                'year' => $row->year,
                'is_active' => $row->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Jurusan', 'Wali Kelas', 'Angkatan', 'Status'];
    }
}


