<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Jurusan</title>
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
    <h3>Daftar Jurusan</h3>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($majors as $major)
            <tr>
                <td>{{ $major->code }}</td>
                <td>{{ $major->name }}</td>
                <td>{{ $major->description }}</td>
                <td>{{ $major->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


