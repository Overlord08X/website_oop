<?php
require_once(__DIR__ . "/../auth.php");
require_perawat_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../rekam_medis_.php");
require_once(__DIR__ . "/../detail_rekam_medis.php");
require_once(__DIR__ . "/../kode_tindakan_terapi.php");

$db = new DBConnection();
$db->init_connect();

$rekamMedisRepo = new RekamMedis($db);
$detailRepo = new DetailRekamMedis($db);
$tindakanRepo = new KodeTindakanTerapi($db);

$id_rm = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_rm) {
    set_flash_message("ID Rekam Medis tidak valid.", "error");
    header("Location: rekam_medis.php");
    exit;
}

// Ambil data utama rekam medis
$rekamMedis = $rekamMedisRepo->getRekamMedisById($id_rm);
if (!$rekamMedis) {
    set_flash_message("Rekam Medis tidak ditemukan.", "error");
    header("Location: rekam_medis.php");
    exit;
}

// Ambil semua tindakan terapi yang tersedia untuk dropdown
$allTindakan = $tindakanRepo->getAll();

// Logika untuk Tambah/Edit/Hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_detail = filter_input(INPUT_POST, 'id_detail', FILTER_VALIDATE_INT);
    $id_tindakan = filter_input(INPUT_POST, 'id_tindakan', FILTER_VALIDATE_INT);
    $detail_text = trim($_POST['detail'] ?? '');

    try {
        if ($action === 'add' && $id_tindakan) {
            if ($detailRepo->create($id_rm, $id_tindakan, $detail_text)) {
                set_flash_message("Tindakan terapi berhasil ditambahkan.", "success");
            } else {
                throw new Exception("Gagal menambahkan tindakan.");
            }
        } elseif ($action === 'edit' && $id_detail && $id_tindakan) {
            if ($detailRepo->update($id_detail, $id_tindakan, $detail_text)) {
                set_flash_message("Tindakan terapi berhasil diperbarui.", "success");
            } else {
                throw new Exception("Gagal memperbarui tindakan.");
            }
        }
    } catch (Exception $e) {
        set_flash_message($e->getMessage(), "error");
    }

    header("Location: detail_rekam_medis.php?id=" . $id_rm);
    exit;
}

// Logika Hapus (via GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id_detail'])) {
    $id_detail_to_delete = filter_input(INPUT_GET, 'id_detail', FILTER_VALIDATE_INT);
    if ($id_detail_to_delete) {
        if ($detailRepo->delete($id_detail_to_delete)) {
            set_flash_message("Tindakan berhasil dihapus.", "success");
        } else {
            set_flash_message("Gagal menghapus tindakan.", "error");
        }
    }
    header("Location: detail_rekam_medis.php?id=" . $id_rm);
    exit;
}

// Ambil daftar detail tindakan untuk rekam medis ini
$detailList = $detailRepo->getByRekamMedisId($id_rm);

// Data untuk form edit
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id_detail'])) {
    $edit_data = $detailRepo->getById((int)$_GET['id_detail']);
}

$db->close_connection();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Rekam Medis #<?= $id_rm ?></title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("./menu.php"); ?>

    <div class="container">
        <h2>Detail Rekam Medis #<?= $id_rm ?></h2>
        <a href="rekam_medis.php">&larr; Kembali ke Daftar Rekam Medis</a>

        <div style="background: #f2f2f2; padding: 15px; border-radius: 8px; margin-top: 20px;">
            <p><strong>Waktu:</strong> <?= htmlspecialchars($rekamMedis['created_at']) ?></p>
            <p><strong>Anamnesa:</strong> <?= nl2br(htmlspecialchars($rekamMedis['anamnesa'])) ?></p>
            <p><strong>Temuan Klinis:</strong> <?= nl2br(htmlspecialchars($rekamMedis['temuan_klinis'])) ?></p>
            <p><strong>Diagnosa:</strong> <?= nl2br(htmlspecialchars($rekamMedis['diagnosa'])) ?></p>
        </div>

        <hr style="margin: 30px 0;">

        <h3>Tindakan Terapi</h3>
        <?php display_flash_message(); ?>

        <!-- Form Tambah/Edit -->
        <form method="post" action="detail_rekam_medis.php?id=<?= $id_rm ?>">
            <h4><?= $edit_data ? 'Edit' : 'Tambah' ?> Tindakan</h4>
            <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add' ?>">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id_detail" value="<?= $edit_data['iddetail_rekam_medis'] ?>">
            <?php endif; ?>

            <label for="id_tindakan">Tindakan Terapi:</label>
            <select name="id_tindakan" id="id_tindakan" required>
                <option value="">-- Pilih Tindakan --</option>
                <?php foreach ($allTindakan as $tindakan): ?>
                    <option value="<?= $tindakan['idkode_tindakan_terapi'] ?>"
                        <?= ($edit_data && $edit_data['idkode_tindakan_terapi'] == $tindakan['idkode_tindakan_terapi']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tindakan['kode'] . ' - ' . $tindakan['deskripsi_tindakan_terapi']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="detail">Detail Tambahan:</label>
            <textarea name="detail" id="detail" rows="3"><?= htmlspecialchars($edit_data['detail'] ?? '') ?></textarea>

            <button type="submit"><?= $edit_data ? 'Update' : 'Tambah' ?> Tindakan</button>
            <?php if ($edit_data): ?>
                <a href="detail_rekam_medis.php?id=<?= $id_rm ?>">Batal Edit</a>
            <?php endif; ?>
        </form>

        <!-- Tabel Daftar Tindakan -->
        <h4 style="margin-top: 30px;">Daftar Tindakan yang Diberikan</h4>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi Tindakan</th>
                    <th>Detail Tambahan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detailList)): ?>
                    <?php foreach ($detailList as $detail): ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['kode']) ?></td>
                            <td><?= htmlspecialchars($detail['deskripsi_tindakan_terapi']) ?></td>
                            <td><?= nl2br(htmlspecialchars($detail['detail'])) ?></td>
                            <td>
                                <a href="detail_rekam_medis.php?id=<?= $id_rm ?>&action=edit&id_detail=<?= $detail['iddetail_rekam_medis'] ?>">Edit</a> |
                                <a href="detail_rekam_medis.php?id=<?= $id_rm ?>&action=delete&id_detail=<?= $detail['iddetail_rekam_medis'] ?>" onclick="return confirm('Yakin hapus tindakan ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Belum ada tindakan terapi yang ditambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>