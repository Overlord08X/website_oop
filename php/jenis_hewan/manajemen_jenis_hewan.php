<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../jenis_hewan.php");

$db = new DBConnection();
$db->init_connect();
$jenisHewanRepo = new JenisHewan($db);

$allJenisHewan = $jenisHewanRepo->getAllJenisHewan();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Jenis Hewan · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Jenis Hewan</h2>
        <?php display_flash_message(); ?>
        <p>
            <a href="tambah_jenis_hewan.php" class="btn">+ Tambah Jenis Hewan</a>
        </p>

        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nama Jenis</th>
                <th>Aksi</th>
            </tr>
            <?php if (!empty($allJenisHewan)): ?>
                <?php foreach ($allJenisHewan as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idjenis_hewan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_jenis_hewan']) ?></td>
                        <td>
                            <a href="edit_jenis_hewan.php?id=<?= $row['idjenis_hewan'] ?>">Edit</a> |
                            <a href="hapus_jenis_hewan.php?id=<?= $row['idjenis_hewan'] ?>" onclick="return confirm('Yakin hapus jenis hewan ini? Ini akan menghapus semua ras hewan yang terkait.')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Belum ada jenis hewan.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>