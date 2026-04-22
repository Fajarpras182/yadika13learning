<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GradesExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $courses;
    protected $guru;

    public function __construct($courses, $guru)
    {
        $this->courses = $courses;
        $this->guru = $guru;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->courses as $course) {
            foreach ($course->assignments as $assignment) {
                foreach ($assignment->grades as $grade) {
                    $data[] = [
                        'Mata Pelajaran' => $course->nama_mata_pelajaran,
                        'Nama Siswa' => $grade->student->name,
                        'Tugas' => $assignment->judul,
                        'Nilai' => $grade->nilai ?? '-',
                        'Tanggal Submit' => $grade->created_at ? $grade->created_at->format('d/m/Y') : '-',
                        'Status' => $grade->status ?? '-',
                    ];
                }
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Mata Pelajaran',
            'Nama Siswa',
            'Tugas',
            'Nilai',
            'Tanggal Submit',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Laporan Nilai Siswa';
    }
}
