<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pet.php");

$db = new DBConnection();
$db->init_connect();
$petRepo = new Pet($db);

$petList = $petRepo->getAllPetDetails();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manajemen Data Pet · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Data Pet</h2>
        <?php display_flash_message(); ?>
        <a href="tambah_pet.php" class="btn">+ Tambah Pet</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pet</th>
                    <th>Jenis Hewan</th>
                    <th>Ras</th>
                    <th>Pemilik</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($petList)): ?>
                    <?php foreach ($petList as $pet): ?>
                        <tr>
                            <td><?= htmlspecialchars($pet['idpet']) ?></td>
                            <td><?= htmlspecialchars($pet['nama']) ?></td>
                            <td><?= htmlspecialchars($pet['nama_jenis_hewan']) ?></td>
                            <td><?= htmlspecialchars($pet['nama_ras']) ?></td>
                            <td><?= htmlspecialchars($pet['nama_pemilik']) ?></td>
                            <td>
                                <a href="edit_pet.php?id=<?= $pet['idpet'] ?>">Edit</a> |
                                <a href="hapus_pet.php?id=<?= $pet['idpet'] ?>" onclick="return confirm('Yakin hapus data pet ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Belum ada data pet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>