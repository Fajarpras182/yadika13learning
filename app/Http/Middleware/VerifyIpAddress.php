<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SesiUjian;

class VerifyIpAddress
{
    /**
     * Handle an incoming request.
     * Memvalidasi bahwa IP klien berada dalam daftar IP/Subnet yang diizinkan.
     * Jika kolom allowed_ip_ranges pada sesi ujian kosong/null, akses diizinkan dari mana saja.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sesiUjianId = $request->route('sesi_ujian_id') ?? $request->input('sesi_ujian_id');

        if (!$sesiUjianId) {
            return $next($request);
        }

        $sesiUjian = SesiUjian::find($sesiUjianId);

        if (!$sesiUjian || empty($sesiUjian->allowed_ip_ranges)) {
            // Tidak ada batasan IP, izinkan akses
            return $next($request);
        }

        $clientIp = $request->ip();
        $allowedRanges = $sesiUjian->allowed_ip_ranges; // JSON cast to array

        $isAllowed = false;
        foreach ($allowedRanges as $range) {
            if ($this->ipInRange($clientIp, $range)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak. Ujian ini hanya bisa dikerjakan dari jaringan sekolah (IP: ' . $clientIp . ' tidak diizinkan).'
            ], 403);
        }

        return $next($request);
    }

    /**
     * Mengecek apakah IP berada di dalam range/subnet CIDR.
     * Mendukung format: "192.168.1.0/24" atau IP tunggal "192.168.1.100"
     */
    private function ipInRange(string $ip, string $range): bool
    {
        // Jika range adalah IP tunggal (tanpa /), bandingkan langsung
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        // CIDR notation
        list($subnet, $bits) = explode('/', $range);
        $ip     = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask   = -1 << (32 - (int)$bits);

        // Pastikan subnet benar-benar di-mask
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }
}
