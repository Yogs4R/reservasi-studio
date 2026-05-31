<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>StudioHub - Equipment Catalog</title>
	<!-- Material Symbols -->
	<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

	<style>
		.card-img-top {
			width: 100%;
			height: 200px;
			object-fit: contain;
			background: #f8f9fa;
		}
	</style>
</head>
<body class="bg-light">

<?php include '../../includes/header.php'; ?>

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
	<!-- Search & Filters Toolbar -->
	<div class="row g-2 mb-4">

    <div class="col-md-6">
        <input
            type="text"
            class="form-control"
            placeholder="Search equipment...">
    </div>

    <div class="col-md-2">
        <select class="form-select">
            <option>All Categories</option>
            <option>Cameras</option>
            <option>Lenses</option>
            <option>Lighting</option>
            <option>Audio</option>
        </select>
    </div>

    <div class="col-md-2">
        <select class="form-select">
            <option>Availability</option>
            <option>Available Now</option>
            <option>Currently Rented</option>
        </select>
    </div>

    <div class="col-md-2">
        <select class="form-select">
            <option>Condition</option>
            <option>Excellent</option>
            <option>Good</option>
            <option>Fair</option>
        </select>
    </div>

</div>

	<!-- Card Grid View -->
	<div class="row g-3" id="view-cards">
		<!-- card -->
		<?php
            require '../../config/koneksi.php';
            $hasil = mysqli_query($conn, "SELECT alat_media.*, kategori.nama_kategori FROM alat_media JOIN kategori ON alat_media.id_kategori = kategori.id_kategori ORDER BY alat_media.id_alat");
                    
            $no = 1;
            while($data = mysqli_fetch_array($hasil)) { ?>
        
			<div class="col-md-6 col-lg-3">
			<div class="card h-100">
				<img class="card-img-top" src="<?php echo $data['foto_alat'] ?>">

				<div class="card-body">
					<small class="text-muted"><?php echo $data['nama_kategori']?></small>

					<h5 class="card-title"><?php echo $data['nama_alat']?></h5>

					<p class="mb-1">
						<strong>Condition:</strong> <?php echo $data['kondisi_alat']?>
					</p>

					<p class="mb-1">
						<strong>Harga:</strong> <?php echo $data['harga']?>/day
					</p>

					<p class="mb-3">
						<strong>Stock:</strong> <?php echo $data['stok']?> Unit
					</p>

					<span class="badge <?php echo ($data['status_ketersediaan'] == 'Tersedia') ? 'bg-success' : 'bg-danger'; ?> mb-3">
						<?php echo ($data['status_ketersediaan'] == 'Tersedia') ? 'Available' : 'Not Available'; ?>
					</span>

					<a href="#" class="btn btn-outline-dark w-100">
						View Details
					</a>
				</div>
			</div>
		</div>            
            <?php 
			$no++;
			}
        ?>
	</div>

</div>

<?php include '../../includes/footer.php'; ?>

</body>
</html>