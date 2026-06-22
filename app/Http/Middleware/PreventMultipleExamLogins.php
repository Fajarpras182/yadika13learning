<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redis;

class PreventMultipleExamLogins
{
    /**
     * Handle an incoming request.
     * Mencegah siswa login/membuka ujian di 2 perangkat atau 2 browser bersamaan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $studentId = auth()->id();
        
        if (!$studentId) {
            return $next($request);
        }

        // Ambil session id PHP aktif saat ini
        $currentSessionId = session()->getId();
        
        $redisKey = "cbt:student:{$studentId}:exam_session";
        $registeredSessionId = Redis::get($redisKey);

        if ($registeredSessionId && $registeredSessionId !== $currentSessionId) {
            // Sesi ditolak karena sudah ada sesi aktif lain
            // Logout pengguna dari perangkat ini, atau tampilkan error
            auth()->logout();
            
            // Redirect dengan error
            return redirect()->route('login')->with('error', 'Akun Anda sedang digunakan untuk ujian di perangkat lain. Silakan selesaikan ujian di perangkat tersebut atau hubungi pengawas.');
        }

        // Daftarkan sesi ini jika belum ada
        if (!$registeredSessionId) {
            // Expire dalam 4 jam (estimasi maksimal waktu ujian)
            Redis::setex($redisKey, 14400, $currentSessionId);
        }

        return $next($request);
    }
}
