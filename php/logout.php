<?php
// website_oop/php/logout.php
require_once("auth.php"); // Memuat fungsi auth dan memulai sesi

// Hapus semua data session
$_SESSION = array();

// Jika ada cookie session, hapus juga
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

set_flash_message("Anda telah berhasil logout.", "success");
header("Location: ./login.php");
exit;
