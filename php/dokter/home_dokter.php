<?php
require_once(__DIR__ . "/../auth.php");
require_dokter_login();

$nama = $_SESSION['user']['nama'];
$role = $_SESSION['user']['role_name'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Home Dokter · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("menu.php"); ?>

    <div class="container">
        <h1>Selamat Datang, drh. <?= htmlspecialchars($nama) ?>!</h1>
        <p>Anda login sebagai <b><?= htmlspecialchars($role) ?></b>.</p>
        <?php display_flash_message(); ?>
        <p>Silakan gunakan menu di atas untuk melihat data rekam medis pasien.</p>
    </div>
</body>

</html>