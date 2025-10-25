<?php
require_once(__DIR__ . "/dbconnection.php");

class RekamMedis
{
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    // CREATE - Simpan rekam medis baru
    public function createRekamMedis(
        int $idreservasi_dokter,
        string $anamnesa,
        string $temuan_klinis,
        string $diagnosa,
        int $dokter_pemeriksa
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO rekam_medis 
            (created_at, anamnesa, temuan_klinis, diagnosa, dokter_pemeriksa, idreservasi_dokter)
            VALUES (NOW(), ?, ?, ?, ?, ?)
        ");
        if (!$stmt) return false;

        $stmt->bind_param("sssii", $anamnesa, $temuan_klinis, $diagnosa, $dokter_pemeriksa, $idreservasi_dokter);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // READ - Ambil semua rekam medis
    public function getAllRekamMedis(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM rekam_medis ORDER BY created_at DESC");
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();
        $rekamMedis = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rekamMedis;
    }

    // READ - Ambil rekam medis berdasarkan ID
    public function getRekamMedisById(int $idrekam_medis): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM rekam_medis WHERE idrekam_medis = ?");
        if (!$stmt) return null;

        $stmt->bind_param("i", $idrekam_medis);
        $stmt->execute();
        $result = $stmt->get_result();
        $rekamMedis = $result->fetch_assoc();
        $stmt->close();

        return $rekamMedis ?: null;
    }

    // UPDATE - Ubah data rekam medis
    public function updateRekamMedis(
        int $idrekam_medis,
        string $anamnesa,
        string $temuan_klinis,
        string $diagnosa,
        int $dokter_pemeriksa
    ): bool {
        $stmt = $this->db->prepare("
            UPDATE rekam_medis 
            SET anamnesa = ?, temuan_klinis = ?, diagnosa = ?, dokter_pemeriksa = ?
            WHERE idrekam_medis = ?
        ");
        if (!$stmt) return false;

        $stmt->bind_param("sssii", $anamnesa, $temuan_klinis, $diagnosa, $dokter_pemeriksa, $idrekam_medis);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // DELETE - Hapus rekam medis
    public function deleteRekamMedis(int $idrekam_medis): bool
    {
        $stmt = $this->db->prepare("DELETE FROM rekam_medis WHERE idrekam_medis = ?");
        if (!$stmt) return false;

        $stmt->bind_param("i", $idrekam_medis);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
