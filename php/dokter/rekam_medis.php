<?php
require_once(__DIR__ . "/../auth.php");
require_dokter_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../rekam_medis_.php");

$db = new DBConnection();
$db->init_connect();
$rekamRepo = new RekamMedis($db);

$allRekamMedis = $rekamRepo->getAllRekamMedis();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Rekam Medis</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Daftar Semua Rekam Medis</h2>
        <?php display_flash_message(); ?>

        <?php if (!empty($allRekamMedis)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID RM</th>
                        <th>Waktu</th>
                        <th>Anamnesa</th>
                        <th>Diagnosa</th>
                        <th>Perawat Pemeriksa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allRekamMedis as $rm): ?>
                        <tr>
                            <td><?= htmlspecialchars($rm['idrekam_medis']) ?></td>
                            <td><?= htmlspecialchars($rm['created_at']) ?></td>
                            <td><?= htmlspecialchars(substr($rm['anamnesa'], 0, 70)) ?>...</td>
                            <td><?= htmlspecialchars(substr($rm['diagnosa'], 0, 70)) ?>...</td>
                            <td><?= htmlspecialchars($rm['dokter_pemeriksa']) ?></td>
                            <td>
                                <a href="detail_rekam_medis.php?id=<?= $rm['idrekam_medis'] ?>">Lihat Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada rekam medis yang tercatat.</p>
        <?php endif; ?>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>