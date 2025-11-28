<?php
session_start();
include 'koneksi.php';

// 1. JIKA TOMBOL SIMPAN DITEKAN
if(isset($_POST['simpan'])){
    $keterangan = $_POST['keterangan'];
    $jumlah     = $_POST['jumlah'];

    $query = "INSERT INTO pengeluaran (keterangan, jumlah, tanggal) VALUES ('$keterangan', '$jumlah', NOW())";
    $run   = mysqli_query($koneksi, $query);

    if($run){
        header("location:keuangan.php?pesan=sukses");
    } else {
        echo "Gagal menyimpan data.";
    }
}

// 2. JIKA TOMBOL HAPUS DITEKAN
elseif(isset($_GET['aksi']) && $_GET['aksi'] == 'hapus'){
    $id = $_GET['id'];
    
    $query = "DELETE FROM pengeluaran WHERE id='$id'";
    $run   = mysqli_query($koneksi, $query);

    if($run){
        header("location:keuangan.php?pesan=hapus");
    }
}

else {
    header("location:keuangan.php");
}
?>