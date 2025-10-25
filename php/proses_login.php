<?php
require_once("auth.php");
require_once("dbconnection.php");

$db = new DBConnection();
$db->init_connect();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $db->prepare("SELECT iduser, nama, email, password FROM user WHERE email = ?");
if (!$stmt) {
    error_log("Prepare failed in proses_login.php (user select): " . $db->getConnection()->error);
    set_flash_message("Terjadi kesalahan sistem. Silakan coba lagi.", "error");
    header("Location: ./login.php");
    exit;
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $stmt_role = $db->prepare("
            SELECT ru.idrole, r.nama_role
            FROM role_user ru
            JOIN role r ON ru.idrole = r.idrole
            WHERE ru.iduser = ? AND ru.status = 1
        ");
        if (!$stmt_role) {
            error_log("Prepare failed in proses_login.php (role select): " . $db->getConnection()->error);
            set_flash_message("Terjadi kesalahan sistem. Silakan coba lagi.", "error");
            header("Location: ./login.php");
            exit;
        }
        $stmt_role->bind_param("i", $row['iduser']);
        $stmt_role->execute();
        $role_result = $stmt_role->get_result();

        if ($role_result && $role_result->num_rows > 0) {
            $role_row = $role_result->fetch_assoc();

            session_regenerate_id(true);

            $_SESSION['user'] = [
                'iduser'    => $row['iduser'],
                'nama'      => $row['nama'],
                'email'     => $row['email'],
                'role_id'   => $role_row['idrole'],
                'role_name' => $role_row['nama_role'],
                'logged_in' => true
            ];

            switch ($role_row['idrole']) {
                case 1:
                    header("Location: ./admin/home_admin.php");
                    exit;
                case 2:
                    header("Location: ./dokter/home_dokter.php");
                    exit;
                case 3:
                    header("Location: ./perawat/home_perawat.php");
                    exit;
                case 4:
                    header("Location: ./resepsionis/home_resepsionis.php");
                    exit;
                default:
                    set_flash_message("Role tidak dikenali.", "error");
                    header("Location: ./login.php");
                    exit;
            }
        } else {
            set_flash_message("Role user tidak aktif atau tidak ditemukan!", "error");
            header("Location: ./login.php");
            exit;
        }
    } else {
        set_flash_message("Password salah!", "error");
        header("Location: ./login.php");
        exit;
    }
} else {
    set_flash_message("Username tidak ditemukan!", "error");
    header("Location: ./login.php");
    exit;
}

$db->close_connection();
