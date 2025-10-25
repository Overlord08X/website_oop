<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../jenis_hewan.php");

$db = new DBConnection();
$db->init_connect();
$jenisHewanRepo = new JenisHewan($db);

if (!isset($_GET['id'])) {
    set_flash_message("ID jenis hewan tidak diberikan.", "error");
    header("Location: manajemen_jenis_hewan.php");
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    set_flash_message("ID jenis hewan tidak valid.", "error");
    header("Location: manajemen_jenis_hewan.php");
    exit;
}

if ($jenisHewanRepo->deleteJenisHewan($id)) {
    set_flash_message("Jenis hewan berhasil dihapus.", "success");
} else {
    set_flash_message("Gagal menghapus jenis hewan. Pastikan tidak ada ras hewan yang terkait dengan jenis ini.", "error");
}

header("Location: manajemen_jenis_hewan.php");
exit;

$db->close_connection();
