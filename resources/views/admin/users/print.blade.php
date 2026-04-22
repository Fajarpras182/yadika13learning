<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Users</title>
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
    <h3>Daftar Users</h3>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>NIS/NIP</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>No HP</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ ucfirst($u->role) }}</td>
                <td>{{ $u->nis_nip }}</td>
                <td>{{ $u->kelas }}</td>
                <td>{{ $u->jurusan }}</td>
                <td>{{ $u->no_hp }}</td>
                <td>{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


