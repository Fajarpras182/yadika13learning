<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Nilai Ujian</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
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
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        .score-good { color: green; font-weight: bold; }
        .score-poor { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Nilai Ujian</h1>
        <p>Tanggal Cetak: {{ now()->format('d M Y H:i') }}</p>
        <p>Guru: {{ auth()->user()->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th>Ujian</th>
                <th>Siswa</th>
                <th>Skor</th>
                <th>Persentase</th>
                <th>Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($examResults as $result)
            <tr>
                <td>{{ $result->ujian->course->nama_mata_pelajaran ?? '-' }}</td>
                <td>{{ Str::limit($result->ujian->judul ?? '-', 30) }}</td>
                <td>{{ $result->student->name }} <br><small>{{ $result->student->nis_nip }}</small></td>
                <td class="{{ ($result->score / $result->ujian->bobot_nilai * 100) >= 70 ? 'score-good' : 'score-poor' }}">
                    {{ number_format($result->score, 2) }} / {{ $result->ujian->bobot_nilai }}
                </td>
                <td>{{ round(($result->score / $result->ujian->bobot_nilai) * 100, 1) }}%</td>
                <td>{{ $result->end_time ? $result->end_time->format('d/m H:i') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada data nilai ujian</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

