<?php
require_once(__DIR__ . "/dbconnection.php");

class Pet
{
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    public function createPet($nama, $tanggal_lahir, $warna_tanda, $jenis_kelamin, $idpemilik, $idras_hewan): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO pet (nama, tanggal_lahir, warna_tanda, jenis_kelamin, idpemilik, idras_hewan)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            error_log("createPet prepare failed: " . $this->db->getConnection()->error);
            return false;
        }

        $stmt->bind_param("ssssii", $nama, $tanggal_lahir, $warna_tanda, $jenis_kelamin, $idpemilik, $idras_hewan);
        $success = $stmt->execute();
        if (!$success) {
            error_log("createPet execute failed: " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function getAllRasHewan(): array
    {
        $stmt = $this->db->prepare("
            SELECT r.idras_hewan, r.nama_ras, j.nama_jenis_hewan
            FROM ras_hewan r
            JOIN jenis_hewan j ON r.idjenis_hewan = j.idjenis_hewan
            ORDER BY j.nama_jenis_hewan, r.nama_ras
        ");
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();
        $rasHewan = [];
        while ($row = $result->fetch_assoc()) {
            $rasHewan[] = $row;
        }
        $stmt->close();
        return $rasHewan;
    }

    public function getAllPemilik(): array
    {
        // Join dengan tabel user untuk mendapatkan nama pemilik
        $sql = "
            SELECT p.idpemilik, u.nama
            FROM pemilik p
            JOIN user u ON p.iduser = u.iduser
            ORDER BY u.nama
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();
        $pemilik = [];
        while ($row = $result->fetch_assoc()) {
            $pemilik[] = $row;
        }
        $stmt->close();
        return $pemilik;
    }

    // Method untuk ambil semua pet beserta pemilik
    public function getAllPet(): array
    {
        $sql = "
            SELECT p.idpet, p.nama, pm.idpemilik, u.nama AS nama_pemilik
            FROM pet p
            JOIN pemilik pm ON p.idpemilik = pm.idpemilik
            JOIN user u ON pm.iduser = u.iduser
            ORDER BY p.nama ASC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();
        $pets = [];
        while ($row = $result->fetch_assoc()) {
            $pets[] = $row;
        }
        $stmt->close();
        return $pets;
    }

    // Method untuk mendapatkan semua detail pet untuk halaman list
    public function getAllPetDetails(): array
    {
        $sql = "
            SELECT 
                p.idpet, p.nama, p.tanggal_lahir, p.warna_tanda, p.jenis_kelamin,
                r.nama_ras,
                j.nama_jenis_hewan,
                u.nama AS nama_pemilik
            FROM pet p
            JOIN ras_hewan r ON p.idras_hewan = r.idras_hewan
            JOIN jenis_hewan j ON r.idjenis_hewan = j.idjenis_hewan
            JOIN pemilik pm ON p.idpemilik = pm.idpemilik
            JOIN user u ON pm.iduser = u.iduser
            ORDER BY p.idpet DESC
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("getAllPetDetails prepare failed: " . $this->db->getConnection()->error);
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Method untuk mendapatkan satu pet berdasarkan ID
    public function getPetById(int $idpet): ?array
    {
        $sql = "
            SELECT idpet, nama, tanggal_lahir, warna_tanda, jenis_kelamin, idpemilik, idras_hewan
            FROM pet
            WHERE idpet = ?
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("getPetById prepare failed: " . $this->db->getConnection()->error);
            return null;
        }
        $stmt->bind_param("i", $idpet);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?? null;
    }

    // Method untuk memperbarui data pet
    public function updatePet(int $idpet, string $nama, string $tanggal_lahir, string $warna_tanda, string $jenis_kelamin, int $idpemilik, int $idras_hewan): bool
    {
        $stmt = $this->db->prepare("
            UPDATE pet SET 
                nama = ?, 
                tanggal_lahir = ?, 
                warna_tanda = ?, 
                jenis_kelamin = ?, 
                idpemilik = ?, 
                idras_hewan = ?
            WHERE idpet = ?
        ");
        if (!$stmt) {
            error_log("updatePet prepare failed: " . $this->db->getConnection()->error);
            return false;
        }
        $stmt->bind_param("ssssiii", $nama, $tanggal_lahir, $warna_tanda, $jenis_kelamin, $idpemilik, $idras_hewan, $idpet);
        return $stmt->execute();
    }

    // Method untuk menghapus pet
    public function deletePet(int $idpet): bool
    {
        // Note: Sebaiknya ada pengecekan relasi, misal ke rekam medis, sebelum menghapus.
        // Untuk saat ini, kita langsung hapus.
        $stmt = $this->db->prepare("DELETE FROM pet WHERE idpet = ?");
        if (!$stmt) {
            error_log("deletePet prepare failed: " . $this->db->getConnection()->error);
            return false;
        }
        $stmt->bind_param("i", $idpet);
        return $stmt->execute();
    }
}
