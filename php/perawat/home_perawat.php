<?php
require_once(__DIR__ . "/../auth.php");
require_perawat_login();

$nama = $_SESSION['user']['nama'];
$role = $_SESSION['user']['role_name'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Home Perawat · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("menu.php"); ?>

    <div class="container">
        <h1>Data Master</h1>
        <h1>Halo, <?= htmlspecialchars($nama) ?>!</h1>
        <p>Anda login sebagai <b><?= htmlspecialchars($role) ?></b>.</p>
        <?php display_flash_message(); ?>

        <div class="menu-box">
            <ul>
                <li><a href="pasien.php" class="btn-master">Manajemen Pasien</a></li>
                <li><a href="reservasi.php" class="btn-master">Reservasi</a></li>
                <li><a href="rekam_medis.php" class="btn-master">Rekam Medis</a></li>
            </ul>
        </div>
    </div>
</body>

</html>