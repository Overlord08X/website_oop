<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori_klinis.php");
require_once(__DIR__ . "/../kategori.php");

$db = new DBConnection();
$db->init_connect();
$repo = new KategoriKlinis($db);
$kategoriRepo = new Kategori($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID tidak valid.", "error");
    header("Location: data_kategori_klinis.php");
    exit;
}

$item = $repo->getKategoriKlinisById($id);
if (!$item) {
    set_flash_message("Data tidak ditemukan.", "error");
    header("Location: data_kategori_klinis.php");
    exit;
}

$allKategori = $kategoriRepo->getAllKategori();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kategori_klinis'] ?? '');
    $idkategori = (int)($_POST['idkategori'] ?? 0);

    if (empty($nama) || $idkategori <= 0) {
        set_flash_message("Nama dan Induk Kategori wajib diisi.", "error");
    } else {
        if ($repo->updateKategoriKlinis($id, $nama, $idkategori)) {
            set_flash_message("Data berhasil diperbarui.", "success");
            header("Location: data_kategori_klinis.php");
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
    <title>Edit Kategori Klinis · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Edit Kategori Klinis</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama_kategori_klinis">Nama Kategori Klinis:</label>
            <input type="text" id="nama_kategori_klinis" name="nama_kategori_klinis" value="<?= htmlspecialchars($item['nama_kategori_klinis']) ?>" required>

            <label for="idkategori">Induk Kategori:</label>
            <select id="idkategori" name="idkategori" required>
                <option value="">-- Pilih Induk Kategori --</option>
                <?php foreach ($allKategori as $kategori): ?>
                    <option value="<?= htmlspecialchars($kategori['idkategori']) ?>" <?= ($kategori['idkategori'] == $item['idkategori']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kategori['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Update</button>
            <a href="data_kategori_klinis.php">Batal</a>
        </form>
    </div>
</body>

</html>