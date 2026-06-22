# Dokumentasi Fitur Nilai Ujian

## Ringkasan Fitur yang Telah Diimplementasikan

Sistem nilai ujian (exam scores) telah diperbaharui dengan fitur-fitur baru untuk memudahkan guru dalam mengelola dan meninjau hasil ujian siswa.

## Fitur-Fitur Baru

### 1. **Filter Berdasarkan Kelas**
Guru dapat memfilter hasil ujian berdasarkan kelas yang dipilih. Ini memudahkan untuk melihat hasil ujian kelompok siswa tertentu.

- Dropdown menu "Semua Kelas" memudahkan pemilihan
- Tabel secara otomatis memfilter baris yang ditampilkan
- Semua kelas yang diajar guru akan tersedia dalam dropdown

### 2. **Review Jawaban Ujian Siswa**
Guru dapat melihat detail jawaban siswa untuk setiap ujian yang telah diselesaikan.

**Fitur pada halaman Review:**
- **Nama dan Data Siswa**: Menampilkan nama, NIS, dan informasi ujian
- **Statistik Skor**: 
  - Jumlah jawaban benar (hijau)
  - Jumlah jawaban salah (merah)
  - Total soal (biru)
- **Tampilan Per Soal**:
  - Nomor soal dengan indikator benar/salah
  - Teks soal lengkap
  - Semua pilihan jawaban dengan:
    - Jawaban yang dipilih siswa (merah jika salah, hijau jika benar)
    - Jawaban kunci (selalu ditampilkan hijau)
    - Pembahasan soal jika tersedia
  - Border soal berwarna sesuai status (hijau = benar, merah = salah)

**Cara Mengakses:**
- Klik tombol "Review" di kolom Aksi pada tabel nilai ujian
- Atau navigasikan ke URL: `/guru/nilai-ujian/{resultId}/review`

### 3. **Export Nilai Ujian ke CSV**
Guru dapat mengunduh data nilai ujian dalam format CSV untuk analisis lebih lanjut atau pembuatan laporan.

**Format Export:**
- Kolom: Mata Pelajaran, Siswa, NIS, Kelas, Ujian, Skor, Nilai Bobot, Persentase, Tanggal Selesai
- File menggunakan delimiter `;` untuk kompatibilitas dengan Excel
- UTF-8 encoding dengan BOM untuk menampilkan karakter Indonesia dengan benar

**Fitur:**
- Export semua data ujian atau filter berdasarkan kelas terlebih dahulu
- Nama file: `nilai-ujian-YYYYMMDD-HHmmss.csv`
- Dapat dibuka langsung dengan Microsoft Excel

**Cara Mengakses:**
- Klik tombol "Export Nilai" di bagian atas tabel
- Jika telah memilih kelas, hanya data kelas tersebut yang akan diexport

## Komponen Teknis

### Routing
```
GET /guru/nilai-ujian                        → nilaiUjian()
GET /guru/nilai-ujian/{resultId}/review      → reviewExamAnswer()
GET /guru/nilai-ujian/export                 → exportExamScores()
```

### Controller Methods

#### `nilaiUjian()`
- Menampilkan daftar semua hasil ujian untuk mata kuliah yang diajar guru
- Data diurutkan berdasarkan waktu selesai terbaru
- Menyertakan relasi dengan: student, sesiUjian, ujian, dan course

#### `reviewExamAnswer($resultId)`
- Menampilkan review detail jawaban ujian siswa
- Mengambil semua pertanyaan ujian dengan jawaban siswa
- Menghitung jumlah benar dan total soal
- Validasi: Hanya guru pemilik ujian yang dapat melihat

#### `exportExamScores(Request $request)`
- Export data ujian ke format CSV
- Mendukung filter berdasarkan `class_id` query parameter
- Menggunakan PHP streaming untuk efisiensi memory
- Menampilkan response sebagai file download

### Models

#### UjianResult
- Relationship: `ujian()` - HasOneThrough ke model Ujian
- Relationship: `answers()` - HasMany ke model UjianAnswer
- Relationship: `student()` - BelongsTo ke model User
- Relationship: `sesiUjian()` - BelongsTo ke model SesiUjian

#### Question
- Relationship: `studentAnswers()` - HasMany ke model UjianAnswer
- Properti: pertanyaan, jawaban_a-e, kunci_jawaban, pembahasan

#### UjianAnswer
- Menyimpan jawaban individual siswa
- Field: selected_answer (pilihan siswa), is_correct (status benar/salah)
- Relationship: `question()` - BelongsTo ke model Question

### Views

#### `guru.nilai-ujian.index`
- Template utama daftar nilai ujian
- Fitur:
  - Dropdown filter kelas (client-side)
  - Tabel dengan informasi ujian dan skor
  - Badge skor dengan warna berdasarkan persentase:
    - Hijau (80-100%) - Paling Baik
    - Biru (70-79%) - Baik
    - Kuning (60-69%) - Cukup
    - Merah (<60%) - Kurang
  - Modal untuk review jawaban

#### `guru.nilai-ujian.review`
- Template detail review jawaban
- Menampilkan:
  - Informasi siswa dan ujian
  - Statistik hasil (benar/salah/total)
  - Setiap soal dengan:
    - Indikator status
    - Teks soal
    - Pilihan jawaban dengan highlight
    - Jawaban siswa vs jawaban kunci
    - Pembahasan jika tersedia

## Workflow

### Workflow Guru: Melihat Hasil Ujian
1. Guru masuk ke menu **Nilai Ujian** dari dashboard
2. Tabel menampilkan semua hasil ujian siswa
3. Guru dapat memfilter berdasarkan kelas
4. Klik tombol **Review** untuk melihat detail jawaban siswa
5. Modal terbuka menampilkan:
   - Ringkasan hasil (benar/salah/total)
   - Detail setiap soal dengan jawaban siswa
   - Perbandingan jawaban siswa vs jawaban kunci

### Workflow Guru: Export Data Nilai
1. Guru masuk ke menu **Nilai Ujian** dari dashboard
2. Secara opsional, filter berdasarkan kelas
3. Klik tombol **Export Nilai**
4. File CSV otomatis diunduh
5. Guru dapat membuka file dengan Excel atau spreadsheet lainnya

## Database Schema

Struktur yang diperlukan sudah ada:
- `ujian_results` - Menyimpan hasil ujian per siswa
- `ujian_answers` - Menyimpan jawaban individual per soal
- `questions` - Bank soal dengan kunci jawaban
- `sesi_ujian` - Sesi ujian dengan tanggal mulai/berakhir

Field penting:
- `ujian_results.score` - Skor akhir siswa
- `ujian_results.start_time` - Waktu mulai ujian
- `ujian_results.end_time` - Waktu selesai ujian
- `ujian_answers.selected_answer` - Pilihan jawaban siswa
- `ujian_answers.is_correct` - Status jawaban benar/salah
- `questions.kunci_jawaban` - Jawaban kunci soal
- `questions.pembahasan` - Pembahasan soal (opsional)

## Konfigurasi

Tidak ada konfigurasi khusus yang diperlukan. Semua pengaturan sudah terintegrasi dengan sistem yang ada.

## Testing Checklist

- [ ] Filter berdasarkan kelas berfungsi dengan baik
- [ ] Review modal terbuka dan menampilkan data yang benar
- [ ] Export CSV menghasilkan file yang dapat dibuka di Excel
- [ ] Pajang benar/salah sesuai dengan data di database
- [ ] Warna badge skor menunjukkan kategori yang benar
- [ ] Pembahasan soal ditampilkan jika ada

## Troubleshooting

### Modal Review Tidak Terbuka
- Pastikan browser mendukung AJAX
- Periksa console browser untuk error
- Verifikasi route `/guru/nilai-ujian/{resultId}/review` terdaftar

### Export CSV Tidak Berfungsi
- Pastikan headers sudah dikirim dengan benar
- Periksa permission file system
- Verifikasi data ujian ada di database

### Data Tidak Ditampilkan
- Pastikan siswa telah menyelesaikan ujian (status = 'completed')
- Verifikasi guru adalah pemilik mata pelajaran ujian
- Periksa database apakah data ujian_answers tersimpan

## Pengembangan Selanjutnya

Fitur yang dapat ditambahkan di masa depan:
1. **Scoring Manual**: Guru dapat memberikan poin tambahan
2. **Print Report**: Export ke PDF format profesional
3. **Statistik Analitik**: Grafik performa siswa per soal
4. **Feedback Individu**: Guru dapat menambah catatan untuk setiap siswa
5. **Perbandingan Kelas**: Grafik perbandingan performa antar kelas
6. **Reminder Belum Review**: Notifikasi untuk ujian yang belum direview guru

## Referensi File

- Controller: `app/Http/Controllers/GuruController.php`
- Models: `app/Models/UjianResult.php`, `app/Models/Question.php`
- Views: `resources/views/guru/nilai-ujian/index.blade.php`, `resources/views/guru/nilai-ujian/review.blade.php`
- Routes: `routes/web.php` (bagian guru routes)

