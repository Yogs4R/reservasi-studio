<?php
// Include header (which initiates session_start() and defines BASE_URL)
include '../includes/header.php';
require '../config/koneksi.php';

// Sorting logic: Default is DESC (highest price)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_desc';
$order_clause = "a.harga DESC";
if ($sort === 'price_asc') {
    $order_clause = "a.harga ASC";
}
?>

<style>
    /* Premium Hover Animations & Custom Styling */
    .pricing-card {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 16px;
        background: #ffffff;
    }
    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        border-color: #000000;
    }
    .pricing-header {
        border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
    }
    .pricing-features li {
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #555555;
    }
    .badge-premium {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #000000;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .hero-pricing {
        background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        padding: 60px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
</style>

<!-- Hero Section -->
<div class="hero-pricing text-center">
    <div class="container">
        <span class="badge bg-dark px-3 py-2 text-uppercase mb-3 tracking-wider">Pricing Plans</span>
        <h1 class="display-4 fw-bold text-dark mb-3">Paket Layanan & Reservasi</h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            Pilih paket studio kreatif dan persewaan alat terbaik dengan harga transparan tanpa biaya tambahan.
        </p>
    </div>
</div>

<div class="container py-5">
    <!-- Studio Packages Section -->
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Paket Studio Kreatif</h2>
        <p class="text-muted">Sewa ruangan produksi multimedia lengkap dengan infrastruktur terbaik</p>
    </div>

    <div class="row g-4 justify-content-center mb-5">
        <!-- Plan 1: Hourly Podcast -->
        <div class="col-md-6 col-lg-4">
            <div class="card pricing-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="pricing-header pb-4 mb-4">
                        <span class="text-uppercase fw-bold text-muted small">Hourly Session</span>
                        <h3 class="fw-bold text-dark mt-2 mb-3">Podcast Lite</h3>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="fs-3 fw-bold text-dark">Rp 150.000</span>
                            <span class="text-muted">/ jam</span>
                        </div>
                    </div>
                    <ul class="list-unstyled pricing-features mb-4">
                        <li><i class="bi bi-check2-circle text-success"></i> Studio Podcast B (Kapasitas 2 Orang)</li>
                        <li><i class="bi bi-check2-circle text-success"></i> 2x Mic Rode PodMic</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Audio Mixer & Headphone Monitoring</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Operator Standby</li>
                        <li><i class="bi bi-x-circle text-danger"></i> Tidak Termasuk Kamera & Video</li>
                    </ul>
                </div>
                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php?type=paket&paket=lite" class="btn btn-outline-dark w-100 py-3 fw-semibold text-uppercase">Pesan Sekarang</a>
            </div>
        </div>

        <!-- Plan 2: Creator Bundle (Popular) -->
        <div class="col-md-6 col-lg-4">
            <div class="card pricing-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between border-dark position-relative">
                <span class="position-absolute top-0 start-50 translate-middle badge badge-premium px-3 py-2 text-uppercase fs-7">Terpopuler</span>
                <div>
                    <div class="pricing-header pb-4 mb-4">
                        <span class="text-uppercase fw-bold text-dark small">Half-Day Session</span>
                        <h3 class="fw-bold text-dark mt-2 mb-3">Visual Creator</h3>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="fs-3 fw-bold text-dark">Rp 300.000</span>
                            <span class="text-muted">/ 3 jam</span>
                        </div>
                    </div>
                    <ul class="list-unstyled pricing-features mb-4">
                        <li><i class="bi bi-check2-circle text-success"></i> Studio Foto White Room</li>
                        <li><i class="bi bi-check2-circle text-success"></i> 2x Godox Softbox Lighting Kit</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Background System (3 Warna)</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Ruang Ganti & Make Up AC</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Free High-speed Wi-Fi</li>
                    </ul>
                </div>
                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php?type=paket&paket=creator" class="btn btn-dark w-100 py-3 fw-semibold text-uppercase shadow">Pesan Sekarang</a>
            </div>
        </div>

        <!-- Plan 3: Pro Production -->
        <div class="col-md-6 col-lg-4">
            <div class="card pricing-card h-100 shadow-sm p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="pricing-header pb-4 mb-4">
                        <span class="text-uppercase fw-bold text-muted small">Full Production</span>
                        <h3 class="fw-bold text-dark mt-2 mb-3">Podcast Pro</h3>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="fs-3 fw-bold text-dark">Rp 600.000</span>
                            <span class="text-muted">/ 4 jam</span>
                        </div>
                    </div>
                    <ul class="list-unstyled pricing-features mb-4">
                        <li><i class="bi bi-check2-circle text-success"></i> Studio Podcast A (Kapasitas 4 Orang)</li>
                        <li><i class="bi bi-check2-circle text-success"></i> 4x Mic Rode PodMic & Rodecaster Pro II</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Kamera Cinema Sony FX30 & Tripod</li>
                        <li><i class="bi bi-check2-circle text-success"></i> Monitoring Video & Operator Perekaman</li>
                        <li><i class="bi bi-check2-circle text-success"></i> File Mentahan langsung transfer (SSD/Cloud)</li>
                    </ul>
                </div>
                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php?type=paket&paket=pro" class="btn btn-outline-dark w-100 py-3 fw-semibold text-uppercase">Pesan Sekarang</a>
            </div>
        </div>
    </div>

    <!-- Equipment Rental Rate Sheet Section -->
    <div class="text-center mt-5 mb-4">
        <h2 class="fw-bold mb-2">Tarif Harian Sewa Alat</h2>
        <p class="text-muted">Daftar harga sewa harian untuk peralatan produksi visual dan audio</p>
    </div>

    <div id="rate-sheet" class="card shadow-sm border overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="ps-4">Nama Alat</th>
                        <th scope="col">Kategori</th>
                        <th scope="col" style="width: 150px;">Kondisi</th>
                        <th scope="col" class="text-end pe-4" style="width: 250px;">
                            Harga Harian (Rp)
                            <span class="ms-2">
                                <?php if ($sort === 'price_asc'): ?>
                                    <a href="?sort=price_desc#rate-sheet" class="text-decoration-none text-warning" title="Urutkan Tertinggi">
                                        <i class="bi bi-arrow-up"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="?sort=price_asc#rate-sheet" class="text-decoration-none text-warning" title="Urutkan Terendah">
                                        <i class="bi bi-arrow-down"></i>
                                    </a>
                                <?php endif; ?>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $eq_query = "SELECT a.*, k.nama_kategori 
                                 FROM alat_media a 
                                 LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
                                 ORDER BY $order_clause";
                    $eq_result = mysqli_query($conn, $eq_query);
                    if ($eq_result && mysqli_num_rows($eq_result) > 0):
                        while ($eq = mysqli_fetch_assoc($eq_result)):
                    ?>
                        <tr>
                            <td class="fw-semibold ps-4">
                                <a href="<?= BASE_URL ?>modules/alat/detail.php?id=<?= $eq['id_alat'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($eq['nama_alat']) ?>
                                </a>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($eq['nama_kategori']) ?></span></td>
                            <td><?= htmlspecialchars($eq['kondisi_alat']) ?></td>
                            <td class="text-end fw-bold pe-4 text-dark">Rp <?= number_format($eq['harga'], 0, ',', '.') ?> / hari</td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">Data sewa alat tidak tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
