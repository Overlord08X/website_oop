<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");
require_once(__DIR__ . "/../role_user.php");

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);
$roleUserRepo = new RoleUser($db);

$id = (int)$_GET['id'];
$user = $userRepo->getUserById($id);

if (!$user) {
    set_flash_message("User tidak ditemukan.", "error");
    header("Location: data_user.php");
    exit;
}

// Ambil semua role di sistem
$allRoles = $roleUserRepo->getAllRoles();

// Ambil role yang dimiliki user ini
$userRoles = $roleUserRepo->getRolesById($id);
$activeUserRoleIds = [];
foreach ($userRoles as $role) {
    if ($role['status'] == 1) {
        $activeUserRoleIds[] = $role['idrole'];
    }
}

// Tentukan general status
$generalStatus = 1;
if (!empty($userRoles)) {
    foreach ($userRoles as $role) {
        if ($role['status'] == 0) {
            $generalStatus = 0;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $selectedRoles = $_POST['roles'] ?? [];
    $selectedRoles = array_map('intval', $selectedRoles);
    $newGeneralStatus = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    if (empty($nama) || empty($email)) {
        set_flash_message("Nama dan Email tidak boleh kosong.", "error");
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message("Format email tidak valid.", "error");
    } else {
        $db->getConnection()->begin_transaction();
        try {
            if (!$userRepo->updateUser($id, $nama, $email)) {
                throw new Exception("Gagal memperbarui data user.");
            }

            if (!$roleUserRepo->updateUserRoles($id, $selectedRoles, $newGeneralStatus)) {
                throw new Exception("Gagal memperbarui role user.");
            }

            $db->getConnection()->commit();
            set_flash_message("User berhasil diperbarui.", "success");
            header("Location: data_user.php");
            exit;
        } catch (Exception $e) {
            $db->getConnection()->rollback();
            error_log("Error updating user ID $id: " . $e->getMessage());
            set_flash_message("Gagal memperbarui user: " . $e->getMessage(), "error");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Edit User</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama">Nama:</label><br>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

            <label>Role:</label><br>
            <?php foreach ($allRoles as $role): ?>
                <input type="checkbox"
                    id="role_<?= $role['idrole'] ?>"
                    name="roles[]"
                    value="<?= $role['idrole'] ?>"
                    <?= in_array($role['idrole'], $activeUserRoleIds) ? 'checked' : '' ?>>
                <label for="role_<?= $role['idrole'] ?>"><?= htmlspecialchars($role['nama_role']) ?></label><br>
            <?php endforeach; ?>
            <br>

            <label for="status">Status (berlaku untuk semua role yang dipilih):</label><br>
            <select id="status" name="status">
                <option value="1" <?= $generalStatus == 1 ? 'selected' : '' ?>>Aktif</option>
                <option value="0" <?= $generalStatus == 0 ? 'selected' : '' ?>>Nonaktif</option>
            </select><br><br>

            <button type="submit">Update</button>
            <a href="data_user.php">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>