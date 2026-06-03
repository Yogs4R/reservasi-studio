<?php
require '../../../config/koneksi.php';

$id_reserv = $_GET['id_reserv'];
$result = mysqli_query(
    $conn,
    "DELETE FROM reservasi WHERE id_reserv =$id_reserv",
);

header('Location: index.php');
?>
