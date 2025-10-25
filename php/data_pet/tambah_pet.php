<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pet.php");

$db = new DBConnection();
$db->init_connect();
$petRepo = new Pet($db);

$pemilikList = $petRepo->getAllPemilik();
$rasHewanList = $petRepo->getAllRasHewan();

$errors = [];
$nama = $tanggal_lahir = $warna_tanda = $jenis_kelamin = $idpemilik = $idras_hewan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $warna_tanda = trim($_POST['warna_tanda'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $idpemilik = (int)($_POST['idpemilik'] ?? 0);
    $idras_hewan = (int)($_POST['idras_hewan'] ?? 0);

    if (empty($nama)) $errors[] = "Nama pet wajib diisi.";
    if (empty($tanggal_lahir)) $errors[] = "Tanggal lahir wajib diisi.";
    if (!in_array($jenis_kelamin, ['M', 'F'])) $errors[] = "Jenis kelamin tidak valid.";
    if ($idpemilik <= 0) $errors[] = "Pemilik wajib dipilih.";
    if ($idras_hewan <= 0) $errors[] = "Ras hewan wajib dipilih.";

    if (empty($errors)) {
        if ($petRepo->createPet($nama, $tanggal_lahir, $warna_tanda, $jenis_kelamin, $idpemilik, $idras_hewan)) {
            set_flash_message("Data pet baru berhasil ditambahkan.", "success");
            header("Location: data_pet.php");
            exit;
        } else {
            set_flash_message("Gagal menyimpan data pet. Terjadi kesalahan sistem.", "error");
        }
    } else {
        foreach ($errors as $err) {
            set_flash_message($err, "error");
        }
    }
}

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Pet - Admin</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Tambah Data Pet</h2>
        <?php display_flash_message(); ?>

        <form action="tambah_pet.php" method="post">
            <label for="nama">Nama Pet:</label>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>" required>

            <label for="warna_tanda">Warna / Tanda Khas:</label>
            <input type="text" id="warna_tanda" name="warna_tanda" value="<?= htmlspecialchars($warna_tanda) ?>">

            <label>Jenis Kelamin:</label>
            <select name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="M" <?= $jenis_kelamin == 'M' ? 'selected' : '' ?>>Jantan</option>
                <option value="F" <?= $jenis_kelamin == 'F' ? 'selected' : '' ?>>Betina</option>
            </select>

            <label>Pemilik:</label>
            <select name="idpemilik" required>
                <option value="">-- Pilih Pemilik --</option>
                <?php foreach ($pemilikList as $pemilik) : ?>
                    <option value="<?= $pemilik['idpemilik'] ?>" <?= $idpemilik == $pemilik['idpemilik'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pemilik['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Ras Hewan:</label>
            <select name="idras_hewan" required>
                <option value="">-- Pilih Ras --</option>
                <?php foreach ($rasHewanList as $ras) : ?>
                    <option value="<?= $ras['idras_hewan'] ?>" <?= $idras_hewan == $ras['idras_hewan'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ras['nama_ras'] . " (" . $ras['nama_jenis_hewan'] . ")") ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Simpan</button>
            <a href="data_pet.php">Batal</a>
        </form>
    </div>
</body>

</html>