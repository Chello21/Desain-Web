<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    // 1. Tangkap data username (bukan email)
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 2. Bersihkan data (Security)
    $username = mysqli_real_escape_string($koneksi, $username);
    $password = mysqli_real_escape_string($koneksi, $password);

    // 3. Cek ke tabel 'admin'
    // Kolom di database Anda adalah: username, password
    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    
    // Cek error query (Opsional, untuk jaga-jaga)
    if(!$query){
        die("Error Query: ".mysqli_error($koneksi));
    }

    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);

        // Simpan sesi
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap']; // Ambil nama lengkap dari tabel admin
        $_SESSION['status'] = "login";

        header("location:index.php");
    } else {
        header("location:login.php?pesan=gagal");
    }
}
?>