<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../auth.php"); 
require_admin_login();

$nama = $_SESSION['user']['nama'];
$role = $_SESSION['user']['role_name'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Master</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h1>Data Master</h1>
        <p>Halo, <?= htmlspecialchars($nama) ?>! Anda login sebagai <b><?= htmlspecialchars($role) ?></b>.</p>
        <?php display_flash_message(); ?>

        <div class="menu-box">
            <ul>
                <li><a href="../data_user/data_user.php" class="btn-master">⚙️ Data User</a></li>
                <li><a href="../manajemen_role/manajemen_role.php" class="btn-master">⚙️ Manajemen Role</a></li>
                <li><a href="../jenis_hewan/manajemen_jenis_hewan.php" class="btn-master">⚙️ Jenis Hewan</a></li>
                <li><a href="../ras_hewan/manajemen_ras_hewan.php" class="btn-master">⚙️ Ras Hewan</a></li>
                <li><a href="../data_pemilik/data_pemilik.php" class="btn-master">⚙️ Data Pemilik</a></li>
                <li><a href="../data_pet/data_pet.php" class="btn-master">⚙️ Data Pet</a></li>
                <li><a href="../data_kategori/data_kategori.php" class="btn-master">⚙️ Data Kategori</a></li>
                <li><a href="../data_kategori_klinis/data_kategori_klinis.php" class="btn-master">⚙️ Data Kategori Klinis</a></li>
                <li><a href="../data_kode_tindakan_terapi/data_kode_tindakan_terapi.php" class="btn-master">⚙️ Data Kode Tindakan Terapi</a></li>
            </ul>
        </div>
    </div>

    <hr>
    <small>&copy; 2025 RSHP | Halaman Data Master</small>
</body>

</html>