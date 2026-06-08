<?php
require_once '../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login session (redirect if not logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include '../../includes/header.php';

// Fetch reservations for the logged-in user
$stmt = $pdo->prepare("SELECT r.*, dr.id_alat, am.nama_alat, am.foto_alat, k.nama_kategori 
                       FROM reservasi r
                       LEFT JOIN detail_reservasi dr ON r.id_reserv = dr.id_reserv
                       LEFT JOIN alat_media am ON dr.id_alat = am.id_alat
                       LEFT JOIN kategori k ON am.id_kategori = k.id_kategori
                       WHERE r.id_user = :id_user
                       ORDER BY r.tgl_reserv DESC");
$stmt->execute(['id_user' => $_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5" style="padding-top: 40px;">
    <!-- Header Page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Riwayat Reservasi</h2>
            <p class="text-muted small">Kelola pemesanan studio dan peralatan kreatif Anda di sini.</p>
        </div>
        <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-dark fw-bold btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Booking Baru
        </a>
    </div>

    <!-- Alert Notification from process -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                <div>
                    <?= htmlspecialchars($_GET['message'] ?? 'Aksi berhasil dilakukan.') ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- History Tables Card -->
    <div class="card border-0 shadow-sm bg-white overflow-hidden" style="border-radius: 12px;">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 60px;">No</th>
                        <th>Studio / Alat</th>
                        <th>Waktu Transaksi</th>
                        <th>Jadwal Sewa</th>
                        <th>Total Biaya</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th class="text-end pe-4" style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-3 text-secondary"></i>
                                Anda belum memiliki riwayat reservasi.
                            </td>
                        </tr>
                    <?php else: 
                        $no = 1;
                        foreach ($bookings as $r):
                            $status = $r['status_reserv'];
                            
                            // Map Status Badges
                            $badge_class = 'bg-secondary';
                            $status_text = htmlspecialchars($status);
                            
                            if ($status === 'Pending') {
                                $badge_class = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                                $status_text = 'Pending';
                            } else if ($status === 'Waiting Payment') {
                                $badge_class = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                $status_text = 'Menunggu Verifikasi';
                            } else if ($status === 'Booked') {
                                $badge_class = 'bg-success-subtle text-success-emphasis border border-success-subtle';
                                $status_text = 'Disetujui (Booked)';
                            } else if ($status === 'Cancelled') {
                                $badge_class = 'bg-danger-subtle text-danger-emphasis border border-danger-subtle';
                                $status_text = 'Dibatalkan';
                            } else if ($status === 'On Going') {
                                $badge_class = 'bg-primary-subtle text-primary-emphasis border border-primary-subtle';
                                $status_text = 'Sedang Digunakan';
                            } else if ($status === 'Finished') {
                                $badge_class = 'bg-dark-subtle text-dark-emphasis border border-dark-subtle';
                                $status_text = 'Selesai';
                            }

                            // Calculate 24h Grace Period Countdown
                            $time_created = strtotime($r['tgl_reserv']);
                            $deadline = $time_created + (24 * 60 * 60);
                            $time_left = $deadline - time();
                            $expired = ($time_left <= 0);
                    ?>
                        <tr>
                            <td class="ps-4 fw-semibold"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="badge bg-dark text-uppercase" style="font-size: 0.65rem;">
                                            <?= htmlspecialchars($r['nama_kategori'] ?? 'Studio') ?>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                                            <?= htmlspecialchars($r['nama_alat'] ?? 'Paket Sesi') ?>
                                        </span>
                                        <small class="text-muted" style="font-size: 0.75rem;">ID Reserv: #<?= $r['id_reserv'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small class="text-dark d-block"><?= date('d M Y', strtotime($r['tgl_reserv'])) ?></small>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= date('H:i', strtotime($r['tgl_reserv'])) ?> WIB</small>
                            </td>
                            <td>
                                <?php if ($r['tgl_mulai'] === $r['tgl_selesai']): ?>
                                    <small class="fw-medium text-dark d-block"><?= date('d M Y', strtotime($r['tgl_mulai'])) ?></small>
                                    <small class="text-muted" style="font-size: 0.75rem;">Sesi Harian / Sesi Khusus</small>
                                <?php else: ?>
                                    <small class="fw-medium text-dark d-block">
                                        <?= date('d M', strtotime($r['tgl_mulai'])) ?> s/d <?= date('d M Y', strtotime($r['tgl_selesai'])) ?>
                                    </small>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        (<?= ceil((strtotime($r['tgl_selesai']) - strtotime($r['tgl_mulai'])) / (60 * 60 * 24)) + 1 ?> hari sewa)
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold text-dark">
                                Rp <?= number_format($r['harga_total'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small">
                                    <i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars($r['metode_pembayaran'] ?? 'Transfer') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $badge_class ?> px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem; border-radius: 4px;">
                                    <?= $status_text ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <?php if ($status === 'Pending' && !$expired): 
                                    $hours = floor($time_left / 3600);
                                    $minutes = floor(($time_left % 3600) / 60);
                                ?>
                                    <a href="<?= BASE_URL ?>modules/pembayaran/upload_bukti.php?id_reserv=<?= $r['id_reserv'] ?>" class="btn btn-sm btn-dark fw-bold px-3 shadow-sm mb-1" style="font-size: 0.8rem; border-radius: 6px;">
                                        <i class="bi bi-upload me-1"></i> Upload Bukti
                                    </a>
                                    <small class="text-danger d-block text-end fw-semibold" style="font-size: 0.7rem;">
                                        <i class="bi bi-clock me-0.5"></i> Sisa waktu: <?= $hours ?>j <?= $minutes ?>m
                                    </small>
                                <?php elseif ($status === 'Pending' && $expired): ?>
                                    <span class="text-muted small fw-medium" style="font-size: 0.8rem;">Batas Waktu Bayar Habis</span>
                                <?php elseif ($status === 'Waiting Payment'): ?>
                                    <span class="text-muted small fw-medium" style="font-size: 0.8rem;">Menunggu Verifikasi Admin</span>
                                <?php else: ?>
                                    <span class="text-muted small" style="font-size: 0.8rem;">Selesai / Sesuai Aturan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include '../../includes/footer.php';
?>