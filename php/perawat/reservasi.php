<?php
require_once(__DIR__ . "/../auth.php");
require_perawat_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../temu_dokter_.php");

$db = new DBConnection();
$db->init_connect();
$temuRepo = new TemuDokter($db);

$daftarReservasi = $temuRepo->getDaftarHariIni();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Reservasi · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Daftar Reservasi Hari Ini</h2>
        <?php display_flash_message(); ?>

        <table>
            <thead>
                <tr>
                    <th>No. Urut</th>
                    <th>Nama Pasien</th>
                    <th>Pemilik</th>
                    <th>Dokter</th>
                    <th>Status Rekam Medis</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($daftarReservasi)): ?>
                    <?php foreach ($daftarReservasi as $reservasi): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservasi['no_urut']) ?></td>
                            <td><?= htmlspecialchars($reservasi['nama_pet']) ?></td>
                            <td><?= htmlspecialchars($reservasi['nama_pemilik']) ?></td>
                            <td><?= htmlspecialchars($reservasi['nama_dokter']) ?></td>
                            <td><?= $reservasi['idrekam_medis'] ? 'Sudah Dibuat' : 'Belum Dibuat' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Tidak ada reservasi untuk hari ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>