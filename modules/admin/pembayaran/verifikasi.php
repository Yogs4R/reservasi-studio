<?php
require_once '../../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$id_reserv = isset($_GET['id_reserv']) ? intval($_GET['id_reserv']) : 0;

if ($id_reserv <= 0) {
    header("Location: ../reservasi/index.php");
    exit();
}

// Fetch reservation, user, and payment details
$stmt = $pdo->prepare("SELECT r.*, u.nama AS nama_user, u.email, u.no_hp, 
                              p.id_pembayaran, p.tgl_pembayaran, p.jml_pembayaran, p.bukti_pembayaran, p.kode_transaksi, 
                              am.nama_alat, k.nama_kategori
                       FROM reservasi r
                       JOIN user u ON r.id_user = u.id_user
                       LEFT JOIN detail_reservasi dr ON r.id_reserv = dr.id_reserv
                       LEFT JOIN alat_media am ON dr.id_alat = am.id_alat
                       LEFT JOIN kategori k ON am.id_kategori = k.id_kategori
                       LEFT JOIN pembayaran p ON r.id_reserv = p.id_reserv
                       WHERE r.id_reserv = :id_reserv");
$stmt->execute(['id_reserv' => $id_reserv]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header("Location: ../reservasi/index.php?status=error&message=" . urlencode("Reservasi tidak ditemukan."));
    exit();
}

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'approve') {
        try {
            $pdo->beginTransaction();
            
            // 1. Update reservasi status to 'Booked'
            $stmt_update = $pdo->prepare("UPDATE reservasi SET status_reserv = 'Booked' WHERE id_reserv = :id_reserv");
            $stmt_update->execute(['id_reserv' => $id_reserv]);
            
            $pdo->commit();
            header("Location: ../reservasi/index.php?status=success&message=" . urlencode("Reservasi #" . $id_reserv . " berhasil disetujui (Booked)."));
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = 'Gagal menyetujui reservasi: ' . $e->getMessage();
        }
    } else if ($action === 'reject') {
        try {
            $pdo->beginTransaction();
            
            // 1. Update reservasi status to 'Cancelled'
            $stmt_update = $pdo->prepare("UPDATE reservasi SET status_reserv = 'Cancelled' WHERE id_reserv = :id_reserv");
            $stmt_update->execute(['id_reserv' => $id_reserv]);
            
            $pdo->commit();
            header("Location: ../reservasi/index.php?status=success&message=" . urlencode("Reservasi #" . $id_reserv . " berhasil ditolak (Cancelled)."));
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = 'Gagal menolak reservasi: ' . $e->getMessage();
        }
    }
}

// Calculate table sizes for sidebar
$tables = ['kategori', 'alat_media', 'user', 'reservasi', 'detail_reservasi', 'pembayaran'];
$table_sizes = [];
foreach ($tables as $t) {
    $size_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$t`");
    $table_sizes[$t] = mysqli_fetch_assoc($size_q)['total'] ?? 0;
}

include '../../../includes/header_admin.php';
?>

<div class="pt-5 pb-4">
    <!-- Breadcrumb & Title -->
    <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none text-muted small">Admin</a></li>
                    <li class="breadcrumb-item"><a href="../reservasi/index.php" class="text-decoration-none text-muted small">Reservasi</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold small" aria-current="page">Verifikasi Pembayaran</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem;">Verifikasi Pembayaran #<?= $data['id_reserv'] ?></h1>
        </div>
        <div class="mt-3 mt-md-0">
            <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-3 py-2 fw-semibold">
                Status: <?= htmlspecialchars($data['status_reserv']) ?>
            </span>
        </div>
    </div>

    <!-- Error Notification -->
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                <div><?= htmlspecialchars($error_msg) ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left Sidebar Navigation Column -->
        <div class="col-lg-3">
            <?php include '../../../includes/sidebar_admin.php'; ?>
            
            <!-- Database Size Tracker Card -->
            <div class="card shadow-sm border border-light-subtle rounded-3 p-3 mt-4 bg-white">
                <h6 class="fw-bold mb-3 text-secondary text-uppercase small"><i class="bi bi-database-fill-gear me-1"></i>Database Rows</h6>
                <div class="list-group list-group-flush small">
                    <?php foreach ($table_sizes as $table_name => $count): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                        <span class="text-muted text-capitalize"><i class="bi bi-table me-2 text-secondary"></i><?= htmlspecialchars($table_name) ?></span>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><?= $count ?> rows</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="col-lg-9">
            <div class="row g-4">
                <!-- Left Details Column -->
                <div class="col-md-6">
                    <!-- Reservation Info Card -->
                    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
                        <div class="card-header bg-dark text-white fw-bold py-3" style="border-radius: 12px 12px 0 0;">
                            <i class="bi bi-calendar-check me-2"></i>Detail Reservasi
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Produk / Studio</small>
                                <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($data['nama_alat'] ?? 'Paket Sesi') ?></span>
                                <span class="badge bg-secondary text-uppercase ms-1" style="font-size: 0.65rem;">
                                    <?= htmlspecialchars($data['nama_kategori'] ?? 'Studio') ?>
                                </span>
                            </div>

                            <hr class="my-3 opacity-10">

                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Jadwal Mulai</small>
                                    <span class="fw-semibold text-dark"><?= date('d M Y', strtotime($data['tgl_mulai'])) ?></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Jadwal Selesai</small>
                                    <span class="fw-semibold text-dark"><?= date('d M Y', strtotime($data['tgl_selesai'])) ?></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Waktu Transaksi</small>
                                    <span class="text-dark" style="font-size: 0.9rem;"><?= date('d M Y, H:i', strtotime($data['tgl_reserv'])) ?> WIB</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Metode Pembayaran</small>
                                    <span class="badge bg-light text-dark border fw-medium"><?= htmlspecialchars($data['metode_pembayaran']) ?></span>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Total Tagihan</small>
                                    <span class="fw-bold text-dark fs-4">Rp <?= number_format($data['harga_total'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details Card -->
                    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 12px;">
                        <div class="card-header bg-secondary text-white fw-bold py-3" style="border-radius: 12px 12px 0 0;">
                            <i class="bi bi-person me-2"></i>Informasi Pelanggan
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Nama Pelanggan</small>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($data['nama_user']) ?></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Email</small>
                                    <span class="text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($data['email']) ?></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">No. HP</small>
                                    <span class="text-dark" style="font-size: 0.9rem;"><?= htmlspecialchars($data['no_hp'] ?? '-') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Decision Action Buttons -->
                    <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                        <div class="card-body p-4 text-center">
                            <h5 class="fw-bold text-dark mb-3">Tindakan Admin</h5>
                            <?php if ($data['status_reserv'] === 'Waiting Payment'): ?>
                                <p class="text-muted small mb-4">Pastikan nominal transfer dan validitas bukti transfer di sebelah kanan sudah sesuai sebelum menyetujui.</p>
                                <form action="" method="POST" class="d-flex gap-3 justify-content-center">
                                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger fw-bold px-4 py-2.5" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?');" style="border-radius: 8px;">
                                        <i class="bi bi-x-circle me-1"></i> Tolak Pembayaran
                                    </button>
                                    <button type="submit" name="action" value="approve" class="btn btn-dark fw-bold px-4 py-2.5" onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran ini?');" style="border-radius: 8px;">
                                        <i class="bi bi-check-circle me-1"></i> Setujui Pembayaran
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-secondary border-0 mb-0 py-3 small">
                                    <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                                    Pemesanan ini sudah berstatus <strong><?= htmlspecialchars($data['status_reserv']) ?></strong>. Tidak diperlukan tindakan verifikasi lebih lanjut.
                                </div>
                            <?php endif; ?>
                            <a href="../reservasi/index.php" class="btn btn-link text-muted text-decoration-none d-block mt-3 small">
                                Kembali ke Daftar Reservasi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Receipt Column -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-white h-100" style="border-radius: 12px;">
                        <div class="card-header bg-dark text-white fw-bold py-3" style="border-radius: 12px 12px 0 0;">
                            <i class="bi bi-file-image me-2"></i>Bukti Transfer & Pembayaran
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <?php if ($data['id_pembayaran']): ?>
                                <div class="p-3 bg-light rounded-3 mb-4 small border">
                                    <div class="row g-2">
                                        <div class="col-5 text-muted">Kode Transaksi:</div>
                                        <div class="col-7 text-dark fw-bold text-end"><?= htmlspecialchars($data['kode_transaksi']) ?></div>
                                        
                                        <div class="col-5 text-muted">Tgl. Transfer:</div>
                                        <div class="col-7 text-dark text-end"><?= date('d M Y, H:i', strtotime($data['tgl_pembayaran'])) ?> WIB</div>
                                        
                                        <div class="col-5 text-muted">Nominal Transfer:</div>
                                        <div class="col-7 text-dark fw-bold text-end text-success">Rp <?= number_format($data['jml_pembayaran'], 0, ',', '.') ?></div>
                                    </div>
                                </div>

                                <small class="text-muted d-block mb-2 text-uppercase fw-semibold" style="font-size: 0.75rem;">Struk / Gambar Bukti:</small>
                                <div class="flex-grow-1 d-flex align-items-center justify-content-center border rounded-3 p-3 bg-light overflow-hidden position-relative" style="min-height: 250px;">
                                    <?php if ($data['bukti_pembayaran'] && file_exists('../../../assets/img/uploads/' . $data['bukti_pembayaran'])): ?>
                                        <img src="../../../assets/img/uploads/<?= htmlspecialchars($data['bukti_pembayaran']) ?>" alt="Bukti Pembayaran" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image-fill fs-1 d-block mb-2 text-secondary"></i>
                                            <span class="small">Berkas gambar bukti transfer tidak ditemukan di server (<?= htmlspecialchars($data['bukti_pembayaran'] ?? '') ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="my-auto text-center py-5 text-muted">
                                    <i class="bi bi-wallet-fill fs-1 d-block mb-3 text-secondary"></i>
                                    <span class="fw-medium small d-block">Belum Ada Pembayaran</span>
                                    <p class="small text-muted px-4 mt-2">Pelanggan belum mengunggah bukti transfer untuk nomor reservasi ini.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../../../includes/footer_admin.php';
?>
