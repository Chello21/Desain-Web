<?php
$host = "localhost";
$user = "root";
$pass = "";
// PERBAIKAN DI SINI:
// Ganti 'depotairgelon' menjadi 'depotairgalon' (pakai 'a')
$db   = "depotairgalon"; 
$port = 3307;

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>