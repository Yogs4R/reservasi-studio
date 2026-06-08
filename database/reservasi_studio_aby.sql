-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Jun 2026 pada 17.49
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

-- --------------------------------------------------------

--
-- Struktur dari tabel `alat_media`
--

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
(9, 'Elgato Stream Deck MK2', 'Perangkat shortcut khusus livestream dan content creator.', 5, 40000.00, 3, 'Baik', '../../assets/img/uploads/Elgato Stream Deck MK2.png', 'Tersedia'),
(10, 'Logitech Brio 4K', 'Webcam profesional resolusi 4K untuk meeting dan streaming.', 5, 60000.00, 4, 'Baik', '../../assets/img/uploads/Logitech Brio 4K.png', 'Tersedia'),
(11, 'Focusrite Scarlett 2i2', 'Audio interface USB untuk recording profesional.', 6, 75000.00, 3, 'Baik', '../../assets/img/uploads/Focusrite Scarlett 2i2.png', 'Tersedia'),
(12, 'Audio Technica AT2020', 'Mikrofon condenser untuk voice over dan recording studio.', 6, 50000.00, 4, 'Baik', '../../assets/img/uploads/Audio Technica AT2020.png', 'Tersedia'),
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
(1, 1, 1, 1, 150000.00, 150000.00),
(2, 2, 7, 1, 250000.00, 250000.00),
(3, 3, 15, 1, 350000.00, 350000.00),
(4, 4, 6, 1, 200000.00, 200000.00),
(5, 5, 3, 1, 450000.00, 450000.00),
(6, 6, 3, 1, 450000.00, 450000.00),
(7, 6, 13, 1, 100000.00, 100000.00),
(8, 6, 14, 1, 50000.00, 50000.00),
(9, 7, 8, 1, 300000.00, 300000.00),
(10, 8, 17, 1, 250000.00, 250000.00),
(11, 8, 19, 1, 25000.00, 25000.00),
(12, 9, 16, 1, 500000.00, 500000.00),
(13, 9, 1, 1, 150000.00, 150000.00),
(14, 9, 13, 1, 100000.00, 100000.00),
(15, 10, 2, 1, 180000.00, 180000.00),
(16, 11, 6, 1, 200000.00, 200000.00),
(17, 11, 9, 1, 20000.00, 20000.00),
(18, 12, 8, 1, 300000.00, 300000.00),
(19, 12, 13, 1, 100000.00, 100000.00),
(20, 13, 17, 1, 250000.00, 250000.00),
(21, 13, 12, 1, 25000.00, 25000.00),
(22, 14, 16, 1, 500000.00, 500000.00),
(23, 14, 17, 1, 250000.00, 250000.00),
(24, 14, 13, 1, 100000.00, 100000.00),
(25, 15, 1, 1, 150000.00, 150000.00),
(26, 16, 7, 1, 250000.00, 250000.00),
(27, 16, 10, 1, 60000.00, 60000.00),
(28, 16, 19, 1, 10000.00, 10000.00),
(29, 17, 16, 1, 500000.00, 500000.00),
(30, 17, 17, 1, 250000.00, 250000.00),
(31, 17, 8, 1, 300000.00, 300000.00),
(32, 17, 5, 2, 50000.00, 100000.00),
(33, 18, 17, 1, 250000.00, 250000.00),
(34, 18, 9, 1, 25000.00, 25000.00),
(35, 19, 2, 1, 180000.00, 180000.00),
(36, 19, 19, 1, 10000.00, 10000.00),
(37, 20, 3, 1, 450000.00, 450000.00),
(38, 20, 17, 1, 250000.00, 250000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

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
(9, 'Komputer Editing', 'Kategori perangkat komputer dan workstation untuk editing foto, video, desain grafis, dan produksi multimedia.'),
(10, 'Aksesoris Produksi', 'Kategori perlengkapan pendukung produksi seperti memory card, battery pack, monitor eksternal, dan tas kamera.');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

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
(1, 1, '2026-06-01 08:20:00', 150000.00, 'bukti_001.jpg', 'TRX20260601001'),
(2, 2, '2026-06-01 09:35:00', 250000.00, 'bukti_002.jpg', 'TRX20260601002'),
(3, 3, '2026-06-02 10:50:00', 350000.00, 'bukti_003.jpg', 'TRX20260602003'),
(4, 5, '2026-06-03 11:05:00', 450000.00, 'bukti_005.jpg', 'TRX20260603005'),
(5, 6, '2026-06-03 15:35:00', 600000.00, 'bukti_006.jpg', 'TRX20260603006'),
(6, 7, '2026-06-04 08:55:00', 300000.00, 'bukti_007.jpg', 'TRX20260604007'),
(7, 8, '2026-06-04 14:15:00', 275000.00, 'bukti_008.jpg', 'TRX20260604008'),
(8, 9, '2026-06-05 09:30:00', 750000.00, 'bukti_009.jpg', 'TRX20260605009'),
(9, 12, '2026-06-06 12:50:00', 400000.00, 'bukti_012.jpg', 'TRX20260606012'),
(10, 13, '2026-06-07 09:20:00', 275000.00, 'bukti_013.jpg', 'TRX20260607013'),
(11, 14, '2026-06-07 14:55:00', 850000.00, 'bukti_014.jpg', 'TRX20260607014'),
(12, 15, '2026-06-08 08:25:00', 150000.00, 'bukti_015.jpg', 'TRX20260608015'),
(13, 16, '2026-06-08 11:40:00', 320000.00, 'bukti_016.jpg', 'TRX20260608016'),
(14, 17, '2026-06-09 13:05:00', 950000.00, 'bukti_017.jpg', 'TRX20260609017'),
(15, 18, '2026-06-09 15:30:00', 275000.00, 'bukti_018.jpg', 'TRX20260609018'),
(16, 20, '2026-06-10 16:10:00', 700000.00, 'bukti_020.jpg', 'TRX20260610020');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reservasi`
--

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
(1, 1, '2026-06-01 08:15:00', '2026-06-05', '2026-06-05', 'Finished', 150000.00, 'QRIS'),
(2, 2, '2026-06-01 09:30:00', '2026-06-06', '2026-06-06', 'Finished', 250000.00, 'Transfer Bank'),
(3, 3, '2026-06-02 10:45:00', '2026-06-07', '2026-06-07', 'Finished', 350000.00, 'E-Wallet'),
(4, 4, '2026-06-02 13:20:00', '2026-06-08', '2026-06-08', 'Cancelled', 200000.00, 'QRIS'),
(5, 5, '2026-06-03 11:00:00', '2026-06-09', '2026-06-09', 'Finished', 450000.00, 'Transfer Bank'),
(6, 6, '2026-06-03 15:30:00', '2026-06-10', '2026-06-11', 'Finished', 600000.00, 'Virtual Account'),
(7, 7, '2026-06-04 08:50:00', '2026-06-12', '2026-06-12', 'Booked', 300000.00, 'QRIS'),
(8, 8, '2026-06-04 14:10:00', '2026-06-13', '2026-06-13', 'Booked', 275000.00, 'E-Wallet'),
(9, 9, '2026-06-05 09:25:00', '2026-06-14', '2026-06-15', 'Booked', 750000.00, 'Transfer Bank'),
(10, 10, '2026-06-05 16:40:00', '2026-06-16', '2026-06-16', 'Pending', 180000.00, 'QRIS'),
(11, 1, '2026-06-06 10:10:00', '2026-06-17', '2026-06-17', 'Pending', 220000.00, 'E-Wallet'),
(12, 2, '2026-06-06 12:45:00', '2026-06-18', '2026-06-18', 'Waiting Payment', 400000.00, 'Transfer Bank'),
(13, 3, '2026-06-07 09:15:00', '2026-06-19', '2026-06-19', 'Waiting Payment', 275000.00, 'QRIS'),
(14, 4, '2026-06-07 14:50:00', '2026-06-20', '2026-06-21', 'Booked', 850000.00, 'Virtual Account'),
(15, 5, '2026-06-08 08:20:00', '2026-06-22', '2026-06-22', 'Finished', 150000.00, 'E-Wallet'),
(16, 6, '2026-06-08 11:35:00', '2026-06-23', '2026-06-23', 'On Going', 320000.00, 'QRIS'),
(17, 7, '2026-06-09 13:00:00', '2026-06-24', '2026-06-25', 'On Going', 1150000.00, 'Transfer Bank'),
(18, 8, '2026-06-09 15:25:00', '2026-06-26', '2026-06-26', 'Booked', 275000.00, 'QRIS'),
(19, 9, '2026-06-10 10:40:00', '2026-06-27', '2026-06-27', 'Pending', 190000.00, 'E-Wallet'),
(20, 10, '2026-06-10 16:05:00', '2026-06-28', '2026-06-29', 'Waiting Payment', 700000.00, 'Virtual Account');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT IGNORE INTO `user` (`id_user`, `nama`, `email`, `id_password`, `no_hp`) VALUES
(1, 'Admin StudioHub', 'admin@studiohub.com', '$2y$10$tM28Y1Qx7rC8Zz8v2z8yXeC.m9pZ9BwD7qQ1q0lZ3F4z7e9K3p4mK', '081234567890'),
(2, 'Abyan Santoso', 'abyan@gmail.com', '$2y$10$oXg8D7n9wH1qY6Z.z8z8YeC.m9pZ9BwD7qQ1q0lZ3F4z7e9K3p4mK', '085712345678'),
(3, 'Ahmad Fauzi', 'ahmad.fauzi@gmail.com', '$2y$10$abcdefghijklmnopqrstuv1234567890abcdefghi', '081234567801'),
(4, 'Siti Nurhaliza', 'siti.nurhaliza@gmail.com', '$2y$10$bcdefghijklmnopqrstuvw1234567890abcdefghij', '081234567802'),
(5, 'Budi Santoso', 'budi.santoso@gmail.com', '$2y$10$cdefghijklmnopqrstuvwx1234567890abcdefghijk', '081234567803'),
(6, 'Dewi Lestari', 'dewi.lestari@gmail.com', '$2y$10$defghijklmnopqrstuvwxy1234567890abcdefghijkl', '081234567804'),
(7, 'Rizky Pratama', 'rizky.pratama@gmail.com', '$2y$10$efghijklmnopqrstuvwxyz1234567890abcdefghijklm', '081234567805'),
(8, 'Nabila Putri', 'nabila.putri@gmail.com', '$2y$10$fghijklmnopqrstuvwxyza1234567890abcdefghijklmn', '081234567806'),
(9, 'Fajar Ramadhan', 'fajar.ramadhan@gmail.com', '$2y$10$ghijklmnopqrstuvwxyzab1234567890abcdefghijklmno', '081234567807'),
(10, 'Intan Permata', 'intan.permata@gmail.com', '$2y$10$hijklmnopqrstuvwxyzabc1234567890abcdefghijklmnop', '081234567808'),
(11, 'Dimas Saputra', 'dimas.saputra@gmail.com', '$2y$10$ijklmnopqrstuvwxyzabcd1234567890abcdefghijklmnopq', '081234567809'),
(12, 'Putri Maharani', 'putri.maharani@gmail.com', '$2y$10$jklmnopqrstuvwxyzabcde1234567890abcdefghijklmnopqr', '081234567810');

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
  MODIFY `id_alat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT untuk tabel `detail_reservasi`
--
ALTER TABLE `detail_reservasi`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reserv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
