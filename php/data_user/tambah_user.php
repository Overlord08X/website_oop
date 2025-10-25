<?php
require_once(__DIR__ . "/../auth.php");
require_admin_login();

require_once(__DIR__ . "/../dbconnection.php");
require_once(__DIR__ . "/../user.php");
require_once(__DIR__ . "/../role_user.php");

$db = new DBConnection();
$db->init_connect();
$userRepo = new User($db);
$roleUserRepo = new RoleUser($db);
$roles = $roleUserRepo->getAllRoles(); // <-- RoleUser harus sudah di-inisialisasi dulu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & sanitasi input
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $repass = $_POST['repassword'] ?? '';
    $selectedRoles = $_POST['roles'] ?? []; // array dari checkbox (boleh multi)
    if (!is_array($selectedRoles)) {
        $selectedRoles = [$selectedRoles];
    }

    // Validasi sederhana
    if ($nama === '' || $email === '' || $pass === '' || $repass === '') {
        set_flash_message("Semua field harus diisi.", "error");
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message("Format email tidak valid.", "error");
    } else if ($pass !== $repass) {
        set_flash_message("Password tidak sama.", "error");
    } else {
        // Buat user dulu -> perlu createUser mengembalikan ID user baru
        $userId = $userRepo->createUser($nama, $email, $pass);
        if ($userId) {
            // Assign role (jika ada)
            foreach ($selectedRoles as $rid) {
                $rid = (int)$rid;
                if ($rid > 0) {
                    $roleUserRepo->assignRoleToUser($userId, $rid);
                }
            }

            set_flash_message("User berhasil ditambahkan.", "success");
            header("Location: data_user.php");
            exit;
        } else {
            set_flash_message("Gagal menambah user. Mungkin email sudah terdaftar.", "error");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User · RSHP Unair</title>
    <link rel="stylesheet" href="../../aset/css/style.css">
</head>

<body>
    <?php include("../admin/menu.php"); ?>
    <div class="container">
        <h2>Tambah User</h2>
        <?php display_flash_message(); ?>
        <form method="post">
            <label for="nama">Nama:</label><br>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="repassword">Retype Password:</label><br>
            <input type="password" id="repassword" name="repassword" required><br><br>

            <label>Role (centang untuk memberikan role):</label><br>
            <?php foreach ($roles as $role): ?>
                <input type="checkbox" id="role_<?= $role['idrole'] ?>" name="roles[]" value="<?= $role['idrole'] ?>">
                <label for="role_<?= $role['idrole'] ?>"><?= htmlspecialchars($role['nama_role']) ?></label><br>
            <?php endforeach; ?>
            <br>

            <button type="submit">Simpan</button>
            <a href="data_user.php">Batal</a>
        </form>
    </div>
</body>

</html>
<?php $db->close_connection(); ?>