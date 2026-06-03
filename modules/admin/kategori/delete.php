<?php
require '../../../config/koneksi.php';

$id_kategori = $_GET['id_kategori'];
$result = mysqli_query(
    $conn,
    "DELETE FROM kategori WHERE id_kategori =$id_kategori",
);

header('Location: index.php');
?>
