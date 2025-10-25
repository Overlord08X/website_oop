<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori_klinis.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID tidak valid.", "error");
    header("Location: data_kategori_klinis.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$repo = new KategoriKlinis($db);

if ($repo->deleteKategoriKlinis($id)) {
    set_flash_message("Data berhasil dihapus.", "success");
} else {
    set_flash_message("Gagal menghapus data.", "error");
}

$db->close_connection();
header("Location: data_kategori_klinis.php");
exit;
