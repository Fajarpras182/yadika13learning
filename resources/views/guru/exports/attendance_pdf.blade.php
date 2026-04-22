<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran - {{ $course->nama_mata_pelajaran }}</title>
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
            border: 1px solid #0952efff;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
        }
        .summary h3 {
            margin-bottom: 15px;
        }
        .status-hadir { color: green; }
        .status-izin { color: orange; }
        .status-sakit { color: blue; }
        .status-alpa { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Kehadiran</h1>
        <p><strong>Mata Pelajaran:</strong> {{ $course->nama_mata_pelajaran }}</p>
        <p><strong>Kelas:</strong> {{ $course->schoolClass->name ?? $course->kelas ?? '-' }} - {{ $course->schoolClass->major->name ?? $course->jurusan ?? '-' }}</p>
        <p><strong>Semester:</strong> {{ $course->semester }}</p>
        <p><strong>Tanggal Export:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpa</th>
                <th>Total Kehadiran</th>
                <th>Presentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                @php
                    $studentAttendance = $attendanceSummary->get($student->id, collect());
                    $hadir = $studentAttendance->where('status', 'hadir')->sum('count');
                    $izin = $studentAttendance->where('status', 'izin')->sum('count');
                    $sakit = $studentAttendance->where('status', 'sakit')->sum('count');
                    $alpa = $studentAttendance->where('status', 'alpa')->sum('count');
                    $total = $hadir + $izin + $sakit + $alpa;
                    $percentage = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                    <td class="status-hadir">{{ $hadir }}</td>
                    <td class="status-izin">{{ $izin }}</td>
                    <td class="status-sakit">{{ $sakit }}</td>
                    <td class="status-alpa">{{ $alpa }}</td>
                    <td>{{ $total }}</td>
                    <td>{{ $percentage }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Ringkasan Kehadiran Kelas</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalHadir = $attendanceSummary->flatten(1)->where('status', 'hadir')->sum('count');
                    $totalIzin = $attendanceSummary->flatten(1)->where('status', 'izin')->sum('count');
                    $totalSakit = $attendanceSummary->flatten(1)->where('status', 'sakit')->sum('count');
                    $totalAlpa = $attendanceSummary->flatten(1)->where('status', 'alpa')->sum('count');
                    $grandTotal = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;
                @endphp
                <tr>
                    <td class="status-hadir">Hadir</td>
                    <td>{{ $totalHadir }}</td>
                    <td>{{ $grandTotal > 0 ? round(($totalHadir / $grandTotal) * 100, 2) : 0 }}%</td>
                </tr>
                <tr>
                    <td class="status-izin">Izin</td>
                    <td>{{ $totalIzin }}</td>
                    <td>{{ $grandTotal > 0 ? round(($totalIzin / $grandTotal) * 100, 2) : 0 }}%</td>
                </tr>
                <tr>
                    <td class="status-sakit">Sakit</td>
                    <td>{{ $totalSakit }}</td>
                    <td>{{ $grandTotal > 0 ? round(($totalSakit / $grandTotal) * 100, 2) : 0 }}%</td>
                </tr>
                <tr>
                    <td class="status-alpa">Alpa</td>
                    <td>{{ $totalAlpa }}</td>
                    <td>{{ $grandTotal > 0 ? round(($totalAlpa / $grandTotal) * 100, 2) : 0 }}%</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td>Total</td>
                    <td>{{ $grandTotal }}</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
