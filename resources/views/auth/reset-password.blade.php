@extends('layouts.app')

@section('title', 'Reset Password - E-Learning SMK Yadika 13')

@section('content')

<style>
    body {
        background: url("{{ asset('bg/bg2.png') }}") no-repeat center center fixed;
        background-size: cover;
    }

    .reset-password-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .reset-password-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 40px;
        max-width: 600px;
        width: 100%;
    }

    .reset-password-title {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        text-align: center;
    }

    .reset-password-desc {
        color: #7f8c8d;
        font-size: 14px;
        text-align: center;
        margin-bottom: 25px;
        line-height: 1.6;
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

    .password-strength {
        margin-top: 8px;
        font-size: 12px;
    }

    .strength-indicator {
        height: 4px;
        border-radius: 2px;
        margin-top: 5px;
        background-color: #ddd;
    }

    .strength-indicator.weak {
        background-color: #dc3545;
    }

    .strength-indicator.medium {
        background-color: #ffc107;
    }

    .strength-indicator.strong {
        background-color: #28a745;
    }

    .btn-reset {
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

    .btn-reset:hover {
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

    .password-requirements {
        background-color: #f8f9fa;
        border-left: 3px solid #7c3aed;
        padding: 12px 15px;
        border-radius: 4px;
        margin-top: 15px;
        font-size: 12px;
        color: #495057;
    }

    .password-requirements ul {
        margin: 0;
        padding-left: 20px;
    }

    .password-requirements li {
        margin-bottom: 5px;
    }

    .input-group-text {
        border: 1px solid #ddd;
        background-color: white;
    }

    .input-group-text button {
        border: none;
        background: transparent;
        color: #7c3aed;
        cursor: pointer;
    }

    .input-group-text button:hover {
        color: #6d28d9;
    }
</style>

<div class="reset-password-container">
    <div class="reset-password-card">

        <h2 class="reset-password-title">Reset Password</h2>
        <p class="reset-password-desc">
            Masukkan email Anda dan password baru untuk mengatur ulang akses Anda.
        </p>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-times-circle me-2"></i>{{ $error }}</div>
                @endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" id="resetPasswordForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="mb-3">
                <label for="email" class="form-label">EMAIL</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $email) }}"
                       required 
                       readonly>
                @error('email')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">PASSWORD BARU</label>
                <div class="input-group">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Masukkan password baru"
                           required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-indicator" id="strengthIndicator"></div>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">KONFIRMASI PASSWORD</label>
                <div class="input-group">
                    <input type="password" 
                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           placeholder="Ulangi password baru"
                           required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordMatch" style="margin-top: 8px; font-size: 12px; color: #dc3545; display: none;">
                    <i class="fas fa-times-circle me-1"></i> Password tidak sesuai
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="password-requirements">
                <strong style="color: #2c3e50;">Persyaratan Password:</strong>
                <ul>
                    <li id="req-length">Minimal 8 karakter</li>
                    <li id="req-uppercase">Mengandung huruf besar (A-Z)</li>
                    <li id="req-lowercase">Mengandung huruf kecil (a-z)</li>
                    <li id="req-number">Mengandung angka (0-9)</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-reset" id="submitBtn" disabled>
                <i class="fas fa-lock me-2"></i> Reset Password
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
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const strengthIndicator = document.getElementById('strengthIndicator');
    const passwordMatch = document.getElementById('passwordMatch');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        
        // Check length
        if (password.length >= 8) {
            strength++;
            document.getElementById('req-length').style.color = '#28a745';
            document.getElementById('req-length').innerHTML = '<i class="fas fa-check-circle"></i> Minimal 8 karakter';
        } else {
            document.getElementById('req-length').style.color = '#dc3545';
            document.getElementById('req-length').innerHTML = '<i class="fas fa-times-circle"></i> Minimal 8 karakter';
        }

        // Check uppercase
        if (/[A-Z]/.test(password)) {
            strength++;
            document.getElementById('req-uppercase').style.color = '#28a745';
            document.getElementById('req-uppercase').innerHTML = '<i class="fas fa-check-circle"></i> Mengandung huruf besar (A-Z)';
        } else {
            document.getElementById('req-uppercase').style.color = '#dc3545';
            document.getElementById('req-uppercase').innerHTML = '<i class="fas fa-times-circle"></i> Mengandung huruf besar (A-Z)';
        }

        // Check lowercase
        if (/[a-z]/.test(password)) {
            strength++;
            document.getElementById('req-lowercase').style.color = '#28a745';
            document.getElementById('req-lowercase').innerHTML = '<i class="fas fa-check-circle"></i> Mengandung huruf kecil (a-z)';
        } else {
            document.getElementById('req-lowercase').style.color = '#dc3545';
            document.getElementById('req-lowercase').innerHTML = '<i class="fas fa-times-circle"></i> Mengandung huruf kecil (a-z)';
        }

        // Check number
        if (/[0-9]/.test(password)) {
            strength++;
            document.getElementById('req-number').style.color = '#28a745';
            document.getElementById('req-number').innerHTML = '<i class="fas fa-check-circle"></i> Mengandung angka (0-9)';
        } else {
            document.getElementById('req-number').style.color = '#dc3545';
            document.getElementById('req-number').innerHTML = '<i class="fas fa-times-circle"></i> Mengandung angka (0-9)';
        }

        // Update strength indicator
        strengthIndicator.className = 'strength-indicator';
        if (strength === 1) {
            strengthIndicator.classList.add('weak');
        } else if (strength === 2 || strength === 3) {
            strengthIndicator.classList.add('medium');
        } else if (strength === 4) {
            strengthIndicator.classList.add('strong');
        }

        return strength;
    }

    // Check password match
    function checkPasswordMatch() {
        if (passwordConfirmInput.value && passwordInput.value !== passwordConfirmInput.value) {
            passwordMatch.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        } else {
            passwordMatch.style.display = 'none';
            checkSubmitButton();
            return true;
        }
    }

    // Check if form can be submitted
    function checkSubmitButton() {
        const strength = checkPasswordStrength(passwordInput.value);
        const matches = passwordInput.value === passwordConfirmInput.value;
        const bothFilled = passwordInput.value && passwordConfirmInput.value;
        
        submitBtn.disabled = !(strength === 4 && matches && bothFilled);
    }

    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkSubmitButton();
    });

    passwordConfirmInput.addEventListener('input', function() {
        checkPasswordMatch();
    });

    // Form submission
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        if (!checkPasswordMatch()) {
            e.preventDefault();
        }
    });
</script>
@endpush

@endsection
