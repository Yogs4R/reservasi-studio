<?php
require_once '../../../config/koneksi.php';

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
?>

<?php include '../../../includes/header_admin.php'; ?>
<!-- Header/Title Block -->
<div class="pt-5 pb-4">
    <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1 text-dark">Kalender Reservasi</h1>
            <p class="text-muted mb-0 small">
                Analytical database overview, performance metrics, and live system monitoring.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
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
                        <span class="text-muted text-capitalize"><i class="bi bi-table me-2 text-secondary"></i><?= htmlspecialchars(
                            $table_name,
                        ) ?></span>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><?= $count ?> rows</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="col-lg-9">

        </div>
    </div>
</div>
<?php include '../../../includes/footer_admin.php'; ?>
