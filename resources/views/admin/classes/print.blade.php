<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Kelas</title>
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
    <h3>Daftar Kelas</h3>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Wali Kelas</th>
                <th>Angkatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classes as $class)
            <tr>
                <td>{{ $class->name }}</td>
                <td>{{ $class->major->name ?? '-' }}</td>
                <td>{{ $class->homeroom_teacher ?? '-' }}</td>
                <td>{{ $class->year ?? '-' }}</td>
                <td>{{ $class->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


