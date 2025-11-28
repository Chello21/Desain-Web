<?php
session_start();
include 'koneksi.php';

// Cek Login
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
    exit();
}

// 1. AMBIL DATA LAMA
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
    $data = mysqli_fetch_array($query);
} else {
    header("location:users.php");
}

// 2. PROSES UPDATE DATA
if(isset($_POST['update'])){
    $id_user = $_POST['id'];
    $nama = $_POST['nama'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $update = mysqli_query($koneksi, "UPDATE users SET nama='$nama', telepon='$telepon', email='$email', password='$password' WHERE id='$id_user'");

    if($update){
        echo "<script>alert('Data Berhasil Diupdate!'); window.location='users.php';</script>";
    } else {
        echo "<script>alert('Gagal update data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - WaterLink</title>
    
    <link rel="stylesheet" href="style.css">
    
    <link rel="stylesheet" href="edit_user.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <div class="brand">
            <h2><i class="fas fa-tint"></i> SegarJaya</h2>
        </div>
        <div class="menu">
            <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="users.php" class="active"><i class="fas fa-users-cog"></i> <span>Data Pengguna</span></a>
            <a href="logout.php" style="color: var(--danger);"><i class="fas fa-sign-out-alt"></i> <span>Keluar</span></a>
        </div>
    </div>

    <div class="main-content">
        <header>
            <h1>Edit Data Pengguna</h1>
            <div class="user-profile">
                <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?></span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </header>

        <div class="form-container">
            <div class="form-header">
                <h2>Formulir Perubahan Data</h2>
            </div>
            
            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo $data['nama']; ?>" required>
                </div>

                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="<?php echo $data['telepon']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password" class="form-control" value="<?php echo $data['password']; ?>" required>
                </div>

                <div class="form-actions">
                    <button type="submit" name="update" class="btn-save">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    
                    <a href="users.php" class="btn-cancel">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>