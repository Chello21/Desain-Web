<?php
session_start();
include 'koneksi.php';

// Cek apakah ada ID yang dikirim
if(isset($_GET['id'])){
    $id = $_GET['id'];

    // Hapus data berdasarkan ID
    $query = mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");

    if($query){
        // Jika berhasil, balik ke users.php
        header("location:users.php?pesan=hapus_sukses");
    } else {
        echo "Gagal menghapus data.";
    }
} else {
    header("location:users.php");
}
?>