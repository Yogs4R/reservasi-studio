<?php
require_once '../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: form_booking.php");
    exit();
}

$tipe_booking = $_POST['tipe_booking'] ?? 'harian';
$metode_pembayaran = $_POST['metode_pembayaran'] ?? 'Transfer Bank Mandiri';

$id_alat = 0;
$tgl_mulai = '';
$tgl_selesai = '';
$harga_satuan = 0;
$harga_total = 0;

if ($tipe_booking === 'harian') {
    $id_alat = isset($_POST['id_alat']) ? intval($_POST['id_alat']) : 0;
    $tgl_mulai = $_POST['tgl_mulai'] ?? '';
    $tgl_selesai = $_POST['tgl_selesai'] ?? '';
    
    if (empty($id_alat) || empty($tgl_mulai) || empty($tgl_selesai)) {
        header("Location: form_booking.php?status=error&message=Semua kolom wajib diisi.");
        exit();
    }
    
    // Validasi Tanggal Harian
    if (strtotime($tgl_mulai) < strtotime(date('Y-m-d'))) {
        header("Location: form_booking.php?status=error&message=Tanggal mulai sewa tidak boleh hari kemarin.");
        exit();
    }
    
    if (strtotime($tgl_selesai) < strtotime($tgl_mulai)) {
        header("Location: form_booking.php?status=error&message=Tanggal selesai sewa tidak boleh mendahului tanggal mulai.");
        exit();
    }
    
    // Fetch product details
    $stmt = $pdo->prepare("SELECT nama_alat, harga, status_ketersediaan, stok FROM alat_media WHERE id_alat = :id_alat");
    $stmt->execute(['id_alat' => $id_alat]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: form_booking.php?status=error&message=Studio/Alat tidak ditemukan.");
        exit();
    }
    
    if (!in_array($product['status_ketersediaan'], ['Tersedia', 'Disewa'])) {
        header("Location: form_booking.php?status=error&message=Status studio/alat sedang tidak tersedia.");
        exit();
    }
    
    $harga_satuan = floatval($product['harga']);
    $diff_time = strtotime($tgl_selesai) - strtotime($tgl_mulai);
    $durasi = ceil($diff_time / (60 * 60 * 24)) + 1;
    $harga_total = $durasi * $harga_satuan;
    
} else if ($tipe_booking === 'paket') {
    $paket_plan = $_POST['paket_plan'] ?? '';
    $tgl_sesi = $_POST['tgl_sesi'] ?? '';
    
    if (empty($paket_plan) || empty($tgl_sesi)) {
        header("Location: form_booking.php?status=error&message=Harap pilih paket dan tanggal sesi.");
        exit();
    }
    
    // Validasi Tanggal Sesi
    if (strtotime($tgl_sesi) < strtotime(date('Y-m-d'))) {
        header("Location: form_booking.php?status=error&message=Tanggal sesi sewa tidak boleh hari kemarin.");
        exit();
    }
    
    $tgl_mulai = $tgl_sesi;
    $tgl_selesai = $tgl_sesi;
    
    // Map package to product id and price based on pricing.php
    if ($paket_plan === 'lite') {
        $id_alat = 7; // Studio Podcast A
        $harga_satuan = 150000.00;
    } else if ($paket_plan === 'creator') {
        $id_alat = 8; // Studio Foto White Room
        $harga_satuan = 300000.00;
    } else if ($paket_plan === 'pro') {
        $id_alat = 7; // Studio Podcast A
        $harga_satuan = 600000.00;
    } else {
        header("Location: form_booking.php?status=error&message=Paket tidak valid.");
        exit();
    }
    
    $harga_total = $harga_satuan;

    // Fetch product details for package item
    $stmt = $pdo->prepare("SELECT nama_alat, harga, status_ketersediaan, stok FROM alat_media WHERE id_alat = :id_alat");
    $stmt->execute(['id_alat' => $id_alat]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: form_booking.php?status=error&message=Studio/Alat tidak ditemukan.");
        exit();
    }
    
    if (!in_array($product['status_ketersediaan'], ['Tersedia', 'Disewa'])) {
        header("Location: form_booking.php?status=error&message=Status studio/alat sedang tidak tersedia.");
        exit();
    }
} else {
    header("Location: form_booking.php");
    exit();
}

// 3. VALIDASI BENTROKAN JADWAL BERDASARKAN STOK
$stok_total = intval($product['stok'] ?? 1);
$current_date = new DateTime($tgl_mulai);
$end_date = new DateTime($tgl_selesai);
$end_date->modify('+1 day'); // inclusive

$interval = new DateInterval('P1D');
$period = new DatePeriod($current_date, $interval, $end_date);

$max_rented = 0;
foreach ($period as $dt) {
    $date_str = $dt->format('Y-m-d');
    
    $stmt_rented = $pdo->prepare("
        SELECT COALESCE(SUM(dr.jumlah), 0) AS total_rented
        FROM reservasi r
        JOIN detail_reservasi dr ON r.id_reserv = dr.id_reserv
        WHERE dr.id_alat = :id_alat
          AND r.status_reserv IN ('Pending', 'Waiting Payment', 'Booked', 'On Going')
          AND r.tgl_mulai <= :date_str
          AND r.tgl_selesai >= :date_str
    ");
    $stmt_rented->execute([
        'id_alat' => $id_alat,
        'date_str' => $date_str
    ]);
    $rented = intval($stmt_rented->fetch(PDO::FETCH_ASSOC)['total_rented'] ?? 0);
    if ($rented > $max_rented) {
        $max_rented = $rented;
    }
}

if ($max_rented + 1 > $stok_total) {
    header("Location: form_booking.php?status=error&message=" . urlencode("Stok untuk " . $product['nama_alat'] . " tidak mencukupi pada tanggal pilihan Anda (Sudah ter-booking " . $max_rented . " dari " . $stok_total . " unit)."));
    exit();
}

// 4. DATABASE TRANSACTION SAVE
try {
    $pdo->beginTransaction();
    
    // Simpan ke tabel reservasi
    $stmt_reserv = $pdo->prepare("INSERT INTO reservasi (id_user, tgl_reserv, tgl_mulai, tgl_selesai, status_reserv, harga_total, metode_pembayaran) 
                                  VALUES (:id_user, NOW(), :tgl_mulai, :tgl_selesai, 'Pending', :harga_total, :metode_pembayaran)");
    $stmt_reserv->execute([
        'id_user' => $_SESSION['user_id'],
        'tgl_mulai' => $tgl_mulai,
        'tgl_selesai' => $tgl_selesai,
        'harga_total' => $harga_total,
        'metode_pembayaran' => $metode_pembayaran
    ]);
    
    $new_id_reserv = $pdo->lastInsertId();
    
    // Simpan ke tabel detail_reservasi
    $stmt_detail = $pdo->prepare("INSERT INTO detail_reservasi (id_reserv, id_alat, jumlah, harga_satuan, subtotal) 
                                  VALUES (:id_reserv, :id_alat, 1, :harga_satuan, :subtotal)");
    $stmt_detail->execute([
        'id_reserv' => $new_id_reserv,
        'id_alat' => $id_alat,
        'harga_satuan' => $harga_satuan,
        'subtotal' => $harga_total
    ]);
    
    $pdo->commit();
    
    header("Location: riwayat.php?status=success&message=" . urlencode("Booking berhasil dibuat! Silakan upload bukti pembayaran dalam waktu 24 jam."));
    exit();
    
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: form_booking.php?status=error&message=" . urlencode("Gagal menyimpan data sewa: " . $e->getMessage()));
    exit();
}
?>