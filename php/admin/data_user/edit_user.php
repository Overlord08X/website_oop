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

$id = (int)$_GET['id'];
$user = $userRepo->getUserById($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    if ($userRepo->updateUser($id, $nama, $email)) {
        header("Location: data_user.php");
        exit;
    } else {
        echo "Gagal update user";
    }
}
?>
<form method="post">
    Nama: <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>"><br>
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"><br>
    <button type="submit">Update</button>
</form>