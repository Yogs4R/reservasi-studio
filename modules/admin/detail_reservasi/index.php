<?php
require '../../../config/koneksi.php';

$id_reserv = isset($_GET['id_reserv']) ? (int)$_GET['id_reserv'] : 0;

if ($id_reserv <= 0) {
    header("Location: ../reservasi/index.php");
    exit();
}

// Fetch reservation main data along with user details
$reservQuery = "
    SELECT r.*, u.nama, u.email, u.no_hp 
    FROM reservasi r 
    LEFT JOIN user u ON r.id_user = u.id_user 
    WHERE r.id_reserv = '$id_reserv'
";
$reservHasil = mysqli_query($conn, $reservQuery);
$reservData = mysqli_fetch_assoc($reservHasil);

if (!$reservData) {
    echo "<script>alert('Reservasi tidak ditemukan.'); window.location.href='../reservasi/index.php';</script>";
    exit();
}

// Fetch detail items (alat_media) rented in this reservation
$detailQuery = "
    SELECT dr.*, am.nama_alat, am.foto_alat, c.nama_kategori
    FROM detail_reservasi dr
    JOIN alat_media am ON dr.id_alat = am.id_alat
    LEFT JOIN kategori c ON am.id_kategori = c.id_kategori
    WHERE dr.id_reserv = '$id_reserv'
";
$detailHasil = mysqli_query($conn, $detailQuery);

// Fetch payment data if exists
$pembayaranQuery = "
    SELECT * FROM pembayaran WHERE id_reserv = '$id_reserv' LIMIT 1
";
$pembayaranHasil = mysqli_query($conn, $pembayaranQuery);
$pembayaranData = mysqli_fetch_assoc($pembayaranHasil);

// Database tables overview rows count
$tables = [
    'kategori',
    'alat_media',
    'user',
    'reservasi',
    'detail_reservasi',
    'pembayaran',
];
$table_sizes = [];
foreach ($tables as $t) {
    $size_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$t`");
    $table_sizes[$t] = mysqli_fetch_assoc($size_q)['total'] ?? 0;
}

include '../../../includes/header_admin.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Reservasi #<?= $id_reserv ?></title>
    <style>
        .detail-card {
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            background: #ffffff;
        }
        .item-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="pt-5 pb-4">
    <!-- Header / Title -->
    <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1 text-dark">Detail Reservasi #<?= $id_reserv ?></h1>
            <p class="text-muted mb-0 small">
                Rincian informasi pemesanan studio, persewaan alat, dan status pembayaran.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="../reservasi/index.php" class="btn btn-outline-dark me-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-3 py-2 fw-semibold">
                <i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i') ?>
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Sidebar Navigation Column -->
        <div class="col-lg-3">
            <?php include '../../../includes/sidebar_admin.php'; ?>
            
            <!-- Database Size Tracker Card -->
            <div class="card shadow-sm border border-light-subtle rounded-3 p-3 mt-4 bg-white">
                <h6 class="fw-bold mb-3 text-secondary text-uppercase small"><i class="bi bi-database-fill-gear me-1"></i>Database Rows Count</h6>
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

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="row g-4">
                <!-- Info Pelanggan & Reservasi -->
                <div class="col-md-6">
                    <div class="card detail-card shadow-sm p-4 h-100">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-person-circle text-primary me-2"></i>Informasi Pelanggan
                        </h5>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Nama Pelanggan</label>
                            <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($reservData['nama']) ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Email</label>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($reservData['email'] ?? '-') ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">Nomor HP / WhatsApp</label>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($reservData['no_hp'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card detail-card shadow-sm p-4 h-100">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-calendar-check text-primary me-2"></i>Status Reservasi
                        </h5>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small d-block">Tanggal Mulai</label>
                                <span class="fw-semibold text-dark"><?= date('d F Y', strtotime($reservData['tgl_mulai'])) ?></span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">Tanggal Selesai</label>
                                <span class="fw-semibold text-dark"><?= date('d F Y', strtotime($reservData['tgl_selesai'])) ?></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-muted small d-block">Status Reservasi</label>
                                <?php
                                $status = $reservData['status_reserv'];
                                $badge_class = 'bg-secondary';
                                if ($status === 'Pending') {
                                    $badge_class = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                } else if ($status === 'Waiting Payment') {
                                    $badge_class = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                } else if ($status === 'Booked') {
                                    $badge_class = 'bg-success-subtle text-success-emphasis border border-success-subtle';
                                } else if ($status === 'Cancelled') {
                                    $badge_class = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
                                } else if ($status === 'On Going') {
                                    $badge_class = 'bg-primary-subtle text-primary-emphasis border border-primary-subtle';
                                } else if ($status === 'Finished') {
                                    $badge_class = 'bg-dark-subtle text-dark-emphasis border border-dark-subtle';
                                }
                                ?>
                                <span class="badge <?= $badge_class ?> px-2.5 py-1.5 fw-semibold mt-1">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small d-block">Metode Pembayaran</label>
                                <span class="badge bg-light text-dark border fw-semibold mt-1 py-1.5">
                                    <?= htmlspecialchars($reservData['metode_pembayaran'] ?? 'Cash') ?>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Total Biaya Reservasi</label>
                            <span class="fw-bold text-dark-emphasis fs-4">Rp <?= number_format($reservData['harga_total'], 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Detail Item yang Disewa -->
                <div class="col-12">
                    <div class="card detail-card shadow-sm p-4">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-box-seam text-primary me-2"></i>Item Studio / Alat yang Disewa
                        </h5>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nama Item</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Kuantitas</th>
                                        <th>Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($detailHasil) > 0): ?>
                                        <?php while ($item = mysqli_fetch_assoc($detailHasil)): ?>
                                            <tr>
                                                <td>
                                                    <?php if (!empty($item['foto_alat'])): ?>
                                                        <img src="<?= htmlspecialchars($item['foto_alat']) ?>" class="item-img shadow-sm border border-light" alt="Foto">
                                                    <?php else: ?>
                                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center item-img border border-light shadow-sm">
                                                            <i class="bi bi-camera fs-3"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark"><?= htmlspecialchars($item['nama_alat']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis"><?= htmlspecialchars($item['nama_kategori'] ?? '-') ?></span>
                                                </td>
                                                <td class="text-center fw-semibold"><?= $item['jumlah'] ?> unit</td>
                                                <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                                                <td class="text-end fw-bold text-dark-emphasis">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">Tidak ada detail item reservasi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pembayaran jika ada -->
                <?php if ($pembayaranData): ?>
                <div class="col-12">
                    <div class="card detail-card shadow-sm p-4">
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-credit-card text-primary me-2"></i>Informasi Transaksi & Pembayaran
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Kode Transaksi</label>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($pembayaranData['kode_transaksi']) ?></span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Tanggal Bayar</label>
                                <span class="fw-semibold text-dark"><?= date('d F Y, H:i', strtotime($pembayaranData['tgl_pembayaran'])) ?></span>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small d-block">Jumlah Dibayar</label>
                                <span class="fw-bold text-success">Rp <?= number_format($pembayaranData['jml_pembayaran'], 0, ',', '.') ?></span>
                            </div>
                            
                            <?php if (!empty($pembayaranData['bukti_pembayaran'])): ?>
                            <div class="col-12 mt-3">
                                <label class="text-muted small d-block mb-2">Bukti Pembayaran</label>
                                <a href="<?= BASE_URL ?>assets/img/uploads/<?= htmlspecialchars($pembayaranData['bukti_pembayaran']) ?>" target="_blank" class="d-inline-block shadow-sm rounded border p-2">
                                    <img src="<?= BASE_URL ?>assets/img/uploads/<?= htmlspecialchars($pembayaranData['bukti_pembayaran']) ?>" style="max-height: 200px; max-width: 100%; border-radius: 6px; object-fit: contain;" alt="Bukti Pembayaran">
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php include '../../../includes/footer_admin.php'; ?>