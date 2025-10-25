<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");

$db = new DBConnection();
$db->init_connect();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_role = trim($_POST['nama_role']);

    if (empty($nama_role)) {
        set_flash_message("Nama role tidak boleh kosong.", "error");
    } else {
        $stmt = $conn->prepare("INSERT INTO role (nama_role) VALUES (?)");
        if (!$stmt) {
            error_log("Prepare failed in tambah_role.php (insert role): " . $conn->error);
            set_flash_message("Gagal menyiapkan statement. Silakan coba lagi.", "error");
        } else {
            $stmt->bind_param("s", $nama_role);
            if ($stmt->execute()) {
                set_flash_message("Role berhasil ditambahkan.", "success");
                header("Location: manajemen_role.php");
                exit;
            } else {
                error_log("Execute failed in tambah_role.php (insert role): " . $stmt->error);
                set_flash_message("Gagal menambah role. Silakan coba lagi.", "error");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Role · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("../admin/menu.php"); ?>

    <div class="container">
        <h2>Tambah Role</h2>
        <?php display_flash_message(); ?>
        <form method="POST">
            <label for="nama_role">Nama Role:</label><br>
            <input type="text" id="nama_role" name="nama_role" required>
            <br><br>
            <button type="submit">Simpan</button>
            <a href="manajemen_role.php">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>