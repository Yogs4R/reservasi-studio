<?php
// Include header (which initiates session_start() and defines BASE_URL)
include '../../includes/header.php';
require '../../config/koneksi.php';

// Get and sanitize the tool ID from the URL parameter
$id_alat = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch details of the specific equipment
$query = "SELECT a.*, k.nama_kategori 
          FROM alat_media a 
          LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
          WHERE a.id_alat = ?";
$stmt = $conn->prepare($query);
$tool = null;

if ($stmt) {
    $stmt->bind_param("i", $id_alat);
    $stmt->execute();
    $result = $stmt->get_result();
    $tool = $result->fetch_assoc();
    $stmt->close();
}

// If tool not found, display an error
if (!$tool):
?>
<div class="container py-5 text-center">
    <div class="py-5">
        <i class="bi bi-exclamation-octagon text-danger display-1 mb-3"></i>
        <h2 class="fw-bold">Peralatan Tidak Ditemukan</h2>
        <p class="text-muted">Item yang Anda cari tidak tersedia atau telah dihapus.</p>
        <a href="index.php" class="btn btn-dark mt-3 px-4 py-2">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Katalog
        </a>
    </div>
</div>
<?php
    include '../../includes/footer.php';
    exit;
endif;

// Dynamically resolve image path
$foto = $tool['foto_alat'];
if (strpos($foto, '../../') === 0) {
    $foto = str_replace('../../', BASE_URL, $foto);
} else if (strpos($foto, 'assets/') === 0) {
    $foto = BASE_URL . $foto;
}
?>

<div class="container py-5">
    <!-- Breadcrumbs / Back button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-dark btn-sm d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2">
            <i class="bi bi-arrow-left"></i> Kembali ke Katalog
        </a>
    </div>

    <!-- Product Details Card -->
    <div class="card border shadow-sm overflow-hidden" style="border-radius: 12px;">
        <div class="row g-0">
            <!-- Left Side: Image Gallery/Container -->
            <div class="col-lg-6 bg-light d-flex align-items-center justify-content-center p-4 border-end" style="min-height: 400px;">
                <img src="<?= $foto ?>" class="img-fluid rounded shadow-sm" style="max-height: 380px; object-fit: contain;" alt="<?= htmlspecialchars($tool['nama_alat']) ?>">
            </div>

            <!-- Right Side: Details and Call to Action -->
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between">
                <div>
                    <!-- Category Badge -->
                    <span class="badge bg-secondary mb-2 px-3 py-2 text-uppercase fs-7 fw-semibold tracking-wider">
                        <?= htmlspecialchars($tool['nama_kategori']) ?>
                    </span>

                    <!-- Product Title -->
                    <h1 class="fw-bold text-dark mb-3"><?= htmlspecialchars($tool['nama_alat']) ?></h1>

                    <!-- Rating or Stats Mock (Premium Design Touch) -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="d-flex text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <span class="text-muted small">| Kondisi: <strong><?= htmlspecialchars($tool['kondisi_alat']) ?></strong></span>
                    </div>

                    <!-- Description -->
                    <h5 class="fw-semibold text-secondary">Deskripsi</h5>
                    <p class="text-muted mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                        <?= nl2br(htmlspecialchars($tool['desc_alat'])) ?>
                    </p>

                    <!-- Specifications List -->
                    <div class="row g-2 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <i class="bi bi-boxes fs-4 text-dark"></i>
                                <div>
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Stok Tersedia</small>
                                    <strong class="text-dark fs-5"><?= $tool['stok'] ?> Unit</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                                <i class="bi bi-check-circle-fill fs-4 <?= $tool['status_ketersediaan'] === 'Tersedia' ? 'text-success' : 'text-danger' ?>"></i>
                                <div>
                                    <small class="text-muted d-block text-uppercase" style="font-size: 0.75rem;">Status</small>
                                    <strong class="text-dark fs-5"><?= htmlspecialchars($tool['status_ketersediaan']) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <!-- Pricing and Booking Button -->
                    <div class="border-top pt-4 mt-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <small class="text-muted d-block">Harga Sewa / Hari</small>
                            <span class="fs-2 fw-bold text-dark">Rp <?= number_format($tool['harga'], 0, ',', '.') ?></span>
                        </div>
                        <div>
                            <?php if ($tool['status_ketersediaan'] === 'Tersedia' && $tool['stok'] > 0): ?>
                                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php?id_alat=<?= $tool['id_alat'] ?>" class="btn btn-dark btn-lg px-5 py-3 fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-calendar-check me-2"></i> Book Now
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg px-5 py-3 fw-bold text-uppercase" disabled>
                                    <i class="bi bi-slash-circle me-2"></i> Tidak Tersedia
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
