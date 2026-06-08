<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Jakarta');

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

// Access Control check: Require admin login
if (!isset($_SESSION['user_id']) || ($_SESSION['user_id'] != 1 && strtolower($_SESSION['email'] ?? '') !== 'admin@studiohub.com')) {
    header("Location: " . BASE_URL . "modules/auth/login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudioHub Admin Panel</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS via CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Local Style CSS using dynamic BASE_URL -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    
    <!-- Admin Monochrome Style Overrides -->
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #1a1a1a;
            background-color: #fafafa;
        }
        /* Monochrome Admin Navbar */
        .navbar-admin {
            background-color: #000000 !important;
            border-bottom: 2px solid #111111;
        }
        .navbar-admin .navbar-brand {
            color: #ffffff !important;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .navbar-admin .nav-link {
            color: #b3b3b3 !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .navbar-admin .nav-link:hover, .navbar-admin .nav-link.active {
            color: #ffffff !important;
        }
        /* Buttons override to strict monochrome */
        .btn-dark {
            background-color: #000000 !important;
            border-color: #000000 !important;
            color: #ffffff !important;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-dark:hover {
            background-color: #222222 !important;
            border-color: #222222 !important;
        }
        .btn-outline-dark {
            color: #000000 !important;
            border-color: #000000 !important;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-outline-dark:hover {
            background-color: #000000 !important;
            color: #ffffff !important;
        }
        /* Sidebar active links monochrome style */
        .list-group-item-action {
            transition: all 0.2s ease;
        }
        .list-group-item-action:hover {
            background-color: #f1f1f1 !important;
            color: #000000 !important;
        }
        /* Dashboard KPI Cards styling override to fit monochrome theme */
        .bg-gradient-revenue {
            background: #111111 !important;
        }
        .bg-gradient-bookings {
            background: #222222 !important;
        }
        .bg-gradient-equip {
            background: #333333 !important;
        }
        .bg-gradient-users {
            background: #444444 !important;
        }
        .kpi-card {
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        .kpi-card .icon-circle {
            background: rgba(255, 255, 255, 0.15) !important;
        }
    </style>
</head>
<body>

    <!-- Admin Simple & Elegant Dark Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin fixed-top shadow-sm">
        <div class="container-fluid px-lg-5">
            <!-- Brand / Logo -->
            <a class="navbar-brand fw-bold text-uppercase d-flex align-items-center gap-2" href="<?= BASE_URL ?>modules/admin/index.php">
                STUDIOHUB <span class="badge bg-white text-dark fw-bold py-1 px-2 border rounded-pill" style="font-size: 0.65rem;">ADMIN</span>
            </a>
            
            <!-- Auth Section and Toggler Container -->
            <div class="d-flex align-items-center gap-3 ms-auto ms-lg-0 order-lg-3">
                <div class="d-flex align-items-center gap-3">
                    <?php if (isset($_SESSION['nama'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle fw-semibold px-3 py-1.5 btn-sm border-white-50" type="button" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars(
                                    $_SESSION['nama'],
                                ) ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-menu-item dropdown-item fw-medium py-2" href="<?= BASE_URL ?>index.php"><i class="bi bi-house me-2"></i>Main Web</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-menu-item dropdown-item text-danger fw-semibold py-2" href="<?= BASE_URL ?>modules/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Toggler for mobile -->
                <button class="navbar-toggler ms-2 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbarContent" aria-controls="adminNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <!-- Navbar Links
            <div class="collapse navbar-collapse order-lg-2" id="adminNavbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/admin/index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/admin/produk/index.php">Equipment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/admin/kategori/index.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>modules/admin/user/index.php">Users</a>
                    </li>
                </ul>
            </div> -->
        </div>
    </nav>

    <!-- Main Container Left Open -->
    <div class="container mt-4">
