# SIMPESISIR – Sistem Informasi Pencatatan Pengamatan Ekosistem Pesisir

UTS Praktikum Individual – Sistem Basis Data (PHP + MySQL).
Aplikasi untuk mencatat hasil pengamatan ekosistem pesisir (mangrove, lamun, terumbu karang) per lokasi, dengan login, dua peran (admin & petugas), dan CRUD.

## Kebutuhan
- XAMPP (Apache + MySQL/MariaDB), PHP 7.4 atau lebih baru
- Browser

## Instalasi
1. Salin folder `simpesisir` ke `C:\xampp\htdocs\` (hasilnya `C:\xampp\htdocs\simpesisir`).
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin` → tab **Import** → pilih `database.sql` → **Go**.
   (Database `db_simpesisir`, tabel, constraint, dan data awal akan terbentuk.)
4. Cek `config/koneksi.php`. Default: host `localhost`, user `root`, password kosong, database `db_simpesisir`, `BASE_URL` = `/simpesisir`. Ubah jika berbeda.
5. Buka `http://localhost/simpesisir/` di browser.

## Akun demonstrasi
| Peran   | Username   | Password awal |
|---------|------------|---------------|
| Admin   | `admin`    | disampaikan terpisah |
| Petugas | `petugas1` | disampaikan terpisah |

Password tersimpan sebagai hash bcrypt (`password_hash()`), diverifikasi dengan `password_verify()`. Segera ganti password awal lewat menu Pengguna (admin).

## Struktur folder
```
simpesisir/
├── database.sql          # DDL + constraint + data awal
├── config/koneksi.php    # konfigurasi & koneksi (mysqli, exception mode)
├── includes/             # init (session, CSRF, guard login/peran), header, footer, validasi
├── login.php, logout.php, dashboard.php, index.php
├── pengamatan/           # CRUD pengamatan (admin & petugas)
├── lokasi/               # CRUD lokasi (admin)
├── users/                # CRUD pengguna (admin)
└── assets/style.css
```

## Hak akses
| Fitur | Admin | Petugas |
|---|---|---|
| Login/logout, dashboard | ✔ | ✔ |
| Lihat semua pengamatan | ✔ | ✔ |
| Tambah pengamatan | ✔ | ✔ |
| Ubah/hapus pengamatan | semua data | hanya data miliknya |
| Kelola lokasi | ✔ | ✘ (403) |
| Kelola pengguna | ✔ | ✘ (403) |

## Fitur keamanan
- Password di-hash (bcrypt); tidak ada kredensial di source code.
- Pesan gagal login seragam ("Username atau password salah").
- `session_regenerate_id(true)` setelah login; cookie session `HttpOnly` + `SameSite=Lax`.
- Pemeriksaan login & peran di server pada setiap halaman (`require_login()`, `require_role()`); peran diambil ulang dari database, bukan dari form/URL.
- Prepared statement di semua query yang memakai input pengguna.
- Output di-escape (`htmlspecialchars`) untuk mencegah XSS.
- Token CSRF pada semua form POST (termasuk hapus dan logout); operasi hapus/logout hanya lewat POST.
- Foreign key `ON DELETE RESTRICT`: lokasi/pengguna yang masih dipakai pengamatan tidak bisa dihapus, dan error ditangani dengan pesan ramah.
- Error database dicatat di log server, pengguna hanya melihat pesan umum.
