<?php
require_once '../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

include '../../includes/header.php';

// Fetch all available products grouped by category
$stmt = $pdo->prepare("SELECT a.id_alat, a.nama_alat, a.harga, a.stok, k.nama_kategori 
                       FROM alat_media a 
                       LEFT JOIN kategori k ON a.id_kategori = k.id_kategori 
                       WHERE a.status_ketersediaan = 'Tersedia' AND a.stok > 0
                       ORDER BY k.nama_kategori, a.nama_alat");
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Map products by category
$grouped_items = [];
foreach ($items as $item) {
    $cat = $item['nama_kategori'] ?? 'Lain-lain';
    $grouped_items[$cat][] = $item;
}

// Get preselected product ID if any
$preselected_id = isset($_GET['id_alat']) ? intval($_GET['id_alat']) : 0;
?>

<div class="container my-5" style="padding-top: 40px;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php" class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Formulir Booking</li>
                </ol>
            </nav>

            <!-- Alert Notification -->
            <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div>
                            <strong>Pemesanan Gagal!</strong><br>
                            <?= htmlspecialchars($_GET['message'] ?? 'Terjadi bentrokan jadwal atau kesalahan pada data.') ?>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Premium Booking Card -->
            <div class="card border-0 shadow-lg p-4 p-md-5 bg-white" style="border-radius: 12px;">
                <div class="text-center mb-5">
                    <span class="badge bg-dark text-uppercase px-3 py-1.5 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Reservasi Instan</span>
                    <h2 class="fw-bold text-dark mb-1">Formulir Booking Studio & Alat</h2>
                    <p class="text-muted small">Pilih tipe layanan sewa harian atau paket sesi kreatif.</p>
                </div>

                <form action="<?= BASE_URL ?>modules/reservasi/proses_booking.php" method="POST" id="bookingForm" class="needs-validation" novalidate>
                    
                    <!-- 1. TIPE RESERVASI (Sewa Harian vs Paket Sesi dari pricing.php) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small mb-3">TIPE RESERVASI</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="tipe_booking" id="tipe_harian" value="harian" checked autocomplete="off">
                                <label class="btn btn-outline-dark w-100 p-3 text-start border-2 h-100" for="tipe_harian" style="border-radius: 8px;">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 fs-3 me-3"></i>
                                        <div>
                                            <span class="d-block fw-bold mb-1">Sewa Harian</span>
                                            <span class="text-muted small d-block" style="font-size: 0.75rem; line-height: 1.2;">Sewa studio/alat dengan durasi fleksibel (hitungan hari)</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="tipe_booking" id="tipe_paket" value="paket" autocomplete="off">
                                <label class="btn btn-outline-dark w-100 p-3 text-start border-2 h-100" for="tipe_paket" style="border-radius: 8px;">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-gift fs-3 me-3"></i>
                                        <div>
                                            <span class="d-block fw-bold mb-1">Paket Sesi</span>
                                            <span class="text-muted small d-block" style="font-size: 0.75rem; line-height: 1.2;">Pilih paket dari halaman pricing (durasi jam/sesi khusus)</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 2a. SECTION: SEWA HARIAN -->
                    <div id="section_harian">
                        <!-- Product Selection -->
                        <div class="mb-4">
                            <label for="id_alat" class="form-label fw-semibold text-dark small">PILIH STUDIO ATAU ALAT KREATIF</label>
                            <select class="form-select form-select-lg border" id="id_alat" name="id_alat" style="border-radius: 8px; font-size: 0.95rem; border-color: #dee2e6;">
                                <option value="" disabled <?= $preselected_id == 0 ? 'selected' : '' ?>>-- Pilih Studio / Alat --</option>
                                <?php foreach ($grouped_items as $category => $productList): ?>
                                    <optgroup label="<?= htmlspecialchars($category) ?>">
                                        <?php foreach ($productList as $product): ?>
                                            <?php 
                                                $selected = ($product['id_alat'] == $preselected_id) ? 'selected' : '';
                                                $harga_formatted = 'Rp ' . number_format($product['harga'], 0, ',', '.') . ' / hari';
                                            ?>
                                            <option value="<?= $product['id_alat'] ?>" data-price="<?= $product['harga'] ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($product['nama_alat']) ?> (<?= $harga_formatted ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Silakan pilih studio atau peralatan terlebih dahulu.</div>
                        </div>

                        <!-- Dates Selection -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="tgl_mulai" class="form-label fw-semibold text-dark small">TANGGAL MULAI SEWA</label>
                                <input type="date" class="form-control form-control-lg border" id="tgl_mulai" name="tgl_mulai" min="<?= date('Y-m-d') ?>" style="border-radius: 8px; font-size: 0.95rem; border-color: #dee2e6;">
                                <div class="invalid-feedback">Tanggal mulai tidak valid.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="tgl_selesai" class="form-label fw-semibold text-dark small">TANGGAL SELESAI SEWA</label>
                                <input type="date" class="form-control form-control-lg border" id="tgl_selesai" name="tgl_selesai" min="<?= date('Y-m-d') ?>" style="border-radius: 8px; font-size: 0.95rem; border-color: #dee2e6;">
                                <div class="invalid-feedback">Tanggal selesai tidak valid.</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2b. SECTION: PAKET SESI (dari pricing.php) -->
                    <div id="section_paket" style="display: none;">
                        <!-- Package Plan Selection -->
                        <div class="mb-4">
                            <label for="paket_plan" class="form-label fw-semibold text-dark small">PILIH PAKET STUDIO KREATIF</label>
                            <select class="form-select form-select-lg border" id="paket_plan" name="paket_plan" style="border-radius: 8px; font-size: 0.95rem; border-color: #dee2e6;">
                                <option value="" disabled selected>-- Pilih Paket --</option>
                                <option value="lite" data-price="150000" data-id-alat="7">Podcast Lite (Rp 150.000 / jam)</option>
                                <option value="creator" data-price="300000" data-id-alat="8">Visual Creator (Rp 300.000 / 3 jam)</option>
                                <option value="pro" data-price="600000" data-id-alat="7">Podcast Pro (Rp 600.000 / 4 jam)</option>
                            </select>
                            <div class="invalid-feedback">Silakan pilih paket studio kreatif.</div>
                        </div>

                        <!-- Single Date Selection for Packages -->
                        <div class="mb-4">
                            <label for="tgl_sesi" class="form-label fw-semibold text-dark small">TANGGAL SESI</label>
                            <input type="date" class="form-control form-control-lg border" id="tgl_sesi" name="tgl_sesi" min="<?= date('Y-m-d') ?>" style="border-radius: 8px; font-size: 0.95rem; border-color: #dee2e6;">
                            <div class="invalid-feedback">Tanggal sesi tidak valid.</div>
                        </div>
                    </div>

                    <!-- 3. DYNAMIC PAYMENT SUMMARY SECTION -->
                    <div class="p-4 bg-light border rounded-3 mb-4" id="summaryCard" style="display: none; border-color: #e3e6f0;">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase" style="letter-spacing: 0.5px;">Rincian Estimasi Biaya</h6>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Harga / Tarif:</span>
                            <span class="fw-semibold text-dark small" id="summary_price_per_day">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="summary_duration_row">
                            <span class="text-muted small">Durasi Sewa:</span>
                            <span class="fw-semibold text-dark small" id="summary_duration">0 hari</span>
                        </div>
                        <hr class="my-3 opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Total Pembayaran:</span>
                            <span class="fs-4 fw-bold text-dark" id="summary_total">Rp 0</span>
                        </div>
                    </div>

                    <!-- 4. METODE PEMBAYARAN DINAMIS (PayPal, Banks, E-Wallet) -->
                    <div class="mb-4 border-top pt-4">
                        <label class="form-label fw-semibold text-dark small mb-3">METODE PEMBAYARAN</label>
                        
                        <!-- Pilihan Utama (Main Categories) -->
                        <div class="row g-2 mb-4" id="main_payment_categories">
                            <div class="col-md-3">
                                <input type="radio" class="btn-check" name="kategori_pembayaran" id="pay_cat_bank" value="bank" checked autocomplete="off">
                                <label class="btn btn-outline-secondary w-100 py-2.5 fw-semibold" for="pay_cat_bank" style="border-radius: 6px; font-size: 0.85rem;">
                                    <i class="bi bi-bank me-1"></i> Transfer Bank
                                </label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="btn-check" name="kategori_pembayaran" id="pay_cat_wallet" value="wallet" autocomplete="off">
                                <label class="btn btn-outline-secondary w-100 py-2.5 fw-semibold" for="pay_cat_wallet" style="border-radius: 6px; font-size: 0.85rem;">
                                    <i class="bi bi-wallet2 me-1"></i> E-Wallet
                                </label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="btn-check" name="kategori_pembayaran" id="pay_cat_qris" value="qris" autocomplete="off">
                                <label class="btn btn-outline-secondary w-100 py-2.5 fw-semibold" for="pay_cat_qris" style="border-radius: 6px; font-size: 0.85rem;">
                                    <i class="bi bi-qr-code-scan me-1"></i> QRIS
                                </label>
                            </div>
                            <div class="col-md-3">
                                <input type="radio" class="btn-check" name="kategori_pembayaran" id="pay_cat_international" value="international" autocomplete="off">
                                <label class="btn btn-outline-secondary w-100 py-2.5 fw-semibold" for="pay_cat_international" style="border-radius: 6px; font-size: 0.85rem;">
                                    <i class="bi bi-globe me-1"></i> Internasional
                                </label>
                            </div>
                        </div>

                        <!-- Hidden Input to Store Selected Child Method -->
                        <input type="hidden" name="metode_pembayaran" id="selected_payment_method" value="Transfer Bank Mandiri" required>

                        <!-- Anak Pilihan (Child Options) Container -->
                        <div class="border p-3 rounded-3 bg-white" style="border-radius: 8px;">
                            
                            <!-- 4a. BANK TRANSFER OPTIONS -->
                            <div id="payment_details_bank" class="payment-child-section">
                                <div class="row g-2">
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100 active" data-method-val="Transfer Bank Mandiri" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/mandiri.webp" alt="Mandiri" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">Mandiri</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="Transfer Bank BCA" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/bca.webp" alt="BCA" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">BCA</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="Transfer Bank BNI" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/bni.webp" alt="BNI" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">BNI</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="Transfer Bank BRI" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/bri.webp" alt="BRI" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">BRI</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4b. E-WALLET OPTIONS -->
                            <div id="payment_details_wallet" class="payment-child-section" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="E-Wallet GoPay" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/gopay.webp" alt="GoPay" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">GoPay</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="E-Wallet DANA" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/dana.webp" alt="DANA" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">DANA</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="E-Wallet OVO" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/ovo.webp" alt="OVO" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">OVO</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="E-Wallet ShopeePay" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/shopeepay.webp" alt="ShopeePay" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">ShopeePay</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4c. INTERNATIONAL OPTIONS -->
                            <div id="payment_details_international" class="payment-child-section" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-6 col-sm-4">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="PayPal Internasional" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/paypal.webp" alt="PayPal" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">PayPal</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="Stripe Internasional" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/stripe.webp" alt="Stripe" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">Stripe</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4d. QRIS OPTIONS -->
                            <div id="payment_details_qris" class="payment-child-section" style="display: none;">
                                <div class="row g-2 justify-content-center">
                                    <div class="col-6 col-sm-4">
                                        <div class="payment-method-card border rounded p-2 text-center h-100" data-method-val="QRIS" style="cursor: pointer; border-radius: 6px; transition: all 0.2s;">
                                            <div class="payment-logo-placeholder mb-2 d-flex align-items-center justify-content-center" style="height: 35px;">
                                                <img src="<?= BASE_URL ?>assets/img/qris.webp" alt="QRIS" style="height: 24px; max-width: 80px; object-fit: contain;">
                                            </div>
                                            <span class="small fw-semibold text-dark d-block" style="font-size: 0.75rem;">QRIS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <small class="text-muted d-block mt-2">Simulasi integrasi pembayaran digital. Proses validasi transaksi dilakukan oleh Admin di panel belakang.</small>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold py-3 text-uppercase" style="border-radius: 8px; font-size: 0.95rem; transition: background-color 0.2s ease;">
                            <i class="bi bi-calendar-check me-2"></i> Konfirmasi Booking
                        </button>
                        <a href="<?= BASE_URL ?>index.php" class="btn btn-link text-muted text-decoration-none w-100 text-center mt-3 small">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                        </a>
                    </div>

                </form>
            </div>
            
        </div>
    </div>
</div>

<!-- Custom CSS for Card States -->
<style>
.payment-method-card {
    border-color: #dee2e6 !important;
}
.payment-method-card.active {
    border-color: #000000 !important;
    background-color: #f8f9fa !important;
    box-shadow: 0 0 0 2px #000000;
}
.payment-method-card:hover {
    border-color: #6c757d !important;
    background-color: #fdfdfd;
}

/* Fix contrast issues for radio description inside btn-outline-dark */
.btn-check:checked + .btn-outline-dark {
    background-color: #212529 !important;
    border-color: #212529 !important;
    color: #ffffff !important;
}
.btn-check:checked + .btn-outline-dark .text-muted {
    color: rgba(255, 255, 255, 0.75) !important;
}
</style>

<!-- Scripts for Dynamic Pricing & Client Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeHarian = document.getElementById('tipe_harian');
    const tipePaket = document.getElementById('tipe_paket');
    const sectionHarian = document.getElementById('section_harian');
    const sectionPaket = document.getElementById('section_paket');

    const productSelect = document.getElementById('id_alat');
    const inputStart = document.getElementById('tgl_mulai');
    const inputEnd = document.getElementById('tgl_selesai');

    const paketSelect = document.getElementById('paket_plan');
    const inputSesi = document.getElementById('tgl_sesi');

    const summaryCard = document.getElementById('summaryCard');
    const sumPricePerDay = document.getElementById('summary_price_per_day');
    const sumDurationRow = document.getElementById('summary_duration_row');
    const sumDuration = document.getElementById('summary_duration');
    const sumTotal = document.getElementById('summary_total');
    
    // Toggle Reservation Types
    tipeHarian.addEventListener('change', toggleReservationType);
    tipePaket.addEventListener('change', toggleReservationType);

    function toggleReservationType() {
        if (tipeHarian.checked) {
            sectionHarian.style.display = 'block';
            sectionPaket.style.display = 'none';
            // Enable inputs in harian, disable in paket to prevent validation issues
            productSelect.disabled = false;
            inputStart.disabled = false;
            inputEnd.disabled = false;
            paketSelect.disabled = true;
            inputSesi.disabled = true;
        } else {
            sectionHarian.style.display = 'none';
            sectionPaket.style.display = 'block';
            productSelect.disabled = true;
            inputStart.disabled = true;
            inputEnd.disabled = true;
            paketSelect.disabled = false;
            inputSesi.disabled = false;
        }
        calculateTotal();
    }

    // Trigger toggle initialization
    toggleReservationType();

    // Parse URL query parameters to preselect package or products
    const urlParams = new URLSearchParams(window.location.search);
    const typeParam = urlParams.get('type');
    const paketParam = urlParams.get('paket');

    if (typeParam === 'paket') {
        tipePaket.checked = true;
        toggleReservationType();
        
        if (paketParam) {
            paketSelect.value = paketParam;
            calculateTotal();
        }
    }

    // Formatting helper
    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Set end date min attribute based on start date selection
    inputStart.addEventListener('change', function() {
        if (inputStart.value) {
            inputEnd.min = inputStart.value;
            if (inputEnd.value && inputEnd.value < inputStart.value) {
                inputEnd.value = inputStart.value;
            }
        }
        calculateTotal();
    });

    inputEnd.addEventListener('change', calculateTotal);
    productSelect.addEventListener('change', calculateTotal);
    paketSelect.addEventListener('change', calculateTotal);
    inputSesi.addEventListener('change', calculateTotal);

    function calculateTotal() {
        if (tipeHarian.checked) {
            // Sewa Harian calculation
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (!selectedOption || selectedOption.value === "") {
                summaryCard.style.display = 'none';
                return;
            }

            const pricePerDay = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            if (!inputStart.value || !inputEnd.value) {
                summaryCard.style.display = 'block';
                sumPricePerDay.textContent = formatRupiah(pricePerDay) + ' / hari';
                sumDurationRow.style.display = 'flex';
                sumDuration.textContent = '-';
                sumTotal.textContent = formatRupiah(0);
                return;
            }

            const dateStart = new Date(inputStart.value);
            const dateEnd = new Date(inputEnd.value);
            const diffTime = dateEnd - dateStart;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                const totalPrice = diffDays * pricePerDay;
                summaryCard.style.display = 'block';
                sumPricePerDay.textContent = formatRupiah(pricePerDay) + ' / hari';
                sumDurationRow.style.display = 'flex';
                sumDuration.textContent = diffDays + ' hari';
                sumTotal.textContent = formatRupiah(totalPrice);
            } else {
                summaryCard.style.display = 'none';
            }
        } else {
            // Paket Sesi calculation
            const selectedOption = paketSelect.options[paketSelect.selectedIndex];
            if (!selectedOption || selectedOption.value === "") {
                summaryCard.style.display = 'none';
                return;
            }

            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            summaryCard.style.display = 'block';
            sumPricePerDay.textContent = formatRupiah(price) + ' (Tarif Paket)';
            sumDurationRow.style.display = 'none'; // No "days" duration logic for package sessions
            sumTotal.textContent = formatRupiah(price);
        }
    }

    // --- Dynamic Payment Category Selector ---
    const payCatBank = document.getElementById('pay_cat_bank');
    const payCatWallet = document.getElementById('pay_cat_wallet');
    const payCatQris = document.getElementById('pay_cat_qris');
    const payCatInt = document.getElementById('pay_cat_international');
    
    const detailsBank = document.getElementById('payment_details_bank');
    const detailsWallet = document.getElementById('payment_details_wallet');
    const detailsQris = document.getElementById('payment_details_qris');
    const detailsInt = document.getElementById('payment_details_international');

    const paymentInput = document.getElementById('selected_payment_method');
    const paymentCards = document.querySelectorAll('.payment-method-card');

    function togglePaymentCategory() {
        // Hide all child sections
        detailsBank.style.display = 'none';
        detailsWallet.style.display = 'none';
        detailsQris.style.display = 'none';
        detailsInt.style.display = 'none';

        // Remove active class from all payment option cards
        paymentCards.forEach(c => c.classList.remove('active'));

        let defaultCard = null;

        if (payCatBank.checked) {
            detailsBank.style.display = 'block';
            defaultCard = detailsBank.querySelector('.payment-method-card');
        } else if (payCatWallet.checked) {
            detailsWallet.style.display = 'block';
            defaultCard = detailsWallet.querySelector('.payment-method-card');
        } else if (payCatQris.checked) {
            detailsQris.style.display = 'block';
            defaultCard = detailsQris.querySelector('.payment-method-card');
        } else if (payCatInt.checked) {
            detailsInt.style.display = 'block';
            defaultCard = detailsInt.querySelector('.payment-method-card');
        }

        // Set the default payment selection for the active category
        if (defaultCard) {
            defaultCard.classList.add('active');
            paymentInput.value = defaultCard.getAttribute('data-method-val');
        }
    }

    // Set payment listeners
    payCatBank.addEventListener('change', togglePaymentCategory);
    payCatWallet.addEventListener('change', togglePaymentCategory);
    payCatQris.addEventListener('change', togglePaymentCategory);
    payCatInt.addEventListener('change', togglePaymentCategory);

    // Child Option Click Listeners
    paymentCards.forEach(card => {
        card.addEventListener('click', function() {
            paymentCards.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            paymentInput.value = this.getAttribute('data-method-val');
        });
    });

    // Run calculations on initial load
    if (productSelect.value !== "") {
        calculateTotal();
    }

    // Bootstrap validation trigger
    const form = document.getElementById('bookingForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>

<?php
include '../../includes/footer.php';
?>