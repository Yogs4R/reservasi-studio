<?php
require_once '../../config/koneksi.php';

// Inisiasi session login jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect jika belum login (fallback login simulasi jika kosong)
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2;
    $_SESSION['nama'] = 'Abyan Santoso';
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
    $stmt = $pdo->prepare("SELECT harga, status_ketersediaan FROM alat_media WHERE id_alat = :id_alat");
    $stmt->execute(['id_alat' => $id_alat]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: form_booking.php?status=error&message=Studio/Alat tidak ditemukan.");
        exit();
    }
    
    if ($product['status_ketersediaan'] !== 'Tersedia') {
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
} else {
    header("Location: form_booking.php");
    exit();
}

// 3. VALIDASI BENTROKAN JADWAL (Real-time Calendar Check)
$stmt_check = $pdo->prepare("SELECT COUNT(*) AS total 
                             FROM reservasi r 
                             JOIN detail_reservasi dr ON r.id_reserv = dr.id_reserv 
                             WHERE dr.id_alat = :id_alat 
                               AND r.status_reserv IN ('Pending', 'Waiting Payment', 'Booked', 'On Going') 
                               AND r.tgl_mulai <= :tgl_selesai 
                               AND r.tgl_selesai >= :tgl_mulai");
$stmt_check->execute([
    'id_alat' => $id_alat,
    'tgl_mulai' => $tgl_mulai,
    'tgl_selesai' => $tgl_selesai
]);
$conflict_count = $stmt_check->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

if ($conflict_count > 0) {
    header("Location: form_booking.php?status=error&message=Jadwal untuk studio/alat tersebut sudah ter-booking pada tanggal pilihan Anda.");
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