<?php
$host = 'localhost';
$dbname = 'reservasi_studio';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $dbname);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>