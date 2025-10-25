<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);

$id = (int)$_GET['id'];
$newPass = null;

if ($id <= 0) {
    set_flash_message("ID user tidak valid.", "error");
    header("Location: data_user.php");
    exit;
}

$newPass = $userRepo->resetPasswordRandom($id);

if ($newPass) {
    // Password baru ditampilkan di halaman ini, tidak di-redirect langsung
    // Ini lebih aman daripada menyimpannya di session atau URL.
} else {
    set_flash_message("Gagal mereset password user ID " . htmlspecialchars($id) . ". Silakan coba lagi.", "error");
    header("Location: data_user.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Random · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("../admin/menu.php"); ?>
    <div class="container">
        <h2>Reset Password Random</h2>
        <?php if ($newPass): ?>
            <p>Password baru untuk user ID <b><?= htmlspecialchars($id) ?></b> adalah:</p>
            <p class="success" style="font-size: 1.2em;"><b><?= htmlspecialchars($newPass) ?></b></p>
            <p>Harap catat password ini dan berikan kepada user. Password ini tidak akan ditampilkan lagi.</p>
        <?php else: ?>
            <?php display_flash_message(); // Menampilkan pesan error jika gagal
            ?>
        <?php endif; ?>
        <br>
        <a href="data_user.php">Kembali ke Data User</a>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>