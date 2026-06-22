<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchedulesExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows->map(function ($row) {
            return [
                'day' => $row->day,
                'time' => $row->start_time . ' - ' . $row->end_time,
                'course' => $row->course->nama_mata_pelajaran ?? '',
                'class' => $row->schoolClass->name ?? '',
                'room' => $row->room,
                'is_active' => $row->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    public function headings(): array
    {
        return ['Hari', 'Waktu', 'Mata Pelajaran', 'Kelas', 'Ruang', 'Status'];
    }
}


