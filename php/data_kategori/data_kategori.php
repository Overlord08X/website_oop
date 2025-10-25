<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori.php");

$db = new DBConnection();
$db->init_connect();
$kategoriRepo = new Kategori($db);

$kategoriList = $kategoriRepo->getAllKategori();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Kategori · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Kategori</h2>
        <?php display_flash_message(); ?>
        <a href="tambah_kategori.php" class="btn">+ Tambah Kategori</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kategoriList as $kategori): ?>
                    <tr>
                        <td><?= htmlspecialchars($kategori['idkategori']) ?></td>
                        <td><?= htmlspecialchars($kategori['nama_kategori']) ?></td>
                        <td>
                            <a href="edit_kategori.php?id=<?= $kategori['idkategori'] ?>">Edit</a> |
                            <a href="hapus_kategori.php?id=<?= $kategori['idkategori'] ?>" onclick="return confirm('Yakin hapus kategori ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>