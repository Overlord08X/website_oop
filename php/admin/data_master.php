<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/./data_user/user.php");

// hanya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../login.php");
    exit;
}

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

        <div class="menu-box">
            <a href="./data_user/data_user.php" class="btn-master">👤 Data User</a>
            <a href="./manajemen_role.php" class="btn-master">⚙️ Manajemen Role</a>
        </div>
    </div>

    <hr>
    <small>&copy; 2025 RSHP | Halaman Data Master</small>
</body>

</html>