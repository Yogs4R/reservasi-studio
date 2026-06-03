<?php
require '../../../config/koneksi.php';
$targetDir = '../../../assets/img/uploads/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $desc_produk = $_POST['desc_produk'];
    $kategori_produk = $_POST['kategori_produk'];
    $harga_produk = $_POST['harga_produk'];
    $stok_produk = $_POST['stok_produk'];
    $kondisi_produk = $_POST['kondisi_produk'];

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
        move_uploaded_file($file['tmp_name'], $targetPath);
    }

    $ketersediaan_produk = $_POST['ketersediaan_produk'];

    $query = "INSERT INTO alat_media (nama_alat, desc_alat, id_kategori, harga, stok, kondisi_alat, foto_alat, status_ketersediaan) VALUES('$nama_produk','$desc_produk','$kategori_produk','$harga_produk','$stok_produk','$kondisi_produk','../../assets/img/uploads/$fileName', '$ketersediaan_produk');";
    $hasil = mysqli_query($conn, $query);

    if ($hasil) {
        header('location: index.php');
    } else {
        echo 'Data gagal ditambahkan';
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
                <div class="col-md-8 mb-4">
                    <label for="nama_produk" class="form-label">Masukkan Nama Produk :</label>
                    <input type="text" name="nama_produk" class="form-control" placeholder="Nama Produk" required>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="desc_produk" class="form-label">Masukkan Deskripsi Produk :</label>
                    <input type="text" name="desc_produk" class="form-control" placeholder="Deskripsi Produk" required>
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
] ?>"><?= $data['nama_kategori'] ?></option>
                        <?php }
                        ?>
                    </select>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="harga_produk" class="form-label">Masukkan Harga Produk :</label>
                    <input type="number" name="harga_produk" class="form-control" placeholder="Harga Produk" required>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="stok_produk" class="form-label">Masukkan Stok Produk :</label>
                    <input type="number" name="stok_produk" class="form-control" placeholder="Stok Produk" required>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="kondisi_produk" class="form-label">Masukkan Kondisi Produk :</label>
                    <select name="kondisi_produk" class="form-select" required>
                        <option value="">Kondisi Produk</option>
                        <option value="Baik">Baik</option>
                        <option value="Perlu Perawatan">Perlu Perawatan</option>
                    </select>
                </div>
                <div class="col-md-8 mb-4">
                    <label for="foto_produk" class="form-label">Masukkan Foto Produk :</label>
                    
                    <input type="file" name="foto_produk" id="foto_produk" class="form-control" accept="image/*" required>

                    <!-- Preview image -->
                    <img id="preview" src="#" alt="Preview Gambar"
                        class="img-fluid mt-3 d-none"
                        style="max-height: 250px; object-fit: contain;">
                </div>
                <div class="col-md-8 mb-4">
                    <label for="ketersediaan_produk" class="form-label">Ketersediaan Produk :</label>
                    <select name="ketersediaan_produk" class="form-select" required>
                        <option value="">Ketersediaan Produk</option>
                        <option value="Tersedia">Tersedia</option>
                        <option value="Disewa">Disewa</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-8 mb-4">
                    <button type="submit" name="submit" class="btn btn-dark w-100">Submit</button>
                </div>
            </form>
        </div>
    <?php include '../../../includes/footer.php'; ?>
    <script>
    document.getElementById('foto_produk').addEventListener('change', function (event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview');

        if (file) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };

            reader.readAsDataURL(file);
        } else {
            preview.src = "#";
            preview.classList.add('d-none');
        }
    });
    </script>
</body>
</html>
