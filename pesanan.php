<?php
session_start();
include 'koneksi.php';

// Cek Login
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - WaterLink</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <h2><i class="fas fa-tint"></i> SegarJaya</h2>
        </div>
        <div class="menu">
            <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="pesanan.php" class="active"><i class="fas fa-shopping-cart"></i> <span>Pesanan Masuk</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Data Pengguna</span></a>
            <a href="stok.php"><i class="fas fa-boxes"></i> <span>Stok Galon</span></a>
            <a href="keuangan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Keuangan</span></a>
            <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Daftar Pesanan Masuk</h1>
            <div class="user-profile">
                <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?></span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </header>

        <div class="recent-orders">
            <h2>Kelola Pesanan Pelanggan</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Tgl</th>
                        <th>Pelanggan</th>
                        <th>Pesanan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ambil data orders urutkan dari yang terbaru
                    $query = mysqli_query($koneksi, "SELECT * FROM orders ORDER BY id DESC");
                    
                    while($row = mysqli_fetch_array($query)){
                        // Logika Warna Status
                        if($row['status'] == 'Menunggu'){
                            $badge = 'badge-warning'; // Kuning
                        } elseif($row['status'] == 'Diantar'){
                            $badge = 'badge-success'; // Hijau Muda (Kita anggap biru proses)
                            $style = "background:#e3f2fd; color:#1976d2;"; // Custom Biru
                        } else {
                            $badge = 'badge-success'; // Hijau
                            $style = "";
                        }
                    ?>
                    <tr>
                        <td><?php echo date('d/m H:i', strtotime($row['tanggal'])); ?></td>
                        <td>
                            <strong><?php echo $row['nama_pelanggan']; ?></strong><br>
                            <small style="color:#888;"><?php echo $row['alamat']; ?></small>
                        </td>
                        <td>
                            <?php echo $row['jenis_air']; ?><br>
                            <b>x <?php echo $row['jumlah']; ?></b>
                        </td>
                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        
                        <td>
                            <span class="badge <?php echo $badge; ?>" style="<?php echo isset($style) ? $style : ''; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        
                        <td>
                            <?php if($row['status'] == 'Menunggu'){ ?>
                                <a href="proses_pesanan.php?id=<?php echo $row['id']; ?>&aksi=antar" class="btn-action" style="background:var(--primary-blue);" title="Kirim Barang">
                                    <i class="fas fa-truck"></i> Antar
                                </a>
                            <?php } elseif($row['status'] == 'Diantar'){ ?>
                                <a href="proses_pesanan.php?id=<?php echo $row['id']; ?>&aksi=selesai" class="btn-action" style="background:var(--success);" title="Selesaikan">
                                    <i class="fas fa-check"></i> Selesai
                                </a>
                            <?php } else { ?>
                                <span style="color:var(--success); font-size:12px;"><i class="fas fa-check-circle"></i> Tuntas</span>
                            <?php } ?>

                            <a href="proses_pesanan.php?id=<?php echo $row['id']; ?>&aksi=hapus" class="btn-action btn-delete" onclick="return confirm('Hapus pesanan ini?')" style="margin-left:5px;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>