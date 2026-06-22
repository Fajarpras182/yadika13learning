<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySafeExamBrowser
{
    /**
     * Handle an incoming request.
     * Mengunci rute ujian hanya untuk browser SEB.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->header('User-Agent');
        
        // Memastikan request datang dari Safe Exam Browser
        if (strpos($userAgent, 'SEB') === false && config('app.env') === 'production') {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak. Anda harus menggunakan Safe Exam Browser untuk mengikuti ujian ini.'
            ], 403);
            // Atau redirect ke halaman peringatan
            // return redirect()->route('seb.warning');
        }

        // Opsional: Validasi X-SafeExamBrowser-RequestHash
        // $requestHash = $request->header('X-SafeExamBrowser-RequestHash');
        // validasi hash sesuai dokumentasi SEB.

        return $next($request);
    }
}
