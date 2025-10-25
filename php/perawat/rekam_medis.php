<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . "/../auth.php");
require_perawat_login(); // Memastikan hanya perawat yang bisa mengakses

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../rekam_medis_.php"); // Perhatikan nama file class RekamMedis
require_once(__DIR__ . "/../temu_dokter_.php"); // Perhatikan nama file class TemuDokter

$db = new DBConnection();
$db->init_connect();

$rekamRepo = new RekamMedis($db);
$temuRepo = new TemuDokter($db);

$errors = [];
$success = "";

// Ambil idrole_user dari user login (sesuai tabel role_user)
// Pastikan user yang login memiliki role Perawat (role_id = 3)
$current_user_id = $_SESSION['user']['iduser'];
$stmt = $db->prepare("SELECT idrole_user FROM role_user WHERE iduser = ? AND idrole = 3 AND status = 1 LIMIT 1");
if (!$stmt) {
    set_flash_message("Terjadi kesalahan sistem saat mengambil role user.", "error");
    header("Location: home_perawat.php");
    exit;
}
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();
$idrole_user = $row['idrole_user'] ?? 0;

if ($idrole_user == 0) {
    set_flash_message("Anda tidak memiliki role Perawat aktif untuk membuat rekam medis.", "error");
    header("Location: home_perawat.php");
    exit;
}

// Inisialisasi variabel untuk form
$idreservasi_dokter_selected = $_POST['idreservasi_dokter'] ?? '';
$anamnesa_val = $_POST['anamnesa'] ?? '';
$temuan_klinis_val = $_POST['temuan_klinis'] ?? '';
$diagnosa_val = $_POST['diagnosa'] ?? '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idreservasi_dokter = (int)($idreservasi_dokter_selected);
    $anamnesa = trim($anamnesa_val);
    $temuan_klinis = trim($temuan_klinis_val);
    $diagnosa = trim($diagnosa_val);

    if ($idreservasi_dokter == 0) {
        $errors[] = "Reservasi wajib dipilih.";
    }
    if ($anamnesa === '') {
        $errors[] = "Anamnesa wajib diisi.";
    }

    if (empty($errors)) {
        // Cek apakah reservasi sudah memiliki rekam medis
        $stmtCheckRM = $db->prepare("SELECT COUNT(*) FROM rekam_medis WHERE idreservasi_dokter = ?");
        if (!$stmtCheckRM) {
            $errors[] = "Gagal memeriksa rekam medis yang sudah ada.";
        } else {
            $stmtCheckRM->bind_param("i", $idreservasi_dokter);
            $stmtCheckRM->execute();
            $countRM = $stmtCheckRM->get_result()->fetch_row()[0];
            $stmtCheckRM->close();

            if ($countRM > 0) {
                $errors[] = "Reservasi ini sudah memiliki rekam medis.";
            } else {
                if ($rekamRepo->createRekamMedis($idreservasi_dokter, $anamnesa, $temuan_klinis, $diagnosa, $idrole_user)) {
                    set_flash_message("Rekam medis berhasil disimpan.", "success");
                    // Redirect untuk mencegah resubmission
                    header("Location: rekam_medis.php");
                    exit;
                } else {
                    set_flash_message("Gagal menyimpan rekam medis.", "error");
                }
            }
        }
    } else {
        foreach ($errors as $error_msg) {
            set_flash_message($error_msg, "error");
        }
    }
}

// Ambil daftar temu dokter hari ini
$daftarHariIni = $temuRepo->getDaftarHariIni();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rekam Medis</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Buat Rekam Medis</h2>

        <?php display_flash_message(); ?>

        <form method="post">
            <label for="idreservasi_dokter">Reservasi:</label><br>
            <select id="idreservasi_dokter" name="idreservasi_dokter" required>
                <option value="">-- Pilih Reservasi --</option>
                <?php foreach ($daftarHariIni as $d): ?>
                    <option value="<?= $d['idreservasi_dokter'] ?>"
                        <?= ($idreservasi_dokter_selected == $d['idreservasi_dokter']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['nama_pet'] . " (" . $d['nama_pemilik'] . ") - No. Urut: " . $d['no_urut']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="anamnesa">Anamnesa:</label><br>
            <textarea id="anamnesa" name="anamnesa" rows="5" required><?= htmlspecialchars($anamnesa_val) ?></textarea><br><br>

            <label for="temuan_klinis">Temuan Klinis:</label><br>
            <textarea id="temuan_klinis" name="temuan_klinis" rows="5"><?= htmlspecialchars($temuan_klinis_val) ?></textarea><br><br>

            <label for="diagnosa">Diagnosa:</label><br>
            <textarea id="diagnosa" name="diagnosa" rows="5"><?= htmlspecialchars($diagnosa_val) ?></textarea><br><br>

            <button type="submit">Simpan Rekam Medis</button>
            <a href="home_perawat.php">Batal</a>
        </form>

        <h3>Daftar Rekam Medis Hari Ini</h3>
        <?php
        $allRekamMedis = $rekamRepo->getAllRekamMedis(); // Ambil semua rekam medis
        if (!empty($allRekamMedis)):
        ?>
            <table border="1" cellpadding="5">
                <tr>
                    <th>ID RM</th>
                    <th>Waktu</th>
                    <th>Anamnesa</th>
                    <th>Diagnosa</th>
                    <th>Dokter Pemeriksa</th>
                    <th>Reservasi ID</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($allRekamMedis as $rm): ?>
                    <tr>
                        <td><?= htmlspecialchars($rm['idrekam_medis']) ?></td>
                        <td><?= htmlspecialchars($rm['created_at']) ?></td>
                        <td><?= htmlspecialchars(substr($rm['anamnesa'], 0, 50)) ?>...</td>
                        <td><?= htmlspecialchars(substr($rm['diagnosa'], 0, 50)) ?>...</td>
                        <td><?= htmlspecialchars($rm['dokter_pemeriksa']) ?></td>
                        <td><?= htmlspecialchars($rm['idreservasi_dokter']) ?></td>
                        <td>
                            <a href="detail_rekam_medis.php?id=<?= $rm['idrekam_medis'] ?>">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Belum ada rekam medis yang tercatat.</p>
        <?php endif; ?>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>