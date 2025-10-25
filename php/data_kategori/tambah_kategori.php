<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../kategori.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if (empty($nama_kategori)) {
        set_flash_message("Nama kategori tidak boleh kosong.", "error");
    } else {
        $db = new DBConnection();
        $db->init_connect();
        $kategoriRepo = new Kategori($db);

        if ($kategoriRepo->createKategori($nama_kategori)) {
            set_flash_message("Kategori berhasil ditambahkan.", "success");
            header("Location: data_kategori.php");
            exit;
        } else {
            set_flash_message("Gagal menambah kategori. Mungkin nama kategori sudah ada.", "error");
        }
        $db->close_connection();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Tambah Kategori Baru</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama_kategori">Nama Kategori:</label>
            <input type="text" id="nama_kategori" name="nama_kategori" required>

            <button type="submit">Simpan</button>
            <a href="data_kategori.php">Batal</a>
        </form>
    </div>
</body>

</html>