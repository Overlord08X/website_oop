<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori_klinis.php");

$db = new DBConnection();
$db->init_connect();
$repo = new KategoriKlinis($db);

$list = $repo->getAllKategoriKlinis();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Kategori Klinis · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Kategori Klinis</h2>
        <?php display_flash_message(); ?>
        <a href="tambah_kategori_klinis.php" class="btn">+ Tambah Kategori Klinis</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori Klinis</th>
                    <th>Induk Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['idkategori_klinis']) ?></td>
                        <td><?= htmlspecialchars($item['nama_kategori_klinis']) ?></td>
                        <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                        <td>
                            <a href="edit_kategori_klinis.php?id=<?= $item['idkategori_klinis'] ?>">Edit</a> |
                            <a href="hapus_kategori_klinis.php?id=<?= $item['idkategori_klinis'] ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>