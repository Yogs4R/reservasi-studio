<?php
require '../../../config/koneksi.php';

$id_user = $_GET['id_user'];
$result = mysqli_query($conn, "DELETE FROM user WHERE id_user =$id_user");

header('Location: index.php');
?>
