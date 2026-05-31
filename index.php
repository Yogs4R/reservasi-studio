<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include 'includes/header.php' ?>
    
    <h1>Home / Dashboard</h1>

    <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Nama Alat</th>
                    <!-- <th scope="col" style="text-align:center">deskripsi kategori</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                    require './config/koneksi.php';
                    $hasil = mysqli_query($conn, "SELECT * FROM alat_media ORDER BY id_alat");
                    
                    $no = 1;
                    while($data = mysqli_fetch_array($hasil)) {
                        echo "<tr>";
                        echo "<th>" . $no . "</th>";
                        echo "<td>" . $data['nama_alat'] . "</td>";
                        // echo "<td>" . $data['desc_kategori'] . "</td>";                        
                        
                        
                        // Kolom Aksi (Edit dan Delete)
                        echo "<td style='text-align:center'>
                                <a href='update.php?id_alat=" . $data['id_alat'] . "' class='btn btn-warning btn-sm' title='edit'>
                                    <i class='bi bi-pencil-square'></i>
                                </a> 
                                <a href='delete.php?id_alat=" . $data['id_alat'] . "' class='btn btn-danger btn-sm' title='hapus'>
                                    <i class='bi bi-trash'></i>
                                </a>
                              </td>";
                        echo "</tr>";
                        $no++;
                    }
                ?>
            </tbody>
        </table>

    <?php include 'includes/footer.php' ?>
</body>
</html>
