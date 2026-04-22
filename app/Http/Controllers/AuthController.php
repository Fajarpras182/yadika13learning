<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
}
