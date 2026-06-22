<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih Telah Mengikuti Ujian Online</title>
    <style>
        @page {
            margin: 1cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        
        .container {
            width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8mm;
            border-bottom: 2px solid #444;
            padding-bottom: 3mm;
        }
        
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }
        
        .content {
            width: 100%;
        }
        
        .info-table {
            width: 85%;
            margin: 15mm auto;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #333;
        }
        
        .info-table td {
            padding: 3.5mm 5mm;
            border: 1px solid #ccc;
            font-size: 12px;
            vertical-align: middle;
        }
        
        .info-table td:first-child {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 40%;
            color: #333;
            border-right: 1px solid #333;
        }
        
        .info-table td:last-child {
            background-color: #ffffff;
            width: 60%;
            word-wrap: break-word;
            font-weight: 500;
        }

        .footer-text {
            text-align: center;
            margin-top: 20mm;
            padding-top: 5mm;
            border-top: 1px dashed #999;
            font-size: 11px;
            color: #444;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">Terima Kasih Anda Telah Mengikuti Ujian Online</div>
        </div>

        <!-- Content -->
        <div class="content">
            <table class="info-table">
                <tr>
                    <td>NIS</td>
                    <td>{{ $user->nis_nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>{{ strtoupper($user->name ?? '-') }}</td>
                </tr>
                <tr>
                    <td>Tanggal Mulai</td>
                    <td>{{ $sesi->waktu_mulai->format('Y-m-d H:i') }} - {{ $sesi->waktu_selesai->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <td>Mata Pelajaran</td>
                    <td>{{ strtoupper($sesi->ujian->course->nama_mata_pelajaran ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td>Jumlah Soal</td>
                    <td>{{ $totalQuestions }}</td>
                </tr>
                <tr>
                    <td>Jumlah Benar</td>
                    <td>{{ $correctCount }}</td>
                </tr>
                <tr>
                    <td>Jumlah Salah</td>
                    <td>{{ $totalQuestions - $correctCount }}</td>
                </tr>
                <tr>
                    <td>Skor</td>
                    <td>{{ number_format($result->score, 2) }} / {{ $sesi->ujian->bobot_nilai }}</td>
                </tr>
                <tr>
                    <td>Persentase</td>
                    <td>{{ $percentageScore }}%</td>
                </tr>
            </table>
        </div>

        <!-- Footer Text -->
        <div class="footer-text">
            <strong>Simpan sebagai bukti anda telah mengikuti ujian online</strong>
        </div>
    </div>
</body>
</html>
