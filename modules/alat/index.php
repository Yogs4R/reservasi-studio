<?php
// Include header (which initiates session_start())
include '../../includes/header.php';
require '../../config/koneksi.php';

// --- USER / GUEST VIEW: Equipment Catalog ---
    
    // Filter & Search Logic
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $cat_filter = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
    $avail_filter = isset($_GET['availability']) ? mysqli_real_escape_string($conn, $_GET['availability']) : '';
    
    $query = "SELECT a.*, k.nama_kategori 
              FROM alat_media a 
              LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
              WHERE 1=1";
              
    if ($search !== '') {
        $query .= " AND (a.nama_alat LIKE '%$search%' OR a.desc_alat LIKE '%$search%')";
    }
    if ($cat_filter > 0) {
        $query .= " AND a.id_kategori = $cat_filter";
    }
    if ($avail_filter === 'Tersedia') {
        $query .= " AND a.status_ketersediaan IN ('Tersedia', 'Disewa')";
    } elseif ($avail_filter === 'Tidak tersedia') {
        $query .= " AND a.status_ketersediaan NOT IN ('Tersedia', 'Disewa')";
    }
    if (!empty($_GET['condition'])) {
        $condition = mysqli_real_escape_string($conn, $_GET['condition']);
        $query .= " AND a.kondisi_alat = '$condition'";
    }
    $query .= " ORDER BY a.id_alat";
    $hasil = mysqli_query($conn, $query);
?>

<!-- Header Section -->
<div class="container pb-4">
    <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Katalog Peralatan</h1>
            <p class="text-muted mb-0">Temukan peralatan produksi visual dan audio terbaik untuk disewa.</p>
        </div>
    </div>
</div>

<!-- Search & Filters Toolbar -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-5">
        <input type="text" name="search" class="form-control" placeholder="Cari peralatan..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-2">
        <select name="kategori" class="form-select">
            <option value="">Semua Kategori</option>
            <?php
            $cat_res = mysqli_query($conn, "SELECT * FROM kategori");
            while ($c = mysqli_fetch_array($cat_res)) {
                $selected = ($cat_filter == $c['id_kategori']) ? 'selected' : '';
                echo "<option value='{$c['id_kategori']}' $selected>" . htmlspecialchars($c['nama_kategori']) . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="availability" class="form-select">
            <option value="">Ketersediaan</option>
            <option value="Tersedia" <?= ($avail_filter === 'Tersedia') ? 'selected' : '' ?>>Tersedia</option>
            <option value="Tidak tersedia" <?= ($avail_filter === 'Tidak tersedia') ? 'selected' : '' ?>>Tidak Tersedia</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="condition" class="form-select">
            <option value="">Kondisi</option>
            <option value="Baik" <?= (isset($_GET['condition']) && $_GET['condition'] === 'Baik') ? 'selected' : '' ?>>Baik</option>
            <option value="Perlu Perawatan" <?= (isset($_GET['condition']) && $_GET['condition'] === 'Perlu Perawatan') ? 'selected' : '' ?>>Perlu Perawatan</option>
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-dark w-100">Filter</button>
    </div>
</form>

<!-- Card Grid View -->
<div class="row g-3" id="view-cards">
    <?php if (mysqli_num_rows($hasil) > 0): ?>
        <?php while ($data = mysqli_fetch_array($hasil)): 
            // Dynamically resolve image path: if it starts with relative paths, map using BASE_URL
            $foto = $data['foto_alat'];
            if (strpos($foto, '../../') === 0) {
                $foto = str_replace('../../', BASE_URL, $foto);
            } else if (strpos($foto, 'assets/') === 0) {
                $foto = BASE_URL . $foto;
            }
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100">
                <img class="card-img-top" src="<?= $foto ?>" style="width: 100%; height: 200px; object-fit: contain; background: #f8f9fa;">
                <div class="card-body">
                    <small class="text-muted"><?= htmlspecialchars($data['nama_kategori']) ?></small>
                    <h5 class="card-title"><?= htmlspecialchars($data['nama_alat']) ?></h5>
                    <p class="mb-1"><strong>Kondisi:</strong> <?= htmlspecialchars($data['kondisi_alat']) ?></p>
                    <p class="mb-1"><strong>Harga:</strong> Rp <?= number_format($data['harga'], 0, ',', '.') ?>/hari</p>
                    <p class="mb-3"><strong>Stok:</strong> <?= $data['stok'] ?> Unit</p>
                    <?php
                    $is_bookable = in_array($data['status_ketersediaan'], ['Tersedia', 'Disewa']) && $data['stok'] > 0;
                    $badge_class = 'bg-danger';
                    $badge_text = 'Tidak Tersedia';
                    if ($is_bookable) {
                        if ($data['status_ketersediaan'] === 'Tersedia') {
                            $badge_class = 'bg-success';
                            $badge_text = 'Tersedia';
                        } else {
                            $badge_class = 'bg-info';
                            $badge_text = 'Disewa';
                        }
                    }
                    ?>
                    <span class="badge <?= $badge_class ?> mb-3">
                        <?= $badge_text ?>
                    </span>
                    <a href="detail.php?id=<?= $data['id_alat'] ?>" class="btn btn-outline-dark w-100">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 py-5 text-center">
            <div class="fs-1 text-muted"><i class="bi bi-inbox"></i></div>
            <h5 class="fw-semibold mt-2">Tidak Ada Produk</h5>
            <p class="text-muted small">Coba masukkan pencarian atau filter kategori lainnya.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>

