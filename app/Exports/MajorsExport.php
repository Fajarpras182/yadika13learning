<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MajorsExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows->map(function ($row) {
            return [
                'code' => $row->code,
                'name' => $row->name,
                'description' => $row->description,
                'is_active' => $row->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    public function headings(): array
    {
        return ['Kode', 'Nama', 'Deskripsi', 'Status'];
    }
}


