<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoursesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows->map(function ($c) {
            return [
                'kode' => $c->kode_mata_pelajaran,
                'nama' => $c->nama_mata_pelajaran,
                'guru' => $c->guru->name ?? '',
                'kelas' => $c->kelas,
                'jurusan' => $c->jurusan,
                'semester' => $c->semester,
                'sks' => $c->sks,
                'status' => $c->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    public function headings(): array
    {
        return ['Kode', 'Nama', 'Guru', 'Kelas', 'Jurusan', 'Semester', 'SKS', 'Status'];
    }
}


