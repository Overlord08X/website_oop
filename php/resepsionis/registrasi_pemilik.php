<?php
require_once(__DIR__ . "/../auth.php");
require_login();

if ($_SESSION['user']['role_id'] != 4) {
    set_flash_message("Anda tidak memiliki akses ke halaman ini.", "error");
    header("Location: ../login.php");
    exit;
}

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../pemilik.php");

$db = new DBConnection();
$db->init_connect();
$pemilikRepo = new Pemilik($db);

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$nama = "";
$email = "";
$password = "";
$no_wa_raw = "";
$no_wa = "";
$alamat = "";

/**
 * Normalisasi nomor WA ke format +62xxxxxxxxxx
 */
function normalize_whatsapp(string $raw): string
{
    $digits = preg_replace('/\D+/', '', $raw);
    if ($digits === '') return '';

    if (strpos($digits, '0') === 0) {
        $digits = '62' . substr($digits, 1);
    } elseif (strpos($digits, '8') === 0) {
        $digits = '62' . $digits;
    }
    return '+' . $digits;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $errors[] = "Permintaan tidak valid (CSRF).";
    }

    // Ambil input
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $no_wa_raw = trim($_POST['no_wa'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    // Validasi
    if ($nama === '') $errors[] = "Nama wajib diisi.";
    if ($email === '') {
        $errors[] = "Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if ($password === '') {
        $errors[] = "Password wajib diisi.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }
    if ($no_wa_raw === '') {
        $errors[] = "Nomor WhatsApp wajib diisi.";
    } else {
        $no_wa = normalize_whatsapp($no_wa_raw);
        if (!preg_match('/^\+62\d{8,13}$/', $no_wa)) {
            $errors[] = "Nomor WhatsApp tidak valid. Contoh: 0812xxxx atau +62812xxxx.";
        }
    }
    if ($alamat === '') $errors[] = "Alamat wajib diisi.";

    if (empty($errors)) {
        if ($pemilikRepo->existsByEmail($email)) {
            $errors[] = "Email sudah terdaftar.";
        } else {
            $created = $pemilikRepo->createPemilik($nama, $email, $password, $no_wa, $alamat);
            if ($created !== null) {
                set_flash_message("Registrasi pemilik berhasil.", "success");
                header("Location: home_resepsionis.php");
                exit;
            } else {
                $errors[] = "Gagal menyimpan data pemilik. Mungkin email sudah terdaftar atau terjadi kesalahan sistem.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Registrasi Pemilik - Resepsionis</title>
    <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
    <?php include("./menu.php"); ?>
    <div class="container">
        <h1>Registrasi Pemilik</h1>

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
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <label for="nama">Nama:</label><br />
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required /><br />

            <label for="email">Email:</label><br />
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required /><br />

            <label for="password">Password:</label><br />
            <input type="password" id="password" name="password" required /><br />

            <label for="no_wa">Nomor WhatsApp:</label><br />
            <input type="text" id="no_wa" name="no_wa" value="<?= htmlspecialchars($no_wa_raw) ?>" required /><br />

            <label for="alamat">Alamat:</label><br />
            <textarea id="alamat" name="alamat" required><?= htmlspecialchars($alamat) ?></textarea><br />

            <button type="submit" class="btn-master">Daftar</button>
            <a href="home_resepsionis.php" class="btn-master">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>