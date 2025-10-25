<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kode_tindakan_terapi.php");
require_once(__DIR__ . "/../kategori.php");
require_once(__DIR__ . "/../kategori_klinis.php");

$db = new DBConnection();
$db->init_connect();

$repo = new KodeTindakanTerapi($db);
$kategoriRepo = new Kategori($db);
$kategoriKlinisRepo = new KategoriKlinis($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID tidak valid.", "error");
    header("Location: data_kode_tindakan_terapi.php");
    exit;
}

$item = $repo->getById($id);
if (!$item) {
    set_flash_message("Data tidak ditemukan.", "error");
    header("Location: data_kode_tindakan_terapi.php");
    exit;
}

$allKategori = $kategoriRepo->getAllKategori();
$allKategoriKlinis = $kategoriKlinisRepo->getAllKategoriKlinis();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $idkategori = (int)($_POST['idkategori'] ?? 0);
    $idkategori_klinis = (int)($_POST['idkategori_klinis'] ?? 0);

    if (empty($kode) || empty($deskripsi) || $idkategori <= 0 || $idkategori_klinis <= 0) {
        set_flash_message("Semua field wajib diisi.", "error");
    } else {
        if ($repo->update($id, $kode, $deskripsi, $idkategori, $idkategori_klinis)) {
            set_flash_message("Data berhasil diperbarui.", "success");
            header("Location: data_kode_tindakan_terapi.php");
            exit;
        } else {
            set_flash_message("Gagal memperbarui data.", "error");
        }
    }
}

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Kode Tindakan Terapi · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Edit Kode Tindakan Terapi</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="kode">Kode:</label>
            <input type="text" id="kode" name="kode" required maxlength="5" value="<?= htmlspecialchars($item['kode']) ?>">

            <label for="deskripsi">Deskripsi Tindakan:</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" required><?= htmlspecialchars($item['deskripsi_tindakan_terapi']) ?></textarea>

            <label for="idkategori">Kategori:</label>
            <select id="idkategori" name="idkategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($allKategori as $kategori): ?>
                    <option value="<?= htmlspecialchars($kategori['idkategori']) ?>" <?= ($kategori['idkategori'] == $item['idkategori']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kategori['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="idkategori_klinis">Kategori Klinis:</label>
            <select id="idkategori_klinis" name="idkategori_klinis" required>
                <option value="">-- Pilih Kategori Klinis --</option>
                <?php foreach ($allKategoriKlinis as $kategoriKlinis): ?>
                    <option value="<?= htmlspecialchars($kategoriKlinis['idkategori_klinis']) ?>" <?= ($kategoriKlinis['idkategori_klinis'] == $item['idkategori_klinis']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kategoriKlinis['nama_kategori_klinis']) ?> (<?= htmlspecialchars($kategoriKlinis['nama_kategori']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Update</button>
            <a href="data_kode_tindakan_terapi.php">Batal</a>
        </form>
    </div>
</body>

</html>