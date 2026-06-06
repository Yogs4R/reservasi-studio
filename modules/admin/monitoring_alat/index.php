<?php

require '../../../config/koneksi.php';
$query = 'SELECT * FROM alat_media WHERE 1 = 1';

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND nama_alat LIKE '%$search%'";
}

$query .= ' ORDER BY id_alat;';
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
            <h1 class="fw-bold mb-1 text-dark">Tools Monitor</h1>
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
                    <input type="text" name="search" class="form-control" placeholder="Search Kategori" value="<?= $_GET[
                        'search'
                    ] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">
                        Search
                    </button>
                </div>
            </form>
            <div class="row g-2 mb-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Alat</th>
                            <th>Stok</th>
                            <th>Kondisi Alat</th>
                            <th>Status Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;

                        while ($data = mysqli_fetch_array($hasil)) { ?> 
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $data[
                                        'nama_alat'
                                    ]; ?></td>
                                    <td><?php echo $data[
                                        'stok'
                                    ]; ?></td>
                                    <td><?php echo $data[
                                        'kondisi_alat'
                                    ]; ?></td>
                                    <td><?php echo $data[
                                        'status_ketersediaan'
                                    ]; ?></td>
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