@extends('layouts.app')

@section('title', 'Detail Soal - E-Learning SMK Yadika 13')
@section('page-title', 'Detail Soal')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-question-circle me-2"></i>Detail Soal
                    </h6>
                    <a href="{{ route('guru.bank-soal') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p>
                            <strong>Ujian:</strong><br>
                            {{ $question->ujian->judul ?? '-' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <strong>Mata Pelajaran:</strong><br>
                            {{ $question->course->nama_mata_pelajaran ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mb-4 p-4 bg-light rounded">
                    <h6 class="mb-3"><strong>{{ $question->nomor_soal ?? 1 }}. Pertanyaan</strong></h6>
                    <p class="mb-0">{{ $question->pertanyaan }}</p>
                </div>

                <div class="mb-4">
                    <h6 class="mb-3"><strong>Pilihan Jawaban</strong></h6>
                    
                    @php
                        $options = [
                            'A' => $question->jawaban_a,
                            'B' => $question->jawaban_b,
                            'C' => $question->jawaban_c,
                            'D' => $question->jawaban_d,
                            'E' => $question->jawaban_e,
                        ];
                    @endphp

                    @foreach($options as $key => $text)
                    @if($text)
                        <div class="p-3 mb-2 rounded {{ strtoupper($question->kunci_jawaban) === $key ? 'bg-success bg-opacity-10 border border-success' : 'bg-light' }}">
                            <p class="mb-0">
                                <strong>{{ $key }}.</strong> {{ $text }}
                                @if(strtoupper($question->kunci_jawaban) === $key)
                                    <span class="badge bg-success float-end">✓ Jawaban Kunci</span>
                                @endif
                            </p>
                        </div>
                    @endif
                    @endforeach
                </div>

                @if($question->pembahasan)
                <div class="mb-4 p-4 bg-info bg-opacity-10 rounded border border-info">
                    <h6 class="mb-2"><i class="fas fa-lightbulb text-warning me-2"></i><strong>Pembahasan</strong></h6>
                    <p class="mb-0">{!! $question->pembahasan !!}</p>
                </div>
                @endif

                <div class="mb-4">
                    <p>
                        <strong>Status:</strong>
                        <span class="badge {{ $question->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('guru.bank-soal.edit', $question->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Soal
                    </a>
                    <form method="POST" action="{{ route('guru.bank-soal.delete', $question->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus soal ini?')">
                            <i class="fas fa-trash me-2"></i>Hapus Soal
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
