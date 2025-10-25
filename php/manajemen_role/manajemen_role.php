<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");

$db = new DBConnection();
$db->init_connect();
$conn = $db->getConnection(); // Dapatkan koneksi mysqli dari objek DBConnection

$result = $conn->query("SELECT * FROM role ORDER BY idrole ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Role · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Role</h2>
        <?php display_flash_message(); // Menampilkan flash message
        ?>
        <p>
            <a href="tambah_role.php" class="btn">+ Tambah Role</a>
        </p>

        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID Role</th>
                <th>Nama Role</th>
                <th>Aksi</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idrole']) ?></td>
                        <td><?= htmlspecialchars($row['nama_role']) ?></td>
                        <td>
                            <a href="edit_role.php?id=<?= (int)$row['idrole'] ?>">Edit</a> |
                            <a href="hapus_role.php?id=<?= (int)$row['idrole'] ?>" onclick="return confirm('Yakin hapus role ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Belum ada role.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>