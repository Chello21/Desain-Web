<?php
session_start();
include 'koneksi.php';

// Atur Zona Waktu
date_default_timezone_set('Asia/Makassar'); 

if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}

// FUNGSI HITUNG HARI INI
function hitungHariIni($koneksi, $jenis, $tipe){
    $tanggal_hari_ini = date('Y-m-d');
    $query = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM riwayat_stok WHERE jenis='$jenis' AND tipe='$tipe' AND DATE(tanggal) = '$tanggal_hari_ini'");
    if(!$query) return 0;
    $data = mysqli_fetch_assoc($query);
    return $data['total'] == "" ? 0 : $data['total'];
}

// AMBIL DATA STOK UTAMA (Termasuk kolom harga_modal yang baru)
$stok_isi_ulang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM stok WHERE jenis='Air Isi Ulang'"));
$stok_segel     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM stok WHERE jenis='Air Segel'"));

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Harian - WaterLink</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .monitoring-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .monitor-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-top: 5px solid var(--primary-blue); }
        .monitor-header { display: flex; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; align-items: flex-start; }
        .monitor-header h2 { font-size: 20px; color: var(--dark-blue); margin-bottom: 5px; }
        
        /* Tampilan Harga Jual & Modal */
        .price-info { text-align: right; }
        .price-tag { background: #e3f2fd; color: var(--primary-blue); padding: 5px 10px; border-radius: 10px; font-weight: bold; font-size: 14px; display: inline-block; }
        .modal-tag { display: block; font-size: 11px; color: #888; margin-top: 5px; font-style: italic; }

        .big-stock { font-size: 48px; font-weight: bold; color: #333; text-align: center; margin: 20px 0; }
        .big-stock span { font-size: 14px; color: #888; font-weight: normal; }
        .daily-stats { display: flex; justify-content: space-between; background: #f9f9f9; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .stat-item { text-align: center; width: 48%; }
        .stat-label { font-size: 12px; color: #666; display: block; }
        .stat-value { font-size: 20px; font-weight: bold; }
        .green { color: var(--success); }
        .red { color: var(--danger); }
        .action-area { display: flex; gap: 10px; }
        .btn-input { flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><h2><i class="fas fa-tint"></i> SegarJaya</h2></div>
        <div class="menu">
            <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> <span>Pesanan Masuk</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Data Pengguna</span></a>
            <a href="stok.php" class="active"><i class="fas fa-boxes"></i> <span>Stok Galon</span></a>
            <a href="keuangan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Keuangan</span></a>
            <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Monitoring Stok Harian</h1>
            <div class="user-profile">
                <span><?php echo date('d F Y'); ?></span>
            </div>
        </header>

        <div class="monitoring-grid">
            
            <div class="monitor-card">
                <div class="monitor-header">
                    <div>
                        <h2>Air Isi Ulang</h2>
                        <small style="color: #666;">Refill Galon Biasa</small>
                    </div>
                    <div class="price-info">
                        <span class="price-tag">Jual: Rp <?php echo number_format($stok_isi_ulang['harga'],0,',','.'); ?></span>
                        <span class="modal-tag">Modal: Rp <?php echo number_format($stok_isi_ulang['harga_modal'],0,',','.'); ?></span>
                    </div>
                </div>
                
                <div class="big-stock">
                    <?php echo $stok_isi_ulang['jumlah']; ?> <span>Galon Tersedia</span>
                </div>

                <div class="daily-stats">
                    <div class="stat-item">
                        <span class="stat-label">Masuk Hari Ini</span>
                        <span class="stat-value green">+ <?php echo hitungHariIni($koneksi, 'Air Isi Ulang', 'Masuk'); ?></span>
                    </div>
                    <div class="stat-item" style="border-left: 1px solid #ddd;">
                        <span class="stat-label">Terjual Hari Ini</span>
                        <span class="stat-value red">- <?php echo hitungHariIni($koneksi, 'Air Isi Ulang', 'Keluar'); ?></span>
                    </div>
                </div>

                <form action="proses_stok.php" method="POST" class="action-area">
                    <input type="hidden" name="jenis" value="Air Isi Ulang">
                    <input type="number" name="jumlah" class="btn-input" placeholder="Jumlah..." required min="1">
                    <select name="tipe" class="btn-input">
                        <option value="Keluar">Catat Terjual (Keluar)</option>
                        <option value="Masuk">Tambah Stok (Masuk)</option>
                    </select>
                    <button type="submit" name="submit" class="btn-action"><i class="fas fa-check"></i></button>
                </form>
            </div>

            <div class="monitor-card" style="border-top-color: var(--warning);">
                <div class="monitor-header">
                    <div>
                        <h2>Air Segel</h2>
                        <small style="color: #666;">Galon Pabrik</small>
                    </div>
                    <div class="price-info">
                        <span class="price-tag" style="background:#fff3e0; color:#ef6c00;">Jual: Rp <?php echo number_format($stok_segel['harga'],0,',','.'); ?></span>
                        <span class="modal-tag">Modal: Rp <?php echo number_format($stok_segel['harga_modal'],0,',','.'); ?></span>
                    </div>
                </div>
                
                <div class="big-stock">
                    <?php echo $stok_segel['jumlah']; ?> <span>Galon Tersedia</span>
                </div>

                <div class="daily-stats">
                    <div class="stat-item">
                        <span class="stat-label">Masuk Hari Ini</span>
                        <span class="stat-value green">+ <?php echo hitungHariIni($koneksi, 'Air Segel', 'Masuk'); ?></span>
                    </div>
                    <div class="stat-item" style="border-left: 1px solid #ddd;">
                        <span class="stat-label">Terjual Hari Ini</span>
                        <span class="stat-value red">- <?php echo hitungHariIni($koneksi, 'Air Segel', 'Keluar'); ?></span>
                    </div>
                </div>

                <form action="proses_stok.php" method="POST" class="action-area">
                    <input type="hidden" name="jenis" value="Air Segel">
                    <input type="number" name="jumlah" class="btn-input" placeholder="Jumlah..." required min="1">
                    <select name="tipe" class="btn-input">
                        <option value="Keluar">Catat Terjual (Keluar)</option>
                        <option value="Masuk">Tambah Stok (Masuk)</option>
                    </select>
                    <button type="submit" name="submit" class="btn-action" style="background:var(--warning);"><i class="fas fa-check"></i></button>
                </form>
            </div>

        </div>

        <div class="recent-orders">
            <h2>10 Riwayat Transaksi Terakhir</h2>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Jenis Air</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_history = mysqli_query($koneksi, "SELECT * FROM riwayat_stok ORDER BY id DESC LIMIT 10");
                    while($row = mysqli_fetch_array($query_history)){
                        $color = ($row['tipe'] == 'Masuk') ? 'var(--success)' : 'var(--danger)';
                        $sign  = ($row['tipe'] == 'Masuk') ? '+' : '-';
                    ?>
                    <tr>
                        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                        <td><?php echo $row['jenis']; ?></td>
                        <td>
                            <span style="font-weight:bold; color: <?php echo $color; ?>">
                                <?php echo $row['tipe']; ?>
                            </span>
                        </td>
                        <td style="font-weight:bold;"><?php echo $sign . $row['jumlah']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>