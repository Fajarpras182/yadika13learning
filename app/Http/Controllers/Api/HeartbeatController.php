<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class HeartbeatController extends Controller
{
    private const CACHE_PREFIX = 'cbt:';

    /**
     * Endpoint untuk mendeteksi bahwa klien (siswa) masih aktif dan online.
     * Dipanggil setiap 15 detik via JS setInterval.
     */
    public function ping(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exam_session_id' => 'required|integer',
            'student_id'      => 'required|integer',
        ]);

        $key = self::CACHE_PREFIX . "session:{$validated['exam_session_id']}:state";
        
        // Update waktu terakhir aktif untuk session ini
        Redis::hset($key, "last_active", now()->timestamp);
        
        // Broadcast event menggunakan WebSockets untuk Real-Time Dashboard
        event(new \App\Events\StudentHeartbeatReceived($validated['exam_session_id'], $validated['student_id'], 'online'));

        return response()->json([
            'status' => 'success',
            'message' => 'Heartbeat acknowledged'
        ], 200);
    }
}
