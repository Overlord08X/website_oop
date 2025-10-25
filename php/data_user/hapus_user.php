<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);

if (!isset($_GET['id'])) {
    set_flash_message("ID user tidak diberikan.", "error");
    header("Location: data_user.php");
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    set_flash_message("ID user tidak valid.", "error");
    header("Location: data_user.php");
    exit;
}

try {
    if ($userRepo->deleteUser($id)) {
        set_flash_message("User berhasil dihapus.", "success");
    } else {
        set_flash_message("Gagal menghapus user. Silakan coba lagi.", "error");
    }
} catch (Exception $e) {
    error_log("Error hapus user ID $id: " . $e->getMessage());
    set_flash_message("Terjadi kesalahan saat menghapus user: " . $e->getMessage(), "error"); // Tampilkan pesan error lebih detail untuk debugging
}

header("Location: data_user.php");
exit;

$db->close_connection();
