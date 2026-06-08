<?php
require '../../../config/koneksi.php';

// Ambil bulan dan tahun dari parameter GET, default bulan & tahun sekarang
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Validasi bulan & tahun
if ($month < 1 || $month > 12) {
    $month = (int)date('n');
}
if ($year < 1970 || $year > 2100) {
    $year = (int)date('Y');
}

// Menghitung bulan sebelumnya dan bulan berikutnya
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Format bulan untuk query SQL
$startDate = sprintf('%04d-%02d-01', $year, $month);
$endDate = date('Y-m-t', strtotime($startDate));
$daysInMonth = (int)date('t', strtotime($startDate));
$firstDayOfMonth = (int)date('w', strtotime($startDate)); // 0 (Sun) - 6 (Sat)

// Ambil daftar semua alat/media untuk monitoring
$alatQuery = "SELECT id_alat, nama_alat, stok, kondisi_alat, status_ketersediaan FROM alat_media";
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $alatQuery .= " WHERE nama_alat LIKE '%$search%'";
}
$alatQuery .= " ORDER BY id_alat ASC";
$alatHasil = mysqli_query($conn, $alatQuery);

$list_alat = [];
while ($row = mysqli_fetch_assoc($alatHasil)) {
    $list_alat[] = $row;
}

// Ambil semua reservasi yang aktif pada bulan ini yang statusnya disetujui/booked/success/finish/paid
$reservQuery = "
    SELECT r.id_reserv, r.tgl_mulai, r.tgl_selesai, r.status_reserv, dr.id_alat, dr.jumlah, u.nama AS nama_customer
    FROM reservasi r
    JOIN detail_reservasi dr ON r.id_reserv = dr.id_reserv
    LEFT JOIN user u ON r.id_user = u.id_user
    WHERE (r.tgl_mulai <= '$endDate' AND r.tgl_selesai >= '$startDate')
      AND LOWER(r.status_reserv) NOT IN ('failed', 'batal', 'dibatalkan')
";
$reservHasil = mysqli_query($conn, $reservQuery);

// Petakan jumlah alat yang disewa per tanggal per id_alat
// Format: $usage_tracker[id_alat][tanggal_str] = [ 'total_rented' => X, 'details' => [ [reservasi_info], ... ] ]
$usage_tracker = [];
while ($row = mysqli_fetch_assoc($reservHasil)) {
    $id_alat = $row['id_alat'];
    $tgl_mulai = $row['tgl_mulai'];
    $tgl_selesai = $row['tgl_selesai'];
    $qty = (int)$row['jumlah'];
    
    // Iterasi melalui setiap tanggal reservasi
    $current = strtotime($tgl_mulai);
    $last = strtotime($tgl_selesai);
    
    while ($current <= $last) {
        $dateStr = date('Y-m-d', $current);
        // Pastikan hanya mencatat tanggal dalam bulan yang aktif
        if ($dateStr >= $startDate && $dateStr <= $endDate) {
            if (!isset($usage_tracker[$id_alat][$dateStr])) {
                $usage_tracker[$id_alat][$dateStr] = [
                    'total_rented' => 0,
                    'details' => []
                ];
            }
            $usage_tracker[$id_alat][$dateStr]['total_rented'] += $qty;
            $usage_tracker[$id_alat][$dateStr]['details'][] = [
                'id_reserv' => $row['id_reserv'],
                'nama' => $row['nama_customer'],
                'jumlah' => $qty,
                'status' => $row['status_reserv']
            ];
        }
        $current = strtotime('+1 day', $current);
    }
}

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

// Nama-nama bulan Indonesia
$monthNames = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

include '../../../includes/header_admin.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Penggunaan Alat</title>
    <style>
        .calendar-table {
            table-layout: fixed;
            width: 100%;
        }
        .calendar-table th {
            text-align: center;
            padding: 10px 0;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .calendar-table td {
            height: 130px;
            vertical-align: top;
            padding: 6px;
            border: 1px solid #e2e8f0;
            position: relative;
            background-color: #fff;
            transition: background-color 0.2s;
        }
        .calendar-table td:hover {
            background-color: #f8fafc;
        }
        .calendar-table td.empty-day {
            background-color: #f1f5f9;
        }
        .day-number {
            font-weight: bold;
            font-size: 0.9rem;
            color: #475569;
            margin-bottom: 5px;
            display: inline-block;
        }
        .day-number.today {
            background-color: #0f172a;
            color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
        }
        .monitor-list {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            max-height: 95px;
        }
        .monitor-item {
            font-size: 0.72rem;
            padding: 2px 4px;
            margin-bottom: 2px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            transition: transform 0.1s;
        }
        .monitor-item:hover {
            transform: scale(1.02);
        }
        /* Color scale for tool occupancy */
        .occupancy-none {
            background-color: #f0fdf4;
            color: #166534;
            border-left: 3px solid #22c55e;
        }
        .occupancy-partial {
            background-color: #fffbeb;
            color: #92400e;
            border-left: 3px solid #f59e0b;
        }
        .occupancy-full {
            background-color: #fef2f2;
            color: #991b1b;
            border-left: 3px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="pt-5 pb-4">
        <!-- Title bar -->
        <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1 text-dark">Tools Monitor</h1>
                <p class="text-muted mb-0 small">
                    Monitoring okupansi penggunaan alat studio per hari berdasarkan total stok barang.
                </p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-dark-subtle text-dark border border-dark-subtle px-3 py-2 fw-semibold">
                    <i class="bi bi-clock me-1"></i> <?= date('d M Y, H:i') ?>
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Sidebar Navigation Column -->
            <div class="col-lg-3">
                <?php include '../../../includes/sidebar_admin.php'; ?>
                
                <!-- Database Size Tracker Card -->
                <div class="card shadow-sm border border-light-subtle rounded-3 p-3 mt-4 bg-white">
                    <h6 class="fw-bold mb-3 text-secondary text-uppercase small"><i class="bi bi-database-fill-gear me-1"></i>Database Rows Count</h6>
                    <div class="list-group list-group-flush small">
                        <?php foreach ($table_sizes as $table_name => $count): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent py-2">
                            <span class="text-muted text-capitalize"><i class="bi bi-table me-2 text-secondary"></i><?= htmlspecialchars($table_name) ?></span>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><?= $count ?> rows</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
                    <form method="GET" class="row g-2 align-items-center">
                        <input type="hidden" name="month" value="<?= $month ?>">
                        <input type="hidden" name="year" value="<?= $year ?>">
                        <div class="col-md-9 col-sm-8">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama alat..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <button type="submit" class="btn btn-dark w-100">
                                <i class="bi bi-search me-1"></i> Cari Alat
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    <!-- Month Navigation Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold text-dark mb-0">
                            <?= $monthNames[$month] ?> <?= $year ?>
                        </h3>
                        <div class="btn-group">
                            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?><?= !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" class="btn btn-outline-dark">
                                <i class="bi bi-chevron-left"></i> Sebelumnya
                            </a>
                            <a href="?month=<?= date('n') ?>&year=<?= date('Y') ?><?= !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" class="btn btn-dark">
                                Bulan Ini
                            </a>
                            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?><?= !empty($_GET['search']) ? '&search='.urlencode($_GET['search']) : '' ?>" class="btn btn-outline-dark">
                                Selanjutnya <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="table-responsive">
                        <table class="table calendar-table m-0">
                            <thead>
                                <tr>
                                    <th class="text-danger">Minggu</th>
                                    <th>Senin</th>
                                    <th>Selasa</th>
                                    <th>Rabu</th>
                                    <th>Kamis</th>
                                    <th>Jumat</th>
                                    <th class="text-primary">Sabtu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dayCounter = 1;
                                $todayDate = date('Y-m-d');
                                
                                echo '<tr>';
                                
                                // Cetak kolom kosong sebelum tanggal 1
                                for ($i = 0; $i < $firstDayOfMonth; $i++) {
                                    echo '<td class="empty-day"></td>';
                                }
                                
                                $currentColumn = $firstDayOfMonth;
                                while ($dayCounter <= $daysInMonth) {
                                    if ($currentColumn == 7) {
                                        echo '</tr><tr>';
                                        $currentColumn = 0;
                                    }
                                    
                                    $currentDateStr = sprintf('%04d-%02d-%02d', $year, $month, $dayCounter);
                                    $isToday = ($currentDateStr === $todayDate);
                                    
                                    echo '<td>';
                                    echo '<span class="day-number ' . ($isToday ? 'today' : '') . '">' . $dayCounter . '</span>';
                                    echo '<div class="monitor-list">';
                                    
                                    // Tampilkan fraksi okupansi untuk masing-masing alat yang terdaftar/dicari
                                    foreach ($list_alat as $alat) {
                                        $id_alat = $alat['id_alat'];
                                        $stok = (int)$alat['stok'];
                                        $rented = 0;
                                        $details = [];
                                        
                                        if (isset($usage_tracker[$id_alat][$currentDateStr])) {
                                            $rented = $usage_tracker[$id_alat][$currentDateStr]['total_rented'];
                                            $details = $usage_tracker[$id_alat][$currentDateStr]['details'];
                                        }
                                        
                                        // Skip jika alat tidak disewa sama sekali hari ini (opsional untuk memperbersih layar, atau tetap tampilkan yang disewa saja)
                                        if ($rented === 0) {
                                            continue;
                                        }
                                        
                                        $class = 'occupancy-none';
                                        if ($rented > 0) {
                                            $class = ($rented >= $stok) ? 'occupancy-full' : 'occupancy-partial';
                                        }
                                        
                                        // Buat parameter objek detail untuk modal Javascript
                                        $modalPayload = [
                                            'tanggal' => $currentDateStr,
                                            'nama_alat' => $alat['nama_alat'],
                                            'stok' => $stok,
                                            'rented' => $rented,
                                            'details' => $details
                                        ];
                                        
                                        $escapedPayload = htmlspecialchars(json_encode($modalPayload), ENT_QUOTES, 'UTF-8');
                                        
                                        echo '<div class="monitor-item ' . $class . '" onclick="showMonitorDetail(' . $escapedPayload . ')" title="Klik untuk info detail penyewaan">';
                                        echo '<span class="text-truncate" style="max-width: 65%;">' . htmlspecialchars($alat['nama_alat']) . '</span>';
                                        echo '<span class="fw-bold">' . $rented . '/' . $stok . '</span>';
                                        echo '</div>';
                                    }
                                    
                                    echo '</div>';
                                    echo '</td>';
                                    
                                    $dayCounter++;
                                    $currentColumn++;
                                }
                                
                                // Cetak kolom kosong setelah akhir bulan
                                while ($currentColumn < 7) {
                                    echo '<td class="empty-day"></td>';
                                    $currentColumn++;
                                }
                                
                                echo '</tr>';
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitor Detail Modal -->
    <div class="modal fade" id="monitorModal" tabindex="-1" aria-labelledby="monitorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="monitorModalLabel">Detail Penggunaan Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Nama Alat</label>
                        <span class="fw-bold text-dark fs-5" id="modalNamaAlat">-</span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small d-block">Tanggal Monitoring</label>
                            <span class="fw-semibold text-dark" id="modalTanggal">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Rasio Ketersediaan (Disewa / Total)</label>
                            <span class="fw-bold text-danger" id="modalRasio">-</span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="text-muted small d-block mb-2 fw-semibold">Daftar Reservasi Aktif Hari Ini</label>
                        <div class="list-group" id="modalReservList">
                            <!-- Items list will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMonitorDetail(payload) {
            document.getElementById('modalNamaAlat').innerText = payload.nama_alat;
            
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const dateFormatted = new Date(payload.tanggal).toLocaleDateString('id-ID', options);
            document.getElementById('modalTanggal').innerText = dateFormatted;
            
            document.getElementById('modalRasio').innerText = payload.rented + ' / ' + payload.stok;
            
            const reservListEl = document.getElementById('modalReservList');
            reservListEl.innerHTML = '';
            
            if (payload.details && payload.details.length > 0) {
                payload.details.forEach(item => {
                    const itemHtml = `
                        <div class="list-group-item d-flex align-items-center justify-content-between py-2.5">
                            <div>
                                <h6 class="mb-0 fw-bold small text-dark">${item.nama}</h6>
                                <small class="text-muted">Jumlah disewa: ${item.jumlah} unit</small>
                            </div>
                            <a href="<?= BASE_URL ?>modules/pembayaran/verifikasi.php?id_reserv=${item.id_reserv}" class="btn btn-sm btn-outline-dark py-1 px-2.5 small">
                                Reservasi #${item.id_reserv}
                            </a>
                        </div>
                    `;
                    reservListEl.insertAdjacentHTML('beforeend', itemHtml);
                });
            } else {
                reservListEl.innerHTML = '<div class="text-center py-2 text-muted small">Tidak ada reservasi aktif pada tanggal ini</div>';
            }
            
            const modal = new bootstrap.Modal(document.getElementById('monitorModal'));
            modal.show();
        }
    </script>
</body>
</html>
<?php include '../../../includes/footer_admin.php'; ?>