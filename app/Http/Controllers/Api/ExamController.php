<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Exam\ExamCacheService;
use App\Models\UjianAnswer;
use App\Models\UjianResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function __construct(
        private readonly ExamCacheService $examCacheService
    ) {}

    /**
     * Endpoint untuk Auto-Save jawaban ke Redis.
     * Tidak menyentuh MySQL demi performa tinggi.
     */
    public function autoSave(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_session_id' => 'required|integer',
            'ujian_result_id' => 'required|integer',
            'soal_id'         => 'required|integer',
            'option_id'       => 'required',
        ]);

        try {
            $result = UjianResult::findOrFail($validated['ujian_result_id']);
            if ($result->status !== 'in_progress') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ujian sudah tidak aktif atau sudah selesai.'
                ], 422);
            }

            $this->examCacheService->saveTemporaryAnswer(
                $validated['exam_session_id'], 
                $validated['soal_id'], 
                $validated['option_id']
            );

            UjianAnswer::updateOrCreate(
                [
                    'ujian_result_id' => $validated['ujian_result_id'],
                    'question_id' => $validated['soal_id'],
                ],
                [
                    'selected_answer' => (string) $validated['option_id'],
                ]
            );
            
            return response()->json([
                'status' => 'success',
                'message' => 'Answer cached successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save answer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint untuk menandai soal sebagai Ragu-Ragu (Doubtful).
     * Digunakan oleh tombol kuning "Tandai Ragu" di navigasi soal.
     */
    public function markDoubtful(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ujian_result_id' => 'required|integer',
            'question_id'     => 'required|integer',
            'is_doubtful'     => 'required|boolean',
        ]);

        try {
            UjianAnswer::updateOrCreate(
                [
                    'ujian_result_id' => $validated['ujian_result_id'],
                    'question_id'     => $validated['question_id'],
                ],
                [
                    'is_doubtful' => $validated['is_doubtful'],
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => $validated['is_doubtful'] ? 'Soal ditandai ragu-ragu' : 'Tanda ragu-ragu dihapus',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menandai soal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint untuk Bulk Sync jawaban dari IndexedDB (Offline Mode).
     * Dipanggil saat koneksi internet siswa pulih setelah terputus.
     */
    public function bulkSync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'answers'                      => 'required|array|min:1',
            'answers.*.exam_session_id'    => 'required|integer',
            'answers.*.soal_id'            => 'required|integer',
            'answers.*.option_id'          => 'required|integer',
        ]);

        $synced = 0;
        $failed = 0;

        try {
            foreach ($validated['answers'] as $answer) {
                try {
                    $this->examCacheService->saveTemporaryAnswer(
                        $answer['exam_session_id'],
                        $answer['soal_id'],
                        $answer['option_id']
                    );
                    $synced++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }

            return response()->json([
                'status'  => 'success',
                'message' => "Bulk sync selesai. Berhasil: {$synced}, Gagal: {$failed}",
                'synced'  => $synced,
                'failed'  => $failed,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bulk sync gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
