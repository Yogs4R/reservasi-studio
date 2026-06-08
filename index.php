<?php 
require_once 'config/koneksi.php';
include 'includes/header.php'; 

// Fetch 2 available studios
$stmt_studio = $pdo->prepare("SELECT a.*, k.nama_kategori 
                              FROM alat_media a 
                              LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
                              WHERE k.nama_kategori = 'Studio Kreatif' AND a.status_ketersediaan IN ('Tersedia', 'Disewa') 
                              LIMIT 2");
$stmt_studio->execute();
$studios = $stmt_studio->fetchAll(PDO::FETCH_ASSOC);

// Fetch 2 available equipment (not in Studio Kreatif category)
$stmt_alat = $pdo->prepare("SELECT a.*, k.nama_kategori 
                            FROM alat_media a 
                            LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
                            WHERE k.nama_kategori != 'Studio Kreatif' AND a.status_ketersediaan IN ('Tersedia', 'Disewa') 
                            LIMIT 2");
$stmt_alat->execute();
$alats = $stmt_alat->fetchAll(PDO::FETCH_ASSOC);

$popular_items = array_merge($studios, $alats);
?>

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
                <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php?id_alat=7" class="btn btn-light fw-bold text-dark btn-sm">Sewa</a>
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

<!-- Section: Cara Booking -->
<div class="py-5 my-5 border-top">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Cara Booking Mudah</h2>
        <p class="text-muted">Langkah sederhana untuk menyewa studio dan alat kreatif pilihan Anda</p>
    </div>
    <div class="row g-4">
        <div class="col-md-3 col-sm-6 text-center">
            <div class="card step-card p-3 h-100">
                <div class="step-number">01</div>
                <h5 class="fw-bold mb-2">Pilih Katalog</h5>
                <p class="text-muted small mb-0">Temukan studio podcast, studio foto, atau perlengkapan kamera yang Anda butuhkan di katalog lengkap kami.</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <div class="card step-card p-3 h-100">
                <div class="step-number">02</div>
                <h5 class="fw-bold mb-2">Tentukan Jadwal</h5>
                <p class="text-muted small mb-0">Pilih tanggal sewa dan tentukan jam penggunaan yang sesuai dengan jadwal produksi konten Anda.</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <div class="card step-card p-3 h-100">
                <div class="step-number">03</div>
                <h5 class="fw-bold mb-2">Bayar Instan</h5>
                <p class="text-muted small mb-0">Lakukan pembayaran secara aman menggunakan berbagai metode pembayaran digital yang tersedia.</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 text-center">
            <div class="card step-card p-3 h-100">
                <div class="step-number">04</div>
                <h5 class="fw-bold mb-2">Siap Berkreasi</h5>
                <p class="text-muted small mb-0">Datang ke studio atau ambil perlengkapan Anda di lokasi, dan siap untuk memproduksi karya terbaik!</p>
            </div>
        </div>
    </div>
</div>

<!-- Section: Studio & Alat Populer -->
<div class="py-5 my-5 border-top">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Studio & Alat Populer</h2>
        <p class="text-muted">Pilihan terfavorit yang sering disewa oleh para kreator konten</p>
    </div>
    <div class="row g-4">
        <?php if (!empty($popular_items)): ?>
            <?php foreach ($popular_items as $item): 
                $foto_path = str_replace('../../', BASE_URL, $item['foto_alat']);
                $is_studio = ($item['nama_kategori'] === 'Studio Kreatif');
                $badge_text = $is_studio ? 'Studio' : 'Alat';
                $harga_formatted = 'Rp ' . number_format($item['harga'], 0, ',', '.') . ' / hari';
            ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 p-3">
                        <div class="bg-light rounded mb-3 text-center py-4" style="height: 160px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= htmlspecialchars($foto_path) ?>" alt="<?= htmlspecialchars($item['nama_alat']) ?>" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-dark text-uppercase small" style="font-size: 0.7rem;"><?= $badge_text ?></span>
                            <span class="text-warning small"><i class="bi bi-star-fill"></i> 4.9</span>
                        </div>
                        <h5 class="fw-bold mb-2 fs-6"><?= htmlspecialchars($item['nama_alat']) ?></h5>
                        <p class="text-muted small mb-3"><?= htmlspecialchars(substr($item['desc_alat'], 0, 80)) ?><?= strlen($item['desc_alat']) > 80 ? '...' : '' ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Mulai dari</small>
                                <span class="fw-bold text-dark" style="font-size: 0.95rem;"><?= $harga_formatted ?></span>
                            </div>
                            <a href="<?= BASE_URL ?>modules/reservasi/form_booking.php?id_alat=<?= $item['id_alat'] ?>" class="btn btn-outline-dark btn-sm fw-semibold">Sewa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4">
                <p class="text-muted">Tidak ada studio atau alat populer yang tersedia saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Section: Testimoni -->
<div class="py-5 my-5 border-top overflow-hidden">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Testimoni Kreator</h2>
        <p class="text-muted">Apa kata mereka yang telah berkarya bersama StudioHub</p>
    </div>
    
    <div class="marquee-container">
        <!-- Row 1: Left scrolling -->
        <div class="marquee-track marquee-left mb-4">
            <!-- Duplicate 1 -->
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Studionya sangat bersih dan kedap suara! Hasil rekaman podcast saya jadi sangat jernih tanpa noise."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Rian</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Podcaster</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Kamera Sony A7IV yang disewa dalam kondisi prima dan lensa tambahannya bersih sekali. Sangat recommended!"</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Siti</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Photographer</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Pelayanannya sangat ramah dan cepat. Proses booking via website ini juga gampang banget."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Budi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">YouTuber</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i></div>
                <p class="text-muted small mb-3">"Studio cyclorama-nya luas dan lighting-nya sangat lengkap. Sukses terus untuk StudioHub!"</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Dewi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Creative Director</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Sewa alat di sini murah tapi kualitasnya profesional. Sangat membantu untuk project kuliahan."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Fikri</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Mahasiswa</small>
            </div>
            
            <!-- Duplicate 2 for infinite effect -->
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Studionya sangat bersih dan kedap suara! Hasil rekaman podcast saya jadi sangat jernih tanpa noise."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Rian</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Podcaster</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Kamera Sony A7IV yang disewa dalam kondisi prima dan lensa tambahannya bersih sekali. Sangat recommended!"</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Siti</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Photographer</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Pelayanannya sangat ramah dan cepat. Proses booking via website ini juga gampang banget."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Budi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">YouTuber</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i></div>
                <p class="text-muted small mb-3">"Studio cyclorama-nya luas dan lighting-nya sangat lengkap. Sukses terus untuk StudioHub!"</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Dewi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Creative Director</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Sewa alat di sini murah tapi kualitasnya profesional. Sangat membantu untuk project kuliahan."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Fikri</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Mahasiswa</small>
            </div>
        </div>

        <!-- Row 2: Right scrolling -->
        <div class="marquee-track marquee-right">
            <!-- Duplicate 1 -->
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Sangat puas dengan fasilitas studio podcast. Operatornya juga sangat membantu saat setup awal."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Andi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Content Creator</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Mic Shure SM7B-nya oke banget, tidak ada kendala sama sekali. Mixer-nya juga versi terbaru."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Lina</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Voice Actor</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i></div>
                <p class="text-muted small mb-3">"Terima kasih StudioHub! Kamera mirrorless-nya buat acara lamaran saya kemarin jadi terdokumentasi dengan baik."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Hendra</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Client</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Sistem booking-nya otomatis, transparan, dan ga ribet. Pasti bakal sewa di sini lagi."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Sarah</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Event Organizer</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Studio fotonya ber-AC dingin dan banyak pilihan background warna-warni. Mantap!"</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Joni</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Fashion Designer</small>
            </div>
            
            <!-- Duplicate 2 for infinite effect -->
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Sangat puas dengan fasilitas studio podcast. Operatornya juga sangat membantu saat setup awal."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Andi</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Content Creator</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Mic Shure SM7B-nya oke banget, tidak ada kendala sama sekali. Mixer-nya juga versi terbaru."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Lina</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Voice Actor</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i></div>
                <p class="text-muted small mb-3">"Terima kasih StudioHub! Kamera mirrorless-nya buat acara lamaran saya kemarin jadi terdokumentasi dengan baik."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Hendra</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Client</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Sistem booking-nya otomatis, transparan, dan ga ribet. Pasti bakal sewa di sini lagi."</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Sarah</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Event Organizer</small>
            </div>
            <div class="testimonial-card">
                <div class="text-warning mb-2"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                <p class="text-muted small mb-3">"Studio fotonya ber-AC dingin dan banyak pilihan background warna-warni. Mantap!"</p>
                <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Joni</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Fashion Designer</small>
            </div>
        </div>
    </div>
</div>

<!-- Section: FAQ Accordion -->
<div class="py-5 my-5 border-top">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Pertanyaan Umum (FAQ)</h2>
        <p class="text-muted">Butuh informasi lebih lanjut? Temukan jawabannya di bawah ini</p>
    </div>
    
    <div class="accordion custom-accordion mx-auto" id="faqAccordion" style="max-width: 800px;">
        <!-- FAQ 1 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    Bagaimana cara mengubah jadwal (reschedule) sewa studio/alat?
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Anda dapat melakukan reschedule maksimal 24 jam sebelum jadwal booking dimulai dengan menghubungi admin WhatsApp kami. Perubahan jadwal tergantung ketersediaan studio atau peralatan pada tanggal baru yang Anda pilih.
                </div>
            </div>
        </div>
        <!-- FAQ 2 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Apakah biaya sewa studio sudah termasuk perlengkapan dan properti?
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Ya, untuk sewa studio podcast sudah termasuk mic Shure SM7B, mixer Rodecaster Pro, dan lighting basic. Untuk studio foto, sudah tersedia beberapa warna background gulung (cyclorama) serta 2 lampu studio basic flash/continuous. Detail spesifikasi alat tertera pada detail masing-masing studio.
                </div>
            </div>
        </div>
        <!-- FAQ 3 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Bagaimana kebijakan jika terjadi kerusakan alat saat disewa?
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Penyewa bertanggung jawab penuh atas segala kerusakan yang terjadi selama masa penyewaan. Jika terjadi kerusakan fisik akibat kelalaian penggunaan, penyewa wajib mengganti biaya perbaikan atau penggantian unit alat sesuai kesepakatan tertulis saat serah terima.
                </div>
            </div>
        </div>
        <!-- FAQ 4 -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    Apakah saya bisa memperpanjang durasi sewa di tempat?
                </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted small">
                    Perpanjangan durasi sewa (extend) dapat dilakukan secara langsung di lokasi jika jadwal setelah sesi Anda masih kosong (tidak ada booking dari pelanggan lain). Silakan konfirmasi ke staf on-site kami 30 menit sebelum sesi Anda berakhir.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
