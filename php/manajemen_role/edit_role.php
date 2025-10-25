<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");

$db = new DBConnection();
$db->init_connect();
$conn = $db->getConnection();

$id = (int)$_GET['id'];

// Ambil data role yang akan diedit
$stmt = $conn->prepare("SELECT idrole, nama_role FROM role WHERE idrole=?");
if (!$stmt) {
    error_log("Prepare failed in edit_role.php (select role): " . $conn->error);
    set_flash_message("Terjadi kesalahan sistem. Silakan coba lagi.", "error");
    header("Location: manajemen_role.php");
    exit;
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();

if (!$role) {
    set_flash_message("Role tidak ditemukan.", "error");
    header("Location: manajemen_role.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_role = trim($_POST['nama_role']);

    if (empty($nama_role)) {
        set_flash_message("Nama role tidak boleh kosong.", "error");
    } else {
        $stmt = $conn->prepare("UPDATE role SET nama_role=? WHERE idrole=?");
        if (!$stmt) {
            error_log("Prepare failed in edit_role.php (update role): " . $conn->error);
            set_flash_message("Gagal menyiapkan statement. Silakan coba lagi.", "error");
        } else {
            $stmt->bind_param("si", $nama_role, $id);
            if ($stmt->execute()) {
                set_flash_message("Role berhasil diperbarui.", "success");
                header("Location: manajemen_role.php");
                exit;
            } else {
                error_log("Execute failed in edit_role.php (update role): " . $stmt->error);
                set_flash_message("Gagal memperbarui role. Silakan coba lagi.", "error");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Role · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Edit Role</h2>
        <?php display_flash_message(); ?>
        <form method="POST">
            <label for="nama_role">Nama Role:</label><br>
            <input type="text" id="nama_role" name="nama_role" value="<?= htmlspecialchars($role['nama_role']) ?>" required>
            <br><br>
            <button type="submit">Update</button>
            <a href="manajemen_role.php">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>