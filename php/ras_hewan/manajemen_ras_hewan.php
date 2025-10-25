<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../ras_hewan.php");
require_once(__DIR__ . "/../jenis_hewan.php");

$db = new DBConnection();
$db->init_connect();
$rasHewanRepo = new RasHewan($db);
$jenisHewanRepo = new JenisHewan($db);

$allRasHewan = $rasHewanRepo->getAllRasHewan();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Ras Hewan · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Ras Hewan</h2>
        <?php display_flash_message(); ?>
        <p>
            <a href="tambah_ras_hewan.php" class="btn">+ Tambah Ras Hewan</a>
        </p>

        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Nama Ras</th>
                <th>Jenis Hewan</th>
                <th>Aksi</th>
            </tr>
            <?php if (!empty($allRasHewan)): ?>
                <?php foreach ($allRasHewan as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['idras_hewan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_ras']) ?></td>
                        <td><?= htmlspecialchars($row['nama_jenis_hewan']) ?></td>
                        <td>
                            <a href="edit_ras_hewan.php?id=<?= $row['idras_hewan'] ?>">Edit</a> |
                            <a href="hapus_ras_hewan.php?id=<?= $row['idras_hewan'] ?>" onclick="return confirm('Yakin hapus ras hewan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Belum ada ras hewan.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>