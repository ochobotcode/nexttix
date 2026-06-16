# 🎵 NexTix — Sistem Tiket Konser

Aplikasi web manajemen & penjualan tiket konser dengan dua sisi: halaman publik untuk pelanggan membeli tiket, dan panel admin untuk mengelola konser, tiket, pesanan, dan laporan.

---

## 📦 Cara Install (XAMPP / Laragon / dll)

### 1. Extract & Letakkan Folder
Letakkan folder `nexttix` di dalam folder `htdocs` server kamu, contoh:
```
E:\Software\pemrograman\xampp_no_uac\htdocs\nexttix\
```

### 2. Buat & Import Database
1. Buka **phpMyAdmin** → `http://localhost/phpmyadmin`
2. Klik tab **Import**
3. Pilih file `database.sql` dari folder project
4. Klik **Go** — ini otomatis membuat database `nexttix` lengkap dengan tabel, data contoh konser, dan akun.

### 3. ⚠️ WAJIB: Reset Password
Hash password di `database.sql` adalah placeholder. Setelah import, jalankan:
```
http://localhost/nexttix/reset_password.php
```
Ini akan mengatur ulang semua password ke nilai default (lihat tabel akun di bawah). **Setelah berhasil, hapus file `reset_password.php`** dari folder project untuk keamanan.

### 4. Cek Konfigurasi URL
Buka `config/app.php`, pastikan baris ini sesuai dengan nama folder project kamu:
```php
define('APP_URL', 'http://localhost/nexttix');
```
Jika kamu menamai folder berbeda (misal `tiketku`), ubah jadi `http://localhost/tiketku`.

### 5. Pastikan Folder Upload Bisa Ditulis
Folder `uploads/posters/` harus punya izin tulis (write permission) agar upload poster konser berfungsi. Di Windows/XAMPP biasanya sudah otomatis bisa.

### 6. Buka Website
```
http://localhost/nexttix/public/index.php
```

---

## 🔑 Akun Default

Semua login (admin, operator, maupun pelanggan) menggunakan **satu halaman yang sama**:
```
http://localhost/nexttix/login.php
```

| Role | Email | Password |
|---|---|---|
| **Admin** (akses penuh) | admin@nexttix.id | admin123 |
| **Operator** (tanpa kelola pengguna) | operator@nexttix.id | admin123 |
| **Pelanggan (contoh)** | budi@gmail.com | user123 |
| **Pelanggan (contoh)** | siti@gmail.com | user123 |
| **Pelanggan (contoh)** | ahmad@gmail.com | user123 |

Sistem otomatis mendeteksi apakah email cocok dengan akun **admin/operator** atau **pelanggan**, lalu mengarahkan ke dashboard yang sesuai.

---

## ✨ Fitur

### Halaman Publik (Pelanggan)
- Beranda dengan konser unggulan & konser mendatang
- Katalog konser dengan pencarian & filter (kota, status)
- Halaman detail konser + pilih kategori tiket & jumlah
- Checkout dengan ringkasan pesanan & pilihan metode pembayaran
- Riwayat "Tiket Saya" dengan filter status (Semua / Lunas / Pending)
- E-Tiket digital yang bisa dicetak / disimpan sebagai PDF
- Kelola profil & ganti password

### Panel Admin
- **Dashboard** — statistik real-time, grafik pendapatan bulanan & status pesanan (Chart.js)
- **Konser** — CRUD lengkap, upload poster, atur status & featured
- **Tiket** — CRUD kategori tiket per konser, kontrol stok otomatis
- **Pesanan** — kelola transaksi, konfirmasi/batalkan pesanan pending
- **Laporan** — filter berdasarkan tanggal & tipe (transaksi/konser/tiket), lengkap dengan **export ke Excel (.xls)** dan **export ke PDF** (cetak/simpan)
- **Pengguna Admin** — khusus role Admin, kelola akun admin & operator lainnya

---

## 🗂️ Struktur Folder

```
nexttix/
├── admin/          → Panel admin (dashboard, konser, tiket, orders, laporan, users)
├── auth/           → Guard session (check_admin.php, check_user.php)
├── assets/
│   ├── css/        → main.css (publik) & admin.css (panel admin)
│   └── js/         → main.js
├── config/         → app.php (helper & konstanta), database.php (koneksi PDO)
├── includes/       → navbar, sidebar, footer (shared)
├── public/         → Halaman publik (index, katalog, konser, checkout, dst.)
├── uploads/posters/→ Tempat upload poster konser
├── database.sql    → Schema + data contoh
├── login.php / register.php / logout.php
└── reset_password.php → Jalankan sekali, lalu hapus
```

---

## 🛠️ Tips Pemecahan Masalah

**Menu/link "Not Found"** → Pastikan `APP_URL` di `config/app.php` sesuai nama folder project di `htdocs`.

**"Email atau password salah"** → Jalankan `reset_password.php` (lihat langkah 3).

**Error koneksi database** → Cek `config/database.php`, sesuaikan `DB_USER`/`DB_PASS` jika MySQL kamu memakai password (default XAMPP: user `root`, password kosong).

**Upload poster gagal** → Pastikan folder `uploads/posters/` ada dan bisa ditulis.

**Export PDF membuka dialog print** → Ini disengaja — klik "Save as PDF" / "Simpan sebagai PDF" pada dialog cetak browser.
