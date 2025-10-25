<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pemilik.php");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DBConnection();
    $db->init_connect();
    $pemilikRepo = new Pemilik($db);

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_wa = trim($_POST['no_wa'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama)) $errors[] = "Nama wajib diisi.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid.";
    if (empty($password)) $errors[] = "Password wajib diisi.";
    if (empty($no_wa)) $errors[] = "No. WA wajib diisi.";
    if (empty($alamat)) $errors[] = "Alamat wajib diisi.";

    if ($pemilikRepo->existsByEmail($email)) {
        $errors[] = "Email sudah terdaftar.";
    }

    if (empty($errors)) {
        $result = $pemilikRepo->createPemilik($nama, $email, $password, $no_wa, $alamat);
        if ($result) {
            set_flash_message("Pemilik baru berhasil ditambahkan.", "success");
            header("Location: data_pemilik.php");
            exit;
        } else {
            set_flash_message("Gagal menambahkan pemilik. Silakan coba lagi.", "error");
        }
    } else {
        foreach ($errors as $err) {
            set_flash_message($err, "error");
        }
    }
    $db->close_connection();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Pemilik</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Tambah Pemilik Baru</h2>
        <?php display_flash_message(); ?>
        <form action="tambah_pemilik.php" method="post">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="no_wa">No. WhatsApp:</label>
            <input type="text" id="no_wa" name="no_wa" required value="<?= htmlspecialchars($_POST['no_wa'] ?? '') ?>">
            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
            <button type="submit">Simpan</button>
            <a href="data_pemilik.php">Batal</a>
        </form>
    </div>
</body>

</html>