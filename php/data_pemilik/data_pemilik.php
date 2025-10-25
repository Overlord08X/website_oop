<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pemilik.php");
require_once(__DIR__ . "/../config.php");

$db = new DBConnection();
$db->init_connect();
$pemilikRepo = new Pemilik($db);

$pemilikList = $pemilikRepo->getAllPemilikWithDetails();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Pemilik · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Data Pemilik</h2>
        <?php display_flash_message(); ?>
        <a href="tambah_pemilik.php" class="btn">+ Tambah Pemilik</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. WA</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pemilikList)): ?>
                    <?php foreach ($pemilikList as $pemilik): ?>
                        <tr>
                            <td><?= htmlspecialchars($pemilik['idpemilik']) ?></td>
                            <td><?= htmlspecialchars($pemilik['nama']) ?></td>
                            <td><?= htmlspecialchars($pemilik['email']) ?></td>
                            <td><?= htmlspecialchars($pemilik['no_wa']) ?></td>
                            <td><?= htmlspecialchars($pemilik['alamat']) ?></td>
                            <td>
                                <a href="edit_pemilik.php?id=<?= $pemilik['idpemilik'] ?>">Edit</a> |
                                <a href="hapus_pemilik.php?id=<?= $pemilik['idpemilik'] ?>" onclick="return confirm('Yakin hapus pemilik ini? Semua data pet yang terkait juga akan terhapus.')">Hapus</a> |
                                <a href="reset_pass.php?id=<?= $pemilik['iduser'] ?>" onclick="return confirm('Yakin reset password ke default (<?= DEFAULT_ADMIN_PASSWORD ?>)?')">Reset Pass</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Belum ada data pemilik.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>