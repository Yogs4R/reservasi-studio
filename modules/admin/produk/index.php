<?php
require '../../../config/koneksi.php';
$query = "
SELECT a.*, k.nama_kategori FROM alat_media a LEFT JOIN kategori k ON a.id_kategori = k.id_kategori WHERE 1 = 1
";

//Filter untuk input search
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND a.nama_alat LIKE '%$search%'";
}

//Filter untuk input option kategori
if (!empty($_GET['kategori'])) {
    $kategori = (int) $_GET['kategori'];
    $query .= " AND a.id_kategori = $kategori";
}

//Filter untuk input option availability
if (!empty($_GET['availability'])) {
    if ($_GET['availability'] == 'Tersedia') {
        $query .= " AND a.status_ketersediaan = 'Tersedia'";
    } elseif ($_GET['availability'] == 'Maintenance') {
        $query .= " AND a.status_ketersediaan = 'Maintenance'";
    } elseif ($_GET['availability'] == 'Disewa') {
        $query .= " AND a.status_ketersediaan = 'Disewa'";
    }
}

//Filter untuk input option condition
if (!empty($_GET['condition'])) {
    if ($_GET['condition'] == 'baik') {
        $query .= " AND a.kondisi_alat = 'Baik'";
    } elseif ($_GET['condition'] == 'perlu perawatan') {
        $query .= " AND a.kondisi_alat = 'Perlu Perawatan'";
    }
}

$query .= ' ORDER BY a.id_alat;';

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
    <!-- Header -->
    <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1 text-dark">Produk Management</h1>
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
            <!-- Form Pilihan Tipe yang Mau diedit -->
            <!-- Search input -->
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search Item" 
                    value="<?= $_GET['search'] ?? '' ?>">
                </div>
            
                <!-- Tipe Input -->
                <div class="col-md-2" style="width: 15%;">
                    <select name="kategori" class="form-select">
                        <option value=""> kategori </option>
                        <?php
                        $kategori = mysqli_query(
                            $conn,
                            'SELECT * FROM kategori',
                        );
                        while ($k = mysqli_fetch_array($kategori)) {
                            $selected =
                                ($_GET['kategori'] ?? null) == $k['id_kategori']
                                    ? 'selected'
                                    : '';

                            echo "
                                <option value='{$k['id_kategori']}' $selected>
                                    {$k['nama_kategori']}
                                </option>
                            ";
                        }
                        ?>

                    </select>
                </div>

                <!-- Availability Input -->
                <div class="col-md-2" style="width: 15%;">
                    <select name="availability" class="form-select">
                        <option value="">Availability</option>
                        <option value="Tersedia" 
                            <?= ($_GET['availability'] ?? '') == 'Tersedia'
                                ? 'selected'
                                : '' ?>>
                            Tersedia
                        </option>
                        <option value="Maintenance" 
                            <?= ($_GET['availability'] ?? '') == 'Maintenance'
                                ? 'selected'
                                : '' ?>>
                            Maintanance
                        </option>
                        <option value="Disewa"
                        <?= ($_GET['availability'] ?? '') == 'Disewa'
                            ? 'selected'
                            : '' ?>>
                            Disewa
                        </option>
                    </select>
                </div>

                <!-- Condition Input -->
                <div class="col-md-2" style="width: 15%;">
                    <select name="condition" class="form-select">
                        <option value="">Condition</option>
                        <option value="baik" 
                            <?= ($_GET['condition'] ?? '') == 'baik'
                                ? 'selected'
                                : '' ?>>
                            Baik
                        </option>
                        <option value="perlu perawatan" 
                            <?= ($_GET['condition'] ?? '') == 'perlu perawatan'
                                ? 'selected'
                                : '' ?>>
                            Perlu Perawatan
                        </option>
                    </select>
                </div>

                <!-- Tombol Submit -->
                <div class="col-md-1" >
                    <button type="submit" class="btn btn-dark w-100">
                        Filter
                    </button>
                </div>

                <!-- Tombol Add Item -->
                <div class="col-md-2" style="width: 12.5%;">
                    <a href="./create.php" class="btn btn-success w-100">
                        Add Item
                    </a>
                </div>
            </form>

            <!-- Tampilan Tabel -->
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Kondisi</th>
                        <th scope="col">Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($data = mysqli_fetch_array($hasil)) { ?> 
                                <tr>
                                    <td><?php echo $no; ?></td>
                                    <td><?php echo $data['nama_alat']; ?></td>
                                    <td><?php echo $data[
                                        'nama_kategori'
                                    ]; ?></td>
                                    <td><?php echo $data['harga']; ?></td>
                                    <td><?php echo $data['stok']; ?></td>
                                    <td><?php echo $data[
                                        'kondisi_alat'
                                    ]; ?></td>
                                    <td><?php echo $data[
                                        'status_ketersediaan'
                                    ]; ?></td>
                                    <td>
                                        <a href="./update.php?id_alat=<?= $data[
                                            'id_alat'
                                        ] ?>" class="btn btn-warning btn-sm" title="edit" style="padding-bottom: 8px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                            </svg>
                                        </a>
                                        <a href="./delete.php?id_alat=<?= $data[
                                            'id_alat'
                                        ] ?>" class="btn btn-danger btn-sm" title="delete" style="padding-bottom: 8px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            <?php $no++;}
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php include '../../../includes/footer_admin.php'; ?>    
</body>
</html>