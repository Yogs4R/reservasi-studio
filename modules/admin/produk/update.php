<?php
require '../../../config/koneksi.php';
$targetDir = '../../../assets/img/uploads/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$id_alat = isset($_GET['id_alat']) ? (int) $_GET['id_alat'] : 0;
if ($id_alat > 0) {
    $query = "SELECT * FROM alat_media WHERE id_alat=$id_alat";
    $hasil = mysqli_query($conn, $query);
    $dataAwal = mysqli_fetch_array($hasil);
} else {
    $dataAwal = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil id_alat dari hidden input POST jika ada, atau dari GET
    $id_alat = isset($_POST['id_alat'])
        ? (int) $_POST['id_alat']
        : (isset($_GET['id_alat'])
            ? (int) $_GET['id_alat']
            : 0);

    if ($id_alat <= 0) {
        die('ID Produk tidak valid!');
    }

    $nama_produk = $_POST['nama_produk'];
    $desc_produk = $_POST['desc_produk'];
    $kategori_produk = $_POST['kategori_produk'];
    $harga_produk = $_POST['harga_produk'];
    $stok_produk = $_POST['stok_produk'];
    $kondisi_produk = $_POST['kondisi_produk'];

    $fotoPath = isset($dataAwal['foto_alat']) ? $dataAwal['foto_alat'] : '';

    // Jika ada file foto baru yang diupload
    if (!empty($_FILES['foto_produk']['name'])) {
        $file = $_FILES['foto_produk'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        do {
            $randomName = uniqid('foto_');
            $fileName = "{$randomName}.{$ext}";
            $targetPath = $targetDir . $fileName;
        } while (file_exists($targetPath));

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($ext), $allowedExt)) {
            die('Format Foto ditolak!!!!!!!!');
        } else {
            // Hapus foto lama jika ada dan bukan default
            if (
                isset($dataAwal['foto_alat']) &&
                !empty($dataAwal['foto_alat']) &&
                file_exists('../../../' . $dataAwal['foto_alat'])
            ) {
                unlink('../../../' . $dataAwal['foto_alat']);
            }
            move_uploaded_file($file['tmp_name'], $targetPath);
            $fotoPath = '../../assets/img/uploads/' . $fileName;
        }
    }

    $ketersediaan_produk = $_POST['ketersediaan_produk'];

    $query = "UPDATE alat_media SET nama_alat='$nama_produk', desc_alat='$desc_produk', id_kategori='$kategori_produk', harga='$harga_produk', stok='$stok_produk', kondisi_alat='$kondisi_produk', foto_alat='$fotoPath', status_ketersediaan='$ketersediaan_produk' WHERE id_alat=$id_alat";
    $hasil = mysqli_query($conn, $query);

    if ($hasil) {
        header('location: index.php');
    } else {
        echo 'Data gagal diupdate';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include '../../../includes/header.php'; ?>
        <div class="container pb-4 mt-5">
            <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
                <div>
                    <h1 class="fw-bold mb-1">Tambah Item</h1>
                    <p class="text-muted mb-0">
                        Browse available equipment and creative assets.
                    </p>
                </div>
            </div>
        </div>
        <div class="g-2 mb-4" style="margin-left: 250px; margin-right: 20px;">
            <form method="POST" action="<?php echo $_SERVER[
                'PHP_SELF'
            ]; ?>" enctype="multipart/form-data">
                <!-- Hidden input untuk id_alat -->
                <input type="hidden" name="id_alat" value="<?= htmlspecialchars(
                    $id_alat,
                ) ?>">
                <div class="col-md-8 mb-4">
                    <label for="nama_produk" class="form-label">Masukkan Nama Produk :</label>
                    <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk" required value="<?= isset(
                        $dataAwal['nama_alat'],
                    )
                        ? htmlspecialchars($dataAwal['nama_alat'])
                        : '' ?>">
                </div>
                <div class="col-md-8 mb-4">
                    <label for="desc_produk" class="form-label">Masukkan Deskripsi Produk :</label>
                    <input type="text" name="desc_produk" class="form-control" placeholder="Deskripsi Produk" required value="<?= isset(
                        $dataAwal['desc_alat'],
                    )
                        ? htmlspecialchars($dataAwal['desc_alat'])
                        : '' ?>">
                </div>
                <div class="col-md-8 mb-4">
                    <label for="kategori_produk" class="form-label">Masukkan Kategori Produk :</label>
                    <select name="kategori_produk" class="form-select" required>
                        <option value="">Pilih Kategori</option>
                        <?php
                        $hasil = mysqli_query(
                            $conn,
                            'SELECT * FROM kategori ORDER BY id_kategori;',
                        );
                        $no = 1;
                        while ($data = mysqli_fetch_array($hasil)) { ?>
                            <option value="<?= $data[
                                'id_kategori'
                            ] ?>" title="<?= $data[
    'desc_kategori'
] ?>" <?= isset($dataAwal['id_kategori']) &&
$dataAwal['id_kategori'] == $data['id_kategori']
    ? 'selected'
    : '' ?>><?= $data['nama_kategori'] ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="harga_produk" class="form-label">Masukkan Harga Produk :</label>
                    <input type="text" name="harga_produk" class="form-control" placeholder="Harga Produk" required value="<?= isset(
                        $dataAwal['harga'],
                    )
                        ? htmlspecialchars($dataAwal['harga'])
                        : '' ?>">
                </div>
                <div class="col-md-8 mb-4">
                    <label for="stok_produk" class="form-label">Masukkan Stok Produk :</label>
                    <input type="text" name="stok_produk" class="form-control" placeholder="Stok Produk" required value="<?= isset(
                        $dataAwal['stok'],
                    )
                        ? htmlspecialchars($dataAwal['stok'])
                        : '' ?>">
                </div>
                <div class="col-md-8 mb-4">
                    <label for="kondisi_produk" class="form-label">Masukkan Kondisi Produk :</label>
                    <select name="kondisi_produk" class="form-select" required>
                        <option value="">Kondisi Produk</option>
                        <option value="Baik" <?= isset(
                            $dataAwal['kondisi_alat'],
                        ) && $dataAwal['kondisi_alat'] == 'Baik'
                            ? 'selected'
                            : ' ' ?>>Baik</option>
                        <option value="Perlu Perawatan" <?= isset(
                            $dataAwal['kondisi_alat'],
                        ) && $dataAwal['kondisi_alat'] == 'Perlu Perawatan'
                            ? 'selected'
                            : ' ' ?>>Perlu Perawatan</option>
                    </select>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="foto_produk" class="form-label">Masukkan Foto Produk :</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-text text-muted mb-2 d-block">Foto Lama:</label>
                            <div id="fotoLamaContainer" class="mb-3 border rounded p-2" style="background-color: #f8f9fa;">
                                <?php if (
                                    isset($dataAwal['foto_alat']) &&
                                    !empty($dataAwal['foto_alat'])
                                ): ?>
                                    <img id="fotoLamaPreview" src="../<?= htmlspecialchars(
                                        $dataAwal['foto_alat'],
                                    ) ?>" alt="Foto Produk Lama" class="img-fluid" style="max-width: 100%; height: auto; border-radius: 4px;">
                                <?php else: ?>
                                    <p class="text-muted m-0">Tidak ada foto sebelumnya</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-text text-muted mb-2 d-block">Preview Foto Baru:</label>
                            <div id="fotoBaruContainer" class="mb-3 border rounded p-2" style="background-color: #f8f9fa; min-height: 150px; display: flex; align-items: center; justify-content: center;">
                                <p class="text-muted m-0">Pilih file untuk preview</p>
                            </div>
                        </div>
                    </div>
                    <input type="file" name="foto_produk" id="foto_produk" class="form-control" accept="image/jpeg,image/jpg,image/png,image/webp">
                    <small class="form-text text-muted mt-2 d-block">Format yang didukung: JPG, JPEG, PNG, WEBP (Max 5MB)</small>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="ketersediaan_produk" class="form-label">Ketersediaan Produk :</label>
                    <select name="ketersediaan_produk" class="form-select" required>
                        <option value="">Ketersediaan Produk</option>
                        <option value="Tersedia" <?= isset(
                            $dataAwal['status_ketersediaan'],
                        ) && $dataAwal['status_ketersediaan'] == 'Tersedia'
                            ? 'selected'
                            : ' ' ?>>Tersedia</option>
                        <option value="Disewa" <?= isset(
                            $dataAwal['status_ketersediaan'],
                        ) && $dataAwal['status_ketersediaan'] == 'Disewa'
                            ? 'selected'
                            : ' ' ?>>Disewa</option>
                        <option value="Maintenance" <?= isset(
                            $dataAwal['status_ketersediaan'],
                        ) && $dataAwal['status_ketersediaan'] == 'Maintenance'
                            ? 'selected'
                            : ' ' ?>>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-8 mb-4">
                    <button type="submit" name="submit" class="btn btn-dark w-100">Submit</button>
                </div>
            </form>
        </div>
    <?php include '../../../includes/footer.php'; ?>
    
    <script>
        // Preview foto baru ketika user memilih file
        document.getElementById('foto_produk').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const fotoBaruContainer = document.getElementById('fotoBaruContainer');
            
            if (file) {
                // Validasi ukuran file (max 5MB)
                const maxSize = 5 * 1024 * 1024; // 5MB dalam bytes
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB');
                    event.target.value = '';
                    fotoBaruContainer.innerHTML = '<p class="text-muted m-0">Pilih file untuk preview</p>';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    fotoBaruContainer.innerHTML = '<img src="' + e.target.result + '" alt="Preview Foto Baru" class="img-fluid" style="max-width: 100%; height: auto; border-radius: 4px;">';
                };
                reader.readAsDataURL(file);
            } else {
                fotoBaruContainer.innerHTML = '<p class="text-muted m-0">Pilih file untuk preview</p>';
            }
        });
        
        // Optional: Clear preview jika cancel di file input
        document.getElementById('foto_produk').addEventListener('click', function(event) {
            if (event.detail === 2) { // Double click
                event.target.value = '';
                document.getElementById('fotoBaruContainer').innerHTML = '<p class="text-muted m-0">Pilih file untuk preview</p>';
            }
        });
    </script>
</body>
</html>
