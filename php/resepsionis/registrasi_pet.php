<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../auth.php");
require_login();

if ($_SESSION['user']['role_id'] != 4) {
    set_flash_message("Anda tidak memiliki akses ke halaman ini.", "error");
    header("Location: ../login.php");
    exit;
}

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
    $idpemilik = $_POST['idpemilik'] ?? '';
    $idras_hewan = $_POST['idras_hewan'] ?? '';

    if ($nama === '') $errors[] = "Nama pet wajib diisi.";
    if ($tanggal_lahir === '') $errors[] = "Tanggal lahir wajib diisi.";
    if ($jenis_kelamin !== 'M' && $jenis_kelamin !== 'F') $errors[] = "Jenis kelamin tidak valid.";
    if ($idpemilik == '') $errors[] = "Pemilik wajib dipilih.";
    if ($idras_hewan == '') $errors[] = "Ras hewan wajib dipilih.";

    if (empty($errors)) {
        if ($petRepo->createPet($nama, $tanggal_lahir, $warna_tanda, $jenis_kelamin, $idpemilik, $idras_hewan)) {
            set_flash_message("Registrasi pet berhasil.", "success");
            header("Location: home_resepsionis.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data pet. Terjadi kesalahan sistem.";
            error_log("createPet gagal: " . $db->getConnection()->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Registrasi Pet - Resepsionis</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h1>Registrasi Pet</h1>

        <?php if (!empty($errors)) : ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err) : ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php display_flash_message(); ?>

        <form action="" method="post" autocomplete="off">
            <label for="nama">Nama Pet:</label><br>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required><br>

            <label for="tanggal_lahir">Tanggal Lahir:</label><br>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>" required><br>

            <label for="warna_tanda">Warna / Tanda Khas:</label><br>
            <input type="text" id="warna_tanda" name="warna_tanda" value="<?= htmlspecialchars($warna_tanda) ?>"><br>

            <label>Jenis Kelamin:</label><br>
            <select name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="M" <?= $jenis_kelamin == 'M' ? 'selected' : '' ?>>Jantan</option>
                <option value="F" <?= $jenis_kelamin == 'F' ? 'selected' : '' ?>>Betina</option>
            </select><br>

            <label>Pemilik:</label><br>
            <select name="idpemilik" required>
                <option value="">-- Pilih Pemilik --</option>
                <?php foreach ($pemilikList as $pemilik) : ?>
                    <option value="<?= $pemilik['idpemilik'] ?>" <?= $idpemilik == $pemilik['idpemilik'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($pemilik['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label>Ras Hewan:</label><br>
            <select name="idras_hewan" required>
                <option value="">-- Pilih Ras --</option>
                <?php foreach ($rasHewanList as $ras) : ?>
                    <option value="<?= $ras['idras_hewan'] ?>" <?= $idras_hewan == $ras['idras_hewan'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ras['nama_ras'] . " (" . $ras['nama_jenis_hewan'] . ")") ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <button type="submit" class="btn-master">Daftar</button>
            <a href="home_resepsionis.php" class="btn-master">Batal</a>
        </form>
    </div>

</body>

</html>

<?php $db->close_connection(); ?>