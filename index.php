<?php
session_start();
include 'koneksi.php';

// Atur Zona Waktu
date_default_timezone_set('Asia/Makassar'); 

// Cek Login
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}

// --- LOGIKA BARU STATISTIK ---
$tanggal_hari_ini = date('Y-m-d');

// 1. Hitung Galon Terjual Hari Ini
$query_jual = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM orders WHERE DATE(tanggal) = '$tanggal_hari_ini'");
$data_jual  = mysqli_fetch_assoc($query_jual);
$galon_terjual = $data_jual['total'] == "" ? 0 : $data_jual['total'];

// 2. Hitung Pendapatan Hari Ini
$query_uang = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM orders WHERE DATE(tanggal) = '$tanggal_hari_ini'");
$data_uang  = mysqli_fetch_assoc($query_uang);
$pendapatan = $data_uang['total'] == "" ? 0 : $data_uang['total'];

// 3. LOGIKA BARU: Hitung Pesanan Belum Selesai
// (Mengambil semua yang statusnya BUKAN 'Selesai')
$query_unfinished = mysqli_query($koneksi, "SELECT count(*) as total FROM orders WHERE status != 'Selesai'");
$data_unfinished  = mysqli_fetch_assoc($query_unfinished);
$pesanan_belum_selesai = $data_unfinished['total'];

// 4. LOGIKA BARU: Hitung Total Stok Galon Tersedia
// (Menjumlahkan kolom 'jumlah' dari semua jenis di tabel stok)
$query_stok = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM stok");
$data_stok  = mysqli_fetch_assoc($query_stok);
$total_stok = $data_stok['total'] == "" ? 0 : $data_stok['total'];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - WaterLink</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <h2><i class="fas fa-tint"></i> SegarJaya</h2>
        </div>
        <div class="menu">
            <a href="index.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> <span>Pesanan Masuk</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Data Pengguna</span></a>
            <a href="stok.php"><i class="fas fa-boxes"></i> <span>Stok Galon</span></a>
            <a href="keuangan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Keuangan</span></a>
            <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard Admin</h1>
            <div class="user-profile">
                <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?></span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </header>

        <div class="cards">
            <div class="card">
                <div class="card-info">
                    <h3><?php echo $galon_terjual; ?></h3>
                    <p>Galon Terjual Hari Ini</p>
                </div>
                <div class="card-icon">
                    <i class="fas fa-tint"></i>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h3>
                    <p>Pendapatan Hari Ini</p>
                </div>
                <div class="card-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3><?php echo $pesanan_belum_selesai; ?></h3>
                    <p>Pesanan Belum Selesai</p>
                </div>
                <div class="card-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3><?php echo $total_stok; ?></h3>
                    <p>Total Stok Galon</p>
                </div>
                <div class="card-icon">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
        </div>

        <div class="recent-orders">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Pesanan Terbaru</h2>
                <a href="pesanan.php" style="font-size: 14px; text-decoration: none; color: var(--primary-blue);">Lihat Semua ></a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query_orders = mysqli_query($koneksi, "SELECT * FROM orders ORDER BY id DESC LIMIT 5");
                    
                    if(mysqli_num_rows($query_orders) == 0){
                        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada pesanan masuk.</td></tr>";
                    }

                    while($row = mysqli_fetch_array($query_orders)){
                        if($row['status'] == 'Menunggu'){
                            $badge = 'badge-warning'; 
                        } elseif($row['status'] == 'Diantar'){
                            $badge = 'badge-success'; 
                            $custom_style = "background:#e3f2fd; color:#1976d2;";
                        } else {
                            $badge = 'badge-success'; 
                            $custom_style = "";
                        }
                    ?>
                    <tr>
                        <td>#ORD-00<?php echo $row['id']; ?></td>
                        <td><?php echo $row['nama_pelanggan']; ?></td>
                        <td><?php echo $row['alamat']; ?></td>
                        <td><?php echo $row['jumlah']; ?> Galon</td>
                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="badge <?php echo $badge; ?>" style="<?php echo isset($custom_style)?$custom_style:''; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['status'] == 'Menunggu') { ?>
                                <a href="proses_pesanan.php?id=<?php echo $row['id']; ?>&aksi=antar" class="btn-action">Proses</a>
                            <?php } elseif($row['status'] == 'Diantar') { ?>
                                <a href="proses_pesanan.php?id=<?php echo $row['id']; ?>&aksi=selesai" class="btn-action" style="background:var(--success);">Selesai</a>
                            <?php } else { ?>
                                <button class="btn-action" style="background:#ccc; cursor: default;">Detail</button>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>