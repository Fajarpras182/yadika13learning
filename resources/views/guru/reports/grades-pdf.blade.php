<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Tugas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
            word-break: break-word;
            word-wrap: break-word;
            max-width: 0;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Nilai Tugas</h1>
        <p>Tanggal: {{ now()->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="mata-pelajaran">Mata Pelajaran</th>
                <th class="tugas">Tugas</th>
                <th class="nama-siswa">Nama Siswa</th>
                <th class="nilai">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gradeData as $index => $grade)
            <tr>
                <td class="mata-pelajaran">{{ $grade['mata_pelajaran'] }}</td>
                <td class="tugas">{{ $grade['tugas'] }}</td>
                <td class="nama-siswa">{{ $grade['nama_siswa'] }}</td>
                <td class="nilai">{{ $grade['nilai'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
