<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");
require_once(__DIR__ . "/../config.php");

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);

$id = (int)$_GET['id'];

if ($id <= 0) {
    set_flash_message("ID user tidak valid.", "error");
    header("Location: data_user.php");
    exit;
}

if ($userRepo->resetPasswordDefault($id)) {
    set_flash_message("Password user ID " . htmlspecialchars($id) . " berhasil direset ke default: <b>" . htmlspecialchars(DEFAULT_ADMIN_PASSWORD) . "</b>", "success");
} else {
    set_flash_message("Gagal mereset password user ID " . htmlspecialchars($id) . ". Silakan coba lagi.", "error");
}

header("Location: data_user.php");
exit;

$db->close_connection();
