<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 180px;
            font-weight: bold;
            color: #333;
        }
        
        .info-value {
            flex: 1;
            color: #333;
        }
        
        .score-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .score-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .score-row:last-child {
            margin-bottom: 0;
        }
        
        .results-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
        }
        
        .question-item {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        
        .question-number {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .option {
            margin-left: 20px;
            margin-bottom: 4px;
            font-size: 12px;
        }
        
        .option.correct {
            color: #333;
            font-weight: bold;
        }
        
        .option.wrong {
            color: #333;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        .footer-note {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">Terima Kasih Anda Telah Mengikuti Ujian Online</div>
        </div>

        <!-- Student & Exam Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">NIS :</span>
                <span class="info-value">{{ $user->nis_nip ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nama :</span>
                <span class="info-value">{{ strtoupper($user->name) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Mulai :</span>
                <span class="info-value">{{ $result->start_time->format('Y-m-d H:i') }} - {{ $result->end_time->format('Y-m-d H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Matakuliah :</span>
                <span class="info-value">{{ strtoupper($sesi->ujian->course->nama_mata_pelajaran ?? 'N/A') }}</span>
            </div>
        </div>

        <!-- Score Info -->
        <div class="score-section">
            <div class="score-row">
                <span>Jumlah Soal :</span>
                <strong>{{ $totalQuestions }}</strong>
            </div>
            <div class="score-row">
                <span>Jumlah Benar :</span>
                <strong>{{ $correctCount }}</strong>
            </div>
            <div class="score-row">
                <span>Jumlah Salah :</span>
                <strong>{{ $totalQuestions - $correctCount }}</strong>
            </div>
        </div>

        <!-- Timing Info -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Waktu Mulai :</span>
                <span class="info-value">{{ $result->start_time->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu Selesai :</span>
                <span class="info-value">{{ $result->end_time->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Durasi Pengerjaan :</span>
                <span class="info-value">{{ $result->time_taken_minutes }} menit</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Cetak :</span>
                <span class="info-value">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Questions & Answers -->
        <div class="results-title">PEMBAHASAN SOAL</div>

        @foreach ($answers as $index => $answer)
            <div class="question-item">
                <div class="question-number">
                    {{ $index + 1 }}. {{ strip_tags($answer->question->pertanyaan) }}
                </div>
                
                @php
                    $options = [
                        'A' => $answer->question->jawaban_a,
                        'B' => $answer->question->jawaban_b,
                        'C' => $answer->question->jawaban_c,
                        'D' => $answer->question->jawaban_d,
                        'E' => $answer->question->jawaban_e ?? null,
                    ];
                    $correctAnswer = $answer->question->kunci_jawaban;
                    $selectedAnswer = $answer->selected_answer;
                @endphp

                @foreach ($options as $option => $text)
                    @if ($text)
                        @if ($option === $correctAnswer)
                            <div class="option correct">
                                <strong>{{ $option }}. {{ strip_tags($text) }}</strong>
                            </div>
                        @else
                            <div class="option">
                                {{ $option }}. {{ strip_tags($text) }}
                            </div>
                        @endif
                    @endif
                @endforeach
            </div>

            @if(($index + 1) % 8 === 0 && $index + 1 !== count($answers))
                <div class="page-break"></div>
            @endif
        @endforeach

        <!-- Footer -->
        <div class="footer">
            <div class="footer-note">Dokumen ini merupakan hasil resmi dari sistem ujian online SMK Yadika 13 Tambun Utara, Simpan sebagai bukti bahwa anda telah mengikuti ujian online</div>
            <div>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
</body>
</html>
