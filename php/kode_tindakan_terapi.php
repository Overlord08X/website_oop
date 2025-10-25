<?php
require_once(__DIR__ . "/dbconnection.php");

class KodeTindakanTerapi
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT 
                ktt.idkode_tindakan_terapi, 
                ktt.kode, 
                ktt.deskripsi_tindakan_terapi, 
                k.nama_kategori, 
                kk.nama_kategori_klinis
            FROM kode_tindakan_terapi ktt
            JOIN kategori k ON ktt.idkategori = k.idkategori
            JOIN kategori_klinis kk ON ktt.idkategori_klinis = kk.idkategori_klinis
            ORDER BY ktt.kode ASC
        ";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("Error getAll KodeTindakanTerapi: " . $this->conn->error);
        return [];
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT idkode_tindakan_terapi, kode, deskripsi_tindakan_terapi, idkategori, idkategori_klinis
            FROM kode_tindakan_terapi 
            WHERE idkode_tindakan_terapi = ?
        ");
        if (!$stmt) {
            error_log("Prepare failed getById KodeTindakanTerapi: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function create(string $kode, string $deskripsi, int $idkategori, int $idkategori_klinis): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO kode_tindakan_terapi (kode, deskripsi_tindakan_terapi, idkategori, idkategori_klinis) 
            VALUES (?, ?, ?, ?)
        ");
        if (!$stmt) {
            error_log("Prepare failed create KodeTindakanTerapi: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ssii", $kode, $deskripsi, $idkategori, $idkategori_klinis);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            error_log("Execute failed create KodeTindakanTerapi: " . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, string $kode, string $deskripsi, int $idkategori, int $idkategori_klinis): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE kode_tindakan_terapi SET 
                kode = ?, 
                deskripsi_tindakan_terapi = ?, 
                idkategori = ?, 
                idkategori_klinis = ? 
            WHERE idkode_tindakan_terapi = ?
        ");
        if (!$stmt) {
            error_log("Prepare failed update KodeTindakanTerapi: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ssiii", $kode, $deskripsi, $idkategori, $idkategori_klinis, $id);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            error_log("Execute failed update KodeTindakanTerapi: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM kode_tindakan_terapi WHERE idkode_tindakan_terapi = ?");
        if (!$stmt) {
            error_log("Prepare failed delete KodeTindakanTerapi: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log("Execute failed delete KodeTindakanTerapi: " . $stmt->error);
            return false;
        }
        return true;
    }
}
