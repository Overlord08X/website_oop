<?php
session_start();

// Fungsi untuk mengatur flash message
function set_flash_message($message, $type = 'info')
{
    $_SESSION['flash_msg'] = ['message' => $message, 'type' => $type];
}

// Fungsi untuk menampilkan flash message
function display_flash_message()
{
    if (isset($_SESSION['flash_msg'])) {
        $msg = $_SESSION['flash_msg'];
        echo "<p class='flash-msg " . htmlspecialchars($msg['type']) . "'>" . htmlspecialchars($msg['message']) . "</p>";
        unset($_SESSION['flash_msg']);
    }
}

// Fungsi untuk memeriksa apakah pengguna adalah admin
function is_admin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1;
}

// Fungsi untuk mengarahkan ke halaman login jika tidak ada sesi atau bukan admin
function require_admin_login()
{
    if (!is_admin()) {
        set_flash_message("Anda tidak memiliki akses ke halaman ini. Silakan login sebagai administrator.", "error");
        header("Location: ../login.php");
        exit;
    }
}

// Fungsi untuk memeriksa apakah pengguna adalah perawat
function is_perawat()
{
    // Asumsi role_id untuk Perawat adalah 3
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 3;
}

// Fungsi untuk mengarahkan ke halaman login jika tidak ada sesi atau bukan perawat
function require_perawat_login()
{
    if (!is_perawat()) {
        set_flash_message("Anda tidak memiliki akses ke halaman ini. Silakan login sebagai perawat.", "error");
        header("Location: ../login.php");
        exit;
    }
}

// Fungsi untuk memeriksa apakah pengguna adalah dokter
function is_dokter()
{
    // Asumsi role_id untuk Dokter adalah 2
    return isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2;
}

// Fungsi untuk mengarahkan ke halaman login jika tidak ada sesi atau bukan dokter
function require_dokter_login()
{
    if (!is_dokter()) {
        set_flash_message("Anda tidak memiliki akses ke halaman ini. Silakan login sebagai dokter.", "error");
        header("Location: ../login.php");
        exit;
    }
}

// Fungsi untuk mengarahkan ke halaman login jika tidak ada sesi
function require_login()
{
    if (!isset($_SESSION['user'])) {
        set_flash_message("Anda harus login untuk mengakses halaman ini.", "error");
        header("Location: ../login.php");
        exit;
    }
}
