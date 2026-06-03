# Adversarial Review Request

Please run the `bmad-review-adversarial-general` skill on the following changed files and paste the findings back here.

## Files to Review

### 1. `index.php`
```php
<?php 
include 'includes/header.php'; 
require_once 'config/koneksi.php';

// Fetch 2 Popular Studios (Category: Studio Kreatif)
$studios_query = "SELECT a.*, k.nama_kategori FROM alat_media a 
                 LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
                 WHERE k.nama_kategori = 'Studio Kreatif' 
                 ORDER BY a.id_alat ASC LIMIT 2";
$studios_res = mysqli_query($conn, $studios_query);

// Fetch 4 Popular Equipments (Exclude Studio Kreatif)
$gears_query = "SELECT a.*, k.nama_kategori FROM alat_media a 
               LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
               WHERE k.nama_kategori != 'Studio Kreatif' OR a.id_kategori IS NULL
               ORDER BY a.harga DESC LIMIT 4";
$gears_res = mysqli_query($conn, $gears_query);
?>

</div> <!-- Close the default container from header.php to enable full-width bands -->

<!-- 1. HERO BANNER SECTION (Dark & Ambient Theme) -->
<section class="hero-section text-white d-flex align-items-center">
    <div class="container px-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start">
                <span class="badge bg-primary text-uppercase fw-bold px-3 py-2 mb-3 shadow-sm" style="font-size: 0.8rem; letter-spacing: 1px;">Kreativitas Tanpa Batas</span>
                <h1 class="display-3 fw-extrabold hero-title mb-3">Sewa Studio & Alat Kreatif Terbaik</h1>
                <p class="lead text-white-50 mb-4 fs-5" style="max-width: 540px;">
                    Temukan studio podcast profesional, studio foto modern, kamera mirrorless, lighting, dan perlengkapan kreator terbaik untuk menyempurnakan hasil karya Anda.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="<?= BASE_URL ?>modules/kategori/index.php" class="btn btn-primary btn-lg px-4 py-3 fw-bold shadow-sm transition-all">
                        <i class="bi bi-compass-fill me-2"></i>Jelajahi Katalog
                    </a>
                    <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-outline-light btn-lg px-4 py-3 fw-bold">
                        Booking Sekarang
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6 text-center">
                <!-- Visual Accent Box (Studio Podcast A highlight card) -->
                <div class="glass-card text-white p-5 rounded-4 shadow-lg mx-auto" style="max-width: 480px; transform: rotate(1deg);">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="badge bg-success text-uppercase fw-bold px-3 py-1.5 fs-12">Pilihan Utama</span>
                        <span class="text-warning"><i class="bi bi-star-fill me-1"></i> Best Seller</span>
                    </div>
                    <h3 class="fw-bold mb-2">Studio Podcast A</h3>
                    <p class="text-white-50 small mb-4">
                        Ruangan ber-AC kedap suara lengkap dengan 4 mic Shure SM7B dan mixer podcast Rodecaster Pro II.
                    </p>
                    <hr class="opacity-25 my-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <small class="text-white-50 d-block">Harga Sewa</small>
                            <span class="fs-4 fw-bold">Rp 250.000 <span class="fs-6 text-white-50 fw-normal">/ hari</span></span>
                        </div>
                        <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-light fw-bold text-dark px-4 py-2">Sewa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. HIGHLIGHT CATEGORIES GRID (Light Theme) -->
<section class="py-5 bg-light border-bottom">
    <div class="container px-lg-5 py-3">
        <div class="text-center mb-5">
            <span class="text-primary text-uppercase fw-bold small tracking-wider">Kategori Pilihan</span>
            <h2 class="fw-bold text-dark mt-1">Layanan Pendukung Produksi Anda</h2>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-3">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-camera-reels"></i></div>
                    <h5 class="fw-bold text-dark">Peralatan Premium</h5>
                    <p class="text-muted small mb-0">
                        Kamera mirrorless full-frame, lensa tajam, gimbal stabilizer, dan drone siap pakai untuk produksi visual kelas atas.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-3">
                    <div class="fs-1 text-success mb-3"><i class="bi bi-mic"></i></div>
                    <h5 class="fw-bold text-dark">Studio Akustik</h5>
                    <p class="text-muted small mb-0">
                        Ruangan kedap suara lengkap dengan acoustic panel, pencahayaan RGB studio, dan background cyclorama siap pakai.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-3">
                    <div class="fs-1 text-warning mb-3"><i class="bi bi-lightning-charge"></i></div>
                    <h5 class="fw-bold text-dark">Layanan Cepat</h5>
                    <p class="text-muted small mb-0">
                        Sistem verifikasi reservasi real-time, opsi pembayaran instan, dan proses administrasi yang transparan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. POPULAR STUDIOS SECTION (Dynamic SQL Data) -->
<section class="py-5 bg-white">
    <div class="container px-lg-5 py-3">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small">Studio Pilihan</span>
                <h2 class="fw-bold text-dark mb-0">Sewa Studio Kreatif</h2>
            </div>
            <a href="<?= BASE_URL ?>modules/kategori/index.php" class="btn btn-outline-dark fw-semibold btn-sm">Lihat Semua Studio</a>
        </div>
        <div class="row g-4">
            <?php if ($studios_res && mysqli_num_rows($studios_res) > 0): ?>
                <?php while ($studio = mysqli_fetch_array($studios_res)): 
                    $foto = $studio['foto_alat'];
                    if (strpos($foto, '../../') === 0) {
                        $foto = str_replace('../../', BASE_URL, $foto);
                    } else if (strpos($foto, 'assets/') === 0) {
                        $foto = BASE_URL . $foto;
                    }
                ?>
                <div class="col-md-6">
                    <div class="card product-card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="img-container" style="height: 250px;">
                            <img src="<?= $foto ?>" alt="<?= htmlspecialchars($studio['nama_alat']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-body p-4">
                            <span class="badge bg-light text-primary mb-2 fw-semibold"><?= htmlspecialchars($studio['nama_kategori']) ?></span>
                            <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($studio['nama_alat']) ?></h4>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($studio['desc_alat']) ?></p>
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <div>
                                    <span class="text-muted d-block small">Tarif Sewa</span>
                                    <span class="fs-5 fw-bold text-dark">Rp <?= number_format($studio['harga'], 0, ',', '.') ?> <span class="fs-6 text-muted fw-normal">/ hari</span></span>
                                </div>
                                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-dark fw-bold px-4">Booking</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 py-4 text-center">
                    <p class="text-muted">Studio belum tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 4. POPULAR EQUIPMENTS SECTION (Dynamic SQL Data) -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container px-lg-5 py-3">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary text-uppercase fw-bold small">Alat Terlaris</span>
                <h2 class="fw-bold text-dark mb-0">Perlengkapan Produksi Terpopuler</h2>
            </div>
            <a href="<?= BASE_URL ?>modules/alat/index.php" class="btn btn-outline-dark fw-semibold btn-sm">Lihat Semua Alat</a>
        </div>
        <div class="row g-4">
            <?php if ($gears_res && mysqli_num_rows($gears_res) > 0): ?>
                <?php while ($gear = mysqli_fetch_array($gears_res)): 
                    $foto = $gear['foto_alat'];
                    if (strpos($foto, '../../') === 0) {
                        $foto = str_replace('../../', BASE_URL, $foto);
                    } else if (strpos($foto, 'assets/') === 0) {
                        $foto = BASE_URL . $foto;
                    }
                ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card border-0 shadow-sm rounded-3 overflow-hidden h-100">
                        <div class="img-container">
                            <img src="<?= $foto ?>" alt="<?= htmlspecialchars($gear['nama_alat']) ?>">
                        </div>
                        <div class="card-body p-3 d-flex flex-column">
                            <small class="text-primary fw-semibold mb-1"><?= htmlspecialchars($gear['nama_kategori']) ?></small>
                            <h5 class="card-title text-dark fw-bold mb-2 text-truncate" title="<?= htmlspecialchars($gear['nama_alat']) ?>"><?= htmlspecialchars($gear['nama_alat']) ?></h5>
                            <p class="text-muted small mb-3 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;"><?= htmlspecialchars($gear['desc_alat']) ?></p>
                            
                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Harga / hari</span>
                                    <span class="fw-bold text-dark">Rp <?= number_format($gear['harga'], 0, ',', '.') ?></span>
                                </div>
                                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php" class="btn btn-outline-dark btn-sm w-100 fw-semibold">Sewa</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 py-4 text-center">
                    <p class="text-muted">Peralatan belum tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 5. HOW IT WORKS SECTION (Step Guide) -->
<section class="py-5 bg-white">
    <div class="container px-lg-5 py-3">
        <div class="text-center mb-5">
            <span class="text-primary text-uppercase fw-bold small">Kemudahan Alur</span>
            <h2 class="fw-bold text-dark mt-1">4 Langkah Mudah Melakukan Booking</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 text-center">
                <div class="step-item">
                    <div class="step-icon-wrapper">
                        <i class="bi bi-search text-primary"></i>
                        <span class="step-number">1</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Pilih Studio & Alat</h5>
                    <p class="text-muted small mb-0">Cari studio musik, podcast, atau perlengkapan media yang Anda butuhkan di katalog lengkap kami.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="step-item">
                    <div class="step-icon-wrapper">
                        <i class="bi bi-calendar-event text-success"></i>
                        <span class="step-number">2</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Pilih Jadwal</h5>
                    <p class="text-muted small mb-0">Tentukan tanggal sewa dan jam pelaksanaan yang sesuai dengan jadwal produksi kreatif Anda.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="step-item">
                    <div class="step-icon-wrapper">
                        <i class="bi bi-credit-card text-warning"></i>
                        <span class="step-number">3</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Lakukan Pembayaran</h5>
                    <p class="text-muted small mb-0">Bayar sewa secara instan melalui transfer bank atau e-wallet dengan konfirmasi otomatis cepat.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 text-center">
                <div class="step-item">
                    <div class="step-icon-wrapper">
                        <i class="bi bi-lightning-charge text-danger"></i>
                        <span class="step-number">4</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Siap Berkreasi!</h5>
                    <p class="text-muted small mb-0">Datang ke studio atau ambil peralatan di lokasi, dan mulailah melahirkan karya hebat Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6. TESTIMONIAL MARQUEE SECTION (Double Opposite Moving Carousels) -->
<section class="py-5 bg-dark text-white overflow-hidden">
    <div class="container px-lg-5 text-center mb-5">
        <span class="text-primary text-uppercase fw-bold small">Suara Kreator</span>
        <h2 class="fw-bold text-white mt-1">Ulasan Mereka yang Telah Berkarya</h2>
        <p class="text-white-50 small" style="max-width: 500px; margin: 0.5rem auto 0 auto;">Lihat ulasan langsung dari para profesional kreatif, podcaster, fotografer, dan musisi yang menyewa di StudioHub.</p>
    </div>
    
    <div class="marquee-container">
        <!-- Row 1: Moving Left (Clockwise/Left scroll) -->
        <div class="marquee-track marquee-left">
            <div class="marquee-content">
                <!-- Card 1 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge">AS</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Abyan Santoso</h6>
                            <small class="text-white-50">Podcaster Profesional</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Studio Podcast A sangat kedap suara dan mic Shure SM7B-nya membuat vokal sangat jernih. Recommended!"</p>
                </div>
                <!-- Card 2 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge">RH</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Rian Hermawan</h6>
                            <small class="text-white-50">Fashion Photographer</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Studio White Room sangat luas, lighting Godox-nya bekerja dengan sangat baik. Harga sangat bersahabat."</p>
                </div>
                <!-- Card 3 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge">SR</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Siti Rahma</h6>
                            <small class="text-white-50">Content Creator</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Sewa DJI Mini 4 Pro di sini sangat mudah, kondisinya prima. Pelayanan admin sangat cepat responnya."</p>
                </div>
            </div>
            <!-- Duplicate content for seamless scrolling -->
            <div class="marquee-content" aria-hidden="true">
                <!-- Card 1 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge">AS</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Abyan Santoso</h6>
                            <small class="text-white-50">Podcaster Profesional</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Studio Podcast A sangat kedap suara dan mic Shure SM7B-nya membuat vokal sangat jernih. Recommended!"</p>
                </div>
                <!-- Card 2 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge">RH</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Rian Hermawan</h6>
                            <small class="text-white-50">Fashion Photographer</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Studio White Room sangat luas, lighting Godox-nya bekerja dengan sangat baik. Harga sangat bersahabat."</p>
                </div>
                <!-- Card 3 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge">SR</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Siti Rahma</h6>
                            <small class="text-white-50">Content Creator</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Sewa DJI Mini 4 Pro di sini sangat mudah, kondisinya prima. Pelayanan admin sangat cepat responnya."</p>
                </div>
            </div>
        </div>
        
        <!-- Row 2: Moving Right (Counter-clockwise/Right scroll) -->
        <div class="marquee-track marquee-right">
            <div class="marquee-content">
                <!-- Card 4 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge alt-color">BP</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Budi Prasetyo</h6>
                            <small class="text-white-50">Commercial Videographer</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Sony FX30 yang saya sewa kondisinya bersih, sensor bersih, dan baterainya full. Mantap sekali servisnya!"</p>
                </div>
                <!-- Card 5 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge alt-color">DM</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Dina Marlina</h6>
                            <small class="text-white-50">Lifestyle Vlogger</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Peralatan livestreaming-nya lengkap sekali, Stream Deck dan Logitech Brio sangat membantu project saya."</p>
                </div>
                <!-- Card 6 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge alt-color">FW</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Ferry Wijaya</h6>
                            <small class="text-white-50">Singer & Producer</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Focusrite Scarlett & AT2020-nya bekerja sempurna untuk recording lagu demo saya. Sukses terus StudioHub!"</p>
                </div>
            </div>
            <!-- Duplicate content for seamless scrolling -->
            <div class="marquee-content" aria-hidden="true">
                <!-- Card 4 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge alt-color">BP</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Budi Prasetyo</h6>
                            <small class="text-white-50">Commercial Videographer</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Sony FX30 yang saya sewa kondisinya bersih, sensor bersih, dan baterainya full. Mantap sekali servisnya!"</p>
                </div>
                <!-- Card 5 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge alt-color">DM</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Dina Marlina</h6>
                            <small class="text-white-50">Lifestyle Vlogger</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Peralatan livestreaming-nya lengkap sekali, Stream Deck dan Logitech Brio sangat membantu project saya."</p>
                </div>
                <!-- Card 6 -->
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="avatar-badge alt-color">FW</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Ferry Wijaya</h6>
                            <small class="text-white-50">Singer & Producer</small>
                        </div>
                    </div>
                    <div class="text-warning mb-2 small"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="mb-0 text-white-50 small">"Focusrite Scarlett & AT2020-nya bekerja sempurna untuk recording lagu demo saya. Sukses terus StudioHub!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 7. FAQ SECTION (Interactive Bootstrap Accordion) -->
<section class="py-5 bg-white">
    <div class="container px-lg-5 py-3" style="max-width: 900px;">
        <div class="text-center mb-5">
            <span class="text-primary text-uppercase fw-bold small">Tanya Jawab</span>
            <h2 class="fw-bold text-dark mt-1">FAQ (Pertanyaan Umum)</h2>
            <p class="text-muted small">Temukan jawaban cepat untuk beberapa pertanyaan yang paling sering diajukan mengenai layanan StudioHub.</p>
        </div>
        
        <div class="accordion faq-accordion" id="faqAccordion">
            <!-- FAQ 1 -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        Bagaimana cara mengubah jadwal (reschedule) booking?
                    </button>
                </h3>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Anda dapat mengajukan perubahan jadwal (reschedule) paling lambat <strong>24 jam sebelum waktu sewa dimulai</strong>. Hubungi Customer Service kami via WhatsApp dengan menyertakan detail kode reservasi untuk dibantu proses pemindahannya secara instan.
                    </div>
                </div>
            </div>
            
            <!-- FAQ 2 -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Apakah biaya sewa studio sudah termasuk perlengkapan/alat?
                    </button>
                </h3>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Ya! Setiap penyewaan studio (seperti Studio Podcast A) sudah termasuk perlengkapan default di dalam ruangan tersebut (seperti mikrofon Rode/Shure, audio interface, peredam suara, dan pencahayaan dasar). Namun, untuk penyewaan kamera mirrorless atau drone di luar kelengkapan studio default harus disewa secara terpisah melalui katalog peralatan kami.
                    </div>
                </div>
            </div>
            
            <!-- FAQ 3 -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Bagaimana jika terjadi kerusakan alat saat disewa?
                    </button>
                </h3>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Semua peralatan diperiksa kualitas dan kondisinya sebelum dan sesudah disewa. Apabila terjadi kerusakan akibat kelalaian penyewa (terjatuh, terkena air, dsb.), penyewa diwajibkan bertanggung jawab atas biaya perbaikan atau penggantian unit sesuai kesepakatan tertulis di formulir syarat penyewaan.
                    </div>
                </div>
            </div>
            
            <!-- FAQ 4 -->
            <div class="accordion-item">
                <h3 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        Apakah saya bisa memperpanjang durasi sewa di tempat?
                    </button>
                </h3>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Perpanjangan durasi sewa studio atau alat dapat dilakukan di lokasi <strong>apabila tidak ada reservasi lain</strong> dari pengguna lain langsung setelah jam sewa Anda selesai. Anda dapat langsung mengonfirmasi perpanjangan waktu sewa ke staff on-site kami dan menyelesaikan pembayarannya.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mt-4"> <!-- Reopen the default bootstrap container from header.php so that footer.php can close it properly -->
<?php include 'includes/footer.php'; ?>
