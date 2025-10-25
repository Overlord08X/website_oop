<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pemilik.php");

$db = new DBConnection();
$db->init_connect();
$pemilikRepo = new Pemilik($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash_message("ID pemilik tidak valid.", "error");
    header("Location: data_pemilik.php");
    exit;
}

$pemilik = $pemilikRepo->getPemilikById($id);
if (!$pemilik) {
    set_flash_message("Pemilik tidak ditemukan.", "error");
    header("Location: data_pemilik.php");
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_wa = trim($_POST['no_wa'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama)) $errors[] = "Nama wajib diisi.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid.";
    if (empty($no_wa)) $errors[] = "No. WA wajib diisi.";
    if (empty($alamat)) $errors[] = "Alamat wajib diisi.";

    // Cek duplikasi email jika email diubah
    if ($email !== $pemilik['email'] && $pemilikRepo->existsByEmail($email)) {
        $errors[] = "Email sudah terdaftar.";
    }

    if (empty($errors)) {
        $success = $pemilikRepo->updatePemilik($id, $pemilik['iduser'], $nama, $email, $no_wa, $alamat);
        if ($success) {
            set_flash_message("Data pemilik berhasil diperbarui.", "success");
            header("Location: data_pemilik.php");
            exit;
        } else {
            set_flash_message("Gagal memperbarui data pemilik.", "error");
        }
    } else {
        foreach ($errors as $err) {
            set_flash_message($err, "error");
        }
    }
    // Refresh data pemilik untuk ditampilkan di form jika ada error
    $pemilik = array_merge($pemilik, $_POST);
}

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Pemilik</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h2>Edit Pemilik: <?= htmlspecialchars($pemilik['nama']) ?></h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($pemilik['nama']) ?>">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($pemilik['email']) ?>">
            <label for="no_wa">No. WhatsApp:</label>
            <input type="text" id="no_wa" name="no_wa" required value="<?= htmlspecialchars($pemilik['no_wa']) ?>">
            <label for="alamat">Alamat:</label>
            <textarea id="alamat" name="alamat" rows="3" required><?= htmlspecialchars($pemilik['alamat']) ?></textarea>
            <button type="submit">Update</button>
            <a href="data_pemilik.php">Batal</a>
        </form>
    </div>
</body>

</html>