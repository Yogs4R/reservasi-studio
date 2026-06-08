<?php
require_once '../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_hp = trim($_POST['no_hp'] ?? '');

    if (empty($nama) || empty($email) || empty($password)) {
        $error_msg = 'Nama, Email, dan Password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Format alamat email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error_msg = 'Password minimal terdiri dari 6 karakter.';
    } else {
        // Check if email already exists
        $stmt_check = $pdo->prepare("SELECT COUNT(*) AS total FROM user WHERE email = :email");
        $stmt_check->execute(['email' => $email]);
        $email_exists = $stmt_check->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        if ($email_exists > 0) {
            $error_msg = 'Alamat email sudah terdaftar. Gunakan email lain.';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt_insert = $pdo->prepare("INSERT INTO user (nama, email, id_password, no_hp) VALUES (:nama, :email, :id_password, :no_hp)");
                $stmt_insert->execute([
                    'nama' => $nama,
                    'email' => $email,
                    'id_password' => $hashed_password,
                    'no_hp' => !empty($no_hp) ? $no_hp : null
                ]);

                header("Location: login.php?status=success&message=" . urlencode("Registrasi berhasil! Silakan masuk dengan akun Anda."));
                exit();
            } catch (Exception $e) {
                $error_msg = 'Gagal mendaftarkan akun: ' . $e->getMessage();
            }
        }
    }
}

include '../../includes/header.php';
?>

<div class="container my-5" style="padding-top: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8 col-sm-10">
            
            <!-- Notifications -->
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div><?= htmlspecialchars($error_msg) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Signup Card -->
            <div class="card border-0 shadow p-4 bg-white" style="border-radius: 12px;">
                <div class="text-center mb-4 pb-2 border-bottom">
                    <span class="badge bg-dark text-uppercase px-2.5 py-1.5 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Daftar Akun</span>
                    <h3 class="fw-bold text-dark mb-1">Sign Up</h3>
                    <p class="text-muted small">Buat akun StudioHub untuk memulai pemesanan studio dan alat.</p>
                </div>

                <form action="" method="POST" class="needs-validation" novalidate>
                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-semibold text-dark small">NAMA LENGKAP</label>
                        <input type="text" class="form-control border" id="nama" name="nama" placeholder="Masukkan nama lengkap" required style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Nama lengkap wajib diisi.</div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-dark small">ALAMAT EMAIL</label>
                        <input type="email" class="form-control border" id="email" name="email" placeholder="contoh@gmail.com" required style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Masukkan email yang sah.</div>
                    </div>

                    <!-- Nomor HP (Optional) -->
                    <div class="mb-3">
                        <label for="no_hp" class="form-label fw-semibold text-dark small">NOMOR HP / WHATSAPP</label>
                        <input type="tel" class="form-control border" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx (opsional)" style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold text-dark small">PASSWORD</label>
                        <input type="password" class="form-control border" id="password" name="password" placeholder="Minimal 6 karakter" required minlength="6" style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Password minimal 6 karakter.</div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold py-2.5 text-uppercase" style="border-radius: 8px; font-size: 0.9rem;">
                            <i class="bi bi-person-plus me-2"></i> Register
                        </button>
                        
                        <p class="text-center mt-4 mb-0 small text-muted">
                            Sudah memiliki akun? 
                            <a href="login.php" class="text-dark fw-bold text-decoration-none">Log In disini</a>
                        </p>
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
