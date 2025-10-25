<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pemilik.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID pemilik tidak valid.", "error");
    header("Location: data_pemilik.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$pemilikRepo = new Pemilik($db);

if ($pemilikRepo->deletePemilik($id)) {
    set_flash_message("Pemilik berhasil dihapus.", "success");
} else {
    set_flash_message("Gagal menghapus pemilik. Mungkin ada data terkait yang tidak bisa dihapus.", "error");
}

$db->close_connection();
header("Location: data_pemilik.php");
exit;
