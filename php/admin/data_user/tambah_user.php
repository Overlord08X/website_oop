<?php
session_start();
require_once(__DIR__ . "/../../dbconnection.php");
require_once(__DIR__ . "/../data_user/user.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../login.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $repass = $_POST['repassword'];

    if ($pass !== $repass) {
        echo "Password tidak sama";
    } else {
        if ($userRepo->createUser($nama, $email, $pass)) {
            header("Location: data_user.php");
            exit;
        } else {
            echo "Gagal tambah user";
        }
    }
}
?>
<form method="post">
    Nama: <input type="text" name="nama" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    Retype Password: <input type="password" name="repassword" required><br>
    <button type="submit">Simpan</button>
</form>