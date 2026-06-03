-- Database: `reservasi_studio`
CREATE DATABASE IF NOT EXISTS `reservasi_studio`;
USE `reservasi_studio`;

-- IMPORTANT: Tambahan kolom baru di tabel 'reservasi', run query SQL ini di XAMPP Shell
-- ALTER TABLE `reservasi` ADD COLUMN `tgl_mulai` date NOT NULL AFTER `tgl_reserv`, ADD COLUMN `tgl_selesai` date NOT NULL AFTER `tgl_mulai`;

-- --------------------------------------------------------
-- Table structure for table `kategori`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `desc_kategori` text DEFAULT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `alat_media`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `alat_media` (
  `id_alat` int(11) NOT NULL AUTO_INCREMENT,
  `nama_alat` varchar(100) NOT NULL,
  `desc_alat` text DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `kondisi_alat` varchar(100) DEFAULT NULL,
  `foto_alat` varchar(255) DEFAULT NULL,
  `status_ketersediaan` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_alat`),
  KEY `id_kategori` (`id_kategori`),
  CONSTRAINT `alat_media_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `user`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `id_password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `reservasi`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reservasi` (
  `id_reserv` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `tgl_reserv` datetime NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status_reserv` varchar(50) DEFAULT 'Pending',
  `harga_total` decimal(10,2) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_reserv`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `detail_reservasi`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `detail_reservasi` (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_reserv` int(11) DEFAULT NULL,
  `id_alat` int(11) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `id_reserv` (`id_reserv`),
  KEY `id_alat` (`id_alat`),
  CONSTRAINT `detail_reservasi_ibfk_1` FOREIGN KEY (`id_reserv`) REFERENCES `reservasi` (`id_reserv`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detail_reservasi_ibfk_2` FOREIGN KEY (`id_alat`) REFERENCES `alat_media` (`id_alat`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `pembayaran`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_reserv` int(11) DEFAULT NULL,
  `tgl_pembayaran` datetime NOT NULL,
  `jml_pembayaran` decimal(10,2) NOT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `kode_transaksi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  KEY `id_reserv` (`id_reserv`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_reserv`) REFERENCES `reservasi` (`id_reserv`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- SEED DATA AWAL (INITIAL SAMPLE DATA)
-- --------------------------------------------------------

-- 1. Insert Data Kategori
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

-- 2. Insert Data Alat_Media (Peralatan)
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
(12, 'Audio Technica AT2020', 'Mikrofon condenser untuk voice over and recording studio.', 6, 50000.00, 4, 'Baik', '../../assets/img/uploads/Audio Technica AT2020.png', 'Tersedia'),
(13, 'Aputure 300D', 'Lampu LED profesional dengan intensitas tinggi.', 7, 100000.00, 3, 'Baik', '../../assets/img/uploads/Aputure 300D.png', 'Tersedia'),
(14, 'Godox Softbox Kit', 'Paket softbox lengkap untuk studio foto dan video.', 7, 50000.00, 5, 'Baik', '../../assets/img/uploads/Godox Softbox Kit.png', 'Tersedia'),
(15, 'DJI Mini 4 Pro', 'Drone ringan dengan kemampuan video 4K HDR.', 8, 350000.00, 1, 'Baik', '../../assets/img/uploads/DJI Mini 4 Pro.png', 'Tersedia'),
(16, 'DJI Air 3', 'Drone profesional dual-camera untuk kebutuhan komersial.', 8, 500000.00, 1, 'Baik', '../../assets/img/uploads/DJI Air 3.png', 'Disewa'),
(17, 'Mac Mini M2', 'Komputer editing video dan desain grafis berbasis Apple Silicon.', 9, 250000.00, 2, 'Baik', '../../assets/img/uploads/Mac Mini M2.png', 'Tersedia'),
(18, 'PC Editing Ryzen 9', 'Workstation editing video 4K dengan GPU RTX 4070.', 9, 350000.00, 1, 'Baik', '../../assets/img/uploads/PC Editing Ryzen 9.png', 'Tersedia'),
(19, 'Monitor External Feelworld F6', 'Monitor kamera 5.5 inci untuk monitoring video.', 10, 35000.00, 4, 'Baik', '../../assets/img/uploads/Monitor External Feelworld F6.png', 'Tersedia'),
(20, 'Tripod Manfrotto Compact', 'Tripod ringan dan kokoh untuk kamera dan smartphone.', 10, 25000.00, 5, 'Perlu Perawatan', '../../assets/img/uploads/Tripod Manfrotto Compact.png', 'Maintenance');

-- 3. Insert Data User
INSERT IGNORE INTO `user` (`id_user`, `nama`, `email`, `id_password`, `no_hp`) VALUES
(1, 'Admin StudioHub', 'admin@studiohub.com', '$2y$10$tM28Y1Qx7rC8Zz8v2z8yXeC.m9pZ9BwD7qQ1q0lZ3F4z7e9K3p4mK', '081234567890'),
(2, 'Abyan Santoso', 'abyan@gmail.com', '$2y$10$oXg8D7n9wH1qY6Z.z8z8YeC.m9pZ9BwD7qQ1q0lZ3F4z7e9K3p4mK', '085712345678');
