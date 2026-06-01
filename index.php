<?php include 'includes/header.php'; ?>

<!-- Hero Banner Section -->
<div class="row align-items-center py-5 my-5">
    <div class="col-lg-6 mb-4 mb-lg-0 text-center text-lg-start">
        <h1 class="display-4 fw-bold text-dark mb-3">Sewa Studio & Alat Kreatif Terbaik</h1>
        <p class="lead text-muted mb-4">
            Temukan studio podcast kedap suara, studio foto profesional, kamera mirrorless, lighting, dan perlengkapan kreator terbaik untuk menyempurnakan hasil karya Anda.
        </p>
        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
            <a href="<?= BASE_URL ?>modules/kategori/index.php" class="btn btn-dark btn-lg px-4 fw-semibold shadow-sm">
                Jelajahi Katalog
            </a>
            <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-outline-dark btn-lg px-4 fw-semibold">
                Booking Sekarang
            </a>
        </div>
    </div>
    
    <div class="col-lg-6 text-center">
        <!-- Visual Accent Box -->
        <div class="bg-dark text-white p-5 rounded-4 shadow-lg mx-auto" style="max-width: 480px; transform: rotate(1deg);">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="badge bg-secondary text-uppercase fw-bold px-2.5 py-1.5">StudioHub Choice</span>
                <span class="text-warning"><i class="bi bi-star-fill"></i> Best Seller</span>
            </div>
            <h3 class="fw-bold mb-2">Studio Podcast A</h3>
            <p class="text-white-50 small mb-4">
                Ruangan ber-AC kedap suara lengkap dengan 4 mic Shure SM7B dan mixer podcast profesional.
            </p>
            <hr class="opacity-25">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-white-50 d-block text-start">Harga Sewa</small>
                    <span class="fs-4 fw-bold">Rp 250.000 / hari</span>
                </div>
                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-light fw-bold text-dark btn-sm">Sewa</a>
            </div>
        </div>
    </div>
</div>

<!-- Highlight Categories Grid -->
<div class="row g-4 py-4 text-center">
    <div class="col-md-4">
        <div class="card h-100 p-4 border shadow-sm">
            <div class="fs-1 text-primary mb-3"><i class="bi bi-camera-reels"></i></div>
            <h5 class="fw-bold">Peralatan Premium</h5>
            <p class="text-muted small mb-0">
                Pilihan kamera mirrorless full-frame, lensa tajam, dan drone siap pakai untuk produksi konten visual kelas atas.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-4 border shadow-sm">
            <div class="fs-1 text-success mb-3"><i class="bi bi-mic"></i></div>
            <h5 class="fw-bold">Studio Akustik</h5>
            <p class="text-muted small mb-0">
                Ruangan studio kedap suara lengkap dengan peredam, lampu sorot studio, dan background cyclorama siap pakai.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-4 border shadow-sm">
            <div class="fs-1 text-warning mb-3"><i class="bi bi-lightning-charge"></i></div>
            <h5 class="fw-bold">Layanan Instan</h5>
            <p class="text-muted small mb-0">
                Proses penyewaan yang cepat, pengelolaan admin transparan, dan metode verifikasi yang andal.
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
