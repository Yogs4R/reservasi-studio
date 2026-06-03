<?php
require_once '../../config/koneksi.php';

// Fetch overall KPI statistics
$total_reservasi_q = mysqli_query(
    $conn,
    'SELECT COUNT(*) AS total FROM reservasi',
);
$total_reservasi = mysqli_fetch_assoc($total_reservasi_q)['total'] ?? 0;

$total_revenue_q = mysqli_query(
    $conn,
    'SELECT SUM(jml_pembayaran) AS total FROM pembayaran',
);
$total_revenue = mysqli_fetch_assoc($total_revenue_q)['total'] ?? 0;

$total_alat_q = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM alat_media');
$total_alat = mysqli_fetch_assoc($total_alat_q)['total'] ?? 0;

$total_users_q = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM user');
$total_users = mysqli_fetch_assoc($total_users_q)['total'] ?? 0;

// Fetch status ketersediaan
$avail_q = mysqli_query(
    $conn,
    'SELECT status_ketersediaan, COUNT(*) AS jml FROM alat_media GROUP BY status_ketersediaan',
);
$status_counts = ['Tersedia' => 0, 'Disewa' => 0, 'Maintenance' => 0];
while ($row = mysqli_fetch_assoc($avail_q)) {
    $status = $row['status_ketersediaan'];
    if (array_key_exists($status, $status_counts)) {
        $status_counts[$status] = (int) $row['jml'];
    }
}

// Fetch kondisi alat
$cond_q = mysqli_query(
    $conn,
    'SELECT kondisi_alat, COUNT(*) AS jml FROM alat_media GROUP BY kondisi_alat',
);
$condition_counts = ['Baik' => 0, 'Perlu Perawatan' => 0];
while ($row = mysqli_fetch_assoc($cond_q)) {
    $cond = $row['kondisi_alat'];
    if (array_key_exists($cond, $condition_counts)) {
        $condition_counts[$cond] = (int) $row['jml'];
    }
}

// Fetch monthly booking/revenue trend (based on reservasi to ensure visibility of data)
$monthly_q = mysqli_query(
    $conn,
    "
    SELECT 
        DATE_FORMAT(tgl_reserv, '%b %Y') AS bulan, 
        DATE_FORMAT(tgl_reserv, '%Y-%m') AS order_key,
        SUM(harga_total) AS total_revenue,
        COUNT(id_reserv) AS count_booking
    FROM reservasi
    GROUP BY order_key
    ORDER BY order_key ASC
    LIMIT 6
",
);
$monthly_labels = [];
$monthly_revenue = [];
$monthly_bookings = [];
while ($row = mysqli_fetch_assoc($monthly_q)) {
    $monthly_labels[] = $row['bulan'];
    $monthly_revenue[] = (float) $row['total_revenue'];
    $monthly_bookings[] = (int) $row['count_booking'];
}

if (empty($monthly_labels)) {
    $monthly_labels = ['No Active Rentals'];
    $monthly_revenue = [0];
    $monthly_bookings = [0];
}

// Fetch Top 5 Most Booked Equipment
$popular_q = mysqli_query(
    $conn,
    "
    SELECT am.nama_alat, COUNT(dr.id_detail) as kali_disewa, SUM(dr.jumlah) as total_qty
    FROM detail_reservasi dr
    JOIN alat_media am ON dr.id_alat = am.id_alat
    GROUP BY dr.id_alat
    ORDER BY total_qty DESC
    LIMIT 5
",
);
$popular_labels = [];
$popular_data = [];
while ($row = mysqli_fetch_assoc($popular_q)) {
    $popular_labels[] = $row['nama_alat'];
    $popular_data[] = (int) $row['total_qty'];
}

if (empty($popular_labels)) {
    $popular_labels = ['No Rented Items Yet'];
    $popular_data = [0];
}

// Fetch 5 recent bookings
$recent_q = mysqli_query(
    $conn,
    "
    SELECT r.*, u.nama 
    FROM reservasi r 
    LEFT JOIN user u ON r.id_user = u.id_user 
    ORDER BY r.tgl_reserv DESC 
    LIMIT 5
",
);

// Database tables overview rows count
$tables = [
    'kategori',
    'alat_media',
    'user',
    'reservasi',
    'detail_reservasi',
    'pembayaran',
];
$table_sizes = [];
foreach ($tables as $t) {
    $size_q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$t`");
    $table_sizes[$t] = mysqli_fetch_assoc($size_q)['total'] ?? 0;
}

// JSON encode data for JavaScript consumption safely
$monthly_labels_json = json_encode($monthly_labels);
$monthly_revenue_json = json_encode($monthly_revenue);
$monthly_bookings_json = json_encode($monthly_bookings);
$popular_labels_json = json_encode($popular_labels);
$popular_data_json = json_encode($popular_data);
?>

<?php include '../../includes/header_admin.php'; ?>

<!-- Custom CSS for Premium UI Enhancements -->
<style>
    /* Color Palette */
    .bg-gradient-revenue {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .bg-gradient-bookings {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }
    .bg-gradient-equip {
        background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    }
    .bg-gradient-users {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .kpi-card {
        border: none !important;
        border-radius: 12px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .kpi-card .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Charts & Cards Custom spacing */
    .dashboard-card {
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        background: #ffffff;
    }
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="pt-5 pb-4">
    <!-- Header/Title Block -->
    <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1 text-dark">Admin Dashboard</h1>
            <p class="text-muted mb-0 small">
                Analytical database overview, performance metrics, and live system monitoring.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-3 py-2 fw-semibold">
                <i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i') ?>
            </span>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="row g-4">
        <!-- Left Sidebar Navigation Column -->
        <div class="col-lg-3">
            <?php include '../../includes/sidebar_admin.php'; ?>
            
            <!-- Database Size Tracker Card -->
            <div class="card shadow-sm border border-light-subtle rounded-3 p-3 mt-4 bg-white">
                <h6 class="fw-bold mb-3 text-secondary text-uppercase small"><i class="bi bi-database-fill-gear me-1"></i>Database Rows Count</h6>
                <div class="list-group list-group-flush small">
                    <?php foreach ($table_sizes as $table_name => $count): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                        <span class="text-muted text-capitalize"><i class="bi bi-table me-2 text-secondary"></i><?= htmlspecialchars(
                            $table_name,
                        ) ?></span>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><?= $count ?> rows</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right Main Dashboard Content Column -->
        <div class="col-lg-9">
            
            <!-- KPI Cards Row -->
            <div class="row g-3 mb-4">
                <!-- KPI Revenue -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card bg-gradient-revenue text-white shadow-sm h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-3.5">
                            <div>
                                <span class="d-block text-white-50 small text-uppercase fw-semibold mb-1">Total Revenue</span>
                                <h4 class="fw-bold mb-0">Rp <?= number_format(
                                    $total_revenue,
                                    0,
                                    ',',
                                    '.',
                                ) ?></h4>
                            </div>
                            <div class="icon-circle">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- KPI Bookings -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card bg-gradient-bookings text-white shadow-sm h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-3.5">
                            <div>
                                <span class="d-block text-white-50 small text-uppercase fw-semibold mb-1">Total Bookings</span>
                                <h4 class="fw-bold mb-0"><?= number_format(
                                    $total_reservasi,
                                    0,
                                    ',',
                                    '.',
                                ) ?></h4>
                            </div>
                            <div class="icon-circle">
                                <i class="bi bi-calendar2-check fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Equipment -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card bg-gradient-equip text-white shadow-sm h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-3.5">
                            <div>
                                <span class="d-block text-white-50 small text-uppercase fw-semibold mb-1">Equipments</span>
                                <h4 class="fw-bold mb-0"><?= number_format(
                                    $total_alat,
                                    0,
                                    ',',
                                    '.',
                                ) ?></h4>
                            </div>
                            <div class="icon-circle">
                                <i class="bi bi-tools fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Customers -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card kpi-card bg-gradient-users text-white shadow-sm h-100">
                        <div class="card-body d-flex align-items-center justify-content-between p-3.5">
                            <div>
                                <span class="d-block text-white-50 small text-uppercase fw-semibold mb-1">Customers</span>
                                <h4 class="fw-bold mb-0"><?= number_format(
                                    $total_users,
                                    0,
                                    ',',
                                    '.',
                                ) ?></h4>
                            </div>
                            <div class="icon-circle">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1: Revenue Monthly Trend and Equipment Status -->
            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <div class="card dashboard-card shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark m-0">Monthly Booking Trend & Value</h5>
                            <i class="bi bi-graph-up text-secondary"></i>
                        </div>
                        <div style="position: relative; height: 280px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card dashboard-card shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark m-0">Availability Status</h5>
                            <i class="bi bi-pie-chart text-secondary"></i>
                        </div>
                        <div style="position: relative; height: 180px;" class="mb-3">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="d-flex justify-content-around text-center small pt-2 border-top">
                            <div>
                                <span class="fw-semibold d-block text-success"><?= $status_counts[
                                    'Tersedia'
                                ] ?></span>
                                <span class="text-muted small">Tersedia</span>
                            </div>
                            <div>
                                <span class="fw-semibold d-block text-primary"><?= $status_counts[
                                    'Disewa'
                                ] ?></span>
                                <span class="text-muted small">Disewa</span>
                            </div>
                            <div>
                                <span class="fw-semibold d-block text-warning"><?= $status_counts[
                                    'Maintenance'
                                ] ?></span>
                                <span class="text-muted small">Maintenance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2: Popular Equipment and Physical Condition -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card dashboard-card shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark m-0">Popular Rented Items (Quantity)</h5>
                            <i class="bi bi-fire text-danger"></i>
                        </div>
                        <div style="position: relative; height: 220px;">
                            <canvas id="popularChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card dashboard-card shadow-sm p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark m-0">Physical Condition Health</h5>
                            <i class="bi bi-heart-pulse text-info"></i>
                        </div>
                        <div style="position: relative; height: 180px;" class="mb-2">
                            <canvas id="conditionChart"></canvas>
                        </div>
                        <div class="d-flex justify-content-around text-center small pt-3 border-top">
                            <div>
                                <span class="fw-semibold d-block text-success"><?= $condition_counts[
                                    'Baik'
                                ] ?></span>
                                <span class="text-muted small">Baik</span>
                            </div>
                            <div>
                                <span class="fw-semibold d-block text-danger"><?= $condition_counts[
                                    'Perlu Perawatan'
                                ] ?></span>
                                <span class="text-muted small">Perlu Perawatan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Booking Transactions Table Card -->
            <div class="card dashboard-card shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-activity me-1 text-secondary"></i>Recent Bookings</h5>
                    <a href="<?= BASE_URL ?>modules/admin/reservasi/index.php" class="btn btn-sm btn-outline-dark fw-semibold">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Customer Name</th>
                                <th>Date Scheduled</th>
                                <th>Total Price</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($recent_q) > 0): ?>
                                <?php while (
                                    $res = mysqli_fetch_assoc($recent_q)
                                ): ?>
                                    <?php
                                    $status = $res['status_reserv'];
                                    $badge_class = 'bg-secondary';
                                    if (strtolower($status) === 'pending') {
                                        $badge_class =
                                            'bg-warning-subtle text-warning';
                                    } elseif (
                                        strtolower($status) === 'disetujui' ||
                                        strtolower($status) === 'success' ||
                                        strtolower($status) === 'paid'
                                    ) {
                                        $badge_class =
                                            'bg-success-subtle text-success';
                                    } elseif (
                                        strtolower($status) === 'selesai'
                                    ) {
                                        $badge_class =
                                            'bg-info-subtle text-info';
                                    } elseif (
                                        strtolower($status) === 'dibatalkan' ||
                                        strtolower($status) === 'failed' ||
                                        strtolower($status) === 'batal'
                                    ) {
                                        $badge_class =
                                            'bg-danger-subtle text-danger';
                                    }
                                    ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold">#<?= $res[
                                            'id_reserv'
                                        ] ?></td>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars(
                                            $res['nama'] ?? 'Unknown User',
                                        ) ?></td>
                                        <td><?= date(
                                            'd M Y, H:i',
                                            strtotime($res['tgl_reserv']),
                                        ) ?></td>
                                        <td class="fw-bold text-dark-emphasis">Rp <?= number_format(
                                            $res['harga_total'],
                                            0,
                                            ',',
                                            '.',
                                        ) ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars(
                                                $res['metode_pembayaran'] ??
                                                    'Cash',
                                            ) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badge_class ?> px-2.5 py-1.5 fw-semibold"><?= htmlspecialchars(
     $status,
 ) ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= BASE_URL ?>modules/pembayaran/verifikasi.php?id_reserv=<?= $res[
    'id_reserv'
] ?>" class="btn btn-sm btn-dark">
                                                Verify Payment
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No reservations found yet in the system.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Load Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Render Charts JavaScript -->
<script>
    // 1. Monthly Revenue & Booking Trend Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    
    const monthlyLabels = <?= $monthly_labels_json ?>;
    const monthlyRevenue = <?= $monthly_revenue_json ?>;
    const monthlyBookings = <?= $monthly_bookings_json ?>;

    new Chart(revCtx, {
        data: {
            labels: monthlyLabels,
            datasets: [
                {
                    type: 'line',
                    label: 'Booking Value (IDR)',
                    data: monthlyRevenue,
                    borderColor: '#10b981',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointHoverRadius: 7,
                    tension: 0.3,
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    yAxisID: 'y'
                },
                {
                    type: 'bar',
                    label: 'Number of Bookings',
                    data: monthlyBookings,
                    backgroundColor: 'rgba(59, 130, 246, 0.65)',
                    hoverBackgroundColor: '#3b82f6',
                    borderRadius: 6,
                    barPercentage: 0.5,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Inter', size: 12 }
                    }
                },
                tooltip: {
                    padding: 12,
                    titleFont: { family: 'Inter', size: 13, weight: 'bold' },
                    bodyFont: { family: 'Inter', size: 12 }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        font: { family: 'Inter' }
                    }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: { display: false },
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Inter' }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter' } }
                }
            }
        }
    });

    // 2. Equipment Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Available (Tersedia)', 'Rented (Disewa)', 'Maintenance'],
            datasets: [{
                data: [
                    <?= $status_counts['Tersedia'] ?>,
                    <?= $status_counts['Disewa'] ?>,
                    <?= $status_counts['Maintenance'] ?>
                ],
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    padding: 10,
                    bodyFont: { family: 'Inter' }
                }
            }
        }
    });

    // 3. Popular Rented Items Chart (Horizontal Bar Chart)
    const popularCtx = document.getElementById('popularChart').getContext('2d');
    new Chart(popularCtx, {
        type: 'bar',
        data: {
            labels: <?= $popular_labels_json ?>,
            datasets: [{
                data: <?= $popular_data_json ?>,
                backgroundColor: 'rgba(139, 92, 246, 0.75)',
                hoverBackgroundColor: '#8b5cf6',
                borderRadius: 5,
                barPercentage: 0.6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { padding: 10, bodyFont: { family: 'Inter' } }
            },
            scales: {
                x: {
                    grid: { color: '#f1f5f9' },
                    ticks: { stepSize: 1, font: { family: 'Inter' } }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter' } }
                }
            }
        }
    });

    // 4. Equipment Conditions Pie Chart
    const condCtx = document.getElementById('conditionChart').getContext('2d');
    new Chart(condCtx, {
        type: 'pie',
        data: {
            labels: ['Baik', 'Perlu Perawatan'],
            datasets: [{
                data: [
                    <?= $condition_counts['Baik'] ?>,
                    <?= $condition_counts['Perlu Perawatan'] ?>
                ],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { padding: 10, bodyFont: { family: 'Inter' } }
            }
        }
    });
</script>

<?php include '../../includes/footer_admin.php'; ?>
