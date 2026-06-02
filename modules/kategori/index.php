<?php
require '../../config/koneksi.php';

// Fetch all categories
$query = "SELECT * FROM kategori ORDER BY id_kategori";
$hasil = mysqli_query($conn, $query);
?>

<?php include '../../includes/header.php'; ?>

<style>
    /* Premium category card styling */
    .category-card {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        border-color: #000000;
    }
    .category-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #212529;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }
    .category-card:hover .category-icon-wrapper {
        background-color: #212529;
        color: #ffffff;
    }
</style>

<!-- Header Section -->
<div class="container pb-4">
    <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1">Kategori Layanan</h1>
            <p class="text-muted mb-0">
                Pilih kategori untuk melihat daftar peralatan dan ruangan studio yang tersedia.
            </p>
        </div>
    </div>
</div>

<!-- Category Grid View -->
<div class="row g-4" id="view-categories">
    <?php 
    // Helper function to map category names to relevant Bootstrap Icons
    function getCategoryIcon($name) {
        $name_lower = strtolower($name);
        if (strpos($name_lower, 'foto') !== false) return 'bi-camera';
        if (strpos($name_lower, 'video') !== false) return 'bi-camera-reels';
        if (strpos($name_lower, 'podcast') !== false) return 'bi-mic';
        if (strpos($name_lower, 'studio') !== false) return 'bi-building';
        if (strpos($name_lower, 'stream') !== false) return 'bi-broadcast';
        if (strpos($name_lower, 'audio') !== false) return 'bi-music-note-beamed';
        if (strpos($name_lower, 'light') !== false) return 'bi-brightness-high';
        if (strpos($name_lower, 'drone') !== false) return 'bi-airplane';
        if (strpos($name_lower, 'komputer') !== false) return 'bi-pc-display';
        return 'bi-tools'; // Fallback
    }

    while ($data = mysqli_fetch_array($hasil)): 
        $icon = getCategoryIcon($data['nama_kategori']);
    ?>
        <div class="col-md-6 col-lg-4">
            <div class="card category-card h-100 p-4 shadow-sm">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div>
                        <!-- Category Icon -->
                        <div class="category-icon-wrapper">
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        
                        <!-- Title & Description -->
                        <h4 class="fw-bold text-dark mb-2"><?= htmlspecialchars($data['nama_kategori']) ?></h4>
                        <p class="text-muted small mb-4" style="line-height: 1.6;">
                            <?= htmlspecialchars($data['desc_kategori']) ?>
                        </p>
                    </div>

                    <!-- Action Link -->
                    <a href="../alat/index.php?kategori=<?= $data['id_kategori'] ?>" class="btn btn-outline-dark w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        Jelajahi Alat <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>            
    <?php endwhile; ?>
</div>

<?php include '../../includes/footer.php'; ?>
