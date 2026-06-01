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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php include '../../../includes/header.php'; ?>
    <!-- Header -->
    <div class="container pt-5 pb-4 mt-5">
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">

            <div>
                <h1 class="fw-bold mb-1">Admin Page Produk</h1>
                <p class="text-muted mb-0">
                    Browse available equipment and creative assets.
                </p>
            </div>
        </div>
    </div>

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
        <div class="col-md-2">
            <select name="kategori" class="form-select">
                <option value=""> kategori </option>
                <?php
                $kategori = mysqli_query($conn, 'SELECT * FROM kategori');
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
        <div class="col-md-2">
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
        <div class="col-md-2">
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
        <div class="col-md-1">
            <button type="submit" class="btn btn-dark w-100">
                Filter
            </button>
        </div>
    </form>

    <!-- Tampilan Tabel -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Nama</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($data = mysqli_fetch_array($hasil)) { ?> 
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo $data['nama_alat']; ?></td>
                        </tr>
                    <?php $no++;}
            ?>
        </tbody>
    </table>

<?php include '../../../includes/footer.php'; ?>    
</body>
</html>