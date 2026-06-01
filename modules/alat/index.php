<?php
// Include header (which initiates session_start())
include '../../includes/header.php';
require '../../config/koneksi.php';

// Check role
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

if ($role === 'admin'):
    // --- ADMIN VIEW: Dashboard Penjualan Style ---
    
    // Fetch summary statistics
    $total_alat_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM alat_media");
    $total_alat = mysqli_fetch_assoc($total_alat_res)['total'];
    
    $total_kat_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori");
    $total_kategori = mysqli_fetch_assoc($total_kat_res)['total'];
?>

<div class="container pt-4">
    <!-- Breadcrumbs / Title -->
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Dashboard Peralatan</h1>
            <p class="text-muted mb-0">Kelola ketersediaan alat dan inventaris StudioHub.</p>
        </div>
        <div>
            <a href="create.php" class="btn btn-dark d-flex align-items-center gap-2 fw-semibold px-4">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-4">
            <div class="card p-4 border shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase small fw-bold text-muted">Total Alat</span>
                        <h2 class="display-6 fw-bold mt-1 mb-0"><?= $total_alat ?></h2>
                    </div>
                    <div class="fs-1 text-primary"><i class="bi bi-tools"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card p-4 border shadow-sm">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase small fw-bold text-muted">Total Kategori</span>
                        <h2 class="display-6 fw-bold mt-1 mb-0"><?= $total_kategori ?></h2>
                    </div>
                    <div class="fs-1 text-success"><i class="bi bi-grid-3x3-gap"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product List Table -->
    <div class="table-responsive bg-white p-4 rounded shadow-sm border">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 80px;">No.</th>
                    <th scope="col">Nama Alat</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Harga Sewa</th>
                    <th scope="col" style="width: 120px; text-align: center;">Stok</th>
                    <th scope="col" style="width: 150px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT a.*, k.nama_kategori 
                          FROM alat_media a 
                          LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
                          ORDER BY a.id_alat";
                $hasil = mysqli_query($conn, $query);
                
                $no = 1;
                while ($data = mysqli_fetch_array($hasil)):
                ?>
                <tr>
                    <th class="fw-semibold"><?= $no ?></th>
                    <td><?= htmlspecialchars($data['nama_alat']) ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($data['nama_kategori']) ?></span></td>
                    <td class="fw-semibold">Rp <?= number_format($data['harga'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= $data['stok'] ?> Unit</td>
                    <td class="text-center">
                        <a href="update.php?id_alat=<?= $data['id_alat'] ?>" class="btn btn-warning btn-sm mx-1" title="edit">
                            <i class="bi bi-pencil-square"></i>
                        </a> 
                        <a href="delete.php?id_alat=<?= $data['id_alat'] ?>" class="btn btn-danger btn-sm mx-1" title="hapus">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </td>
                </tr>
                <?php $no++; endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
else:
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
        $query .= " AND a.status_ketersediaan = 'Tersedia'";
    } elseif ($avail_filter === 'Tidak tersedia') {
        $query .= " AND a.status_ketersediaan != 'Tersedia'";
    }
    $query .= " ORDER BY a.id_alat";
    $hasil = mysqli_query($conn, $query);
?>

<div class="container pt-4">
    <!-- Page Title -->
    <div class="border-bottom pb-3 mb-4">
        <h1 class="fw-bold mb-1">Katalog Peralatan</h1>
        <p class="text-muted mb-0">Temukan peralatan produksi visual dan audio terbaik untuk disewa.</p>
    </div>

    <!-- Search & Filters Toolbar -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari alat..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <div class="col-md-3">
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
                <option value="Tidak tersedia" <?= ($avail_filter === 'Tidak tersedia') ? 'selected' : '' ?>>Tidak tersedia</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark w-100 fw-semibold">Filter</button>
        </div>
    </form>

    <!-- Catalog Grid -->
    <div class="row g-4 mb-5">
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
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border shadow-sm">
                    <div class="bg-light d-flex align-items-center justify-content-center p-3" style="height: 200px;">
                        <img src="<?= $foto ?>" class="img-fluid rounded" style="max-height: 100%; object-fit: contain;" alt="<?= htmlspecialchars($data['nama_alat']) ?>">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted d-block mb-1"><?= htmlspecialchars($data['nama_kategori']) ?></small>
                        <h5 class="card-title fw-bold mb-1 text-truncate" title="<?= htmlspecialchars($data['nama_alat']) ?>"><?= htmlspecialchars($data['nama_alat']) ?></h5>
                        <p class="small text-muted mb-3 text-truncate-2" style="height: 40px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($data['desc_alat']) ?>
                        </p>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <small class="text-muted d-block small">Harga Sewa</small>
                                    <span class="fw-bold text-dark fs-5">Rp <?= number_format($data['harga'], 0, ',', '.') ?></span>
                                </div>
                                <span class="badge <?= ($data['status_ketersediaan'] === 'Tersedia' && $data['stok'] > 0) ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ($data['status_ketersediaan'] === 'Tersedia' && $data['stok'] > 0) ? 'Tersedia' : 'Disewa' ?>
                                </span>
                            </div>
                            
                            <a href="#" class="btn btn-outline-dark btn-sm w-100 fw-semibold text-uppercase py-2">VIEW DETAILS</a>
                        </div>
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
</div>

<?php endif; ?>

<?php include '../../includes/footer.php'; ?>
