<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class ViolationController extends Controller
{
    private const CACHE_PREFIX = 'cbt:';

    /**
     * Endpoint untuk mendeteksi pelanggaran siswa (misal: pindah tab, minimize browser).
     */
    public function logViolation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_session_id' => 'required|integer',
            'student_id'      => 'required|integer',
            'violation_type'  => 'required|string', // e.g., 'tab_switch', 'window_blur'
        ]);

        $key = self::CACHE_PREFIX . "session:{$validated['exam_session_id']}:violations";
        
        // Simpan jumlah pelanggaran ke Redis (increment)
        $violationsCount = Redis::hincrby($key, $validated['violation_type'], 1);
        
        // Aturan sederhana: Jika pelanggaran lebih dari 3 kali, bekukan ujian.
        $action = 'warning';
        if ($violationsCount > 3) {
            $action = 'force_submit'; // Siswa diskualifikasi atau auto-submit
        }

        // Broadcast event peringatan ke dasbor guru
        event(new \App\Events\ExamViolationDetected($validated['exam_session_id'], $validated['student_id'], $validated['violation_type'], $violationsCount));

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'violations_count' => $violationsCount,
            'message' => 'Violation logged successfully'
        ], 200);
    }
}
