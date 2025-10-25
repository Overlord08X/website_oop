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

$id = (int)$_GET['id'];
$rasHewan = $rasHewanRepo->getRasHewanById($id);
$allJenisHewan = $jenisHewanRepo->getAllJenisHewan();

if (!$rasHewan) {
    set_flash_message("Ras hewan tidak ditemukan.", "error");
    header("Location: manajemen_ras_hewan.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ras = trim($_POST['nama_ras']);
    $idjenis_hewan = (int)$_POST['idjenis_hewan'];

    if (empty($nama_ras) || $idjenis_hewan <= 0) {
        set_flash_message("Nama ras dan jenis hewan tidak boleh kosong.", "error");
    } else {
        if (!$jenisHewanRepo->isJenisHewanExists($idjenis_hewan)) {
            set_flash_message("Jenis hewan yang dipilih tidak valid.", "error");
        } else {
            if ($rasHewanRepo->updateRasHewan($id, $nama_ras, $idjenis_hewan)) {
                set_flash_message("Ras hewan berhasil diperbarui.", "success");
                header("Location: manajemen_ras_hewan.php");
                exit;
            } else {
                set_flash_message("Gagal memperbarui ras hewan. Mungkin ras ini sudah ada untuk jenis hewan yang dipilih.", "error");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ras Hewan · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Edit Ras Hewan</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama_ras">Nama Ras Hewan:</label><br>
            <input type="text" id="nama_ras" name="nama_ras" value="<?= htmlspecialchars($rasHewan['nama_ras']) ?>" required><br><br>

            <label for="idjenis_hewan">Jenis Hewan:</label><br>
            <select id="idjenis_hewan" name="idjenis_hewan" required>
                <option value="">-- Pilih Jenis Hewan --</option>
                <?php if (!empty($allJenisHewan)): ?>
                    <?php foreach ($allJenisHewan as $jenis): ?>
                        <option value="<?= htmlspecialchars($jenis['idjenis_hewan']) ?>"
                            <?= ($jenis['idjenis_hewan'] == $rasHewan['idjenis_hewan']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($jenis['nama_jenis_hewan']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" disabled>Tidak ada jenis hewan tersedia. Tambahkan dulu.</option>
                <?php endif; ?>
            </select><br><br>

            <button type="submit">Update</button>
            <a href="manajemen_ras_hewan.php">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>