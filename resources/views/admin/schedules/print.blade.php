<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Jadwal</title>
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
    <h3>Daftar Jadwal</h3>
    <table>
        <thead>
            <tr>
                <th>Hari</th>
                <th>Waktu</th>
                <th>Mata Pelajaran</th>
                <th>Kelas</th>
                <th>Ruang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $s)
            <tr>
                <td>{{ $s->day }}</td>
                <td>{{ $s->start_time }} - {{ $s->end_time }}</td>
                <td>{{ $s->course->nama_mata_pelajaran ?? '-' }}</td>
                <td>{{ $s->schoolClass->name ?? '-' }}</td>
                <td>{{ $s->room ?? '-' }}</td>
                <td>{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


