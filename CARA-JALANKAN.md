# 🚀 PANDUAN LENGKAP MENJALANKAN E-LEARNING SMK YADIKA 13

**Sistem E-Learning Modern dengan Laravel 10 & Vite**

---

## 📋 **INFORMASI UMUM APLIKASI**

### 🖥️ **Teknologi yang Digunakan**
- **Framework**: Laravel 10.x
- **Bahasa Pemrograman**: PHP 8.1.10
- **Database**: MySQL 5.7+
- **Frontend Build Tool**: Vite 4.x
- **UI Framework**: Bootstrap 5.x
- **Authentication**: Laravel Sanctum 3.2+
- **File Processing**: Laravel Excel, DomPDF

### 💻 **Sistem Operasi yang Didukung**
- **Windows**: 10/11 (64-bit) - **Direkomendasikan**
- **Linux**: Ubuntu 18.04+, CentOS 7+
- **macOS**: 10.15+

### 🖱️ **Persyaratan Hardware Minimum**
- **RAM**: 4GB (disarankan 8GB+)
- **Penyimpanan**: 2GB ruang kosong
- **Prosesor**: Intel Core i3 atau setara

---

## 🔧 **LANGKAH PERSIAPAN SEBELUM INSTALASI**

### **Langkah 1: Download dan Install Laragon**
Laragon adalah paket lengkap Apache, MySQL, dan PHP untuk development.

**Download Laragon:**
1. Buka browser dan kunjungi: https://laragon.org/download/
2. Download versi **Full** (terbaru) atau mau yang gratis tidak ada lisence pilih laragon versi 6
3. Tunggu download selesai

**Cara Install:**
1. Jalankan file installer yang sudah didownload
2. Klik kanan → **Run as administrator**
3. Ikuti langkah instalasi default:
   - Pilih bahasa: **English**
   - Klik **Next**
   - Pilih lokasi instalasi: `C:\laragon` (default)
   - Klik **Install**
   - Tunggu proses instalasi selesai
   - Klik **Finish**

**Verifikasi Instalasi:**
1. Buka aplikasi Laragon
2. Klik tombol **Start All** (hijau)
3. Buka browser → kunjungi `http://localhost`
4. Jika muncul halaman Laragon, instalasi berhasil

### **Langkah 2: Setup PHP di Laragon**
1. Download PHP 8.1.10 dari: https://windows.php.net/download/
2. Pilih **PHP 8.1 (8.1.34)/VS16 x64 Thread Safe**pilih (zip file), atau Pilih **PPHP 8.3 (8.3.29)/VS16 x64 Thread Safe**pilih (zip file)
3. Extract file zip tersebut dan pindahkan folder nya kedalam C:\laragon lalu pilih folder bin, pilih folder php lalu extract
4. jika sudah berhasil di extract selanjutnya, Copy url folder hasil extract `C:\laragon\bin\php\` ke dalam enviroment
5. buka enviroment, pilih systems variable, pilih path, pilih edit, pilih new lalu paste kan url php nya, lalu klik ok 

**Setup di Laragon:**
1. Buka aplikasi Laragon
2. Klik kanan pada ikon Laragon di system tray
3. Pilih **PHP** → **Version** → **php-8.1.10-Win32-vs16-x64**
4. Klik kanan lagi → **PHP** → **Extensions** → aktifkan ekstensi berikut:
   - `curl`
   - `fileinfo`
   - `gd`
   - `mbstring`
   - `mysqli`
   - `openssl`
   - `pdo_mysql`
   - sqlite
   - `zip`

**Verifikasi PHP:**
1. Buka Command Prompt
2. Ketik: `php -v`
3. Jika muncul versi PHP 8.1.10, berarti berhasil

## 📥 **LANGKAH INSTALASI APLIKASI**

### **Langkah 5: Download Project**
1. Pastikan Anda sudah memiliki file project `smkyadika13elearning`
2. Jika belum, download dari repository GitHub atau sumber lainnya
3. Extract file project ke folder yang diinginkan

### **Langkah 6: Buka Project di Visual Studio Code**
1. Buka aplikasi **Visual Studio Code**
2. Klik **File** → **Open Folder**
3. Pilih folder project `smkyadika13elearning`
4. Klik **Select Folder**
5. Buka Terminal di VSCode: **View** → **Terminal**

### **Langkah 7: Install Dependencies PHP (Composer)**
1. Pastikan terminal VSCode terbuka
2. Ketik perintah berikut dan tekan Enter:

```bash
composer install
```

3. Tunggu proses download selesai (akan membuat folder `vendor/`)
4. Jika ada error, coba:

```bash
composer update
```

### **Langkah 8: Install Dependencies Node.js (NPM)**
1. Di terminal VSCode yang sama
2. Ketik perintah berikut dan tekan Enter:

```bash
npm install
```

3. Tunggu proses download selesai (akan membuat folder `node_modules/`)
4. Jika lambat, gunakan:

```bash
npm install --production=false
```

### **Langkah 9: Setup File Environment**
1. Copy file konfigurasi environment:

```bash
copy .env.example .env
```

2. Generate application key Laravel:

```bash
php artisan key:generate
```

3. Buka file `.env` dengan VSCode
4. Sesuaikan konfigurasi database (lihat bagian berikutnya)

### **Langkah 10: Setup Database MySQL**
1. Pastikan Laragon sudah running (tombol Start All hijau)
2. Buka browser → kunjungi `http://localhost/phpmyadmin`
3. Login dengan:
   - **Username**: `root`
   - **Password**: *(kosongkan)*
4. Klik **New** di sebelah kiri
5. Buat database baru:
   - **Database name**: `smkyadika13elearning`
   - **Collation**: `utf8mb4_unicode_ci`
6. Klik **Create**

**Konfigurasi .env untuk Database:**
Buka file `.env` dan pastikan bagian database seperti ini:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smkyadika13elearning
DB_USERNAME=root
DB_PASSWORD=
```

### **Langkah 11: Jalankan Migrasi Database**
1. Di terminal VSCode:

```bash
php artisan migrate
```

2. Tunggu sampai selesai
3. Jika berhasil, akan muncul pesan "Migrated successfully"

### **Langkah 12: Seed Data Awal**
1. Di terminal VSCode:

```bash
php artisan db:seed
```

2. Tunggu sampai selesai
3. Data default akan terisi (admin, guru, siswa)

### **Langkah 13: Compile Assets Frontend**
1. Build assets untuk production:

```bash
npm run build
```

2. Tunggu proses compile selesai
3. Akan membuat folder `public/build/`

### **Langkah 14: Setup Storage Link**
1. Buat symbolic link untuk file uploads:

```bash
php artisan storage:link
```

2. Link akan menghubungkan `storage/app/public` ke `public/storage`

### **Langkah 15: Clear Cache Aplikasi**
1. Bersihkan semua cache:

```bash
php artisan optimize:clear
```

---

## 🚀 **MENJALANKAN APLIKASI**

### **Langkah 16: Jalankan Development Server**
1. Di terminal VSCode:

```bash
php artisan serve
```

2. Tunggu sampai muncul pesan:
   ```
   Laravel development server started: http://127.0.0.1:8000
   ```

3. Jangan tutup terminal ini (server harus tetap running)

### **Langkah 17: Akses Aplikasi di Browser**
1. Buka browser web (Chrome, Firefox, Edge)
2. Kunjungi alamat: `http://127.0.0.1:8000`
3. Halaman login akan muncul

### **Langkah 18: Login dengan Akun Default**

#### **Administrator**
- **Email**: `admin@smkyadika13.sch.id`
- **Password**: `admin123`

#### **Guru**
- **Email**: `budi.santoso@smkyadika13.sch.id`
- **Password**: `guru123`

#### **Siswa**
- **Email**: `andi.pratama@smkyadika13.sch.id`
- **Password**: `siswa123`

---

## 📋 **URUTAN PERINTAH LENGKAP (COPY-PASTE)**

Jika Anda ingin menjalankan semua perintah sekaligus setelah setup awal:

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
copy .env.example .env
php artisan key:generate

# 3. Setup database
php artisan migrate
php artisan db:seed

# 4. Build assets
npm run build

# 5. Setup storage
php artisan storage:link

# 6. Clear cache
php artisan optimize:clear

# 7. Jalankan server
php artisan serve
```

---

## ✅ **CHECKLIST INSTALASI BERHASIL**

- [ ] Laragon terdownload dan terinstall
- [ ] PHP 8.1.10 terdownload dan setup di Laragon
- [ ] Composer terdownload dan terinstall
- [ ] Node.js terdownload dan terinstall
- [ ] Project terbuka di VSCode
- [ ] `composer install` berhasil (folder vendor/ terbuat)
- [ ] `npm install` berhasil (folder node_modules/ terbuat)
- [ ] File `.env` sudah ada dan dikonfigurasi
- [ ] Database `smkyadika13elearning` dibuat di phpMyAdmin
- [ ] `php artisan migrate` berhasil
- [ ] `php artisan db:seed` berhasil
- [ ] `npm run build` berhasil
- [ ] `php artisan storage:link` berhasil
- [ ] `php artisan serve` berhasil dan server running
- [ ] Browser dapat mengakses `http://127.0.0.1:8000`
- [ ] Halaman login muncul tanpa error
- [ ] Login admin berhasil
- [ ] Login guru berhasil
- [ ] Login siswa berhasil

---

## 🆘 **TROUBLESHOOTING (Jika Ada Masalah)**

### **Error: 'php' is not recognized**
**Solusi:**
1. Pastikan Laragon running
2. Buka Command Prompt sebagai Administrator
3. Ketik: `php -v`
4. Jika masih error, restart Command Prompt

### **Error: Database connection failed**
**Solusi:**
1. Pastikan Laragon running (MySQL aktif)
2. Cek file `.env` bagian DB_DATABASE
3. Pastikan database sudah dibuat di phpMyAdmin
4. Restart Laragon jika perlu

### **Error: Permission denied**
**Solusi:**
1. Jalankan VSCode sebagai Administrator
2. Atau cek permission folder project
3. Pastikan folder `storage` dan `bootstrap/cache` writable

### **Error: Port 8000 already in use**
**Solusi:**
1. Ganti port: `php artisan serve --port=8080`
2. Atau stop aplikasi lain yang menggunakan port 8000

### **Error: Assets not loading**
**Solusi:**
1. Jalankan: `npm run build`
2. Clear cache: `php artisan optimize:clear`
3. Refresh browser (Ctrl+F5)

### **Error: Class not found**
**Solusi:**
1. Jalankan: `composer dump-autoload`
2. Jika masih error: `rm -rf vendor && composer install`

### **Laragon tidak bisa start MySQL**
**Solusi:**
1. Buka Services Windows (services.msc)
2. Cari "MySQL" atau "MariaDB"
3. Stop service tersebut
4. Restart Laragon

---

## 🛠️ **PERINTAH DEVELOPMENT LAINNYA**

### **Asset Management**
```bash
# Development dengan hot reload
npm run dev

# Build untuk production
npm run build

# Preview production build
npm run preview
```

### **Database Management**
```bash
# Fresh migrate dengan seed
php artisan migrate:fresh --seed

# Rollback migrasi
php artisan migrate:rollback

# Status migrasi
php artisan migrate:status
```

### **Cache Management**
```bash
# Clear semua cache
php artisan optimize:clear

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 **BANTUAN & SUPPORT**

Jika mengalami kesulitan, silakan hubungi:
- **Email**: support@smkyadika13.sch.id
- **WhatsApp**: +62 xxx-xxxx-xxxx
- **Dokumentasi**: [Link dokumentasi lengkap]

---

## 🎉 **SELESAI!**

Selamat! E-Learning SMK Yadika 13 sudah berhasil dijalankan. Anda dapat mulai menggunakan sistem dengan login menggunakan akun default yang tersedia.
