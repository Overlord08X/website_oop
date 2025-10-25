<?php
require_once(__DIR__ . "/dbconnection.php");

class Pemilik
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    // cek email sudah terdaftar di tabel user
    public function existsByEmail(string $email): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM user WHERE email = ? LIMIT 1");
        if (!$stmt) return false;
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        return (bool)$res->fetch_assoc();
    }

    /**
     * Registrasi pemilik:
     * - insert ke user
     * - insert ke pemilik
     */
    public function createPemilik(
        string $nama,
        string $email,
        string $password,
        string $no_wa,
        string $alamat
    ): ?int {
        $this->conn->begin_transaction();

        try {
            // 1) insert ke user
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $stmtUser = $this->conn->prepare(
                "INSERT INTO user (nama, email, password) VALUES (?, ?, ?)"
            );
            if (!$stmtUser) throw new Exception($this->conn->error);
            $stmtUser->bind_param("sss", $nama, $email, $hashed);
            if (!$stmtUser->execute()) throw new Exception($stmtUser->error);

            $iduser = $this->conn->insert_id;

            // 2) insert ke pemilik
            $stmtPemilik = $this->conn->prepare(
                "INSERT INTO pemilik (no_wa, alamat, iduser) VALUES (?, ?, ?)"
            );
            if (!$stmtPemilik) throw new Exception($this->conn->error);
            $stmtPemilik->bind_param("ssi", $no_wa, $alamat, $iduser);
            if (!$stmtPemilik->execute()) throw new Exception($stmtPemilik->error);

            $idpemilik = $this->conn->insert_id;

            $this->conn->commit();
            return $idpemilik;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("createPemilik failed: " . $e->getMessage());
            // sementara tampilkan langsung biar kelihatan
            echo "DEBUG: " . $e->getMessage();
            return null;
        }
    }

    public function getAllPemilikWithDetails(): array
    {
        $sql = "
            SELECT p.idpemilik, p.no_wa, p.alamat, u.iduser, u.nama, u.email
            FROM pemilik p
            JOIN user u ON p.iduser = u.iduser
            ORDER BY u.nama
        ";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("getAllPemilikWithDetails failed: " . $this->conn->error);
        return [];
    }

    public function getPemilikById(int $idpemilik): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT p.idpemilik, p.no_wa, p.alamat, u.iduser, u.nama, u.email
            FROM pemilik p
            JOIN user u ON p.iduser = u.iduser
            WHERE p.idpemilik = ?
        ");
        if (!$stmt) {
            error_log("getPemilikById prepare failed: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $idpemilik);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function updatePemilik(int $idpemilik, int $iduser, string $nama, string $email, string $no_wa, string $alamat): bool
    {
        $this->conn->begin_transaction();
        try {
            // 1. Update tabel user
            $stmtUser = $this->conn->prepare("UPDATE user SET nama = ?, email = ? WHERE iduser = ?");
            if (!$stmtUser) throw new Exception("Prepare failed (user): " . $this->conn->error);
            $stmtUser->bind_param("ssi", $nama, $email, $iduser);
            if (!$stmtUser->execute()) throw new Exception("Execute failed (user): " . $stmtUser->error);

            // 2. Update tabel pemilik
            $stmtPemilik = $this->conn->prepare("UPDATE pemilik SET no_wa = ?, alamat = ? WHERE idpemilik = ?");
            if (!$stmtPemilik) throw new Exception("Prepare failed (pemilik): " . $this->conn->error);
            $stmtPemilik->bind_param("ssi", $no_wa, $alamat, $idpemilik);
            if (!$stmtPemilik->execute()) throw new Exception("Execute failed (pemilik): " . $stmtPemilik->error);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("updatePemilik failed: " . $e->getMessage());
            return false;
        }
    }

    public function deletePemilik(int $idpemilik): bool
    {
        // Ambil iduser dari idpemilik
        $stmt = $this->conn->prepare("SELECT iduser FROM pemilik WHERE idpemilik = ?");
        if (!$stmt) {
            error_log("deletePemilik (select iduser) prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $idpemilik);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if (!$row) {
            error_log("deletePemilik: idpemilik $idpemilik not found.");
            return false; // Pemilik tidak ditemukan
        }
        $iduser = $row['iduser'];

        $this->conn->begin_transaction();
        try {
            // Hapus semua pet yang dimiliki oleh pemilik ini
            $stmtPet = $this->conn->prepare("DELETE FROM pet WHERE idpemilik = ?");
            if (!$stmtPet) throw new Exception("Prepare failed (pet): " . $this->conn->error);
            $stmtPet->bind_param("i", $idpemilik);
            if (!$stmtPet->execute()) throw new Exception("Execute failed (pet): " . $stmtPet->error);

            // Hapus dari tabel pemilik
            $stmtPemilik = $this->conn->prepare("DELETE FROM pemilik WHERE idpemilik = ?");
            if (!$stmtPemilik) throw new Exception("Prepare failed (pemilik): " . $this->conn->error);
            $stmtPemilik->bind_param("i", $idpemilik);
            if (!$stmtPemilik->execute()) throw new Exception("Execute failed (pemilik): " . $stmtPemilik->error);

            // Hapus dari tabel user
            $stmtUser = $this->conn->prepare("DELETE FROM user WHERE iduser = ?");
            if (!$stmtUser) throw new Exception("Prepare failed (user): " . $this->conn->error);
            $stmtUser->bind_param("i", $iduser);
            if (!$stmtUser->execute()) throw new Exception("Execute failed (user): " . $stmtUser->error);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("deletePemilik failed for idpemilik $idpemilik: " . $e->getMessage());
            return false;
        }
    }
}
