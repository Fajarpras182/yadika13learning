# Dokumentasi Fitur Lupa Password

## Ringkasan
Fitur "Lupa Password" telah diimplementasikan dengan lengkap sesuai dengan design yang Anda berikan. Fitur ini memungkinkan pengguna yang lupa password untuk mereset password mereka melalui link yang dikirimkan ke email.

## File-File yang Dibuat

### 1. **Views (Tampilan)**
- `resources/views/auth/forgot-password.blade.php` - Form untuk memasukkan email
- `resources/views/auth/reset-password.blade.php` - Form untuk mereset password baru
- `resources/views/emails/reset-password.blade.php` - Template email yang dikirimkan

### 2. **Controller**
- `app/Http/Controllers/AuthController.php` - Ditambahkan 4 method baru:
  - `showForgotPassword()` - Menampilkan halaman lupa password
  - `sendResetLink()` - Mengirim link reset ke email
  - `showResetForm()` - Menampilkan form reset password
  - `resetPassword()` - Memproses reset password

### 3. **Mailable Class**
- `app/Mail/ResetPasswordMail.php` - Class untuk mengirim email reset password

### 4. **Routes**
- `GET /forgot-password` - Route ke halaman lupa password
- `POST /forgot-password` - Route untuk mengirim link reset
- `GET /reset-password/{token}` - Route ke halaman reset password
- `POST /reset-password` - Route untuk memproses reset password

### 5. **Login Page**
- `resources/views/auth/login.blade.php` - Ditambahkan link "Lupa Password?"

## Fitur-Fitur yang Tersedia

### 📧 Form Lupa Password
- Input email dengan validasi
- Pesan peringatan untuk cek folder Spam
- Error handling yang jelas
- Design responsive dan modern

### 🔐 Form Reset Password
- Validasi password yang kuat:
  - Minimal 8 karakter
  - Harus mengandung huruf besar (A-Z)
  - Harus mengandung huruf kecil (a-z)
  - Harus mengandung angka (0-9)
- Password strength indicator
- Konfirmasi password matching
- Show/hide password toggle
- Persyaratan password yang transparan

### 📬 Email Reset
- Template email yang profesional dan menarik
- Link reset dengan token yang aman
- Informasi bahwa link berlaku 60 menit
- Instruksi yang jelas untuk pengguna

### 🔒 Keamanan
- Token yang di-hash menggunakan SHA256
- Link reset berlaku hanya 60 menit
- Validasi email dengan database
- Validasi password yang ketat
- Penghapusan token setelah penggunaan

## Cara Penggunaan

### 1. **User Lupa Password**
   - Klik "Lupa Password?" di halaman login
   - Masukkan email terdaftar
   - Klik "Kirim Link Reset Password"
   - Cek email (Inbox atau Spam)

### 2. **User Klik Link Email**
   - Buka email yang diterima
   - Klik tombol "Reset Password Saya"
   - atau copy-paste link yang diberikan

### 3. **User Reset Password**
   - Masukkan password baru sesuai persyaratan
   - Konfirmasi password
   - Klik "Reset Password"
   - Login dengan password baru

## Konfigurasi Email (Mail Configuration)

Pastikan file `.env` sudah dikonfigurasi dengan benar:

```
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="E-Learning SMK Yadika 13"
```

Untuk production, ganti dengan SMTP real provider (Gmail, SendGrid, dll).

## Testing Email Locally

1. Gunakan **Mailpit** (sudah dikonfigurasi di `.env`)
2. Akses http://localhost:8025 untuk melihat email yang terkirim
3. Atau gunakan `php artisan tinker` untuk testing

## Perubahan pada File Existing

### `app/Http/Controllers/AuthController.php`
- Ditambahkan imports untuk DB, Mail, Str, ResetPasswordMail, Carbon
- Ditambahkan 4 method baru untuk handle forgot password flow

### `routes/web.php`
- Ditambahkan 4 routes baru untuk forgot password dan reset password

### `resources/views/auth/login.blade.php`
- Ditambahkan link "Lupa Password?" di bawah tombol login

## Database

Tabel `password_reset_tokens` sudah ada dari migration default Laravel.
- Menyimpan email, token (hashed), dan created_at
- Token dihapus setelah 60 menit atau setelah digunakan

## Validasi Form

### Form Lupa Password
- Email: Required, valid email format, harus ada di database

### Form Reset Password
- Email: Required, valid email
- Token: Required dan harus valid
- Password: 
  - Required
  - Minimal 8 karakter
  - Harus mengandung huruf besar
  - Harus mengandung huruf kecil
  - Harus mengandung angka
  - Must be confirmed

## Error Handling

- Email tidak ditemukan
- Token tidak valid atau kadaluarsa
- Password tidak sesuai persyaratan
- Error saat mengirim email
- Konfirmasi password tidak cocok

## UI/UX Features

✅ Design responsive dan mobile-friendly
✅ Icon Font Awesome untuk visual appeal
✅ Gradient buttons dengan hover effects
✅ Password strength indicator dengan warna
✅ Real-time validation feedback
✅ Clear error messages dalam bahasa Indonesia
✅ Professional email template
✅ Peringatan keamanan yang jelas

## Troubleshooting

### Email tidak terkirim?
1. Cek `.env` file untuk mail configuration
2. Untuk testing lokal, pastikan Mailpit running
3. Check logs di `storage/logs/`

### Token invalid/expired?
- Token berlaku 60 menit saja
- User harus meminta link baru

### Password validation error?
- Pastikan password memenuhi semua 4 persyaratan
- Password strength indicator akan membantu user

## Next Steps (Optional)

Anda bisa menambahkan fitur tambahan seperti:
- Rate limiting untuk mencegah spam
- SMS notification untuk reset password
- Social login (Google, GitHub)
- Two-factor authentication
- Email verification saat login baru
