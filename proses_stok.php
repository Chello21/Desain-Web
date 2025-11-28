<?php
session_start();
include 'koneksi.php';

// Atur zona waktu
date_default_timezone_set('Asia/Makassar');

if(isset($_POST['submit'])){
    
    $jenis  = $_POST['jenis'];  
    $tipe   = $_POST['tipe'];   
    $jumlah = (int) $_POST['jumlah'];

    if($jumlah <= 0){
        echo "<script>alert('Jumlah harus lebih dari 0!'); window.location='stok.php';</script>";
        exit();
    }

    // --- LANGKAH 1: AMBIL DATA MODAL DARI DATABASE ---
    $cek_barang = mysqli_query($koneksi, "SELECT * FROM stok WHERE jenis = '$jenis'");
    $data_barang = mysqli_fetch_assoc($cek_barang);
    
    // Ambil data dari kolom baru 'harga_modal'
    $modal_satuan = $data_barang['harga_modal']; 
    $stok_sekarang = $data_barang['jumlah'];

    // --- LANGKAH 2: LOGIKA UPDATE ---
    if($tipe == "Masuk"){
        // A. Tambah Stok Fisik
        $query_update = "UPDATE stok SET jumlah = jumlah + $jumlah WHERE jenis = '$jenis'";
        
        // B. CATAT PENGELUARAN BERDASARKAN MODAL
        // Rumus: Pengeluaran = Jumlah x Harga Modal (3.000 atau 18.000)
        $total_biaya = $jumlah * $modal_satuan; 
        
        // Buat Keterangan
        $ket_biaya   = "Restok: " . $jenis . " (" . $jumlah . " Unit)";
        
        // Masukkan ke tabel pengeluaran
        mysqli_query($koneksi, "INSERT INTO pengeluaran (keterangan, jumlah, tanggal) VALUES ('$ket_biaya', '$total_biaya', NOW())");

    } else {
        // Jika Keluar (Barang rusak/terjual manual)
        if($stok_sekarang < $jumlah){
            echo "<script>alert('Stok tidak cukup!'); window.location='stok.php';</script>";
            exit();
        }
        $query_update = "UPDATE stok SET jumlah = jumlah - $jumlah WHERE jenis = '$jenis'";
    }

    // Jalankan Update Stok
    $update = mysqli_query($koneksi, $query_update);

    // --- LANGKAH 3: CATAT RIWAYAT STOK ---
    $query_riwayat = "INSERT INTO riwayat_stok (jenis, tipe, jumlah, tanggal) VALUES ('$jenis', '$tipe', '$jumlah', NOW())";
    $riwayat = mysqli_query($koneksi, $query_riwayat);

    if($update && $riwayat){
        header("location:stok.php?pesan=sukses");
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }

} else {
    header("location:stok.php");
}
?>