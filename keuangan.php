<?php
session_start();
include 'koneksi.php';

// Atur Zona Waktu
date_default_timezone_set('Asia/Makassar'); 

if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}

// VARIABEL WAKTU
$bulan_ini = date('m');
$tahun_ini = date('Y');
$nama_bulan = date('F Y');

// --- 1. HITUNG TOTAL BULANAN (UNTUK KARTU ATAS) ---
// Pemasukan Bulanan
$query_masuk = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM orders WHERE MONTH(tanggal) = '$bulan_ini' AND YEAR(tanggal) = '$tahun_ini'");
$data_masuk  = mysqli_fetch_assoc($query_masuk);
$pemasukan   = $data_masuk['total'] == "" ? 0 : $data_masuk['total'];

// Pengeluaran Bulanan
$query_keluar = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM pengeluaran WHERE MONTH(tanggal) = '$bulan_ini' AND YEAR(tanggal) = '$tahun_ini'");
$data_keluar  = mysqli_fetch_assoc($query_keluar);
$pengeluaran  = $data_keluar['total'] == "" ? 0 : $data_keluar['total'];

// Saldo Bulanan
$saldo = $pemasukan - $pengeluaran;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - WaterLink</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Layout Grid */
        .finance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .fin-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        .fin-green { border-top: 5px solid var(--success); }
        .fin-red { border-top: 5px solid var(--danger); }
        .fin-blue { border-top: 5px solid var(--primary-blue); }

        .fin-label { font-size: 14px; color: #666; display: block; margin-bottom: 10px; }
        .fin-value { font-size: 28px; font-weight: bold; }
        
        .text-success { color: var(--success); }
        .text-danger { color: var(--danger); }
        
        /* Layout Input & Tabel Harian */
        .daily-report-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        
        .input-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            height: fit-content;
        }

        /* Tabel Harian */
        .daily-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .daily-table th { background: #f4f4f4; padding: 12px; text-align: left; font-size: 13px; color: #555; }
        .daily-table td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .daily-table tr:hover { background: #f9f9f9; }
        .row-profit { font-weight: bold; color: var(--primary-blue); }
        .row-loss { font-weight: bold; color: var(--danger); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand"><h2><i class="fas fa-tint"></i> SegarJaya</h2></div>
        <div class="menu">
            <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> <span>Pesanan Masuk</span></a>
            <a href="users.php"><i class="fas fa-users-cog"></i> <span>Data Pengguna</span></a>
            <a href="stok.php"><i class="fas fa-boxes"></i> <span>Stok Galon</span></a>
            <a href="keuangan.php" class="active"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Keuangan</span></a>
            <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Laporan Keuangan Per Hari</h1>
            <div class="user-profile">
                <span><?php echo $nama_bulan; ?></span>
            </div>
        </header>

        <div class="finance-grid">
            <div class="fin-card fin-green">
                <span class="fin-label">Total Pemasukan Bulan Ini</span>
                <span class="fin-value text-success">+ Rp <?php echo number_format($pemasukan, 0, ',', '.'); ?></span>
            </div>
            <div class="fin-card fin-red">
                <span class="fin-label">Total Pengeluaran Bulan Ini</span>
                <span class="fin-value text-danger">- Rp <?php echo number_format($pengeluaran, 0, ',', '.'); ?></span>
            </div>
            <div class="fin-card fin-blue">
                <span class="fin-label">Laba Bersih Bulan Ini</span>
                <span class="fin-value" style="color: var(--primary-blue);">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></span>
            </div>
        </div>

        <div class="daily-report-section">
            
            <div class="input-box">
                <h3 style="margin-bottom: 15px; color: var(--dark-blue);">Catat Biaya Operasional</h3>
                <form action="proses_keuangan.php" method="POST">
                    <div style="margin-bottom: 15px;">
                        <label style="font-size: 13px; font-weight: bold;">Keterangan</label>
                        <input type="text" name="keterangan" placeholder="Cth: Bensin, Makan..." required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="font-size: 13px; font-weight: bold;">Jumlah (Rp)</label>
                        <input type="number" name="jumlah" placeholder="0" required 
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>

                    <button type="submit" name="simpan" class="btn-action" style="width: 100%; background: var(--danger);">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </form>

                <div style="margin-top: 30px;">
                    <h4>Catatan:</h4>
                    <p style="font-size: 12px; color: #666; line-height: 1.6;">
                        Tabel di samping menampilkan rincian laba rugi <b>Setiap Hari</b> pada bulan ini.<br><br>
                        - <b>Pemasukan:</b> Dari menu 'Pesanan Masuk'.<br>
                        - <b>Pengeluaran:</b> Dari input form ini.
                    </p>
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 15px; color: var(--text-grey);">Rincian Laba Rugi Harian</h3>
                <table class="daily-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                            <th>Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // MENAMPILKAN DATA PER HARI (DARI TANGGAL 1 SAMPAI HARI INI)
                        $jumlah_hari = date('d'); // Tanggal hari ini (misal tgl 29)
                        
                        // Loop dari tanggal hari ini MUNDUR ke tanggal 1 (Agar data terbaru di atas)
                        for($i = $jumlah_hari; $i >= 1; $i--){
                            
                            // Bentuk format tanggal YYYY-MM-DD
                            $tgl_cek = date('Y-m-').sprintf("%02d", $i); // sprintf buat nambah angka 0 (misal 1 jadi 01)
                            
                            // 1. Ambil Pemasukan per Tanggal Tersebut
                            $q_in = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM orders WHERE DATE(tanggal) = '$tgl_cek'");
                            $d_in = mysqli_fetch_assoc($q_in);
                            $masuk_harian = $d_in['total'] == "" ? 0 : $d_in['total'];

                            // 2. Ambil Pengeluaran per Tanggal Tersebut
                            $q_out = mysqli_query($koneksi, "SELECT SUM(jumlah) as total FROM pengeluaran WHERE DATE(tanggal) = '$tgl_cek'");
                            $d_out = mysqli_fetch_assoc($q_out);
                            $keluar_harian = $d_out['total'] == "" ? 0 : $d_out['total'];

                            // 3. Hitung Laba Harian
                            $laba_harian = $masuk_harian - $keluar_harian;

                            // HANYA TAMPILKAN JIKA ADA TRANSAKSI (Biar tabel gak penuh angka 0)
                            if($masuk_harian == 0 && $keluar_harian == 0){
                                continue; 
                            }
                        ?>
                        <tr>
                            <td>
                                <b><?php echo $i . ' ' . date('M'); ?></b> </td>
                            <td class="text-success">
                                + Rp <?php echo number_format($masuk_harian, 0, ',', '.'); ?>
                            </td>
                            <td class="text-danger">
                                - Rp <?php echo number_format($keluar_harian, 0, ',', '.'); ?>
                            </td>
                            <td>
                                <?php if($laba_harian >= 0) { ?>
                                    <span class="badge badge-success" style="background: #e3f2fd; color: #1565c0;">
                                        Rp <?php echo number_format($laba_harian, 0, ',', '.'); ?>
                                    </span>
                                <?php } else { ?>
                                    <span class="badge badge-danger">
                                        Rp <?php echo number_format($laba_harian, 0, ',', '.'); ?>
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>

                        <?php 
                        // Jika tidak ada transaksi sama sekali bulan ini
                        if($pemasukan == 0 && $pengeluaran == 0){
                            echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>Belum ada data transaksi bulan ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</body>
</html>