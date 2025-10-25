<?php
require_once(__DIR__ . "/dbconnection.php");

class Kategori
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAllKategori(): array
    {
        $sql = "SELECT idkategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("Error getAllKategori: " . $this->conn->error);
        return [];
    }

    public function getKategoriById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT idkategori, nama_kategori FROM kategori WHERE idkategori=?");
        if (!$stmt) {
            error_log("Prepare failed getKategoriById: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function createKategori(string $nama_kategori): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        if (!$stmt) {
            error_log("Prepare failed createKategori: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("s", $nama_kategori);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Menangani error duplikasi (unique constraint)
            if ($e->getCode() == 1062) {
                error_log("Duplicate entry for kategori: " . $nama_kategori);
            } else {
                error_log("Execute failed createKategori: " . $e->getMessage());
            }
            return false;
        }
    }

    public function updateKategori(int $id, string $nama_kategori): bool
    {
        $stmt = $this->conn->prepare("UPDATE kategori SET nama_kategori=? WHERE idkategori=?");
        if (!$stmt) {
            error_log("Prepare failed updateKategori: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("si", $nama_kategori, $id);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                error_log("Duplicate entry for kategori update: " . $nama_kategori);
            } else {
                error_log("Execute failed updateKategori: " . $e->getMessage());
            }
            return false;
        }
    }

    public function deleteKategori(int $id): bool
    {
        try {
            // Cek apakah kategori ini digunakan di tabel lain, misal 'kategori_klinis'
            $stmtCheck = $this->conn->prepare("SELECT 1 FROM kategori_klinis WHERE idkategori = ? LIMIT 1");
            if (!$stmtCheck) {
                throw new Exception("Prepare failed (check kategori_klinis): " . $this->conn->error);
            }
            $stmtCheck->bind_param("i", $id);
            $stmtCheck->execute();
            if ($stmtCheck->get_result()->num_rows > 0) {
                throw new Exception("Kategori ini tidak dapat dihapus karena masih digunakan oleh data kategori klinis.");
            }

            // Lanjutkan penghapusan jika tidak ada relasi
            $stmt = $this->conn->prepare("DELETE FROM kategori WHERE idkategori=?");
            if (!$stmt) {
                throw new Exception("Prepare failed (delete kategori): " . $this->conn->error);
            }
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error in deleteKategori: " . $e->getMessage());
            throw $e; // Lemparkan kembali eksepsi untuk ditangani oleh pemanggil
        }
    }
}
