<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../ras_hewan.php");

$db = new DBConnection();
$db->init_connect();
$rasHewanRepo = new RasHewan($db);

if (!isset($_GET['id'])) {
    set_flash_message("ID ras hewan tidak diberikan.", "error");
    header("Location: manajemen_ras_hewan.php");
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    set_flash_message("ID ras hewan tidak valid.", "error");
    header("Location: manajemen_ras_hewan.php");
    exit;
}

if ($rasHewanRepo->deleteRasHewan($id)) {
    set_flash_message("Ras hewan berhasil dihapus.", "success");
} else {
    set_flash_message("Gagal menghapus ras hewan. Silakan coba lagi.", "error");
}

header("Location: manajemen_ras_hewan.php");
exit;

$db->close_connection();
