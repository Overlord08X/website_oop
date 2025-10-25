<?php
require_once(__DIR__ . "/dbconnection.php");

class JenisHewan
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAllJenisHewan(): array
    {
        $sql = "SELECT idjenis_hewan, nama_jenis_hewan FROM jenis_hewan ORDER BY nama_jenis_hewan ASC";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("Error getAllJenisHewan: " . $this->conn->error);
        return [];
    }

    public function getJenisHewanById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT idjenis_hewan, nama_jenis_hewan FROM jenis_hewan WHERE idjenis_hewan=?");
        if (!$stmt) {
            error_log("Prepare failed getJenisHewanById: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function isJenisHewanExists(int $id): bool
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM jenis_hewan WHERE idjenis_hewan = ?");
        if (!$stmt) {
            error_log("Prepare failed isJenisHewanExists: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows === 1;
    }


    public function createJenisHewan(string $nama_jenis_hewan): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO jenis_hewan (nama_jenis_hewan) VALUES (?)");
        if (!$stmt) {
            error_log("Prepare failed createJenisHewan: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("s", $nama_jenis_hewan);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error code
                error_log("Duplicate entry for jenis_hewan: " . $nama_jenis_hewan);
                return false; // Return false for duplicate entry
            }
            error_log("Execute failed createJenisHewan: " . $e->getMessage());
            return false;
        }
    }

    public function updateJenisHewan(int $id, string $nama_jenis_hewan): bool
    {
        $stmt = $this->conn->prepare("UPDATE jenis_hewan SET nama_jenis_hewan=? WHERE idjenis_hewan=?");
        if (!$stmt) {
            error_log("Prepare failed updateJenisHewan: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("si", $nama_jenis_hewan, $id);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error code
                error_log("Duplicate entry for jenis_hewan update: " . $nama_jenis_hewan);
                return false; // Return false for duplicate entry
            }
            error_log("Execute failed updateJenisHewan: " . $e->getMessage());
            return false;
        }
    }

    public function deleteJenisHewan(int $id): bool
    {
        try {
            // Cek apakah jenis hewan ini digunakan di tabel ras_hewan
            $stmtCheck = $this->conn->prepare("SELECT 1 FROM ras_hewan WHERE idjenis_hewan = ? LIMIT 1");
            if (!$stmtCheck) {
                error_log("Prepare failed deleteJenisHewan (check ras_hewan): " . $this->conn->error);
                return false;
            }
            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            if ($stmtCheck->get_result()->num_rows > 0) {
                // Jenis hewan ini masih digunakan, tidak bisa dihapus
                throw new Exception("Jenis hewan ini tidak dapat dihapus karena masih digunakan oleh satu atau lebih ras hewan.");
            }

            $stmt = $this->conn->prepare("DELETE FROM jenis_hewan WHERE idjenis_hewan=?");
            if (!$stmt) {
                error_log("Prepare failed deleteJenisHewan: " . $this->conn->error);
                return false;
            }
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error in deleteJenisHewan: " . $e->getMessage());
            throw $e; // Lemparkan kembali eksepsi untuk ditangani oleh pemanggil
        }
    }
}
