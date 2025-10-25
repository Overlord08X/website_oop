<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");
require_once(__DIR__ . "/../role_user.php");

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);
$roleUserRepo = new RoleUser($db);

$result = $userRepo->getAllUsers();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen User · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php
    include("./menu.php");
    ?>

    <div class="container">
        <h2>Data User</h2>
        <?php display_flash_message(); ?>
        <a href="tambah_user.php">+ Tambah User</a>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role Aktif</th>
                <th>Aksi</th>
            </tr>
            <?php if ($result['status'] === "success" && !empty($result['data'])): ?>
                <?php foreach ($result['data'] as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['iduser']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <?php
                            // Ambil role untuk user ini
                            $userRoles = $roleUserRepo->getRolesById($row['iduser']);
                            $activeRolesDisplay = [];
                            foreach ($userRoles as $role) {
                                if ($role['status'] == 1) {
                                    $activeRolesDisplay[] = '<span class="role-badge active">' . htmlspecialchars($role['nama_role']) . '</span>';
                                } else {
                                    $activeRolesDisplay[] = '<span class="role-badge inactive">' . htmlspecialchars($role['nama_role']) . ' (Nonaktif)</span>';
                                }
                            }
                            echo empty($activeRolesDisplay) ? 'Tidak ada role' : implode(' ', $activeRolesDisplay);
                            ?>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?= $row['iduser'] ?>">Edit</a> |
                            <a href="hapus_user.php?id=<?= $row['iduser'] ?>" onclick="return confirm('Yakin hapus user ini?')">Hapus</a> |
                            <a href="reset_pass_default.php?id=<?= $row['iduser'] ?>" onclick="return confirm('Yakin reset password ke default (<?= DEFAULT_ADMIN_PASSWORD ?>)?')">Reset Default</a> |
                            <a href="reset_pass_random.php?id=<?= $row['iduser'] ?>" onclick="return confirm('Yakin reset password ke random? Password baru akan ditampilkan.')">Reset Random</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5"><?= $result['status'] === "error" ? "Error: " . htmlspecialchars($result['message']) : "Belum ada user." ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>