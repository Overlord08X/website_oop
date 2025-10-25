<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../auth.php");
require_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../temu_dokter_.php");
require_once(__DIR__ . "/../pet.php");

$db = new DBConnection();
$db->init_connect();

$temu = new TemuDokter($db);
$petRepo = new Pet($db);

$petList = $petRepo->getAllPet(); // ambil semua pet beserta pemilik

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idpet = $_POST['idpet'] ?? 0;
    if ($idpet == 0) $errors[] = "Pet wajib dipilih.";
    else {
        // Ambil idrole_user dari user login (sesuai tabel role_user)
        $current_user_id = $_SESSION['user']['iduser'];
        $stmt = $db->prepare("SELECT idrole_user FROM role_user WHERE iduser = ? AND status = 1 LIMIT 1");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        $idrole_user = $row['idrole_user'] ?? 0;

        if ($idrole_user == 0) {
            $errors[] = "User tidak memiliki akses untuk mendaftar temu dokter.";
        } else {
            if ($temu->daftarPertemuan((int)$idpet, (int)$idrole_user)) {
                set_flash_message("Berhasil daftar temu dokter.", "success");
                header("Location: temu_dokter.php");
                exit;
            } else {
                $errors[] = "Gagal daftar. Silakan coba lagi.";
            }
        }
    }
}

$daftarHariIni = $temu->getDaftarHariIni();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Temu Dokter</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h1>Daftar Temu Dokter</h1>

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

        <form action="" method="post">
            <label>Pilih Pet:</label><br>
            <select name="idpet" required>
                <option value="">-- Pilih Pet --</option>
                <?php foreach ($petList as $pet) : ?>
                    <option value="<?= $pet['idpet'] ?>">
                        <?= htmlspecialchars($pet['nama'] . " (" . $pet['nama_pemilik'] . ")") ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>
            <button type="submit" class="btn-master">Daftar Temu Dokter</button>
        </form>

        <h2>Daftar Hari Ini</h2>
        <table border="1" cellpadding="5">
            <tr>
                <th>No Urut</th>
                <th>Nama Pet</th>
                <th>Nama Pemilik</th>
                <th>Waktu Daftar</th>
                <th>Status</th>
            </tr>
            <?php foreach ($daftarHariIni as $d) : ?>
                <tr>
                    <td><?= $d['no_urut'] ?></td>
                    <td><?= htmlspecialchars($d['nama_pet']) ?></td>
                    <td><?= htmlspecialchars($d['nama_pemilik']) ?></td>
                    <td><?= $d['waktu_daftar'] ?></td>
                    <td><?= $d['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>

</html>
<?php $db->close_connection(); ?>