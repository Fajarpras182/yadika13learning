<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .email-header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            opacity: 0.9;
        }
        .email-content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #7c3aed;
        }
        .message {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .reset-button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s;
        }
        .reset-button:hover {
            transform: scale(1.05);
            color: white;
        }
        .important-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #856404;
        }
        .important-note strong {
            color: #664d03;
        }
        .link-text {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            word-break: break-all;
            font-size: 12px;
            color: #555;
            margin: 15px 0;
            font-family: monospace;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #888;
        }
        .footer a {
            color: #7c3aed;
            text-decoration: none;
        }
        .expiry-warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 12px;
            margin-top: 20px;
            font-size: 12px;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🔐 Reset Password</h1>
            <p>E-Learning SMK Yadika 13</p>
        </div>

        <!-- Content -->
        <div class="email-content">
            <div class="greeting">
                Halo <strong>{{ $user->name }}</strong>,
            </div>

            <div class="message">
                Kami menerima permintaan untuk mengatur ulang password akun Anda. Klik tombol di bawah ini untuk melanjutkan proses reset password.
            </div>

            <div class="reset-button-container">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Reset Password Saya
                </a>
            </div>

            <div class="important-note">
                <strong>⚠️ Catatan Penting:</strong><br>
                Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:
            </div>

            <div class="link-text">
                {{ $resetUrl }}
            </div>

            <div class="message">
                Link reset password ini akan <strong>berlaku selama 60 menit</strong> saja. Setelah itu, Anda perlu meminta link baru.
            </div>

            <div class="expiry-warning">
                <strong>⏰ Keamanan:</strong> Jika Anda tidak meminta reset password ini, abaikan email ini dan password Anda akan tetap aman.
            </div>

            <div class="message" style="margin-top: 25px; font-size: 13px;">
                Pertanyaan atau butuh bantuan? Hubungi administrator melalui halaman login kami.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">© {{ date('Y') }} E-Learning SMK Yadika 13. Semua hak dilindungi.</p>
            <p style="margin: 5px 0 0 0;">Jangan forward email ini kepada orang lain untuk keamanan akun Anda.</p>
        </div>
    </div>
</body>
</html>
