<?php

require '../../../config/koneksi.php';
$query =
    'SELECT a.*, b.status_reserv FROM pembayaran a LEFT JOIN reservasi b ON a.id_reserv = b.id_reserv WHERE 1 = 1';

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND (a.kode_transaksi LIKE '%$search%' OR a.id_reserv LIKE '%$search%')";
}

$query .= ' ORDER BY a.id_pembayaran DESC;';
$hasil = mysqli_query($conn, $query);

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php include '../../../includes/header_admin.php'; ?>
<div class="pt-5 pb-4">
    <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1 text-dark">Payment Status</h1>
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
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Cari Kode Transaksi / ID Reservasi..." value="<?= $_GET[
                        'search'
                    ] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        Cari
                    </button>
                </div>                
            </form>
            <div class="row g-2 mb-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>ID Reservasi</th>
                            <th>Tgl-Pembayaran</th>
                            <th>Jumlah</th>
                            <th>Status Reservasi</th>
                            <th>Bukti</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;

                        while ($data = mysqli_fetch_array($hasil)) { 
                            $status = $data['status_reserv'];
                            $badge_class = 'bg-secondary';
                            $status_text = htmlspecialchars($status ?? 'Unknown');
                            
                            if ($status === 'Pending') {
                                $badge_class = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                            } else if ($status === 'Waiting Payment') {
                                $badge_class = 'bg-info-subtle text-info-emphasis border border-info-subtle';
                                $status_text = 'Waiting Payment';
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
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($data['kode_transaksi']); ?></td>
                                    <td>#<?php echo $data['id_reserv']; ?></td>
                                    <td><small><?php echo date('d M Y, H:i', strtotime($data['tgl_pembayaran'])); ?></small></td>
                                    <td class="fw-bold">Rp <?php echo number_format($data['jml_pembayaran'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge <?= $badge_class ?> px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem; border-radius: 4px;">
                                            <?= $status_text ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($data['bukti_pembayaran']): 
                                            $link_path = 'assets/img/uploads/' . $data['bukti_pembayaran'];
                                            if (file_exists('../../../assets/img/uploads/bukti/' . $data['bukti_pembayaran'])) {
                                                $link_path = 'assets/img/uploads/bukti/' . $data['bukti_pembayaran'];
                                            }
                                        ?>
                                            <a href="<?= BASE_URL . $link_path ?>" target="_blank" class="btn btn-outline-secondary btn-xs py-0 px-2 fw-medium" style="font-size: 0.75rem;">
                                                <i class="bi bi-file-earmark-image"></i> Lihat
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="<?= BASE_URL ?>modules/admin/pembayaran/verifikasi.php?id_reserv=<?= $data['id_reserv'] ?>" class="btn <?= $status === 'Waiting Payment' ? 'btn-dark' : 'btn-outline-secondary' ?> btn-sm text-nowrap fw-bold" style="font-size: 0.75rem;">
                                                <i class="bi bi-shield-check me-1"></i><?= $status === 'Waiting Payment' ? 'Verify' : 'Detail' ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php $no++;}
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include '../../../includes/footer_admin.php'; ?>    
</body>
</html>