<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate BASE_URL dynamically to prevent broken links
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$root_dir = str_replace('\\', '/', dirname(__DIR__));

$doc_root_clean = strtolower($doc_root);
$root_dir_clean = strtolower($root_dir);

if (strpos($root_dir_clean, $doc_root_clean) === 0) {
    $relative_path = substr($root_dir, strlen($doc_root));
} else {
    $relative_path = '/reservasi-studio'; // Fallback
}

$base_url = '/' . ltrim(str_replace('\\', '/', $relative_path), '/') . '/';
if ($base_url === '//') {
    $base_url = '/';
}
if (!defined('BASE_URL')) {
    define('BASE_URL', $base_url);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudioHub</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Local Style CSS using dynamic BASE_URL -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <!-- Dynamic Simple & Elegant Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
        <div class="container-fluid px-lg-5">
            <!-- Brand / Logo -->
            <a class="navbar-brand fw-bold text-uppercase" href="<?= BASE_URL ?>index.php">
                STUDIOHUB
            </a>
            
            <!-- Auth Section and Toggler Container -->
            <div class="d-flex align-items-center gap-3 ms-auto ms-lg-0 order-lg-3">
                <!-- Auth Section -->
                <div class="d-flex align-items-center gap-3">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>modules/auth/login.php" class="btn btn-link text-decoration-none text-dark fw-bold text-uppercase p-0">LOG IN</a>
                        <a href="<?= BASE_URL ?>modules/auth/register.php" class="btn btn-outline-dark fw-semibold text-uppercase px-3 py-1.5 btn-book-now">SIGN UP</a>
                    <?php else: ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-dark dropdown-toggle fw-semibold px-3" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($_SESSION['nama']) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown">
                                <li><a class="dropdown-menu-item dropdown-item fw-medium py-2" href="<?= BASE_URL ?>modules/reservasi/riwayat.php">My Bookings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-menu-item dropdown-item text-danger fw-semibold py-2" href="<?= BASE_URL ?>modules/auth/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Toggler for mobile -->
                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <!-- Navbar Links -->
            <div class="collapse navbar-collapse order-lg-2" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/kategori/index.php">Catalog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/alat/index.php">Equipment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/pricing.php">Pricing</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container Left Open -->
    <div class="container mt-4">