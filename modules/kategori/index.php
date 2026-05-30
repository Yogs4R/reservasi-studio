<?php
include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
	<div>
		<h1 class="display-4 mb-2">Equipment Catalog</h1>
		<p class="lead text-muted">Browse available equipment and creative assets.</p>
	</div>
	<div>
		<div class="btn-group" role="group">
			<button type="button" class="btn btn-dark active" id="btn-card-view">Cards View</button>
			<button type="button" class="btn btn-outline-dark" id="btn-table-view">Table View</button>
		</div>
	</div>
</div>

<div class="row mb-4">
	<div class="col-md-6 mb-2 mb-md-0">
		<input type="text" class="form-control" placeholder="Search equipment...">
	</div>
	<div class="col-md-2 mb-2 mb-md-0">
		<select class="form-control">
			<option>Category: All</option>
			<option>Cameras</option>
			<option>Lenses</option>
			<option>Lighting</option>
			<option>Audio</option>
		</select>
	</div>
	<div class="col-md-2 mb-2 mb-md-0">
		<select class="form-control">
			<option>Availability</option>
			<option>Available Now</option>
			<option>Currently Rented</option>
		</select>
	</div>
	<div class="col-md-2">
		<select class="form-control">
			<option>Condition</option>
			<option>Excellent</option>
			<option>Good</option>
			<option>Fair</option>
		</select>
	</div>
</div>

<!-- Card Grid View -->
<div class="row" id="view-cards">
	<div class="col-md-3 mb-4">
		<div class="card h-100 border-dark">
			<div class="position-relative" style="height: 220px; background: #eee;">
				<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAufCn68Y_z-PH7U56GrjY-VClg-dvKNbFYrbUbLf7buVDplvgNm6-giwGH0njpUJZ7GI7Ba6wKGqSyqfLHroxcYDxsQurEywge2FhbPjyGrxTHv7DoLbPsvZw3U39ltR_RCwCjw5Kcl4c8HnBySOBGdhZdPBEJjoKwg6Cm9xqfjO4oU2edQw61CsrQsDlyPF_ugdaA2Ojc2QbHM9pICExnlliTRyKX_aTtawdSwLY0d41KwsLXC051ytQrZASOE_xjS6Mh1QtPiLU" class="card-img-top h-100 object-fit-cover" alt="SONY A7 IV">
				<span class="badge badge-dark position-absolute" style="top: 10px; right: 10px;">AVAILABLE</span>
			</div>
			<div class="card-body d-flex flex-column">
				<small class="text-muted text-uppercase">Cameras</small>
				<h5 class="card-title mt-1 mb-3">SONY A7 IV</h5>
				<div class="mt-auto">
					<div class="d-flex justify-content-between">
						<span class="text-muted">Condition</span>
						<span class="font-weight-bold">Excellent</span>
					</div>
					<div class="d-flex justify-content-between mt-2">
						<span class="text-muted">Daily Rate</span>
						<span class="h5 mb-0">Rp250.000</span>
					</div>
				</div>
				<a href="#" class="btn btn-outline-dark btn-block mt-4">View Details</a>
			</div>
		</div>
	</div>
	<div class="col-md-3 mb-4">
		<div class="card h-100 border-dark">
			<div class="position-relative" style="height: 220px; background: #eee;">
				<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCenQaUWECRBtxl0TuZ2Eb3wTZ2xJW92IXOgW9fVJho2KqKEJillPgcJg8_V_EEwnwDsrv1QLjfUz-G93QU9lLpLPFR5ZKlPU1zBXVPlG8SO7nc48wphj7IjTdMfJWmPpKacqhzkvoZw3EM1aTFdvYlmz0ieFg_xq3VMsbz_rZolUuLw6PC5oNMfRCigTFCDHu_gj-2PGKr2wIQR8uUsgXefEA_N_CZq4tEIDF_pSOlWYPMhNAyw2S7fo5x_PjV0YLbYiPAtQQGH9Y" class="card-img-top h-100 object-fit-cover" alt="SIGMA 24-70MM F/2.8 ART">
				<span class="badge badge-secondary position-absolute" style="top: 10px; right: 10px;">RENTED</span>
			</div>
			<div class="card-body d-flex flex-column">
				<small class="text-muted text-uppercase">Lenses</small>
				<h5 class="card-title mt-1 mb-3">SIGMA 24-70MM F/2.8 ART</h5>
				<div class="mt-auto">
					<div class="d-flex justify-content-between">
						<span class="text-muted">Condition</span>
						<span class="font-weight-bold">Good</span>
					</div>
					<div class="d-flex justify-content-between mt-2">
						<span class="text-muted">Daily Rate</span>
						<span class="h5 mb-0">Rp150.000</span>
					</div>
				</div>
				<a href="#" class="btn btn-outline-dark btn-block mt-4">View Details</a>
			</div>
		</div>
	</div>
	<div class="col-md-3 mb-4">
		<div class="card h-100 border-dark">
			<div class="position-relative" style="height: 220px; background: #eee;">
				<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuD3nccSK3RsH7ZgzpTWxWptH8XXcsHRsW5TpdC6LHuXTdLpTi0Q3sUTanQPJos4fyBekkoW_bgzyujCkhCUh-P-8dO8ppF11a0_EEUpZf-JFhlIyJV8XvOvOj6f0v1d-sko11LhuHb5up3mBV2M1oHVXDBXZ5zZarN59k1sHlRvEZKr5tHkvK2z2Ev-2kLxHKGxT8XZlLmjHbKt2He4bAfwaCiYd4cffY63vKKXVpJyUzMcXavUhbK84uebcDOxth0EfzZrra819v0" class="card-img-top h-100 object-fit-cover" alt="APUTURE LS 300D II">
				<span class="badge badge-dark position-absolute" style="top: 10px; right: 10px;">AVAILABLE</span>
			</div>
			<div class="card-body d-flex flex-column">
				<small class="text-muted text-uppercase">Lighting</small>
				<h5 class="card-title mt-1 mb-3">APUTURE LS 300D II</h5>
				<div class="mt-auto">
					<div class="d-flex justify-content-between">
						<span class="text-muted">Condition</span>
						<span class="font-weight-bold">Excellent</span>
					</div>
					<div class="d-flex justify-content-between mt-2">
						<span class="text-muted">Daily Rate</span>
						<span class="h5 mb-0">Rp350.000</span>
					</div>
				</div>
				<a href="#" class="btn btn-outline-dark btn-block mt-4">View Details</a>
			</div>
		</div>
	</div>
	<div class="col-md-3 mb-4">
		<div class="card h-100 border-dark">
			<div class="position-relative" style="height: 220px; background: #eee;">
				<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDSSMmcrZtL5BQj8u7uIBJ_eWVBgR3rbuizlvq_T9ekZmE_VVw_UiBCPdPc235JGVnNaYzR7lXURELv7bHuHKNL-FxsUFOfngG9NBMC-TaK2ovHhX-P0Le32JB_4enP6sdaGyef-XmWuyPhOoiKRov9tLOA2BlbQOeeNTlwbFusYORhTyv76RW4DwjBGzRxYqdxmVzL0cvLo0i_K0TuZX-ylz8Po37pMjti0FgoARW5dy38aCTTPcBCDXa5UUNxMm5MSZEtKj6uq_w" class="card-img-top h-100 object-fit-cover" alt="RODE NTG4+">
				<span class="badge badge-dark position-absolute" style="top: 10px; right: 10px;">AVAILABLE</span>
			</div>
			<div class="card-body d-flex flex-column">
				<small class="text-muted text-uppercase">Audio</small>
				<h5 class="card-title mt-1 mb-3">RODE NTG4+</h5>
				<div class="mt-auto">
					<div class="d-flex justify-content-between">
						<span class="text-muted">Condition</span>
						<span class="font-weight-bold">Fair</span>
					</div>
					<div class="d-flex justify-content-between mt-2">
						<span class="text-muted">Daily Rate</span>
						<span class="h5 mb-0">Rp75.000</span>
					</div>
				</div>
				<a href="#" class="btn btn-outline-dark btn-block mt-4">View Details</a>
			</div>
		</div>
	</div>
</div>

<!-- Table View (hidden by default) -->
<div class="d-none" id="view-table">
	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead class="thead-dark">
				<tr>
					<th>Item</th>
					<th>Category</th>
					<th>Condition</th>
					<th>Status</th>
					<th class="text-right">Daily Rate</th>
					<th class="text-right">Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<div class="d-flex align-items-center">
							<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfOwJ42xnj5uqzQrMZ6WAwRgRK7A00rPHc_xuOUoSP8oUVng4o9vDYcqG95T-zDWBAj9N_gZSiiwzvFmtn6N5BhnhER6OP75NptJT3J9RH_HN2zrfx5Oy8IZWTll2RTU9STtCgDRdnzMt9aS8euIqAdvjNtzx1HwcW1OHRRQImmAVNaV0uSL859MuDo8cXFe6ZWiZYGVD8FUNyNhEYbgFV_bqxF0vEY7QcNuasF_WCrmaDWpuem40klV2INbiGuuVmSxUd0KDm5PE" alt="SONY A7 IV" class="rounded mr-2" style="width:48px;height:48px;object-fit:cover;">
							<span class="font-weight-bold">SONY A7 IV</span>
						</div>
					</td>
					<td>Cameras</td>
					<td>Excellent</td>
					<td><span class="badge badge-dark">AVAILABLE</span></td>
					<td class="text-right">Rp250.000</td>
					<td class="text-right"><a href="#" class="btn btn-outline-dark btn-sm">View</a></td>
				</tr>
				<tr>
					<td>
						<div class="d-flex align-items-center">
							<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBviCgtGErbFjSauS8eOx-dEGHDovcqxF0fzN_9x9w3muzWd7lDkfw6LAYl49w8PUvIKvL7dmPm37bxfwldvF9P8UjxuMdhjAaeWH4JXTRz3JZFNluISDAyTAVkkzHUdaiccmlnD56jShJncgR4eVDfUYElR56nTeNR0BntnLM4Iv3f7AxKSSaRw8lNzw1TDJIIwOJeOmMw0KtBxuUsE10_9uHo1t4jHKCY6uO33XVO4sBUWVCdwuGF5Fmi3daRwvEhmHCK-1sgKY0" alt="SIGMA 24-70MM F/2.8" class="rounded mr-2" style="width:48px;height:48px;object-fit:cover;">
							<span class="font-weight-bold">SIGMA 24-70MM F/2.8</span>
						</div>
					</td>
					<td>Lenses</td>
					<td>Good</td>
					<td><span class="badge badge-secondary">RENTED</span></td>
					<td class="text-right">Rp150.000</td>
					<td class="text-right"><a href="#" class="btn btn-outline-dark btn-sm">View</a></td>
				</tr>
				<tr>
					<td>
						<div class="d-flex align-items-center">
							<img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCDl92x-Q1MjdBNAdncgAcYu2boQLyInRWpm6eVWTR_p27_g_ELL55f6_wgSgRicPgG0fbYTxKGHUcx0opvNABs-U1DyqxnwlNkGeaVKXO-1J5NlL8QCrckd37CNWlZBvvBLTBS9OfJvoxBXFlerE6TcfwzICnbT5WCGuVW5C9lpVvgN6qg7Tf5Wl74kYE9qEyzczLFXKOKZI3VAI8vAWaBa6jNhdM4aPiQIYgxm9xBFoAiW5mXUakEdf40sedEkMuiSQdF21uN_30" alt="APUTURE LS 300D II" class="rounded mr-2" style="width:48px;height:48px;object-fit:cover;">
							<span class="font-weight-bold">APUTURE LS 300D II</span>
						</div>
					</td>
					<td>Lighting</td>
					<td>Excellent</td>
					<td><span class="badge badge-dark">AVAILABLE</span></td>
					<td class="text-right">Rp350.000</td>
					<td class="text-right"><a href="#" class="btn btn-outline-dark btn-sm">View</a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation example" class="mt-4">
	<ul class="pagination justify-content-center">
		<li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
		<li class="page-item active"><a class="page-link" href="#">1</a></li>
		<li class="page-item"><a class="page-link" href="#">2</a></li>
		<li class="page-item"><a class="page-link" href="#">3</a></li>
		<li class="page-item disabled"><a class="page-link" href="#">...</a></li>
		<li class="page-item"><a class="page-link" href="#">Next</a></li>
	</ul>
</nav>

<script>
	document.getElementById('btn-card-view').addEventListener('click', function() {
		document.getElementById('view-cards').classList.remove('d-none');
		document.getElementById('view-table').classList.add('d-none');
		this.classList.add('active');
		document.getElementById('btn-table-view').classList.remove('active');
		document.getElementById('btn-table-view').classList.add('btn-outline-dark');
		this.classList.remove('btn-outline-dark');
	});
	document.getElementById('btn-table-view').addEventListener('click', function() {
		document.getElementById('view-cards').classList.add('d-none');
		document.getElementById('view-table').classList.remove('d-none');
		this.classList.add('active');
		document.getElementById('btn-card-view').classList.remove('active');
		document.getElementById('btn-card-view').classList.add('btn-outline-dark');
		this.classList.remove('btn-outline-dark');
	});
</script>

<?php include '../../includes/footer.php'; ?>