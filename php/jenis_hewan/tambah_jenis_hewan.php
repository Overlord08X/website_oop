<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../jenis_hewan.php");

$db = new DBConnection();
$db->init_connect();
$jenisHewanRepo = new JenisHewan($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_jenis_hewan = trim($_POST['nama_jenis_hewan']);

    if (empty($nama_jenis_hewan)) {
        set_flash_message("Nama jenis hewan tidak boleh kosong.", "error");
    } else {
        if ($jenisHewanRepo->createJenisHewan($nama_jenis_hewan)) {
            set_flash_message("Jenis hewan berhasil ditambahkan.", "success");
            header("Location: manajemen_jenis_hewan.php");
            exit;
        } else {
            set_flash_message("Gagal menambah jenis hewan. Mungkin nama jenis sudah ada.", "error");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jenis Hewan · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Tambah Jenis Hewan</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama_jenis_hewan">Nama Jenis Hewan:</label><br>
            <input type="text" id="nama_jenis_hewan" name="nama_jenis_hewan" required><br><br>
            <button type="submit">Simpan</button>
            <a href="manajemen_jenis_hewan.php">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>