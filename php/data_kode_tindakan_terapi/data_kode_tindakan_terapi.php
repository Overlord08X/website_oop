<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kode_tindakan_terapi.php");

$db = new DBConnection();
$db->init_connect();
$repo = new KodeTindakanTerapi($db);

$list = $repo->getAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Kode Tindakan Terapi · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Kode Tindakan Terapi</h2>
        <?php display_flash_message(); ?>
        <a href="tambah_kode_tindakan_terapi.php" class="btn">+ Tambah Kode Tindakan</a>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi</th>
                    <th>Kategori</th>
                    <th>Kategori Klinis</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['kode']) ?></td>
                        <td><?= nl2br(htmlspecialchars(substr($item['deskripsi_tindakan_terapi'], 0, 100))) . '...' ?></td>
                        <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                        <td><?= htmlspecialchars($item['nama_kategori_klinis']) ?></td>
                        <td>
                            <a href="edit_kode_tindakan_terapi.php?id=<?= $item['idkode_tindakan_terapi'] ?>">Edit</a> |
                            <a href="hapus_kode_tindakan_terapi.php?id=<?= $item['idkode_tindakan_terapi'] ?>" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>