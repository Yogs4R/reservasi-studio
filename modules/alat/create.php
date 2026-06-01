<?php
// Include header (which initiates session_start() and defines BASE_URL)
include '../../includes/header.php';
require '../../config/koneksi.php';

// Access control: only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<div class='alert alert-danger my-5 text-center fw-bold'>
            <i class='bi bi-exclamation-triangle-fill'></i> Akses Ditolak! Halaman ini hanya untuk Administrator.
          </div>";
    include '../../includes/footer.php';
    exit;
}

$success_msg = "";
$error_msg = "";

// Form submission handler
if (isset($_POST['submit'])) {
    $nama_alat = mysqli_real_escape_string($conn, $_POST['nama_alat']);
    $desc_alat = mysqli_real_escape_string($conn, $_POST['desc_alat']);
    $id_kategori = (int)$_POST['id_kategori'];
    $harga = (double)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $kondisi_alat = mysqli_real_escape_string($conn, $_POST['kondisi_alat']);
    $status_ketersediaan = mysqli_real_escape_string($conn, $_POST['status_ketersediaan']);
    
    // Image Upload Handling
    $foto_path = "../../assets/img/uploads/default.png"; // Fallback
    if (isset($_FILES['foto_alat']) && $_FILES['foto_alat']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['foto_alat']['name'];
        $file_tmp = $_FILES['foto_alat']['tmp_name'];
        
        // Ensure uploads directory exists
        $upload_dir = "../../assets/img/uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename to avoid collision
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $clean_filename = preg_replace("/[^a-zA-Z0-9\-\_]/", "_", pathinfo($file_name, PATHINFO_FILENAME));
        $new_filename = $clean_filename . "_" . time() . "." . $file_ext;
        $target_file = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file_tmp, $target_file)) {
            $foto_path = "../../assets/img/uploads/" . $new_filename;
        } else {
            $error_msg = "Gagal memindahkan file upload gambar.";
        }
    }
    
    // Execute secure INSERT using prepared statements
    if ($error_msg === "") {
        $stmt = $conn->prepare("INSERT INTO alat_media (nama_alat, desc_alat, id_kategori, harga, stok, kondisi_alat, foto_alat, status_ketersediaan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssidisss", $nama_alat, $desc_alat, $id_kategori, $harga, $stok, $kondisi_alat, $foto_path, $status_ketersediaan);
            if ($stmt->execute()) {
                $success_msg = "Produk berhasil ditambahkan ke inventaris!";
            } else {
                $error_msg = "Gagal menambahkan produk: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Gagal mempersiapkan query: " . $conn->error;
        }
    }
}

// Fetch categories for select input
$categories = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");
?>

<div class="container pt-4">
    <!-- Title & Back Link -->
    <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1">Tambah Peralatan Baru</h1>
            <p class="text-muted mb-0">Lengkapi formulir di bawah untuk menambahkan item baru ke database.</p>
        </div>
        <a href="index.php" class="btn btn-outline-dark fw-semibold d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if ($success_msg !== ""): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?= $success_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_msg !== ""): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= $error_msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Insert Form -->
    <div class="card shadow-sm p-4 border mb-5" style="max-width: 800px; margin: 0 auto;">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row g-3">
                <!-- Nama Alat -->
                <div class="col-12">
                    <label for="nama_alat" class="form-label fw-semibold">Nama Alat / Studio</label>
                    <input type="text" class="form-control" id="nama_alat" name="nama_alat" placeholder="Contoh: Sony FX30 Cinema Camera" required>
                </div>

                <!-- Deskripsi -->
                <div class="col-12">
                    <label for="desc_alat" class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control" id="desc_alat" name="desc_alat" rows="4" placeholder="Detail spesifikasi alat..." required></textarea>
                </div>

                <!-- Kategori -->
                <div class="col-md-6">
                    <label for="id_kategori" class="form-label fw-semibold">Kategori</label>
                    <select class="form-select" id="id_kategori" name="id_kategori" required>
                        <option value="">Pilih Kategori...</option>
                        <?php while ($c = mysqli_fetch_array($categories)): ?>
                            <option value="<?= $c['id_kategori'] ?>"><?= htmlspecialchars($c['nama_kategori']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Kondisi -->
                <div class="col-md-6">
                    <label for="kondisi_alat" class="form-label fw-semibold">Kondisi Alat</label>
                    <select class="form-select" id="kondisi_alat" name="kondisi_alat" required>
                        <option value="Baik">Baik</option>
                        <option value="Perlu Perawatan">Perlu Perawatan</option>
                        <option value="Maintenance">Sedang Maintenance</option>
                    </select>
                </div>

                <!-- Harga Sewa -->
                <div class="col-md-6">
                    <label for="harga" class="form-label fw-semibold">Harga Sewa / Hari (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="number" step="0.01" class="form-control" id="harga" name="harga" placeholder="Contoh: 150000" required>
                    </div>
                </div>

                <!-- Jumlah Stok -->
                <div class="col-md-6">
                    <label for="stok" class="form-label fw-semibold">Jumlah Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" placeholder="Contoh: 5" required>
                </div>

                <!-- Status Ketersediaan -->
                <div class="col-12">
                    <label for="status_ketersediaan" class="form-label fw-semibold">Status Ketersediaan</label>
                    <select class="form-select" id="status_ketersediaan" name="status_ketersediaan" required>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Disewa">Disewa</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>

                <!-- Foto Alat -->
                <div class="col-12">
                    <label for="foto_alat" class="form-label fw-semibold">Foto Produk</label>
                    <input class="form-control" type="file" id="foto_alat" name="foto_alat" accept="image/*">
                    <div class="form-text">Gunakan file gambar berformat PNG, JPG, atau JPEG.</div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 mt-4 text-end">
                    <button type="submit" name="submit" class="btn btn-dark px-4 py-2 fw-semibold">
                        <i class="bi bi-save"></i> Simpan Inventaris
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
