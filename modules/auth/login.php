<?php
require_once '../../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_id'] == 1 || strtolower($_SESSION['email'] ?? '') === 'admin@studiohub.com') {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../../index.php");
    }
    exit();
}

$error_msg = '';
$success_msg = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? '';

    if (empty($email) || empty($password)) {
        $error_msg = 'Email dan Password wajib diisi.';
    } else {
        // Fetch user details
        $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $login_success = false;

            // Try standard password verify
            if (password_verify($password, $user['id_password'])) {
                $login_success = true;
            } 
            // Fallback recovery for default dummy accounts with mock hashes
            else {
                $email_lower = strtolower($email);
                if (($email_lower === 'admin@studiohub.com' && $password === 'admin') || 
                    ($email_lower === 'abyan@gmail.com' && $password === 'password')) {
                    
                    // Re-hash and auto-repair the database row to make it valid
                    $new_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_update = $pdo->prepare("UPDATE user SET id_password = :hash WHERE id_user = :id_user");
                    $stmt_update->execute([
                        'hash' => $new_hash,
                        'id_user' => $user['id_user']
                    ]);
                    $login_success = true;
                }
            }

            if ($login_success) {
                // Initialize Session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];

                // Redirect logic
                if ($user['id_user'] == 1 || strtolower($user['email']) === 'admin@studiohub.com') {
                    header("Location: ../admin/index.php");
                } elseif (!empty($redirect)) {
                    header("Location: " . $redirect);
                } else {
                    header("Location: ../../index.php");
                }
                exit();
            } else {
                $error_msg = 'Password yang Anda masukkan salah.';
            }
        } else {
            $error_msg = 'Akun dengan email tersebut tidak ditemukan.';
        }
    }
}

include '../../includes/header.php';
?>

<div class="container my-5" style="padding-top: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8 col-sm-10">
            
            <!-- Notifications -->
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                        <div><?= htmlspecialchars($success_msg) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                        <div><?= htmlspecialchars($error_msg) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Login Card -->
            <div class="card border-0 shadow p-4 bg-white" style="border-radius: 12px;">
                <div class="text-center mb-4 pb-2 border-bottom">
                    <span class="badge bg-dark text-uppercase px-2.5 py-1.5 mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Selamat Datang</span>
                    <h3 class="fw-bold text-dark mb-1">Log In</h3>
                    <p class="text-muted small">Silakan masuk untuk mengelola pemesanan studio dan alat.</p>
                </div>

                <form action="" method="POST" class="needs-validation" novalidate>
                    <!-- Redirect URL placeholder -->
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold text-dark small">ALAMAT EMAIL</label>
                        <input type="email" class="form-control border" id="email" name="email" placeholder="contoh@gmail.com" required style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Masukkan email yang sah.</div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold text-dark small">PASSWORD</label>
                        <input type="password" class="form-control border" id="password" name="password" placeholder="Masukkan password Anda" required style="border-radius: 8px; font-size: 0.9rem; border-color: #dee2e6;">
                        <div class="invalid-feedback">Password wajib diisi.</div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn btn-dark btn-lg w-100 fw-bold py-2.5 text-uppercase" style="border-radius: 8px; font-size: 0.9rem;">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login
                        </button>
                        
                        <p class="text-center mt-4 mb-0 small text-muted">
                            Belum memiliki akun? 
                            <a href="register.php" class="text-dark fw-bold text-decoration-none">Sign Up disini</a>
                        </p>
                    </div>
                </form>
            </div>

            <!-- Testing Account Info Box -->
            <div class="card border-0 shadow-sm mt-4 bg-light p-3" style="border-radius: 8px; font-size: 0.8rem; border: 1px solid #dee2e6 !important;">
                <div class="fw-bold text-dark mb-2"><i class="bi bi-info-circle me-1"></i>Akun Uji Coba Bawaan:</div>
                <div class="mb-1"><strong>Admin:</strong> admin@studiohub.com / password: <code>admin</code></div>
                <div><strong>Pelanggan:</strong> abyan@gmail.com / password: <code>password</code></div>
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
