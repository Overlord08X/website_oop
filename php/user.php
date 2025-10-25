<?php
require_once(__DIR__ . "/dbconnection.php");
require_once(__DIR__ . "/config.php");

class User
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT u.iduser, u.nama, u.email 
                FROM user u 
                LEFT JOIN pemilik p ON u.iduser = p.iduser WHERE p.iduser IS NULL";
        $res = $this->conn->query($sql);
        if ($res) {
            return ["status" => "success", "data" => $res->fetch_all(MYSQLI_ASSOC)];
        }
        error_log("Error fetching all users: " . $this->conn->error); // Log error
        return ["status" => "error", "message" => "Gagal mengambil data user."];
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT iduser, nama, email FROM user WHERE iduser=?");
        if (!$stmt) {
            error_log("Prepare failed getUserById: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function createUser(string $nama, string $email, string $password): ?int
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO user (nama, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            error_log("Prepare failed createUser: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("sss", $nama, $email, $hashedPassword);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            error_log("Execute failed createUser: " . $stmt->error);
            return null;
        }
    }


    public function updateUser(int $id, string $nama, string $email): bool
    {
        $stmt = $this->conn->prepare("UPDATE user SET nama=?, email=? WHERE iduser=?");
        if (!$stmt) {
            error_log("Prepare failed updateUser: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ssi", $nama, $email, $id);
        return $stmt->execute();
    }

    public function deleteUser(int $id): bool
    {
        // Mulai transaksi
        $this->conn->begin_transaction();

        try {
            // Hapus data di role_user yang terkait dengan user ini
            $stmt1 = $this->conn->prepare("DELETE FROM role_user WHERE iduser = ?");
            if (!$stmt1) {
                throw new Exception("Prepare failed (hapus role_user): " . $this->conn->error);
            }
            $stmt1->bind_param("i", $id);
            if (!$stmt1->execute()) {
                throw new Exception("Execute failed (hapus role_user): " . $stmt1->error);
            }

            // Hapus data user
            $stmt2 = $this->conn->prepare("DELETE FROM user WHERE iduser = ?");
            if (!$stmt2) {
                throw new Exception("Prepare failed (hapus user): " . $this->conn->error);
            }
            $stmt2->bind_param("i", $id);
            if (!$stmt2->execute()) {
                throw new Exception("Execute failed (hapus user): " . $stmt2->error);
            }

            // Commit transaksi
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback jika ada error
            $this->conn->rollback();
            error_log("Error deleteUser for ID $id: " . $e->getMessage()); // Log error
            return false;
        }
    }

    public function resetPasswordDefault(int $id): bool
    {
        $defaultPass = password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE user SET password=? WHERE iduser=?");
        if (!$stmt) {
            error_log("Prepare failed resetPasswordDefault: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("si", $defaultPass, $id);
        return $stmt->execute();
    }

    public function resetPasswordRandom(int $id): ?string
    {
        $newPass = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE user SET password=? WHERE iduser=?");
        if (!$stmt) {
            error_log("Prepare failed resetPasswordRandom: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("si", $hash, $id);
        if ($stmt->execute()) {
            return $newPass;
        }
        error_log("Execute failed resetPasswordRandom: " . $stmt->error); // Log error
        return null;
    }
}
