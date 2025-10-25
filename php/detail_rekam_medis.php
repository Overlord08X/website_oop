<?php
require_once(__DIR__ . "/dbconnection.php");

class DetailRekamMedis
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    /**
     * Mengambil semua tindakan terapi untuk satu rekam medis.
     */
    public function getByRekamMedisId(int $idrekam_medis): array
    {
        $sql = "
            SELECT 
                drm.iddetail_rekam_medis, 
                drm.detail, 
                ktt.kode, 
                ktt.deskripsi_tindakan_terapi
            FROM detail_rekam_medis drm
            JOIN kode_tindakan_terapi ktt ON drm.idkode_tindakan_terapi = ktt.idkode_tindakan_terapi
            WHERE drm.idrekam_medis = ?
            ORDER BY drm.iddetail_rekam_medis ASC
        ";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed getByRekamMedisId: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $idrekam_medis);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil satu detail rekam medis berdasarkan ID-nya.
     */
    public function getById(int $iddetail_rekam_medis): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM detail_rekam_medis WHERE iddetail_rekam_medis = ?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $iddetail_rekam_medis);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?? null;
    }

    /**
     * Menambahkan tindakan terapi baru ke rekam medis.
     */
    public function create(int $idrekam_medis, int $idkode_tindakan_terapi, string $detail): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO detail_rekam_medis (idrekam_medis, idkode_tindakan_terapi, detail) VALUES (?, ?, ?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("iis", $idrekam_medis, $idkode_tindakan_terapi, $detail);
        return $stmt->execute();
    }

    /**
     * Memperbarui tindakan terapi yang sudah ada.
     */
    public function update(int $iddetail_rekam_medis, int $idkode_tindakan_terapi, string $detail): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE detail_rekam_medis SET idkode_tindakan_terapi = ?, detail = ? WHERE iddetail_rekam_medis = ?"
        );
        if (!$stmt) return false;
        $stmt->bind_param("isi", $idkode_tindakan_terapi, $detail, $iddetail_rekam_medis);
        return $stmt->execute();
    }

    /**
     * Menghapus tindakan terapi dari rekam medis.
     */
    public function delete(int $iddetail_rekam_medis): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM detail_rekam_medis WHERE iddetail_rekam_medis = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $iddetail_rekam_medis);
        return $stmt->execute();
    }
}
