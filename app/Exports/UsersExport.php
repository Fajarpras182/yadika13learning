<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows->map(function ($u) {
            return [
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'nis_nip' => $u->nis_nip,
                'kelas' => $u->kelas,
                'jurusan' => $u->jurusan,
                'no_hp' => $u->no_hp,
                'is_active' => $u->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Email', 'Role', 'NIS/NIP', 'Kelas', 'Jurusan', 'No HP', 'Status'];
    }
}


