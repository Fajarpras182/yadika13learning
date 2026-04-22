# Dokumentasi Halaman - E-Learning SMK Yadika 13

## 📋 Daftar Isi
1. [Halaman Admin](#halaman-admin)
2. [Halaman Guru](#halaman-guru)
3. [Halaman Siswa](#halaman-siswa)
4. [Alur Interaksi Pengguna](#alur-interaksi-pengguna)
5. [Komponen UI yang Digunakan](#komponen-ui-yang-digunakan)

---

## 👨‍💼 Halaman Admin

### 1. Dashboard Admin
**Route**: `/admin/dashboard`  
**Fungsi**: Halaman utama admin untuk melihat overview sistem

**Komponen**:
- Statistik cards (Total Users, Total Guru, Total Siswa, Total Courses, Pending Users)
- Recent activities
- Quick actions buttons

**Fitur**:
- View statistik lengkap
- Navigasi cepat ke menu penting
- Overview sistem secara keseluruhan

**Aksi yang Tersedia**:
- Klik stat card untuk melihat detail
- Navigasi ke menu lain melalui sidebar

---

### 2. Data Master
**Route**: `/admin/data-master`  
**Fungsi**: Halaman hub untuk mengakses semua data master

**Komponen**:
- Link ke Jurusan, Kelas, Jadwal
- Quick access buttons

**Fitur**:
- Centralized data master management
- Quick navigation

---

### 3. User Management
**Route**: `/admin/users`  
**Fungsi**: Mengelola semua user (admin, guru, siswa)

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Action buttons (Activate/Deactivate)
- Export buttons (Excel, CSV, PDF, Word)
- Import button

**Fitur**:
- View all users
- Create new user
- Activate/Deactivate user
- Search users
- Filter by role and status
- Export data
- Import data from Excel/CSV

**Aksi yang Tersedia**:
- **Tambah User**: Klik tombol "Tambah User" → Isi form → Simpan
- **Aktivasi**: Klik tombol "Aktivasi" pada user yang nonaktif
- **Nonaktifkan**: Klik tombol "Nonaktifkan" pada user yang aktif
- **Export**: Klik tombol export (Excel/CSV/PDF/Word)
- **Import**: Upload file Excel/CSV → Klik "Import"

---

### 4. Guru Management
**Route**: `/admin/teachers`  
**Fungsi**: Mengelola data guru

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Action buttons
- Export/Import buttons

**Fitur**:
- View all teachers
- Create new teacher
- Activate/Deactivate teacher
- Search teachers
- Filter by status
- Export data
- Import data

**Aksi yang Tersedia**:
- **Tambah Guru**: Klik "Tambah" → Isi form → Simpan
- **Edit**: Klik icon edit pada tabel
- **Aktivasi/Nonaktifkan**: Klik tombol sesuai status
- **Export/Import**: Sama seperti User Management

---

### 5. Siswa Management
**Route**: `/admin/students`  
**Fungsi**: Mengelola data siswa

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Action buttons
- Export/Import buttons

**Fitur**:
- View all students
- Create new student
- Activate/Deactivate student
- Search students
- Filter by status
- Export data
- Import data

**Aksi yang Tersedia**:
- Sama seperti Guru Management

---

### 6. Mata Pelajaran Management
**Route**: `/admin/courses`  
**Fungsi**: Mengelola mata pelajaran

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Export/Import buttons

**Fitur**:
- View all courses
- Search courses
- Filter by kelas, jurusan, status, guru
- Export data
- Import data

---

### 7. Jurusan Management
**Route**: `/admin/majors`  
**Fungsi**: Mengelola data jurusan

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Create/Edit/Delete buttons
- Export/Import buttons

**Fitur**:
- View all majors
- Create new major
- Edit major
- Delete major
- Search majors
- Filter by status
- Export data
- Import data

**Aksi yang Tersedia**:
- **Tambah**: Klik "Tambah" → Isi form → Simpan
- **Edit**: Klik icon edit → Edit data → Simpan
- **Hapus**: Klik icon hapus → Konfirmasi → Hapus

---

### 8. Kelas Management
**Route**: `/admin/classes`  
**Fungsi**: Mengelola data kelas

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Create/Edit/Delete buttons
- Export/Import buttons

**Fitur**:
- View all classes
- Create new class
- Edit class
- Delete class
- Search classes
- Filter by status
- Export data
- Import data

---

### 9. Jadwal Management
**Route**: `/admin/schedules`  
**Fungsi**: Mengelola jadwal pembelajaran

**Komponen**:
- Data table dengan pagination
- Search/filter form
- Create/Edit/Delete buttons
- Export/Import buttons

**Fitur**:
- View all schedules
- Create new schedule
- Edit schedule
- Delete schedule
- Search schedules
- Filter by various criteria
- Export data
- Import data

---

### 10. Profil Admin
**Route**: `/admin/profile`  
**Fungsi**: Mengelola profil admin

**Komponen**:
- Profile form
- Update button
- Password change section

**Fitur**:
- View profile
- Update profile information
- Change password
- Upload profile photo

**Aksi yang Tersedia**:
- **Update Profil**: Edit form → Klik "Simpan"
- **Ubah Password**: Isi password lama dan baru → Klik "Simpan"

---

## 👨‍🏫 Halaman Guru

### 1. Dashboard Guru
**Route**: `/guru/dashboard`  
**Fungsi**: Halaman utama guru untuk melihat overview

**Komponen**:
- Statistik cards (Total Courses, Total Lessons, Total Assignments)
- Recent courses list
- Quick actions

**Fitur**:
- View statistik mengajar
- View recent courses
- Quick navigation

---

### 2. Mata Pelajaran
**Route**: `/guru/courses`  
**Fungsi**: Mengelola mata pelajaran yang diajar

**Komponen**:
- Course cards grid
- Create course button
- Action buttons per course (Materi, Tugas, Ujian, Absensi)

**Fitur**:
- View all courses
- Create new course
- Access materi, tugas, ujian, absensi per course

**Aksi yang Tersedia**:
- **Tambah Mata Pelajaran**: Klik "Tambah Mata Pelajaran" → Isi form → Simpan
- **Akses Materi**: Klik tombol "Materi" pada course card
- **Akses Tugas**: Klik tombol "Tugas" pada course card
- **Akses Ujian**: Klik tombol "Ujian" pada course card
- **Akses Absensi**: Klik tombol "Absensi" pada course card

---

### 3. Materi
**Route**: `/guru/courses/{courseId}/lessons`  
**Fungsi**: Mengelola materi pembelajaran

**Komponen**:
- Data table dengan pagination
- Search form
- Create button
- Edit/Delete buttons

**Fitur**:
- View all lessons
- Create new lesson
- Edit lesson
- Delete lesson
- Search lessons

**Aksi yang Tersedia**:
- **Tambah Materi**: Klik "Tambah Materi" → Isi form (judul, deskripsi, materi, urutan) → Simpan
- **Edit**: Klik icon edit → Edit data → Simpan
- **Hapus**: Klik icon hapus → Konfirmasi → Hapus

---

### 4. Tugas
**Route**: `/guru/courses/{courseId}/assignments`  
**Fungsi**: Mengelola tugas untuk siswa

**Komponen**:
- Data table dengan pagination
- Search form
- Create button
- Edit/Delete/Grade buttons

**Fitur**:
- View all assignments
- Create new assignment
- Edit assignment
- Delete assignment
- Grade assignments
- Search assignments

**Aksi yang Tersedia**:
- **Buat Tugas**: Klik "Buat Tugas" → Isi form (judul, deskripsi, instruksi, deadline, bobot) → Simpan
- **Edit**: Klik icon edit → Edit data → Simpan
- **Nilai**: Klik tombol "Nilai" → Beri nilai dan feedback → Simpan
- **Hapus**: Klik icon hapus → Konfirmasi → Hapus

---

### 5. Ujian
**Route**: `/guru/courses/{courseId}/exams`  
**Fungsi**: Mengelola ujian (UTS, UAS, Kuis)

**Komponen**:
- Data table dengan pagination
- Filter form (jenis, status, tanggal)
- Quick filter buttons (Semua, UTS, UAS, Kuis)
- Create button
- Edit/Delete buttons

**Fitur**:
- View all exams
- Create new exam
- Edit exam
- Delete exam
- Filter by jenis (UTS, UAS, Kuis, Remedial)
- Filter by status
- Filter by tanggal

**Aksi yang Tersedia**:
- **Tambah Ujian**: Klik "Tambah Ujian" → Isi form (judul, jenis, tanggal, durasi, bobot) → Simpan
- **Edit**: Klik icon edit → Edit data → Simpan
- **Hapus**: Klik icon hapus → Konfirmasi → Hapus
- **Filter**: Gunakan filter form atau quick filter buttons

---

### 6. Absensi
**Route**: `/guru/courses/{courseId}/attendances`  
**Fungsi**: Mengelola absensi siswa

**Komponen**:
- Data table dengan pagination
- Filter form (tanggal, status)
- Create button
- Edit/Delete buttons

**Fitur**:
- View all attendances
- Create new attendance
- Edit attendance
- Delete attendance
- Filter by tanggal
- Filter by status (hadir, izin, sakit, alpa)

**Aksi yang Tersedia**:
- **Tambah Absensi**: Klik "Tambah Absensi" → Pilih tanggal → Input status per siswa → Simpan
- **Edit**: Klik icon edit → Edit status → Simpan
- **Hapus**: Klik icon hapus → Konfirmasi → Hapus

---

## 👨‍🎓 Halaman Siswa

### 1. Dashboard Siswa
**Route**: `/siswa/dashboard`  
**Fungsi**: Halaman utama siswa untuk melihat overview

**Komponen**:
- Statistik cards (Enrolled Courses, Pending Assignments)
- Recent courses list
- Recent grades list
- Quick actions

**Fitur**:
- View statistik pembelajaran
- View recent courses
- View recent grades
- Quick navigation

---

### 2. Mata Pelajaran
**Route**: `/siswa/courses`  
**Fungsi**: Melihat mata pelajaran yang diikuti

**Komponen**:
- Course cards grid
- View detail button

**Fitur**:
- View all enrolled courses
- View course details
- Access course materials

**Aksi yang Tersedia**:
- **Lihat Detail**: Klik "Lihat Detail" → View course details, materials, assignments

---

### 3. Detail Mata Pelajaran
**Route**: `/siswa/courses/{id}`  
**Fungsi**: Melihat detail mata pelajaran

**Komponen**:
- Course information
- Lessons list
- Assignments list
- Access buttons

**Fitur**:
- View course information
- View all lessons
- View all assignments
- Access lesson materials
- Access assignments

**Aksi yang Tersedia**:
- **Lihat Materi**: Klik lesson → View lesson content
- **Lihat Tugas**: Klik assignment → View assignment details

---

### 4. Materi
**Route**: `/siswa/courses/{courseId}/lessons/{lessonId}`  
**Fungsi**: Melihat materi pembelajaran

**Komponen**:
- Lesson content
- Navigation buttons

**Fitur**:
- View lesson content
- Navigate to other lessons
- Navigate back to course

---

### 5. Tugas
**Route**: `/siswa/assignments`  
**Fungsi**: Melihat dan mengumpulkan tugas

**Komponen**:
- Data table dengan pagination
- Search form
- View detail button
- Submit button

**Fitur**:
- View all assignments
- Search assignments
- View assignment details
- Submit assignments

**Aksi yang Tersedia**:
- **Lihat Detail**: Klik "Detail" → View assignment details
- **Kumpulkan**: Klik "Kumpulkan" → Upload file atau tulis jawaban → Submit

---

### 6. Detail Tugas
**Route**: `/siswa/assignments/{id}`  
**Fungsi**: Melihat detail tugas dan mengumpulkan

**Komponen**:
- Assignment information
- Instructions
- Submission form
- Submit button

**Fitur**:
- View assignment details
- Read instructions
- Submit assignment (file or text)
- View grade (if already graded)

**Aksi yang Tersedia**:
- **Kumpulkan**: Upload file atau tulis jawaban → Klik "Kumpulkan" → Konfirmasi

---

### 7. Nilai
**Route**: `/siswa/grades`  
**Fungsi**: Melihat nilai dari tugas yang sudah dinilai

**Komponen**:
- Data table dengan pagination
- Grade information
- Feedback (if available)

**Fitur**:
- View all grades
- View grade details
- View feedback from teacher

---

### 8. Profil Siswa
**Route**: `/siswa/profile`  
**Fungsi**: Mengelola profil siswa

**Komponen**:
- Profile form
- Update button

**Fitur**:
- View profile
- Update profile information
- Change password (if needed)

**Aksi yang Tersedia**:
- **Update Profil**: Edit form → Klik "Simpan"

---

## 🔄 Alur Interaksi Pengguna

### Alur Admin - Tambah User
1. Login sebagai admin
2. Klik menu "User Manage" di sidebar
3. Klik tombol "Tambah User" (primary button dengan icon plus)
4. Isi form:
   - Nama
   - Email
   - Password
   - Role (Admin/Guru/Siswa)
   - NIS/NIP
   - Kelas (opsional)
   - Jurusan (opsional)
   - No HP (opsional)
   - Alamat (opsional)
5. Klik "Simpan" (success button)
6. Redirect ke list users dengan pesan sukses
7. User baru muncul di tabel

### Alur Guru - Buat Tugas
1. Login sebagai guru
2. Klik menu "Mata Pelajaran" di sidebar
3. Pilih mata pelajaran (klik card)
4. Klik tombol "Tugas" pada course card
5. Klik "Buat Tugas" (primary button)
6. Isi form:
   - Judul
   - Deskripsi
   - Instruksi
   - Deadline (datetime)
   - Bobot Nilai
7. Klik "Simpan"
8. Redirect ke list tugas dengan pesan sukses
9. Tugas baru muncul di tabel

### Alur Guru - Nilai Tugas
1. Login sebagai guru
2. Navigate ke Tugas → Pilih tugas
3. Klik tombol "Nilai"
4. View list siswa yang sudah mengumpulkan
5. Untuk setiap siswa:
   - View jawaban (text atau file)
   - Input nilai (0-100)
   - Input feedback (opsional)
   - Klik "Simpan"
6. Nilai tersimpan dan siswa bisa melihat

### Alur Siswa - Kumpulkan Tugas
1. Login sebagai siswa
2. Klik menu "Tugas" di sidebar
3. Pilih tugas dari list
4. Klik "Detail" atau langsung klik tugas
5. Baca instruksi tugas
6. Upload file atau tulis jawaban di text area
7. Klik "Kumpulkan" (primary button)
8. Konfirmasi modal muncul
9. Klik "Ya, Kumpulkan"
10. Pesan sukses muncul
11. Status tugas berubah menjadi "Sudah Dikumpulkan"

### Alur Siswa - Lihat Nilai
1. Login sebagai siswa
2. Klik menu "Nilai" di sidebar
3. View list nilai dari semua tugas
4. Klik tugas untuk melihat detail:
   - Nilai
   - Feedback dari guru
   - Status (sudah dinilai/belum dinilai)

---

## 🧩 Komponen UI yang Digunakan

### 1. Button (Tombol)
- **Primary Button**: Untuk aksi utama (Tambah, Simpan, Submit)
- **Success Button**: Untuk aksi sukses (Simpan, Konfirmasi)
- **Danger Button**: Untuk aksi hapus (Hapus, Hapus Data)
- **Outline Button**: Untuk aksi sekunder (Batal, Kembali, Detail)
- **Icon Button**: Button dengan icon untuk aksi cepat (Edit, Hapus, View)

### 2. Card (Kartu)
- **Course Card**: Untuk menampilkan mata pelajaran
- **Stat Card**: Untuk menampilkan statistik
- **Info Card**: Untuk menampilkan informasi

### 3. Table (Tabel)
- **Data Table**: Untuk menampilkan data dalam format tabel
- **Action Column**: Kolom untuk tombol aksi (Edit, Hapus, View)
- **Pagination**: Untuk navigasi halaman
- **Search/Filter**: Untuk mencari dan memfilter data

### 4. Form (Formulir)
- **Input Field**: Untuk input text
- **Textarea**: Untuk input text panjang
- **Select**: Untuk pilihan dropdown
- **Date/DateTime Picker**: Untuk input tanggal/waktu
- **File Upload**: Untuk upload file
- **Validation**: Untuk validasi form

### 5. Modal (Pop-up)
- **Confirmation Modal**: Untuk konfirmasi aksi
- **Form Modal**: Untuk form dalam modal
- **Info Modal**: Untuk menampilkan informasi

### 6. Alert (Peringatan)
- **Success Alert**: Untuk pesan sukses
- **Error Alert**: Untuk pesan error
- **Warning Alert**: Untuk pesan peringatan
- **Info Alert**: Untuk pesan informasi

### 7. Badge (Label)
- **Status Badge**: Untuk status (Aktif, Nonaktif)
- **Type Badge**: Untuk jenis (UTS, UAS, Kuis)
- **Role Badge**: Untuk role (Admin, Guru, Siswa)

### 8. Breadcrumb (Navigasi)
- **Breadcrumb**: Untuk navigasi hierarkis
- **Home → Parent → Current**: Format breadcrumb

### 9. Sidebar (Menu Samping)
- **Menu Items**: Item menu dengan icon
- **Active State**: State aktif untuk menu yang sedang dibuka
- **Hover Effect**: Efek hover untuk interaktivitas

### 10. Navbar (Menu Atas)
- **Page Title**: Judul halaman
- **User Menu**: Menu user dengan avatar dan logout
- **Notification**: Notifikasi (jika ada)

---

## 🎯 Best Practices

### 1. Konsistensi
- Gunakan komponen yang sama di semua halaman
- Warna dan spacing yang konsisten
- Icon yang jelas dan konsisten

### 2. Feedback
- Loading state untuk aksi yang memakan waktu
- Success message setelah aksi berhasil
- Error message yang jelas
- Confirmation modal untuk aksi destructive

### 3. Accessibility
- Kontras warna yang cukup
- Text yang jelas dan mudah dibaca
- Keyboard navigation
- Screen reader friendly

### 4. Performance
- Lazy loading untuk gambar
- Pagination untuk data banyak
- Filter untuk mengurangi data
- Caching untuk data statis

### 5. Mobile First
- Design untuk mobile dulu
- Touch-friendly buttons (min 44x44px)
- Responsive images
- Collapsible sidebar di mobile

---

**Last Updated**: 2024-12-19
**Version**: 1.0.0

