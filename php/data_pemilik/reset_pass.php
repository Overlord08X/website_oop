<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");
require_once(__DIR__ . "/../config.php");

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID user tidak valid.", "error");
    header("Location: data_pemilik.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);

if ($userRepo->resetPasswordDefault($id)) {
    set_flash_message("Password berhasil direset ke default: '" . DEFAULT_ADMIN_PASSWORD . "'", "success");
} else {
    set_flash_message("Gagal mereset password.", "error");
}

$db->close_connection();
header("Location: data_pemilik.php");
exit;
