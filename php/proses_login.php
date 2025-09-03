<?php
session_start();
include_once("dbconnection.php");

$db = new DBConnection();
$db->init_connect(); // ✅ WAJIB, biar bisa query

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $db->prepare("SELECT * FROM user WHERE email = ?");
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
        $stmt_role->bind_param("i", $row['iduser']);
        $stmt_role->execute();
        $role_result = $stmt_role->get_result();

        if ($role_result && $role_result->num_rows > 0) {
            $role_row = $role_result->fetch_assoc();

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
                    echo "Anda adalah Dokter";
                    exit;
                case 3:
                    echo "Anda adalah Perawat";
                    exit;
                case 4:
                    echo "Anda adalah Resepsionis";
                    exit;
                default:
                    $_SESSION['flash_msg'] = "Role tidak dikenali.";
                    header("Location: ./login.php");
                    exit;
            }
        } else {
            $_SESSION['flash_msg'] = "Role user tidak aktif!";
            header("Location: ./login.php");
            exit;
        }
    } else {
        $_SESSION['flash_msg'] = "Password salah!";
        header("Location: ./login.php");
        exit;
    }
} else {
    $_SESSION['flash_msg'] = "Username tidak ditemukan!";
    header("Location: ./login.php");
    exit;
}

$db->close_connection();
