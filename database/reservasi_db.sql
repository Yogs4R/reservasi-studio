-- Pembuatan Database di phpMyAdmin, run command ini jika belum buat database
-- CREATE DATABASE reservasi_studio;
-- USE reservasi_studio;

-- Tabel User (Tidak punya foreign key, dibuat pertama)
CREATE TABLE User (
    id_user INT AUTO_INCREMENT PRIMARY KEY, -- Angka bulat otomatis bertambah, jadi penanda unik (contoh id: 1 -> id: 2)
    nama VARCHAR(100) NOT NULL, -- Teks maksimal 100 karakter, wajib diisi (NOT NULL)
    email VARCHAR(100) NOT NULL UNIQUE, -- Wajib diisi dan tidak boleh ada email kembar (UNIQUE)
    id_password VARCHAR(255) NOT NULL, -- VARCHAR panjang (255) untuk menampung hasil hash password acak demi keamanan
    no_hp VARCHAR(20) -- Boleh kosong, pakai VARCHAR karena nomor HP kadang pakai awalan +62 atau 0
);

-- Tabel Kategori (Tidak punya foreign key, dibuat awal)
CREATE TABLE Kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY, -- ID unik urut otomatis untuk setiap kategori
    nama_kategori VARCHAR(100) NOT NULL, -- Nama kategori wajib diisi
    desc_kategori TEXT -- Pakai TEXT agar bisa menampung deskripsi panjang/paragraf, boleh kosong
);

-- Tabel Alat_Media (Bergantung pada Tabel Kategori)
CREATE TABLE Alat_Media (
    id_alat INT AUTO_INCREMENT PRIMARY KEY, -- ID unik alat otomatis
    nama_alat VARCHAR(100) NOT NULL, -- Nama alat wajib diisi
    desc_alat TEXT, -- Deskripsi spesifikasi alat
    id_kategori INT, -- Tipe INT, jembatan penghubung ke tabel Kategori
    harga DECIMAL(10, 2) NOT NULL, -- DECIMAL untuk presisi uang (10 digit, 2 di belakang koma), mencegah error pembulatan
    stok INT NOT NULL, -- Jumlah fisik barang berupa angka bulat, wajib isi
    kondisi_alat VARCHAR(100), -- Teks status barang (misal: Baik, Rusak), boleh kosong
    foto_alat VARCHAR(255), -- Hanya menyimpan nama/path file gambar (misal: assets/images/kamera.jpg), bukan gambar aslinya
    status_ketersediaan VARCHAR(50), -- Status alat (misal: Tersedia, Disewa)
    FOREIGN KEY (id_kategori) REFERENCES Kategori(id_kategori) ON DELETE SET NULL ON UPDATE CASCADE
    -- Jika Kategori dihapus, kolom ini jadi NULL (kosong) agar data alat tetap aman. Jika ID kategori berubah, otomatis update (CASCADE).
);

-- Tabel Reservasi (Bergantung pada Tabel User)
CREATE TABLE Reservasi (
    id_reserv INT AUTO_INCREMENT PRIMARY KEY, -- Nomor unik setiap struk/transaksi otomatis
    id_user INT, -- Jembatan ke tabel User (siapa yang menyewa)
    tgl_reserv DATETIME NOT NULL, -- Tipe DATETIME menyimpan waktu presisi (Tahun-Bulan-Hari Jam:Menit:Detik atau 2026-05-23 14:30:00)
    status_reserv VARCHAR(50) DEFAULT 'Pending', -- Jika saat insert status dikosongkan, sistem otomatis mengisinya dengan 'Pending'
    harga_total DECIMAL(10, 2), -- Total tagihan semua alat, presisi uang dengan DECIMAL
    metode_pembayaran VARCHAR(50), -- Teks cara bayar (misal: Transfer, GoPay)
    FOREIGN KEY (id_user) REFERENCES User(id_user) ON DELETE CASCADE ON UPDATE CASCADE
    -- Jika user dihapus dari sistem, seluruh riwayat pesanannya otomatis terhapus (CASCADE)
);

-- Tabel Detail_Reservasi (Bergantung pada Tabel Reservasi dan Alat_Media)
-- Ini adalah tabel pemecah relasi Many-to-Many (daftar list rincian barang/ struk)
CREATE TABLE Detail_Reservasi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY, -- ID urut untuk setiap baris keranjang
    id_reserv INT, -- Menunjuk ke nomor struk (transaksi)
    id_alat INT, -- Menunjuk ke alat yang sedang disewa
    jumlah INT NOT NULL, -- Kuantitas alat yang disewa berupa angka bulat
    harga_satuan DECIMAL(10, 2) NOT NULL, -- Membekukan riwayat harga sewa alat saat itu (DECIMAL presisi uang)
    subtotal DECIMAL(10, 2) NOT NULL, -- Hasil kali jumlah dan harga_satuan, mempercepat loading agar PHP tidak perlu ngitung ulang
    FOREIGN KEY (id_reserv) REFERENCES Reservasi(id_reserv) ON DELETE CASCADE ON UPDATE CASCADE, -- Hapus struk = hapus rinciannya
    FOREIGN KEY (id_alat) REFERENCES Alat_Media(id_alat) ON DELETE CASCADE ON UPDATE CASCADE -- Hapus alat = hapus rincian riwayat sewanya
);

-- Tabel Pembayaran (Bergantung pada Tabel Reservasi)
CREATE TABLE Pembayaran (
    id_pembayaran INT AUTO_INCREMENT PRIMARY KEY, -- Nomor unik riwayat pembayaran
    id_reserv INT, -- Menunjuk ke transaksi yang sedang dilunasi
    tgl_pembayaran DATETIME NOT NULL, -- Waktu presisi saat uang/bukti dikirim
    jml_pembayaran DECIMAL(10, 2) NOT NULL, -- Nominal uang transfer presisi
    bukti_pembayaran VARCHAR(255), -- Menyimpan nama/path lokasi file foto struk transfer
    kode_transaksi VARCHAR(100) UNIQUE, -- Kode referensi bank/transfer, wajib unik agar tidak ada bukti palsu ganda
    FOREIGN KEY (id_reserv) REFERENCES Reservasi(id_reserv) ON DELETE CASCADE ON UPDATE CASCADE
    -- Jika transaksi dihapus, data pembayarannya ikut terhapus agar bersih
);
