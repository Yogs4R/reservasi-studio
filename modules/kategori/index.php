<?php
require '../../config/koneksi.php';

// Query dasar
$query = "
	SELECT a.*, k.nama_kategori
	FROM alat_media a
	LEFT JOIN kategori k ON a.id_kategori = k.id_kategori
	WHERE 1=1
	";

// Filter search
if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query .= " AND a.nama_alat LIKE '%$search%'";
}

// Filter kategori
if (!empty($_GET['kategori'])) {
    $kategori = (int) $_GET['kategori'];
    $query .= " AND a.id_kategori = $kategori";
}

// Filter availability berdasarkan status_ketersediaan
if (!empty($_GET['availability'])) {
    if ($_GET['availability'] == 'available') {
        $query .= " AND a.status_ketersediaan = 'Tersedia'";
    } elseif ($_GET['availability'] == 'unavailable') {
        $query .= " AND a.status_ketersediaan != 'Tersedia'";
    }
}

// Filter berdasarkan kondisi
if (!empty($_GET['condition'])) {
    if ($_GET['condition'] == 'Baik') {
        $query .= " AND a.kondisi_alat = 'Baik' ";
    } elseif ($_GET['condition'] == 'Perlu Perawatan') {
        $query .= " AND a.kondisi_alat = 'Perlu Perawatan' ";
    }
}

$query .= ' ORDER BY a.id_alat';

$hasil = mysqli_query($conn, $query);
?>

<?php include '../../includes/header.php'; ?>
<style>
	.card-img-top {
		width: 100%;
		height: 200px;
		object-fit: contain;
		background: #f8f9fa;
	}
</style>


<!-- Header Section -->
<div class="container pt-5 pb-4 mt-5">
	<div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">

		<div>
			<h1 class="fw-bold mb-1">Equipment Catalog</h1>
			<p class="text-muted mb-0">
				Browse available equipment and creative assets.
			</p>
		</div>
	</div>
</div>

<!-- Search & Filters Toolbar -->
	<form method="GET" class="row g-2 mb-4">

		<div class="col-md-5">
			<input
				type="text"
				name="search"
				class="form-control"
				placeholder="Search equipment..."
				value="<?= $_GET['search'] ?? '' ?>">
		</div>

		<div class="col-md-2">
			<select name="kategori" class="form-select">
				<option value="">All Categories</option>

				<?php
    $kategori = mysqli_query($conn, 'SELECT * FROM kategori');

    while ($k = mysqli_fetch_array($kategori)) {
        $selected =
            isset($_GET['kategori']) && $_GET['kategori'] == $k['id_kategori']
                ? 'selected'
                : '';

        echo "
					<option value='{$k['id_kategori']}' $selected>
						{$k['nama_kategori']}
					</option>";
    }
    ?>
			</select>
		</div>

		<div class="col-md-2">
			<select name="availability" class="form-select">
				<option value="">Availability</option>
				<option value="available"
					<?= ($_GET['availability'] ?? '') == 'available' ? 'selected' : '' ?>>
					Available
				</option>

				<option value="unavailable"
					<?= ($_GET['availability'] ?? '') == 'unavailable' ? 'selected' : '' ?>>
					Unavailable
				</option>
			</select>
		</div>

		<div class="col-md-2">
			<select name="condition" class="form-select">
				<option value="">Condition</option>
				<option value="Baik"
					<?= ($_GET['condition'] ?? '') == 'Baik' ? 'selected' : '' ?>>
					Baik
				</option>

				<option value="Perlu Perawatan"
					<?= ($_GET['condition'] ?? '') == 'Perlu Perawatan' ? 'selected' : '' ?>>
					Perlu Perawatan
				</option>
			</select>
		</div>

		<div class="col-md-1">
			<button type="submit" class="btn btn-dark w-100">
				Filter
			</button>
		</div>

	</form>

	<!-- Card Grid View -->
	<div class="row g-3" id="view-cards">


	<!-- card -->

<?php
$no = 1;
while ($data = mysqli_fetch_array($hasil)) { ?>
        
	<div class="col-md-6 col-lg-3">
		<div class="card h-100">
			<img class="card-img-top" src="<?php echo $data['foto_alat']; ?>">

			<div class="card-body">
				<small class="text-muted"><?php echo $data['nama_kategori']; ?></small>

				<h5 class="card-title"><?php echo $data['nama_alat']; ?></h5>

				<p class="mb-1">
					<strong>Condition:</strong> <?php echo $data['kondisi_alat']; ?>
				</p>

				<p class="mb-1">
					<strong>Harga:</strong> <?php echo $data['harga']; ?>/day
				</p>

				<p class="mb-3">
					<strong>Stock:</strong> <?php echo $data['stok']; ?> Unit
				</p>

				<span class="badge <?php echo $data['status_ketersediaan'] == 'Tersedia'
        ? 'bg-success'
        : 'bg-danger'; ?> mb-3">	
					<?php echo $data['status_ketersediaan'] == 'Tersedia'
         ? 'Available'
         : 'Not Available'; ?>
				</span>

				<a href="#" class="btn btn-outline-dark w-100">
					View Details
				</a>
			</div>
		</div>
	</div>            
<?php $no++;}
?>
	</div>

<?php include '../../includes/footer.php'; ?>