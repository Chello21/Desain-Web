<?php
session_start();
include 'koneksi.php'; // Hubungkan ke database

// Cek keamanan: Jika belum login, tendang ke login.php
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
    <title>Data Users - WaterLink</title>
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
            <a href="proses_pesanan.php"><i class="fas fa-shopping-cart"></i> <span>Pesanan Masuk</span></a>
            <a href="users.php" class="active"><i class="fas fa-users-cog"></i> <span>Data Pengguna</span></a>
            <a href="stok.php"><i class="fas fa-boxes"></i> <span>Stok Galon</span></a>
            <a href="keuangan.php"><i class="fas fa-file-invoice-dollar"></i> <span>Laporan Keuangan</span></a>
            <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Data Pengguna (Users)</h1>
            <div class="user-profile">
                <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?></span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </header>

        <div class="recent-orders">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Daftar Akun Pengguna</h2>
                <button class="btn-action" style="padding: 10px 20px; font-size: 14px;">+ Tambah User</button>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th>Password</th> <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php 
    $query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
    $no = 1;
    while($data = mysqli_fetch_array($query)){
    ?>
    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo $data['nama']; ?></td>
        <td><?php echo $data['telepon']; ?></td>
        <td><?php echo $data['email']; ?></td>
        <td>●●●●●</td> <td>
            <div class="btn-group">
                <a href="edit_user.php?id=<?php echo $data['id']; ?>" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                <a href="hapus_user.php?id=<?php echo $data['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus user ini?')">
                    <i class="fas fa-trash"></i> Hapus
                </a>
            </div>
        </td>
    </tr>
    <?php } ?>
</tbody>
            </table>
        </div>
    </div>

</body>
</html>