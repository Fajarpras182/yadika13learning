<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Mata Pelajaran</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h3 { text-align: center; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f0f0f0; }
    </style>
    <script>
        window.onload = () => window.print();
    </script>
    </head>
<body>
    <h3>Daftar Mata Pelajaran</h3>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Guru</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Semester</th>
                <th>SKS</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $c)
            <tr>
                <td>{{ $c->kode_mata_pelajaran }}</td>
                <td>{{ $c->nama_mata_pelajaran }}</td>
                <td>{{ $c->guru->name ?? '-' }}</td>
                <td>{{ $c->kelas }}</td>
                <td>{{ $c->jurusan }}</td>
                <td>{{ $c->semester }}</td>
                <td>{{ $c->sks }}</td>
                <td>{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


