<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID kategori tidak valid.", "error");
    header("Location: data_kategori.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$kategoriRepo = new Kategori($db);

try {
    if ($kategoriRepo->deleteKategori($id)) {
        set_flash_message("Kategori berhasil dihapus.", "success");
    }
} catch (Exception $e) {
    set_flash_message($e->getMessage(), "error");
}

$db->close_connection();
header("Location: data_kategori.php");
exit;
