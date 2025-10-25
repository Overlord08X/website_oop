<?php
// website_oop/php/resepsionis/home_resepsionis.php
require_once(__DIR__ . "/../auth.php"); // Memuat fungsi auth dan memulai sesi
require_login(); // Memastikan user sudah login

// Memastikan hanya resepsionis yang bisa mengakses halaman ini
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 4) { // Asumsi role_id 4 untuk Resepsionis
    set_flash_message("Anda tidak memiliki akses ke halaman ini.", "error");
    header("Location: ../login.php");
    exit;
}

$nama = $_SESSION['user']['nama'];
$role = $_SESSION['user']['role_name'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Resepsionis</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h1>Halo, <?= htmlspecialchars($nama) ?>!</h1>
        <p>Anda login sebagai <b><?= htmlspecialchars($role) ?></b>.</p>
        <?php display_flash_message(); ?>

        <div class="menu-box">
            <ul>
                <li><a href="./registrasi_pemilik.php" class="btn-master">📝 Registrasi Pemilik</a></li>
                <li><a href="./registrasi_pet.php" class="btn-master">🐾 Registrasi Pet</a></li>
                <li><a href="./temu_dokter.php" class="btn-master">🩺 Temu Dokter</a></li>
            </ul>
        </div>
    </div>

    <hr />
    <small>&copy; 2025 RSHP | Halaman Resepsionis</small>
</body>

</html>