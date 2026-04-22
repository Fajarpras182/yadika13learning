<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih Telah Mengikuti Ujian Online</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }
        
        .container {
            width: 210mm;
            height: 297mm;
            padding: 15mm 15mm;
            display: flex;
            flex-direction: column;
            page-break-after: avoid;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10mm;
        }
        
        .header-title {
            font-size: 14px;
            font-weight: bold;
            color: #555;
            line-height: 1.4;
        }
        
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10mm;
            page-break-inside: avoid;
        }
        
        .info-table tr {
            page-break-inside: avoid;
        }
        
        .info-table td {
            padding: 4mm 4mm;
            border: 1px solid #999;
            font-size: 10px;
            height: 6mm;
            vertical-align: middle;
        }
        
        .info-table td:first-child {
            background-color: #cccccc;
            font-weight: bold;
            width: 30%;
            color: #333;
            word-break: break-word;
        }
        
        .info-table td:last-child {
            background-color: #ffffff;
            color: #333;
            word-wrap: break-word;
            word-break: break-word;
        }
        
        .footer-text {
            text-align: center;
            margin-top: auto;
            padding-top: 8mm;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            page-break-inside: avoid;
        }
        
        .footer-text strong {
            font-weight: bold;
            color: #000;
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
            </table>
        </div>

        <!-- Footer Text -->
        <div class="footer-text">
            <strong>Simpan sebagai bukti anda telah mengikuti ujian online</strong>
        </div>
    </div>
</body>
</html>
