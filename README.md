# E-LEARNING SMK YADIKA 13
## Sistem Pembelajaran Online

### 📁 STRUKTUR FOLDER YANG MUDAH DIPAHAMI

#### 🔧 **01-Aplikasi** (Kode Program Utama)
- **Controllers** - Pengendali halaman dan fungsi
- **Models** - Struktur data database
- **Middleware** - Keamanan dan validasi
- **Exports/Imports** - Export/Import data Excel

#### 🎨 **02-Tampilan** (Interface Pengguna)
- **views/admin** - Halaman admin (Data Master, Data Users, Profile)
- **views/guru** - Halaman guru
- **views/siswa** - Halaman siswa
- **views/auth** - Halaman login/register
- **css/js** - File styling dan JavaScript

#### 🗄️ **03-Database** (Data dan Struktur)
- **migrations** - Struktur tabel database
- **seeders** - Data awal (admin, guru, siswa)
- **factories** - Data dummy untuk testing

#### 🛣️ **04-Route** (Alamat URL)
- **web.php** - Alamat halaman website
- **api.php** - Alamat untuk API
- **console.php** - Perintah command line

#### ⚙️ **05-Konfigurasi** (Pengaturan Sistem)
- **app.php** - Pengaturan aplikasi
- **database.php** - Pengaturan database
- **auth.php** - Pengaturan login
- **mail.php** - Pengaturan email

#### 🌐 **06-Publik** (File yang Diakses Browser)
- **index.php** - File utama website
- **favicon.ico** - Icon website
- **storage** - Link ke file upload

#### 📁 **07-File-Upload** (File yang Diupload)
- Foto profil pengguna
- File materi pembelajaran
- Dokumen pendukung

---

### 🔑 **LOGIN ADMIN**
- **Email**: admin@smkyadika13.sch.id
- **Password**: admin123

### 🔑 **LOGIN GURU**
- **Email**: budi.santoso@smkyadika13.sch.id
- **Password**: guru123

### 🔑 **LOGIN SISWA**
- **Email**: andi.pratama@smkyadika13.sch.id
- **Password**: siswa123

---

### 🚀 **CARA MENJALANKAN**
1. Buka Command Prompt di folder ini
2. Ketik: `php artisan serve`
3. Buka browser: `http://127.0.0.1:8000`
4. Login dengan akun di atas

### 📋 **FITUR UTAMA**
- ✅ Dashboard Admin dengan statistik
- ✅ Data Master (Kelas, Jurusan, Semester, Mata Pelajaran, Jenis Ujian, Perangkat)
- ✅ Data Users (Guru dan Siswa) dengan upload foto
- ✅ Profile Settings dengan upload foto profil
- ✅ Export/Import data Excel
- ✅ Upload berbagai jenis file (PDF, DOC, gambar, video)

### 🗑️ **FOLDER YANG DIHAPUS** (Tidak Penting)
- ❌ `tests/` - Folder testing
- ❌ `vendor/` - Dependencies (bisa diinstall ulang)
- ❌ `bootstrap/` - Cache yang bisa dibuat ulang
- ❌ `storage/` - Cache dan session
- ❌ File dokumentasi yang tidak perlu

**Struktur folder sekarang lebih bersih dan mudah dipahami!**

**Last Updated**: 2024-12-19
**Version**: 1.0.0
