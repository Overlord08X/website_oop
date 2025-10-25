<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");

$db = new DBConnection();
$db->init_connect();
$conn = $db->getConnection();

$idrole = (int)$_GET['id'];

if ($idrole <= 0) {
    set_flash_message("ID role tidak valid.", "error");
    header("Location: manajemen_role.php");
    exit;
}

try {
    // Cek dulu apakah role dipakai di role_user
    $cek = $conn->prepare("SELECT COUNT(*) as jml FROM role_user WHERE idrole=?");
    if (!$cek) {
        error_log("Prepare failed in hapus_role.php (check role_user): " . $conn->error);
        throw new Exception("Terjadi kesalahan sistem saat memeriksa role.");
    }
    $cek->bind_param("i", $idrole);
    $cek->execute();
    $res = $cek->get_result()->fetch_assoc();

    if ($res['jml'] > 0) {
        set_flash_message("Role tidak bisa dihapus karena masih dipakai oleh user.", "error");
        header("Location: manajemen_role.php");
        exit;
    }

    // Hapus role
    $stmt = $conn->prepare("DELETE FROM role WHERE idrole=?");
    if (!$stmt) {
        error_log("Prepare failed in hapus_role.php (delete role): " . $conn->error);
        throw new Exception("Terjadi kesalahan sistem saat menghapus role.");
    }
    $stmt->bind_param("i", $idrole);
    if ($stmt->execute()) {
        set_flash_message("Role berhasil dihapus.", "success");
    } else {
        error_log("Execute failed in hapus_role.php (delete role): " . $stmt->error);
        throw new Exception("Gagal menghapus role.");
    }
} catch (Exception $e) {
    set_flash_message($e->getMessage() . " Silakan coba lagi.", "error");
} finally {
    // Selalu tutup koneksi, lalu redirect.
    $db->close_connection();
    header("Location: manajemen_role.php");
    exit;
}
