# Reservasi Studio

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-Native-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![XAMPP](https://img.shields.io/badge/XAMPP-Setup-orange?logo=apache&logoColor=white)](https://www.apachefriends.org/)

Project web reservasi studio berbasis PHP Native.

## Fitur Struktur Awal
- Template layout dasar dengan `includes/header.php` dan `includes/footer.php`
- Modul autentikasi, user, alat, kategori, reservasi, pembayaran, dan laporan
- Asset lokal untuk CSS, JavaScript, dan folder upload gambar

## Cara Menjalankan di XAMPP
1. Pastikan XAMPP sudah terpasang dan service `Apache` serta `MySQL` sudah aktif.
2. Simpan folder project ini di direktori `htdocs`, misalnya:
	`C:\xampp\htdocs\reservasi-studio`
3. Buka browser lalu akses:
	`http://localhost/reservasi-studio`
4. Jika halaman belum tampil sempurna, cek file `includes/header.php` dan `includes/footer.php` untuk memastikan path asset lokal sudah sesuai.

## Import Database di XAMPP
1. Buka `http://localhost/phpmyadmin`.
2. Buat database baru dengan nama `reservasi_db`.
3. Klik database tersebut, lalu pilih menu **Import**.
4. Pilih file `database/reservasi_db.sql`.
5. Klik **Go** untuk menjalankan import.
6. Sesuaikan koneksi database di `config/database.php` jika username, password, atau nama database berbeda.

## Alur Kolaborasi Git untuk Tim
Gunakan alur berikut agar sinkron saat bekerja dalam kelompok:

### Opsi A. Pemula

Pakai alur ini jika ingin langkah yang paling sederhana:

### 1. Clone Project (Satu kali saja)
```bash
git clone https://github.com/Yogs4R/reservasi-studio.git
cd reservasi-studio
```

### 2. Alur git

```bash
git pull origin main
git add .
git commit -m "Deskripsi perubahan"
git push origin main
```

### Opsi B. Advanced

Pakai alur ini jika bekerja dengan branch fitur agar perubahan lebih terkontrol.

### 1. Clone project (Satu kali saja)
```bash
git clone https://github.com/Yogs4R/reservasi-studio.git
cd reservasi-studio
```

### 2. Ambil perubahan terbaru dari branch utama
```bash
git checkout main
git pull origin main
```

### 3. Buat branch fitur atau branch tugas
```bash
git checkout -b nama-branch-fitur
```

### 4. Kerjakan perubahan lalu simpan ke Git
```bash
git add .
git commit -m "Deskripsi perubahan"
git push origin nama-branch-fitur
```

### 5. Setelah selesai bekerja, kembali ke main dan sinkronkan lagi
```bash
git checkout main
git pull origin main
```

### 6. Jika ada branch lain atau pekerjaan tim lain
- Selalu jalankan `git pull origin main` sebelum mulai mengerjakan tugas baru.
- Jika sudah pindah branch, pastikan status kerja bersih sebelum `checkout` ke branch lain.
- Gunakan commit message yang jelas agar teman satu tim mudah memahami perubahan.

## Catatan Tambahan
- File upload disimpan di `assets/img/uploads/`.
- Folder tersebut tetap ada di repository lewat file `.gitkeep`, tetapi isi file upload diabaikan oleh Git.
- File `database/reservasi_db.sql` masih kosong dan siap diisi struktur tabel saat database final sudah dibuat.

## Struktur Folder dan Fungsi

```text
reservasi-studio/                              # Root project
├── assets/                                    # Tempat menyimpan file statis dari Bootstrap & kustomisasi
│   ├── css/                                   # bootstrap.min.css, style.css
│   │   └── style.css                          # CSS utama untuk tampilan project
│   ├── js/                                    # bootstrap.bundle.min.js, script.js
│   │   └── script.js                          # JavaScript utama untuk interaksi halaman
│   └── img/                                   # Direktori upload foto alat & bukti pembayaran
│       └── uploads/                           # Folder khusus hasil upload dari user/admin
│           └── .gitkeep                       # Penanda agar folder uploads tetap masuk Git
├── config/                                    # Konfigurasi inti aplikasi
│   └── database.php                           # Skrip murni untuk koneksi ke MySQL/MariaDB
├── database/                                  # Database project
│   └── reservasi_db.sql                       # File dump SQL yang berisi tabel awal (wajib ada!)
├── includes/                                  # Komponen UI yang dipakai berulang kali
│   ├── header.php                             # Tag <head>, pemanggilan CSS, dan Navbar
│   ├── footer.php                             # Tag penutup </body>, pemanggilan JS, dan Footer
│   └── sidebar.php                            # Sidebar khusus untuk halaman Admin
├── modules/                                   # Core aplikasi yang dibagi berdasarkan entitas/fitur
│   ├── auth/                                  # Modul autentikasi pengguna
│   │   ├── login.php                          # Form login pengguna
│   │   ├── register.php                       # Form pendaftaran akun pengguna
│   │   └── logout.php                         # Proses keluar dari sesi login
│   ├── user/                                  # Modul data pengguna
│   │   └── profil.php                         # Halaman profil pengguna untuk lihat/ubah data akun
│   ├── alat/                                  # Modul data alat studio
│   │   ├── index.php                          # Halaman list data alat
│   │   ├── create.php                         # Form tambah alat baru
│   │   ├── update.php                         # Form ubah data alat
│   │   └── delete.php                         # Proses hapus data alat
│   ├── kategori/                              # CRUD untuk Kategori
│   │   ├── index.php                          # Daftar kategori
│   │   ├── create.php                         # Form tambah kategori
│   │   ├── update.php                         # Form ubah kategori
│   │   └── delete.php                         # Proses hapus kategori
│   ├── reservasi/                             # Modul pemesanan studio
│   │   ├── form_booking.php                   # Form booking studio
│   │   ├── proses_booking.php                 # Proses simpan booking
│   │   └── riwayat.php                        # Riwayat reservasi pengguna
│   ├── pembayaran/                            # Modul pembayaran
│   │   ├── upload_bukti.php                   # Upload bukti pembayaran
│   │   └── verifikasi.php                     # Verifikasi pembayaran oleh admin
│   └── laporan/                               # Modul laporan
│       ├── index.php                          # Laporan dengan filter tanggal
│       └── cetak_pdf.php                      # Proses cetak laporan ke PDF
├── .gitignore                                 # Daftar file/folder yang tidak boleh di-push ke repo
├── README.md                                  # Petunjuk cara instalasi dan menjalankan project
├── LICENSE                                    # Lisensi proyek MIT
└── index.php                                  # Halaman utama (Landing Page)
```

### Fungsi Singkat Tiap Folder
- `assets/`: Menyimpan semua file statis untuk tampilan dan interaksi.
- `config/`: Menyimpan konfigurasi inti, terutama koneksi database.
- `database/`: Menyimpan file SQL untuk import database awal.
- `includes/`: Menyimpan komponen layout yang dipakai berulang.
- `modules/`: Menyimpan seluruh fitur utama aplikasi per modul.
- `.gitignore`: Mengatur file/folder yang tidak ikut dikirim ke repository.
- `README.md`: Dokumentasi project dan panduan penggunaan.
- `LICENSE`: Informasi lisensi proyek.
- `index.php`: Halaman landing atau entry point aplikasi.

## Lisensi
Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lengkap.