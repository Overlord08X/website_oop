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
        if ($repo->create($kode, $deskripsi, $idkategori, $idkategori_klinis)) {
            set_flash_message("Kode tindakan terapi berhasil ditambahkan.", "success");
            header("Location: data_kode_tindakan_terapi.php");
            exit;
        } else {
            set_flash_message("Gagal menambah data. Silakan coba lagi.", "error");
        }
    }
}

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Kode Tindakan Terapi · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Tambah Kode Tindakan Terapi</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="kode">Kode:</label>
            <input type="text" id="kode" name="kode" required maxlength="5">

            <label for="deskripsi">Deskripsi Tindakan:</label>
            <textarea id="deskripsi" name="deskripsi" rows="5" required></textarea>

            <label for="idkategori">Kategori:</label>
            <select id="idkategori" name="idkategori" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($allKategori as $kategori): ?>
                    <option value="<?= htmlspecialchars($kategori['idkategori']) ?>">
                        <?= htmlspecialchars($kategori['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="idkategori_klinis">Kategori Klinis:</label>
            <select id="idkategori_klinis" name="idkategori_klinis" required>
                <option value="">-- Pilih Kategori Klinis --</option>
                <?php foreach ($allKategoriKlinis as $kategoriKlinis): ?>
                    <option value="<?= htmlspecialchars($kategoriKlinis['idkategori_klinis']) ?>">
                        <?= htmlspecialchars($kategoriKlinis['nama_kategori_klinis']) ?> (<?= htmlspecialchars($kategoriKlinis['nama_kategori']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Simpan</button>
            <a href="data_kode_tindakan_terapi.php">Batal</a>
        </form>
    </div>
</body>

</html>