<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pet.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID pet tidak valid.", "error");
    header("Location: data_pet.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$petRepo = new Pet($db);

if ($petRepo->deletePet($id)) {
    set_flash_message("Data pet berhasil dihapus.", "success");
} else {
    set_flash_message("Gagal menghapus data pet. Mungkin ada data rekam medis terkait.", "error");
}

$db->close_connection();
header("Location: data_pet.php");
exit;
