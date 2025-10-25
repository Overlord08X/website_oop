<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori.php");

$db = new DBConnection();
$db->init_connect();
$kategoriRepo = new Kategori($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID kategori tidak valid.", "error");
    header("Location: data_kategori.php");
    exit;
}

$kategori = $kategoriRepo->getKategoriById($id);
if (!$kategori) {
    set_flash_message("Kategori tidak ditemukan.", "error");
    header("Location: data_kategori.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if (empty($nama_kategori)) {
        set_flash_message("Nama kategori tidak boleh kosong.", "error");
    } else {
        if ($kategoriRepo->updateKategori($id, $nama_kategori)) {
            set_flash_message("Kategori berhasil diperbarui.", "success");
            header("Location: data_kategori.php");
            exit;
        } else {
            set_flash_message("Gagal memperbarui kategori. Mungkin nama sudah ada.", "error");
        }
    }
}

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Edit Kategori</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama_kategori">Nama Kategori:</label>
            <input type="text" id="nama_kategori" name="nama_kategori" value="<?= htmlspecialchars($kategori['nama_kategori']) ?>" required>

            <button type="submit">Update</button>
            <a href="data_kategori.php">Batal</a>
        </form>
    </div>
</body>

</html>