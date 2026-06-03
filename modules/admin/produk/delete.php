<?php
require '../../../config/koneksi.php';

$id_alat = $_GET['id_alat'];
$result = mysqli_query($conn, "DELETE FROM alat_media WHERE id_alat =$id_alat");

header('Location: index.php');
?>
