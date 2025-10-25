<?php
require_once(__DIR__ . "/dbconnection.php");

class KategoriKlinis
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    public function getAllKategoriKlinis(): array
    {
        $sql = "
            SELECT kk.idkategori_klinis, kk.nama_kategori_klinis, k.nama_kategori
            FROM kategori_klinis kk
            JOIN kategori k ON kk.idkategori = k.idkategori
            ORDER BY k.nama_kategori, kk.nama_kategori_klinis
        ";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("Error getAllKategoriKlinis: " . $this->conn->error);
        return [];
    }

    public function getKategoriKlinisById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT idkategori_klinis, nama_kategori_klinis, idkategori
            FROM kategori_klinis 
            WHERE idkategori_klinis = ?
        ");
        if (!$stmt) {
            error_log("Prepare failed getKategoriKlinisById: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?? null;
    }

    public function createKategoriKlinis(string $nama, int $idkategori): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO kategori_klinis (nama_kategori_klinis, idkategori) VALUES (?, ?)");
        if (!$stmt) {
            error_log("Prepare failed createKategoriKlinis: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("si", $nama, $idkategori);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Menangani error duplikasi (unique constraint)
            if ($e->getCode() == 1062) {
                error_log("Duplicate entry for kategori_klinis: " . $nama);
            } else {
                error_log("Execute failed createKategoriKlinis: " . $e->getMessage());
            }
            return false;
        }
    }

    public function updateKategoriKlinis(int $id, string $nama, int $idkategori): bool
    {
        $stmt = $this->conn->prepare("UPDATE kategori_klinis SET nama_kategori_klinis = ?, idkategori = ? WHERE idkategori_klinis = ?");
        if (!$stmt) {
            error_log("Prepare failed updateKategoriKlinis: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("sii", $nama, $idkategori, $id);
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                error_log("Duplicate entry for kategori_klinis update: " . $nama);
            } else {
                error_log("Execute failed updateKategoriKlinis: " . $e->getMessage());
            }
            return false;
        }
    }

    public function deleteKategoriKlinis(int $id): bool
    {
        // Opsional: Tambahkan pengecekan relasi ke tabel lain sebelum menghapus
        // try {
        //     ...
        // } catch (Exception $e) { ... }

        $stmt = $this->conn->prepare("DELETE FROM kategori_klinis WHERE idkategori_klinis = ?");
        if (!$stmt) {
            error_log("Prepare failed deleteKategoriKlinis: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            error_log("Execute failed deleteKategoriKlinis: " . $stmt->error);
            return false;
        }
        return true;
    }
}
