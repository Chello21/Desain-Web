<?php
session_start();
include 'koneksi.php';

// Pastikan ada ID dan Aksi yang dikirim
if(isset($_GET['id']) && isset($_GET['aksi'])){
    
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];

    if($aksi == 'antar'){
        // Ubah status jadi 'Diantar'
        $query = "UPDATE orders SET status='Diantar' WHERE id='$id'";
    } 
    elseif($aksi == 'selesai'){
        // Ubah status jadi 'Selesai'
        $query = "UPDATE orders SET status='Selesai' WHERE id='$id'";
    } 
    elseif($aksi == 'hapus'){
        // Hapus data
        $query = "DELETE FROM orders WHERE id='$id'";
    }

    // Jalankan Query
    $run = mysqli_query($koneksi, $query);

    if($run){
        header("location:pesanan.php?pesan=sukses");
    } else {
        echo "Gagal memproses data.";
    }

} else {
    header("location:pesanan.php");
}
?>