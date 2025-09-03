<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once(__DIR__ . "/../../dbconnection.php");
require_once(__DIR__ . "/../data_user/user.php");

// hanya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../../login.php");
    exit;
}

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);
$result = $userRepo->getAllUsers();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen User · RSHP Unair</title>
    <link rel="stylesheet" href="../../../aset/css/style.css">
</head>

<body>
    <?php
    include("../menu.php");
    ?>

    <div class="container">
        <h2>Data User</h2>
        <a href="tambah_user.php">+ Tambah User</a>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
            <?php if ($result['status'] === "success" && !empty($result['data'])): ?>
                <?php foreach ($result['data'] as $row): ?>
                    <tr>
                        <td><?= $row['iduser'] ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $row['iduser'] ?>">Edit</a> |
                            <a href="hapus_user.php?id=<?= $row['iduser'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a> |
                            <a href="reset_pass_default.php?id=<?= $row['iduser'] ?>">Reset Default</a> |
                            <a href="reset_pass_random.php?id=<?= $row['iduser'] ?>">Reset Random</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4"><?= $result['status'] === "error" ? "Error: " . htmlspecialchars($result['message']) : "Belum ada user." ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>