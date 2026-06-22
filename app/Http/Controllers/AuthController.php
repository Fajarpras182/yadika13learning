<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Debug: Log the received email
        \Log::debug('Login attempt for email: ' . $credentials['email']);

        // Fetch user by email for further debug
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            \Log::debug('User not found for email: ' . $credentials['email']);
            return back()->withErrors(['email' => 'Email atau password salah.']);
        } else {
            \Log::debug('User found: ' . $user->email . ' with hashed password: ' . $user->password);
        }

        // Verify password manually for debug
        if (!\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            \Log::debug('Password mismatch for user email: ' . $user->email);
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        if (!Auth::attempt($credentials)) {
            \Log::debug('Auth::attempt failed for user email: ' . $user->email);
            return back()->withErrors(['email' => 'Email atau password salah.']);
        }

        if (!$user->is_active) {
            Auth::logout();
            \Log::debug('User is not active: ' . $user->email);
            return back()->withErrors(['email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.']);
        }

        $request->session()->regenerate();

        // Redirect based on role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'guru':
                return redirect()->route('guru.dashboard');
            case 'siswa':
                return redirect()->route('siswa.dashboard');
            default:
                return redirect()->route('home');
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:guru,siswa',
            'nis_nip' => 'required|string|max:20',
            'kelas' => 'nullable|string|max:10',
            'jurusan' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nis_nip' => $request->nis_nip,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_active' => false, // Menunggu aktivasi admin
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda menunggu aktivasi dari administrator.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Show the forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan di sistem kami.',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
            }

            // Generate reset token
            $token = Str::random(60);
            $hashedToken = hash('sha256', $token);

            // Store token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => $hashedToken,
                    'created_at' => Carbon::now(),
                ]
            );

            // Create reset URL
            $resetUrl = route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);

            // Send email
            Mail::send(new ResetPasswordMail($user, $resetUrl, $token));

            return back()->with('success', 'Link reset password telah dikirimkan ke email Anda. Silakan cek folder Inbox atau Spam.');
        } catch (\Exception $e) {
            \Log::error('Error sending reset link: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Terjadi kesalahan saat mengirim email. Silakan coba lagi.']);
        }
    }

    /**
     * Show the reset password form
     */
    public function showResetForm(Request $request, $token = null)
    {
        if (!$token) {
            return redirect()->route('login')->withErrors(['error' => 'Token tidak valid.']);
        }

        $email = $request->query('email');
        if (!$email) {
            return redirect()->route('login')->withErrors(['error' => 'Email tidak ditemukan.']);
        }

        // Verify token exists and is not expired (60 minutes)
        $hashedToken = hash('sha256', $token);
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $hashedToken)
            ->first();

        if (!$resetRecord) {
            return redirect()->route('login')->withErrors(['error' => 'Token reset password tidak valid.']);
        }

        $expiresAt = Carbon::parse($resetRecord->created_at)->addMinutes(60);
        if (Carbon::now()->isAfter($expiresAt)) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.request')->withErrors(['error' => 'Link reset password sudah kadaluarsa. Silakan buat permintaan baru.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle the password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|confirmed',
            'password_confirmation' => 'required',
        ], [
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak ditemukan.',
            'token.required' => 'Token tidak valid.',
            'password.required' => 'Password baru tidak boleh kosong.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        try {
            $token = $request->token;
            $email = $request->email;
            $hashedToken = hash('sha256', $token);

            // Verify token
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', $hashedToken)
                ->first();

            if (!$resetRecord) {
                return back()->withErrors(['email' => 'Token reset password tidak valid.']);
            }

            // Check if token is expired
            $expiresAt = Carbon::parse($resetRecord->created_at)->addMinutes(60);
            if (Carbon::now()->isAfter($expiresAt)) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                return redirect()->route('password.request')->withErrors(['error' => 'Link reset password sudah kadaluarsa. Silakan buat permintaan baru.']);
            }

            // Update user password
            $user = User::where('email', $email)->first();
            if (!$user) {
                return back()->withErrors(['email' => 'User tidak ditemukan.']);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Delete the reset token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('login')->with('success', 'Password Anda berhasil direset. Silakan login dengan password baru.');
        } catch (\Exception $e) {
            \Log::error('Error resetting password: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Terjadi kesalahan saat mereset password. Silakan coba lagi.']);
        }
    }
}
