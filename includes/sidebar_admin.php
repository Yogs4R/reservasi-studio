<?php
// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    $doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $root_dir = str_replace('\\', '/', dirname(__DIR__));
    $relative_path = (strpos(strtolower($root_dir), strtolower($doc_root)) === 0) ? substr($root_dir, strlen($doc_root)) : '/reservasi-studio';
    $base_url = '/' . ltrim(str_replace('\\', '/', $relative_path), '/') . '/';
    if ($base_url === '//') { $base_url = '/'; }
    define('BASE_URL', $base_url);
}
?>
<!-- Sidebar Admin Navigation -->
<div class="card shadow-sm border border-light-subtle rounded-3 overflow-hidden bg-white mb-4">
    <div class="list-group list-group-flush">
        <div class="list-group-item bg-dark text-white fw-bold py-3">
            <i class="bi bi-shield-lock-fill me-2"></i>Admin Panel
        </div>
        <a href="<?= BASE_URL ?>modules/admin/index.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2.5">
            <i class="bi bi-speedometer2 text-secondary"></i> Dashboard Overview
        </a>
        <a href="<?= BASE_URL ?>modules/admin/produk/index.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2.5">
            <i class="bi bi-tools text-secondary"></i> Equipment Management
        </a>
        <a href="<?= BASE_URL ?>modules/admin/kategori/index.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2.5">
            <i class="bi bi-grid-3x3-gap text-secondary"></i> Category Management
        </a>
        <a href="<?= BASE_URL ?>modules/admin/user/index.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2.5">
            <i class="bi bi-people-fill text-secondary"></i> User Management
        </a>
        <a href="<?= BASE_URL ?>modules/reservasi/riwayat.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2.5">
            <i class="bi bi-calendar3 text-secondary"></i> Booking List
        </a>
        <a href="<?= BASE_URL ?>index.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2.5 text-danger border-top">
            <i class="bi bi-arrow-left-circle-fill"></i> Exit Admin Portal
        </a>
    </div>
</div>
