<div class="review-container">
    <div class="mb-4">
        <h6 class="border-bottom pb-3">
            <strong>{{ $result->ujian->judul }}</strong><br>
            <small class="text-muted">{{ $result->ujian->course->nama_mata_pelajaran }}</small>
        </h6>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <p><strong>Siswa:</strong> {{ $result->student->name }} ({{ $result->student->nis_nip }})</p>
                <p><strong>Skor:</strong> {{ number_format($result->score, 2) }} / {{ $result->ujian->bobot_nilai }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Persentase:</strong> {{ round(($result->score / $result->ujian->bobot_nilai) * 100, 2) }}%</p>
                <p><strong>Waktu Selesai:</strong> {{ $result->end_time ? $result->end_time->format('d/m/Y H:i') : '-' }}</p>
            </div>
        </div>
    </div>

    <div class="score-summary mb-4">
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="p-3 bg-success bg-opacity-10 rounded">
                    <h5 class="text-success mb-0">{{ $correctCount }}</h5>
                    <small class="text-muted">Jawaban Benar</small>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3 bg-danger bg-opacity-10 rounded">
                    <h5 class="text-danger mb-0">{{ $totalCount - $correctCount }}</h5>
                    <small class="text-muted">Jawaban Salah</small>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="p-3 bg-info bg-opacity-10 rounded">
                    <h5 class="text-info mb-0">{{ $totalCount }}</h5>
                    <small class="text-muted">Total Soal</small>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="questions-review">
        @foreach($questions as $question)
        @php
            $answer = $question->studentAnswers->first();
            $isCorrect = $answer && $answer->is_correct;
        @endphp
        <div class="question-item mb-4 p-3 border rounded {{ $isCorrect ? 'border-success' : 'border-danger' }}">
            <div class="question-header mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-1">
                        <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }}">
                            Soal {{ $loop->iteration }}
                        </span>
                    </h6>
                    <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }}">
                        {{ $isCorrect ? '✓ Benar' : '✗ Salah' }}
                    </span>
                </div>
            </div>

            <div class="question-text mb-3">
                <div class="mb-0">{!! $question->pertanyaan !!}</div>
            </div>

            <div class="options-review">
                @php
                    $options = [
                        'a' => $question->jawaban_a,
                        'b' => $question->jawaban_b,
                        'c' => $question->jawaban_c,
                        'd' => $question->jawaban_d,
                        'e' => $question->jawaban_e,
                    ];
                @endphp

                @foreach($options as $key => $optionText)
                @if($optionText)
                @php
                    $isStudentAnswer = ($answer && strtoupper($answer->selected_answer) === strtoupper($key));
                    $isCorrectAnswer = (strtoupper($question->kunci_jawaban) === strtoupper($key));
                @endphp
                <div class="option-item p-2 mb-2 rounded {{ $isCorrectAnswer ? 'bg-success bg-opacity-10 border border-success' : ($isStudentAnswer && !$isCorrect ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-light') }}">
                    <div class="d-flex align-items-start">
                        <span class="badge {{ $isStudentAnswer ? 'bg-dark' : 'bg-secondary' }} me-2">{{ strtoupper($key) }}</span>
                        <div class="flex-grow-1">
                            <div class="mb-0 {{ $isStudentAnswer ? 'fw-bold text-dark' : '' }}">
                                {!! $optionText !!}
                            </div>
                            <div class="mt-1">
                                @if($isCorrectAnswer)
                                    <small class="text-success fw-bold"><i class="fas fa-check-circle"></i> Jawaban Benar</small>
                                @endif
                                @if($isStudentAnswer)
                                    <small class="{{ $isCorrect ? 'text-success' : 'text-danger' }} fw-bold">
                                        <i class="fas {{ $isCorrect ? 'fa-user-check' : 'fa-user-times' }}"></i> Jawaban Siswa
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            {{-- Pembahasan if exists --}}
            @if($question->pembahasan)
            <div class="pembahasan-section mt-3 p-3 bg-info bg-opacity-10 border border-info rounded">
                <h6 class="text-info mb-2"><i class="fas fa-lightbulb"></i> Pembahasan:</h6>
                <p class="mb-0">{!! $question->pembahasan !!}</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<style>
.review-container {
    max-height: 600px;
    overflow-y: auto;
}

.question-item {
    transition: all 0.3s ease;
}

.question-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.option-item {
    cursor: pointer;
    transition: all 0.2s ease;
}

.option-item:hover {
    transform: translateX(5px);
}

.score-summary .p-3 {
    background-color: rgba(0,0,0,0.02);
}
</style>
