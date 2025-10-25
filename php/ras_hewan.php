<?php
// website_oop/php/ras_hewan.php
require_once(__DIR__ . "/dbconnection.php");

class RasHewan
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAllRasHewan(): array
    {
        $sql = "SELECT rh.idras_hewan, rh.nama_ras, rh.idjenis_hewan, jh.nama_jenis_hewan
                FROM ras_hewan rh
                JOIN jenis_hewan jh ON rh.idjenis_hewan = jh.idjenis_hewan
                ORDER BY jh.nama_jenis_hewan ASC, rh.nama_ras ASC";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("Error getAllRasHewan: " . $this->conn->error);
        return [];
    }

    public function getRasHewanById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT rh.idras_hewan, rh.nama_ras, rh.idjenis_hewan, jh.nama_jenis_hewan
            FROM ras_hewan rh
            JOIN jenis_hewan jh ON rh.idjenis_hewan = jh.idjenis_hewan
            WHERE rh.idras_hewan=?
        ");
        if (!$stmt) {
            error_log("Prepare failed getRasHewanById: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function createRasHewan(string $nama_ras, int $idjenis_hewan): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO ras_hewan (nama_ras, idjenis_hewan) VALUES (?, ?)");
        if (!$stmt) {
            error_log("Prepare failed createRasHewan: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("si", $nama_ras, $idjenis_hewan);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error code
                error_log("Duplicate entry for ras_hewan: " . $nama_ras . " for jenis_hewan ID " . $idjenis_hewan);
                return false; // Return false for duplicate entry
            }
            error_log("Execute failed createRasHewan: " . $e->getMessage());
            return false;
        }
    }

    public function updateRasHewan(int $id, string $nama_ras, int $idjenis_hewan): bool
    {
        $stmt = $this->conn->prepare("UPDATE ras_hewan SET nama_ras=?, idjenis_hewan=? WHERE idras_hewan=?");
        if (!$stmt) {
            error_log("Prepare failed updateRasHewan: " . $this->conn->error);
            return false;;
        }
        $stmt->bind_param("sii", $nama_ras, $idjenis_hewan, $id);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error code
                error_log("Duplicate entry for ras_hewan update: " . $nama_ras . " for jenis_hewan ID " . $idjenis_hewan);
                return false; // Return false for duplicate entry
            }
            error_log("Execute failed updateRasHewan: " . $e->getMessage());
            return false;
        }
    }

    public function deleteRasHewan(int $id): bool
    {
        // Saat ini tidak ada tabel lain yang bergantung pada ras_hewan,
        // jadi bisa langsung dihapus. Jika nanti ada, perlu ditambahkan
        // pengecekan atau transaksi seperti di deleteUser/deleteJenisHewan.
        $stmt = $this->conn->prepare("DELETE FROM ras_hewan WHERE idras_hewan=?");
        if (!$stmt) {
            error_log("Prepare failed deleteRasHewan: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
