# Design Guide - E-Learning SMK Yadika 13

## 🎨 Konsep Desain

### Prinsip Desain
- **Minimalis**: Interface yang bersih tanpa elemen berlebihan
- **User-Friendly**: Mudah digunakan oleh semua kalangan (admin, guru, siswa)
- **Modern**: Mengikuti tren desain web terkini
- **Responsif**: Optimal di desktop, tablet, dan mobile
- **Konsisten**: Penggunaan warna, tipografi, dan komponen yang seragam

### Palet Warna

#### Primary Colors
- **Primary Blue**: `#4A90E2` - Warna utama untuk tombol dan aksi utama
- **Secondary Purple**: `#7B68EE` - Warna sekunder untuk highlight
- **Success Green**: `#27AE60` - Untuk sukses/positif
- **Warning Orange**: `#F39C12` - Untuk peringatan
- **Danger Red**: `#E74C3C` - Untuk error/hapus
- **Info Cyan**: `#3498DB` - Untuk informasi

#### Neutral Colors
- **Background Light**: `#F8F9FA` - Background utama
- **Background White**: `#FFFFFF` - Background card
- **Text Primary**: `#2C3E50` - Teks utama
- **Text Secondary**: `#7F8C8D` - Teks sekunder
- **Border**: `#E1E8ED` - Border elemen
- **Hover**: `#F0F4F8` - Hover state

### Tipografi
- **Font Family**: 'Inter', 'Segoe UI', sans-serif
- **Heading 1**: 32px, Bold
- **Heading 2**: 24px, Bold
- **Heading 3**: 20px, Semi-Bold
- **Body**: 16px, Regular
- **Small**: 14px, Regular
- **Caption**: 12px, Regular

### Spacing System
- **XS**: 4px
- **SM**: 8px
- **MD**: 16px
- **LG**: 24px
- **XL**: 32px
- **XXL**: 48px

### Border Radius
- **Small**: 4px
- **Medium**: 8px
- **Large**: 12px
- **Round**: 50px (untuk avatar)

### Shadows
- **Small**: `0 1px 3px rgba(0,0,0,0.1)`
- **Medium**: `0 4px 6px rgba(0,0,0,0.1)`
- **Large**: `0 10px 20px rgba(0,0,0,0.15)`

---

## 🧩 Komponen UI

### 1. Button (Tombol)

#### Primary Button
```html
<button class="btn btn-primary">
    <i class="fas fa-plus me-2"></i>Tambah Data
</button>
```
- Background: Primary Blue
- Text: White
- Padding: 12px 24px
- Border Radius: 8px
- Hover: Darker blue dengan shadow

#### Secondary Button
```html
<button class="btn btn-secondary">Batal</button>
```
- Background: Light gray
- Text: Dark gray
- Border: 1px solid gray

#### Success Button
```html
<button class="btn btn-success">
    <i class="fas fa-check me-2"></i>Simpan
</button>
```
- Background: Success Green
- Text: White

#### Danger Button
```html
<button class="btn btn-danger">
    <i class="fas fa-trash me-2"></i>Hapus
</button>
```
- Background: Danger Red
- Text: White

#### Outline Button
```html
<button class="btn btn-outline-primary">Detail</button>
```
- Background: Transparent
- Border: 2px solid Primary Blue
- Text: Primary Blue
- Hover: Fill dengan Primary Blue

#### Button Sizes
- **Small**: `btn-sm` (padding: 8px 16px)
- **Medium**: Default (padding: 12px 24px)
- **Large**: `btn-lg` (padding: 16px 32px)

### 2. Card (Kartu)

```html
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-book me-2"></i>Judul Card
        </h5>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

**Styling:**
- Background: White
- Border Radius: 12px
- Shadow: Medium
- Padding: 24px
- Border: None

### 3. Sidebar (Menu Samping)

**Struktur:**
- Background: White dengan shadow
- Width: 260px (desktop), hidden (mobile)
- Sticky position
- Logo di atas
- Menu items dengan icon
- Active state dengan highlight

**Menu Item:**
- Padding: 12px 16px
- Border Radius: 8px
- Hover: Light background
- Active: Primary blue background dengan white text
- Icon: 20px, margin-right: 12px

### 4. Navbar (Menu Atas)

**Struktur:**
- Height: 64px
- Background: White
- Shadow: Small
- Sticky position
- Logo/Brand di kiri
- User menu di kanan (avatar, name, dropdown)

### 5. Table (Tabel)

```html
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Kolom 1</th>
                <th>Kolom 2</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Data 1</td>
                <td>Data 2</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary">Edit</button>
                        <button class="btn btn-outline-danger">Hapus</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

**Styling:**
- Border: 1px solid light gray
- Header: Light gray background
- Row hover: Light blue background
- Padding: 12px
- Striped rows untuk readability

### 6. Form (Formulir)

```html
<div class="form-group mb-3">
    <label class="form-label">Nama Field</label>
    <input type="text" class="form-control" placeholder="Masukkan data">
    <small class="form-text text-muted">Hint text</small>
</div>
```

**Styling:**
- Input: Border 1px, border-radius 8px, padding 12px
- Focus: Border primary blue, shadow
- Error: Red border, error message di bawah
- Label: Bold, margin-bottom 8px

### 7. Modal (Pop-up)

```html
<div class="modal fade" id="exampleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Judul Modal</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
```

**Styling:**
- Overlay: Dark dengan opacity
- Modal: White background, border-radius 12px
- Shadow: Large
- Padding: 24px

### 8. Badge (Label)

```html
<span class="badge bg-primary">Active</span>
<span class="badge bg-success">Aktif</span>
<span class="badge bg-danger">Nonaktif</span>
```

**Styling:**
- Padding: 6px 12px
- Border-radius: 20px
- Font-size: 12px
- Font-weight: 600

### 9. Alert (Peringatan)

```html
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>Berhasil menyimpan data
</div>
```

**Variants:**
- Success: Green background
- Error: Red background
- Warning: Orange background
- Info: Blue background

### 10. Breadcrumb (Navigasi)

```html
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Halaman Saat Ini</li>
    </ol>
</nav>
```

---

## 📱 Layout Halaman

### Struktur Umum
```
┌─────────────────────────────────────────┐
│           Navbar (64px)                 │
├──────────┬──────────────────────────────┤
│          │                              │
│ Sidebar  │     Main Content Area        │
│ (260px)  │                              │
│          │                              │
│          │                              │
└──────────┴──────────────────────────────┘
```

### Breakpoints
- **Mobile**: < 768px (Sidebar hidden, hamburger menu)
- **Tablet**: 768px - 1024px (Sidebar collapsible)
- **Desktop**: > 1024px (Sidebar visible)

---

## 👥 Role-Based Design

### 1. Admin Dashboard

**Fitur Utama:**
- Statistik overview (total users, courses, etc.)
- Quick actions (tambah user, course, etc.)
- Recent activities
- Data tables dengan filter dan pagination

**Komponen:**
- Stat cards (4 kolom)
- Chart/Graph (jika ada)
- Data tables
- Action buttons

### 2. Guru Dashboard

**Fitur Utama:**
- Mata pelajaran saya
- Tugas yang perlu dinilai
- Jadwal mengajar
- Statistik siswa

**Komponen:**
- Course cards
- Task list
- Calendar widget
- Progress indicators

### 3. Siswa Dashboard

**Fitur Utama:**
- Mata pelajaran terdaftar
- Tugas yang perlu dikumpulkan
- Nilai terbaru
- Jadwal pembelajaran

**Komponen:**
- Course cards
- Assignment cards
- Grade cards
- Progress bars

---

## 🔄 Alur Interaksi Pengguna

### Alur Admin - Tambah User
1. Klik menu "User Manage"
2. Klik tombol "Tambah User" (primary button)
3. Isi form (nama, email, role, dll)
4. Klik "Simpan" (success button)
5. Redirect ke list users dengan pesan sukses

### Alur Guru - Buat Tugas
1. Klik menu "Mata Pelajaran"
2. Pilih mata pelajaran
3. Klik "Tugas" di card
4. Klik "Buat Tugas" (primary button)
5. Isi form (judul, deskripsi, deadline, dll)
6. Klik "Simpan"
7. Redirect ke list tugas

### Alur Siswa - Kumpulkan Tugas
1. Klik menu "Tugas"
2. Pilih tugas dari list
3. Klik "Detail" atau langsung klik tugas
4. Baca instruksi
5. Upload file atau tulis jawaban
6. Klik "Kumpulkan" (primary button)
7. Konfirmasi modal muncul
8. Klik "Ya, Kumpulkan"
9. Pesan sukses muncul

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

## 📋 Checklist Implementasi

### Phase 1: Foundation
- [x] Setup color palette
- [x] Setup typography
- [x] Setup spacing system
- [ ] Create base components (button, card, etc.)
- [ ] Create layout components (sidebar, navbar)

### Phase 2: Admin Pages
- [ ] Dashboard
- [ ] User Management
- [ ] Course Management
- [ ] Data Master

### Phase 3: Guru Pages
- [ ] Dashboard
- [ ] Mata Pelajaran
- [ ] Materi
- [ ] Tugas
- [ ] Ujian
- [ ] Absensi

### Phase 4: Siswa Pages
- [ ] Dashboard
- [ ] Mata Pelajaran
- [ ] Tugas
- [ ] Nilai
- [ ] Profil

### Phase 5: Polish
- [ ] Animations
- [ ] Transitions
- [ ] Loading states
- [ ] Error handling
- [ ] Mobile optimization

---

## 🚀 Quick Start

1. **Update Layout**: Gunakan layout baru dengan sidebar dan navbar
2. **Update Colors**: Terapkan color palette baru
3. **Update Components**: Gunakan komponen yang sudah didesain
4. **Update Pages**: Terapkan desain ke setiap halaman
5. **Test**: Test di berbagai device dan browser

---

## 📚 Referensi

- [Bootstrap 5](https://getbootstrap.com/)
- [Font Awesome](https://fontawesome.com/)
- [Material Design](https://material.io/design)
- [Ant Design](https://ant.design/)

---

**Last Updated**: 2024-12-19
**Version**: 1.0.0

