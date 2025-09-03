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
if ($userRepo->deleteUser($id)) {
    header("Location: data_user.php");
    exit;
} else {
    echo "Gagal hapus user";
}
