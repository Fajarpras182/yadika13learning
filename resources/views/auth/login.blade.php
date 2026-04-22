@extends('layouts.app')

@section('title', 'Login - E-Learning SMK Yadika 13')

@section('content')

<style>
    body {
        background: url("{{ asset('bg/bg2.png') }}") no-repeat center center fixed;
        background-size: cover;
    }

    .login-title h2 {
        font-weight: 800;
        letter-spacing: 1px;
    }
</style>

<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0 bg-white">
                <div class="card-body p-5">

                    <!-- JUDUL LOGIN DIATUR ULANG -->
                    <div class="text-center mb-4 login-title">
                        <i class="fas fa-graduation-cap text-primary mb-3" style="font-size: 3rem;"></i>
                        <h2 class="mb-0" style="color: #2c3e50;">E-Learning</h2>
                        <h6 class="text-muted mt-1">SMK Yadika 13</h6>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i> Email
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i> Password
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i> Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        <p class="text-muted">Belum punya akun? 
                            <a href="{{ route('register') }}" class="text-decoration-none">Daftar di sini</a>
                        </p>
                    </div>

                    <!-- AKUN DEMO ✅ SUDAH DIHAPUS -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
