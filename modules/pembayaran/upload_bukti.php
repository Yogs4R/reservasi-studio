<?php
require_once '../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check login session (redirect if not logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$id_reserv = isset($_GET['id_reserv']) ? intval($_GET['id_reserv']) : 0;

if ($id_reserv <= 0) {
    header("Location: ../reservasi/riwayat.php");
    exit();
}

// Fetch reservation details
$stmt = $pdo->prepare("SELECT r.*, am.nama_alat 
                       FROM reservasi r
                       LEFT JOIN detail_reservasi dr ON r.id_reserv = dr.id_reserv
                       LEFT JOIN alat_media am ON dr.id_alat = am.id_alat
                       WHERE r.id_reserv = :id_reserv AND r.id_user = :id_user");
$stmt->execute([
    'id_reserv' => $id_reserv,
    'id_user' => $_SESSION['user_id']
]);
$reserv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserv) {
    header("Location: ../reservasi/riwayat.php?status=error&message=" . urlencode("Data reservasi tidak ditemukan."));
    exit();
}

// Check status (only Pending can upload proof)
if ($reserv['status_reserv'] !== 'Pending') {
    header("Location: ../reservasi/riwayat.php?status=error&message=" . urlencode("Status reservasi tidak memungkinkan untuk upload bukti pembayaran."));
    exit();
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pengirim = trim($_POST['nama_pengirim'] ?? '');
    $jml_pembayaran = isset($_POST['jml_pembayaran']) ? floatval($_POST['jml_pembayaran']) : 0;
    
    if (empty($nama_pengirim) || $jml_pembayaran <= 0 || empty($_FILES['bukti_pembayaran']['name'])) {
        $error_msg = 'Semua kolom formulir wajib diisi.';
    } else {
        // Validate and upload receipt image
        $file_name = $_FILES['bukti_pembayaran']['name'];
        $file_size = $_FILES['bukti_pembayaran']['size'];
        $file_tmp = $_FILES['bukti_pembayaran']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error_msg = 'Format gambar bukti pembayaran harus JPG, JPEG, PNG, atau WEBP.';
        } else if ($file_size > 2 * 1024 * 1024) { // 2MB limit
            $error_msg = 'Ukuran berkas gambar maksimal 2 MB.';
        } else {
            // Ensure uploads directory exists
            $upload_dir = '../../assets/img/uploads/bukti/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_file_name = 'bukti_' . $id_reserv . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                // Insert into pembayaran and update status to 'Waiting Payment'
                try {
                    $pdo->beginTransaction();
                    
                    // Generate unique TRX code
                    $kode_transaksi = 'TRX' . date('Ymd') . sprintf("%04d", $id_reserv) . rand(10, 99);
                    
                    // 1. Insert into pembayaran table
                    $stmt_pay = $pdo->prepare("INSERT INTO pembayaran (id_reserv, tgl_pembayaran, jml_pembayaran, bukti_pembayaran, kode_transaksi) 
                                               VALUES (:id_reserv, NOW(), :jml_pembayaran, :bukti_pembayaran, :kode_transaksi)");
                    $stmt_pay->execute([
                        'id_reserv' => $id_reserv,
                        'jml_pembayaran' => $jml_pembayaran,
                        'bukti_pembayaran' => $new_file_name,
                        'kode_transaksi' => $kode_transaksi
                    ]);
                    
                    // 2. Update status_reserv to 'Waiting Payment' in reservasi table
                    $stmt_reserv_update = $pdo->prepare("UPDATE reservasi SET status_reserv = 'Waiting Payment' WHERE id_reserv = :id_reserv");
                    $stmt_reserv_update->execute(['id_reserv' => $id_reserv]);
                    
                    $pdo->commit();
                    
                    header("Location: ../reservasi/riwayat.php?status=success&message=" . urlencode("Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin."));
                    exit();
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error_msg = 'Gagal memproses data pembayaran: ' . $e->getMessage();
                }
            } else {
                $error_msg = 'Gagal mengunggah gambar ke server. Silakan coba lagi.';
            }
        }
    }
}

include '../../includes/header.php';
?>

<div class="container my-5" style="padding-top: 40px;">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../reservasi/riwayat.php" class="text-decoration-none text-muted">Riwayat</a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Unggah Bukti</li>
                </ol>
            </nav>

            <!-- Error Notification -->
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div><?= htmlspecialchars($error_msg) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Premium Upload Card -->
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                <div class="text-center mb-4 border-bottom pb-3">
                    <span class="badge bg-dark text-uppercase px-2.5 py-1.5 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Konfirmasi Transfer</span>
                    <h3 class="fw-bold text-dark mb-1">Unggah Bukti Pembayaran</h3>
                    <p class="text-muted small mb-0">Harap lakukan transfer terlebih dahulu sebelum mengisi formulir ini.</p>
                </div>

                <!-- Reservation Overview -->
                <div class="p-3 bg-light rounded-3 mb-4 small border">
                    <div class="row g-2">
                        <div class="col-6 text-muted">ID Reservasi:</div>
                        <div class="col-6 text-dark fw-bold text-end">#<?= $reserv['id_reserv'] ?></div>
                        
                        <div class="col-6 text-muted">Produk Sewa:</div>
                        <div class="col-6 text-dark fw-bold text-end"><?= htmlspecialchars($reserv['nama_alat'] ?? 'Paket Sesi') ?></div>
                        
                        <div class="col-6 text-muted">Metode Pembayaran:</div>
                        <div class="col-6 text-dark fw-bold text-end"><?= htmlspecialchars($reserv['metode_pembayaran']) ?></div>
                        
                        <hr class="my-2 opacity-10">
                        
                        <div class="col-6 text-muted fw-bold">Total Tagihan:</div>
                        <div class="col-6 text-dark fw-bold text-end text-dark">Rp <?= number_format($reserv['harga_total'], 0, ',', '.') ?></div>
                    </div>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    
                    <!-- Sender Name -->
                    <div class="mb-3">
                        <label for="nama_pengirim" class="form-label fw-semibold text-dark small">NAMA PENGIRIM</label>
                        <input type="text" class="form-control border" id="nama_pengirim" name="nama_pengirim" placeholder="Masukkan nama pemilik rekening pengirim" required style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Silakan isi nama pengirim.</div>
                    </div>

                    <!-- Amount Paid -->
                    <div class="mb-3">
                        <label for="jml_pembayaran" class="form-label fw-semibold text-dark small">JUMLAH TRANSFER (RP)</label>
                        <input type="number" class="form-control border fw-bold text-dark" id="jml_pembayaran" name="jml_pembayaran" value="<?= htmlspecialchars($reserv['harga_total']) ?>" required style="border-radius: 8px; font-size: 0.95rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Silakan isi nominal transfer pembayaran yang sah.</div>
                        <small class="text-muted small">Nominal disarankan sama persis dengan total tagihan sewa.</small>
                    </div>

                    <!-- Receipt Image Upload -->
                    <div class="mb-4">
                        <label for="bukti_pembayaran" class="form-label fw-semibold text-dark small">GAMBAR BUKTI TRANSFER</label>
                        <input type="file" class="form-control border" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Silakan unggah gambar struk bukti transfer.</div>
                        <small class="text-muted small">Format didukung: JPG, JPEG, PNG, WEBP (Maksimal 2MB).</small>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold py-2.5 text-uppercase" style="border-radius: 8px; font-size: 0.9rem;">
                            <i class="bi bi-cloud-upload me-2"></i> Kirim Bukti Transfer
                        </button>
                        <a href="../reservasi/riwayat.php" class="btn btn-link text-muted text-decoration-none w-100 text-center mt-3 small">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
            
        </div>
    </div>
</div>

<script>
// Bootstrap validation trigger
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()
</script>

<?php
include '../../includes/footer.php';
?>