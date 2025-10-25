<?php
require_once(__DIR__ . "/../auth.php");
require_dokter_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../rekam_medis_.php");
require_once(__DIR__ . "/../detail_rekam_medis.php");

$db = new DBConnection();
$db->init_connect();

$rekamMedisRepo = new RekamMedis($db);
$detailRepo = new DetailRekamMedis($db);

$id_rm = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_rm) {
    set_flash_message("ID Rekam Medis tidak valid.", "error");
    header("Location: rekam_medis.php");
    exit;
}

// Ambil data utama rekam medis
$rekamMedis = $rekamMedisRepo->getRekamMedisById($id_rm);
if (!$rekamMedis) {
    set_flash_message("Rekam Medis tidak ditemukan.", "error");
    header("Location: rekam_medis.php");
    exit;
}

// Ambil daftar detail tindakan untuk rekam medis ini
$detailList = $detailRepo->getByRekamMedisId($id_rm);

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Rekam Medis #<?= $id_rm ?></title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Detail Rekam Medis #<?= $id_rm ?></h2>
        <a href="rekam_medis.php">&larr; Kembali ke Daftar Rekam Medis</a>

        <div style="background: #f2f2f2; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: left;">
            <h3>Informasi Umum</h3>
            <p><strong>Waktu Dibuat:</strong> <?= htmlspecialchars($rekamMedis['created_at']) ?></p>
            <p><strong>Perawat Pemeriksa:</strong> <?= htmlspecialchars($rekamMedis['dokter_pemeriksa']) ?></p>
            <hr>
            <p><strong>Anamnesa:</strong></p>
            <p><?= nl2br(htmlspecialchars($rekamMedis['anamnesa'])) ?></p>
            <p><strong>Temuan Klinis:</strong></p>
            <p><?= nl2br(htmlspecialchars($rekamMedis['temuan_klinis'])) ?></p>
            <p><strong>Diagnosa:</strong></p>
            <p><?= nl2br(htmlspecialchars($rekamMedis['diagnosa'])) ?></p>
        </div>

        <hr style="margin: 30px 0;">

        <h3>Tindakan Terapi yang Diberikan</h3>

        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi Tindakan</th>
                    <th>Detail Tambahan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detailList)): ?>
                    <?php foreach ($detailList as $detail): ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['kode']) ?></td>
                            <td><?= htmlspecialchars($detail['deskripsi_tindakan_terapi']) ?></td>
                            <td><?= nl2br(htmlspecialchars($detail['detail'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Tidak ada tindakan terapi yang tercatat untuk rekam medis ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>