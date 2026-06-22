@extends('layouts.app')

@section('title', 'Lupa Password - E-Learning SMK Yadika 13')

@section('content')

<style>
    body {
        background: url("{{ asset('bg/bg2.png') }}") no-repeat center center fixed;
        background-size: cover;
    }

    .forgot-password-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .forgot-password-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 600px;
        width: 100%;
    }

    .forgot-password-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        text-align: center;
    }

    .forgot-password-desc {
        color: #7f8c8d;
        font-size: 14px;
        text-align: center;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .alert-warning-custom {
        background-color: #f8f9fa;
        border: 1px solid #fff3cd;
        border-radius: 5px;
        padding: 12px 15px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }

    .alert-warning-custom i {
        color: #ffc107;
        font-size: 18px;
        margin-right: 10px;
    }

    .alert-warning-custom span {
        color: #664d03;
        font-size: 13px;
        font-weight: 500;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px 15px;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
    }

    .btn-send {
        background-color: #7c3aed;
        border: none;
        color: white;
        padding: 12px 20px;
        font-weight: 600;
        border-radius: 5px;
        width: 100%;
        font-size: 16px;
        margin-top: 15px;
        transition: background-color 0.3s;
    }

    .btn-send:hover {
        background-color: #6d28d9;
        color: white;
    }

    .back-to-login {
        text-align: center;
        margin-top: 20px;
    }

    .back-to-login a {
        color: #7c3aed;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
    }

    .back-to-login a:hover {
        text-decoration: underline;
    }

    .success-message {
        display: none;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        color: #155724;
    }

    .success-message i {
        margin-right: 8px;
    }
</style>

<div class="forgot-password-container">
    <div class="forgot-password-card">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <h2 class="forgot-password-title">Lupa Password?</h2>
        <p class="forgot-password-desc">
            Masukkan email terdaftar Anda. Kami akan mengirimkan link untuk reset password.
        </p>

        <div class="alert-warning-custom">
            <i class="fas fa-triangle-exclamation"></i>
            <span>⚠️ Cek folder <strong>Spam</strong> atau <strong>Inbox</strong> setelah mengirim.</span>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-times-circle me-2"></i>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">EMAIL</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       placeholder="Masukkan email terdaftar" 
                       required autofocus>
                @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-send">
                <i class="fas fa-paper-plane me-2"></i> Kirim Link Reset Password
            </button>
        </form>

        <div class="back-to-login">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke halaman login
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // Form validation feedback
    const form = document.getElementById('forgotPasswordForm');
    const emailInput = document.getElementById('email');

    emailInput.addEventListener('blur', function() {
        if (this.value && !this.value.includes('@')) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }
    });

    form.addEventListener('submit', function(e) {
        if (!emailInput.value) {
            e.preventDefault();
            emailInput.classList.add('is-invalid');
        }
    });
</script>
@endpush

@endsection
