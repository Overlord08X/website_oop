<?php
require_once(__DIR__ . "/../../dbconnection.php");

class User
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAllUsers(): array
    {
        $sql = "SELECT iduser, nama, email FROM user";
        $res = $this->conn->query($sql);
        if ($res) {
            return ["status" => "success", "data" => $res->fetch_all(MYSQLI_ASSOC)];
        }
        return ["status" => "error", "message" => $this->conn->error];
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT iduser, nama, email FROM user WHERE iduser=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function createUser(string $nama, string $email, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO user(nama,email,password) VALUES(?,?,?)");
        $stmt->bind_param("sss", $nama, $email, $hash);
        return $stmt->execute();
    }

    public function updateUser(int $id, string $nama, string $email): bool
    {
        $stmt = $this->conn->prepare("UPDATE user SET nama=?, email=? WHERE iduser=?");
        $stmt->bind_param("ssi", $nama, $email, $id);
        return $stmt->execute();
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM user WHERE iduser=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function resetPasswordDefault(int $id): bool
    {
        $defaultPass = password_hash("123456", PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE user SET password=? WHERE iduser=?");
        $stmt->bind_param("si", $defaultPass, $id);
        return $stmt->execute();
    }

    public function resetPasswordRandom(int $id): string
    {
        $newPass = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, 8);
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE user SET password=? WHERE iduser=?");
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();
        return $newPass; // kirim password plain ke admin untuk disampaikan ke user
    }
}
