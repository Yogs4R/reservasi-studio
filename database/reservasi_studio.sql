-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Jun 2026 pada 12.55
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reservasi_studio`
--
CREATE DATABASE IF NOT EXISTS `reservasi_studio`;
USE `reservasi_studio`;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------

--
-- Struktur dari tabel `alat_media`
--

DROP TABLE IF EXISTS `alat_media`;
CREATE TABLE IF NOT EXISTS `alat_media` (
  `id_alat` int(11) NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `desc_alat` text DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `kondisi_alat` varchar(100) DEFAULT NULL,
  `foto_alat` varchar(255) DEFAULT NULL,
  `status_ketersediaan` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alat_media`
--

INSERT IGNORE INTO `alat_media` (`id_alat`, `nama_alat`, `desc_alat`, `id_kategori`, `harga`, `stok`, `kondisi_alat`, `foto_alat`, `status_ketersediaan`) VALUES
(1, 'Canon EOS R50', 'Kamera mirrorless 24.2MP cocok untuk fotografi produk dan konten sosial media.', 1, 150000.00, 3, 'Baik', '../../assets/img/uploads/Canon EOS R50.png', 'Tersedia'),
(2, 'Sony A6400', 'Kamera mirrorless dengan autofocus real-time tracking.', 1, 180000.00, 2, 'Baik', '../../assets/img/uploads/Sony A6400.png', 'Tersedia'),
(3, 'Sony FX30', 'Cinema camera profesional untuk produksi video berkualitas tinggi.', 2, 450000.00, 1, 'Baik', '../../assets/img/uploads/Sony FX30.png', 'Tersedia'),
(4, 'DJI RS3 Gimbal', 'Gimbal stabilizer untuk menghasilkan video yang stabil.', 2, 120000.00, 2, 'Baik', '../../assets/img/uploads/DJI RS3 Gimbal.png', 'Tersedia'),
(5, 'Rode PodMic', 'Mikrofon dinamis profesional untuk podcast dan streaming.', 3, 50000.00, 6, 'Baik', '../../assets/img/uploads/Rode PodMic.png', 'Tersedia'),
(6, 'Rodecaster Pro II', 'Mixer podcast profesional dengan fitur recording terintegrasi.', 3, 200000.00, 1, 'Baik', '../../assets/img/uploads/Rodecaster Pro II.png', 'Disewa'),
(7, 'Studio Podcast A', 'Ruangan podcast kapasitas 4 orang lengkap dengan peredam suara.', 4, 250000.00, 1, 'Baik', '../../assets/img/uploads/Studio Podcast A.png', 'Tersedia'),
(8, 'Studio Foto White Room', 'Studio foto profesional dengan background putih dan lighting lengkap.', 4, 300000.00, 1, 'Baik', '../../assets/img/uploads/Studio Foto White Room.png', 'Tersedia'),
-- Elgato Stream Deck MK2 description says 'livestream dan content creator.' or 'livestream and content creator.' Let's use 'livestream dan content creator.'
(9, 'Elgato Stream Deck MK2', 'Perangkat shortcut khusus livestream dan content creator.', 5, 40000.00, 3, 'Baik', '../../assets/img/uploads/Elgato Stream Deck MK2.png', 'Tersedia'),
(10, 'Logitech Brio 4K', 'Webcam profesional resolusi 4K untuk meeting dan streaming.', 5, 60000.00, 4, 'Baik', '../../assets/img/uploads/Logitech Brio 4K.png', 'Tersedia'),
(11, 'Focusrite Scarlett 2i2', 'Audio interface USB untuk recording profesional.', 6, 75000.00, 3, 'Baik', '../../assets/img/uploads/Focusrite Scarlett 2i2.png', 'Tersedia'),
(12, 'Audio Technica AT2020', 'Mikrofon condenser untuk voice over and recording studio.', 6, 50000.00, 4, 'Baik', '../../assets/img/uploads/Audio Technica AT2020.png', 'Tersedia'),
(13, 'Aputure 300D', 'Lampu LED profesional dengan intensitas tinggi.', 7, 100000.00, 3, 'Baik', '../../assets/img/uploads/Aputure 300D.png', 'Tersedia'),
(14, 'Godox Softbox Kit', 'Paket softbox lengkap untuk studio foto dan video.', 7, 50000.00, 5, 'Baik', '../../assets/img/uploads/Godox Softbox Kit.png', 'Tersedia'),
(15, 'DJI Mini 4 Pro', 'Drone ringan dengan kemampuan video 4K HDR.', 8, 350000.00, 1, 'Baik', '../../assets/img/uploads/DJI Mini 4 Pro.png', 'Tersedia'),
(16, 'DJI Air 3', 'Drone profesional dual-camera untuk kebutuhan komersial.', 8, 500000.00, 1, 'Baik', '../../assets/img/uploads/DJI Air 3.png', 'Disewa'),
(17, 'Mac Mini M2', 'Komputer editing video dan desain grafis berbasis Apple Silicon.', 9, 250000.00, 2, 'Baik', '../../assets/img/uploads/Mac Mini M2.png', 'Tersedia'),
(18, 'PC Editing Ryzen 9', 'Workstation editing video 4K dengan GPU RTX 4070.', 9, 350000.00, 1, 'Baik', '../../assets/img/uploads/PC Editing Ryzen 9.png', 'Tersedia'),
(19, 'Monitor External Feelworld F6', 'Monitor kamera 5.5 inci untuk monitoring video.', 10, 35000.00, 4, 'Baik', '../../assets/img/uploads/Monitor External Feelworld F6.png', 'Tersedia'),
(20, 'Tripod Manfrotto Compact', 'Tripod ringan dan kokoh untuk kamera dan smartphone.', 10, 25000.00, 5, 'Perlu Perawatan', '../../assets/img/uploads/Tripod Manfrotto Compact.png', 'Maintenance');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_reservasi`
--

DROP TABLE IF EXISTS `detail_reservasi`;
CREATE TABLE IF NOT EXISTS `detail_reservasi` (
  `id_detail` int(11) NOT NULL,
  `id_reserv` int(11) DEFAULT NULL,
  `id_alat` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_reservasi`
--

INSERT IGNORE INTO `detail_reservasi` (`id_detail`, `id_reserv`, `id_alat`, `jumlah`, `harga_satuan`, `subtotal`) VALUES
(1, 1, 2, 1, 180000.00, 1440000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `desc_kategori` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT IGNORE INTO `kategori` (`id_kategori`, `nama_kategori`, `desc_kategori`) VALUES
(1, 'Fotografi', 'Kategori peralatan fotografi seperti kamera, lensa, tripod, lighting, dan aksesoris pendukung pemotretan.'),
(2, 'Videografi', 'Kategori peralatan produksi video seperti kamera cinema, gimbal, drone, teleprompter, dan lighting video.'),
(3, 'Podcast', 'Kategori peralatan audio dan podcast seperti mikrofon, mixer, audio interface, headphone monitoring, dan perangkat recording.'),
(4, 'Studio Kreatif', 'Kategori ruangan studio yang dapat disewa untuk kebutuhan fotografi, videografi, podcast, livestream, maupun produksi konten.'),
(5, 'Livestreaming', 'Kategori perangkat untuk siaran langsung seperti webcam profesional, capture card, stream deck, dan lighting streaming.'),
(6, 'Audio Recording', 'Kategori alat perekaman suara profesional untuk voice over, dubbing, musik, dan produksi audio.'),
(7, 'Lighting', 'Kategori perlengkapan pencahayaan seperti softbox, LED panel, ring light, reflector, dan aksesoris pencahayaan.'),
(8, 'Drone', 'Kategori drone dan aksesorinya untuk kebutuhan pengambilan gambar dan video dari udara.'),
(9, 'Komputer Editing', 'Kategori perangkat komputer and workstation untuk editing foto, video, desain grafis, dan produksi multimedia.'),
(10, 'Aksesoris Produksi', 'Kategori perlengkapan pendukung produksi seperti memory card, battery pack, monitor eksternal, dan tas kamera.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_reserv` int(11) DEFAULT NULL,
  `tgl_pembayaran` datetime NOT NULL,
  `jml_pembayaran` decimal(10,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `kode_transaksi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT IGNORE INTO `pembayaran` (`id_pembayaran`, `id_reserv`, `tgl_pembayaran`, `jml_pembayaran`, `bukti_pembayaran`, `kode_transaksi`) VALUES
(1, 1, '2026-06-08 01:21:13', 1440000.00, 'bukti_1_1780856473.png', 'TRX20260607000163');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservasi`
--

DROP TABLE IF EXISTS `reservasi`;
CREATE TABLE IF NOT EXISTS `reservasi` (
  `id_reserv` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `tgl_reserv` datetime NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status_reserv` varchar(50) DEFAULT 'Pending',
  `harga_total` decimal(10,2) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reservasi`
--

INSERT IGNORE INTO `reservasi` (`id_reserv`, `id_user`, `tgl_reserv`, `tgl_mulai`, `tgl_selesai`, `status_reserv`, `harga_total`, `metode_pembayaran`) VALUES
(1, 2, '2026-06-08 01:20:02', '2026-06-15', '2026-06-22', 'Booked', 1440000.00, 'Transfer Bank Mandiri');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `role` enum('admin','pelanggan') DEFAULT 'pelanggan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT IGNORE INTO `user` (`id_user`, `nama`, `email`, `id_password`, `no_hp`, `role`) VALUES
(1, 'Admin StudioHub', 'admin@studiohub.com', '$2y$10$tM28Y1Qx7rC8Zz8v2z8yXeC.m9pZ9BwD7qQ1q0lZ3F4z7e9K3p4mK', '081234567890', 'admin'),
(2, 'Abyan Santoso', 'abyan@gmail.com', '$2y$10$oXg8D7n9wH1qY6Z.z8z8YeC.m9pZ9BwD7qQ1q0lZ3F4z7e9K3p4mK', '085712345678', 'pelanggan'),
(3, 'Ahmad Fauzi', 'ahmad.fauzi@gmail.com', '$2y$10$abcdefghijklmnopqrstuv1234567890abcdefghi', '081234567801', 'pelanggan'),
(4, 'Siti Nurhaliza', 'siti.nurhaliza@gmail.com', '$2y$10$bcdefghijklmnopqrstuvw1234567890abcdefghij', '081234567802', 'pelanggan'),
(5, 'Budi Santoso', 'budi.santoso@gmail.com', '$2y$10$cdefghijklmnopqrstuvwx1234567890abcdefghijk', '081234567803', 'pelanggan'),
(6, 'Dewi Lestari', 'dewi.lestari@gmail.com', '$2y$10$defghijklmnopqrstuvwxy1234567890abcdefghijkl', '081234567804', 'pelanggan'),
(7, 'Rizky Pratama', 'rizky.pratama@gmail.com', '$2y$10$efghijklmnopqrstuvwxyz1234567890abcdefghijklm', '081234567805', 'pelanggan'),
(8, 'Nabila Putri', 'nabila.putri@gmail.com', '$2y$10$fghijklmnopqrstuvwxyza1234567890abcdefghijklmn', '081234567806', 'pelanggan'),
(9, 'Fajar Ramadhan', 'fajar.ramadhan@gmail.com', '$2y$10$ghijklmnopqrstuvwxyzab1234567890abcdefghijklmno', '081234567807', 'pelanggan'),
(10, 'Intan Permata', 'intan.permata@gmail.com', '$2y$10$hijklmnopqrstuvwxyzabc1234567890abcdefghijklmnop', '081234567808', 'pelanggan'),
(11, 'Dimas Saputra', 'dimas.saputra@gmail.com', '$2y$10$ijklmnopqrstuvwxyzabcd1234567890abcdefghijklmnopq', '081234567809', 'pelanggan'),
(12, 'Putri Maharani', 'putri.maharani@gmail.com', '$2y$10$jklmnopqrstuvwxyzabcde1234567890abcdefghijklmnopqr', '081234567810', 'pelanggan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alat_media`
--
ALTER TABLE `alat_media`
  ADD PRIMARY KEY (`id_alat`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `detail_reservasi`
--
ALTER TABLE `detail_reservasi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_reserv` (`id_reserv`),
  ADD KEY `id_alat` (`id_alat`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `id_reserv` (`id_reserv`);

--
-- Indeks untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reserv`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alat_media`
--
ALTER TABLE `alat_media`
  MODIFY `id_alat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `detail_reservasi`
--
ALTER TABLE `detail_reservasi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reserv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `alat_media`
--
ALTER TABLE `alat_media`
  ADD CONSTRAINT `alat_media_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_reservasi`
--
ALTER TABLE `detail_reservasi`
  ADD CONSTRAINT `detail_reservasi_ibfk_1` FOREIGN KEY (`id_reserv`) REFERENCES `reservasi` (`id_reserv`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_reservasi_ibfk_2` FOREIGN KEY (`id_alat`) REFERENCES `alat_media` (`id_alat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_reserv`) REFERENCES `reservasi` (`id_reserv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =========================================================================
-- MIGRASI: JALANKAN PERINTAH DI BAWAH INI JIKA DATABASE SUDAH DI-IMPORT SEBELUMNYA:
-- =========================================================================
-- ALTER TABLE `user` ADD COLUMN `role` ENUM('admin', 'pelanggan') DEFAULT 'pelanggan' AFTER `id_password`;
-- UPDATE `user` SET `role` = 'admin' WHERE `id_user` = 1;
-- =========================================================================
