<?php
require_once(__DIR__ . "/../auth.php");
require_perawat_login(); // Menggunakan otentikasi perawat

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
    <title>Manajemen Pasien · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Manajemen Data Pasien</h2>
        <?php display_flash_message(); ?>
        <p>Di sini Anda dapat mengelola data pasien (pet). Untuk menambah, mengedit, atau menghapus data, silakan hubungi Administrator.</p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Pasien</th>
                    <th>Jenis Hewan</th>
                    <th>Ras</th>
                    <th>Pemilik</th>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Belum ada data pasien.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>