<?php
require '../../../config/koneksi.php';

// Ambil bulan dan tahun dari parameter GET, default bulan & tahun sekarang
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Validasi bulan
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

// Mengambil reservasi pada bulan ini beserta data user (customer) dan item yang direservasi
$query = "
    SELECT r.*, u.nama, u.email, u.no_hp
    FROM reservasi r
    LEFT JOIN user u ON r.id_user = u.id_user
    WHERE (r.tgl_mulai <= '$endDate' AND r.tgl_selesai >= '$startDate')
    ORDER BY r.tgl_mulai ASC
";
$hasil = mysqli_query($conn, $query);

$reservations = [];
while ($row = mysqli_fetch_assoc($hasil)) {
    // Ambil detail alat yang disewa untuk reservasi ini
    $id_reserv = $row['id_reserv'];
    $detailQuery = "
        SELECT dr.*, am.nama_alat, am.foto_alat
        FROM detail_reservasi dr
        JOIN alat_media am ON dr.id_alat = am.id_alat
        WHERE dr.id_reserv = '$id_reserv'
    ";
    $detailHasil = mysqli_query($conn, $detailQuery);
    $items = [];
    while ($detailRow = mysqli_fetch_assoc($detailHasil)) {
        $items[] = $detailRow;
    }
    $row['items'] = $items;
    $reservations[] = $row;
}

// Menghitung detail kalender
$firstDayOfMonth = date('w', strtotime($startDate)); // 0 (Sun) - 6 (Sat)
$daysInMonth = date('t', strtotime($startDate));

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
    <title>Kalender Reservasi Studio</title>
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
            height: 120px;
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
        .event-list {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            max-height: 85px;
        }
        .event-item {
            font-size: 0.75rem;
            padding: 3px 6px;
            margin-bottom: 3px;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
            transition: transform 0.1s;
        }
        .event-item:hover {
            transform: scale(1.02);
        }
        /* Event Status Colors */
        .event-pending {
            background-color: #fef3c7;
            color: #d97706;
            border-left: 3px solid #f59e0b;
        }
        .event-booked {
            background-color: #dbeafe;
            color: #1d4ed8;
            border-left: 3px solid #3b82f6;
        }
        .event-success {
            background-color: #dcfce7;
            color: #15803d;
            border-left: 3px solid #22c55e;
        }
        .event-failed {
            background-color: #fee2e2;
            color: #b91c1c;
            border-left: 3px solid #ef4444;
        }
        .event-finish {
            background-color: #f3e8ff;
            color: #6b21a8;
            border-left: 3px solid #a855f7;
        }
    </style>
</head>
<body>
    <div class="pt-5 pb-4">
        <!-- Title bar -->
        <div class="p-4 bg-white border border-light shadow-sm rounded-4 mb-4 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold mb-1 text-dark">Kalender Reservasi</h1>
                <p class="text-muted mb-0 small">
                    Visualisasi jadwal reservasi studio dan persewaan alat per bulan secara interaktif.
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

            <!-- Main Calendar Content -->
            <div class="col-lg-9">
                <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
                    
                    <!-- Month Navigation Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold text-dark mb-0">
                            <?= $monthNames[$month] ?> <?= $year ?>
                        </h3>
                        <div class="btn-group">
                            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn btn-outline-dark">
                                <i class="bi bi-chevron-left"></i> Sebelumnya
                            </a>
                            <a href="?month=<?= date('n') ?>&year=<?= date('Y') ?>" class="btn btn-dark">
                                Bulan Ini
                            </a>
                            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn btn-outline-dark">
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
                                
                                // Mulai baris kalender
                                echo '<tr>';
                                
                                // Cetak kolom kosong sebelum tanggal 1
                                for ($i = 0; $i < $firstDayOfMonth; $i++) {
                                    echo '<td class="empty-day"></td>';
                                }
                                
                                // Cetak tanggal-tanggal dalam bulan
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
                                    
                                    // Temukan semua event / reservasi yang melewati hari ini
                                    echo '<div class="event-list">';
                                    foreach ($reservations as $reserv) {
                                        $startDate = $reserv['tgl_mulai'];
                                        $endDate = $reserv['tgl_selesai'];
                                        
                                        if ($currentDateStr >= $startDate && $currentDateStr <= $endDate) {
                                            $status = strtolower($reserv['status_reserv']);
                                            $class = 'event-pending';
                                            if ($status === 'booked') {
                                                $class = 'event-booked';
                                            } elseif ($status === 'success' || $status === 'paid' || $status === 'disetujui') {
                                                $class = 'event-success';
                                            } elseif ($status === 'failed' || $status === 'batal' || $status === 'dibatalkan') {
                                                $class = 'event-failed';
                                            } elseif ($status === 'selesai' || $status === 'finish') {
                                                $class = 'event-finish';
                                            }
                                            
                                            // Ambil nama item pertama yang disewa
                                            $itemName = 'Reservasi';
                                            if (!empty($reserv['items'])) {
                                                $itemName = $reserv['items'][0]['nama_alat'];
                                                if (count($reserv['items']) > 1) {
                                                    $itemName .= ' (+' . (count($reserv['items']) - 1) . ')';
                                                }
                                            }
                                            
                                            $escapedReserv = htmlspecialchars(json_encode($reserv), ENT_QUOTES, 'UTF-8');
                                            echo '<div class="event-item ' . $class . '" onclick="showDetail(' . $escapedReserv . ')" title="' . htmlspecialchars($reserv['nama'] . ' - ' . $itemName) . '">';
                                            echo '<strong>' . htmlspecialchars($reserv['nama']) . '</strong>: ' . htmlspecialchars($itemName);
                                            echo '</div>';
                                        }
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

    <!-- Booking Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Reservasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Nama Pelanggan</label>
                        <span class="fw-bold text-dark fs-5" id="detailNama">-</span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small d-block">Email</label>
                            <span class="fw-semibold text-dark" id="detailEmail">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">No. HP</label>
                            <span class="fw-semibold text-dark" id="detailNoHp">-</span>
                        </div>
                    </div>
                    <hr class="text-muted">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small d-block">Tanggal Mulai</label>
                            <span class="fw-semibold text-dark" id="detailMulai">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Tanggal Selesai</label>
                            <span class="fw-semibold text-dark" id="detailSelesai">-</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small d-block">Status Reservasi</label>
                            <span class="badge py-1.5 px-2.5 fw-semibold" id="detailStatus">-</span>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small d-block">Metode Pembayaran</label>
                            <span class="badge bg-light text-dark border fw-semibold" id="detailMetode">-</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small d-block">Total Biaya</label>
                        <span class="fw-bold text-dark-emphasis fs-5" id="detailHargaTotal">-</span>
                    </div>
                    
                    <div class="mt-4">
                        <label class="text-muted small d-block mb-2 fw-semibold">Item Studio / Alat yang Disewa</label>
                        <div class="list-group" id="detailItemsList">
                            <!-- Items list will be loaded here dynamically -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <a href="#" class="btn btn-dark w-100" id="detailVerifikasiBtn">Verifikasi Pembayaran / Detail</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(reserv) {
            document.getElementById('detailNama').innerText = reserv.nama || 'Tidak diketahui';
            document.getElementById('detailEmail').innerText = reserv.email || '-';
            document.getElementById('detailNoHp').innerText = reserv.no_hp || '-';
            
            // Format Dates
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const dateMulai = new Date(reserv.tgl_mulai).toLocaleDateString('id-ID', options);
            const dateSelesai = new Date(reserv.tgl_selesai).toLocaleDateString('id-ID', options);
            
            document.getElementById('detailMulai').innerText = dateMulai;
            document.getElementById('detailSelesai').innerText = dateSelesai;
            
            // Format Price
            const formattedPrice = 'Rp ' + parseInt(reserv.harga_total).toLocaleString('id-ID');
            document.getElementById('detailHargaTotal').innerText = formattedPrice;
            
            document.getElementById('detailMetode').innerText = reserv.metode_pembayaran || 'Cash';
            
            // Status Badge
            const statusEl = document.getElementById('detailStatus');
            statusEl.innerText = reserv.status_reserv;
            statusEl.className = 'badge py-1.5 px-2.5 fw-semibold';
            
            const status = (reserv.status_reserv || '').toLowerCase();
            if (status === 'pending') {
                statusEl.classList.add('bg-warning-subtle', 'text-warning');
            } else if (status === 'booked') {
                statusEl.classList.add('bg-primary-subtle', 'text-primary');
            } else if (status === 'success' || status === 'paid' || status === 'disetujui') {
                statusEl.classList.add('bg-success-subtle', 'text-success');
            } else if (status === 'failed' || status === 'batal' || status === 'dibatalkan') {
                statusEl.classList.add('bg-danger-subtle', 'text-danger');
            } else if (status === 'selesai' || status === 'finish') {
                statusEl.classList.add('bg-info-subtle', 'text-info');
            } else {
                statusEl.classList.add('bg-secondary-subtle', 'text-secondary');
            }
            
            // Dynamic Items List
            const itemsListEl = document.getElementById('detailItemsList');
            itemsListEl.innerHTML = '';
            
            if (reserv.items && reserv.items.length > 0) {
                reserv.items.forEach(item => {
                    const priceFormatted = 'Rp ' + parseInt(item.harga_satuan).toLocaleString('id-ID');
                    const subtotalFormatted = 'Rp ' + parseInt(item.subtotal).toLocaleString('id-ID');
                    const itemHtml = `
                        <div class="list-group-item d-flex align-items-center justify-content-between py-2.5">
                            <div>
                                <h6 class="mb-0 fw-bold small text-dark">${item.nama_alat}</h6>
                                <small class="text-muted">${item.jumlah} pcs x ${priceFormatted}</small>
                            </div>
                            <span class="fw-bold small text-dark">${subtotalFormatted}</span>
                        </div>
                    `;
                    itemsListEl.insertAdjacentHTML('beforeend', itemHtml);
                });
            } else {
                itemsListEl.innerHTML = '<div class="text-center py-2 text-muted small">Tidak ada item tercatat</div>';
            }
            
            // Set verification redirect link
            const verifyBtn = document.getElementById('detailVerifikasiBtn');
            verifyBtn.href = '<?= BASE_URL ?>modules/pembayaran/verifikasi.php?id_reserv=' + reserv.id_reserv;
            
            // Trigger Bootstrap Modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }
    </script>
</body>
</html>
<?php include '../../../includes/footer_admin.php'; ?>
