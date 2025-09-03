<?php
session_start();

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

header("Location: ./login.php");
exit;
