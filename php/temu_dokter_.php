<?php
require_once(__DIR__ . "/dbconnection.php");

class TemuDokter
{
    private DBConnection $db;

    public function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    // Daftar bertemu dokter
    public function daftarPertemuan(int $idpet, int $idrole_user): bool
    {
        // Pastikan $idrole_user valid
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) AS c FROM role_user WHERE idrole_user = ?");
        if (!$stmtCheck) return false;
        $stmtCheck->bind_param("i", $idrole_user);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $rowCheck = $resCheck->fetch_assoc();
        $stmtCheck->close();
        if ($rowCheck['c'] == 0) return false; // invalid idrole_user

        // Hitung no_urut hari ini
        $stmt = $this->db->prepare("SELECT COUNT(*) AS count_today FROM temu_dokter WHERE DATE(waktu_daftar) = CURDATE()");
        if (!$stmt) return false;

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $no_urut = $row['count_today'] + 1;
        $stmt->close();

        // Insert data
        $stmt = $this->db->prepare("
            INSERT INTO temu_dokter (no_urut, waktu_daftar, status, idpet, idrole_user) 
            VALUES (?, NOW(), 'N', ?, ?)
        ");
        if (!$stmt) return false;

        $stmt->bind_param("iii", $no_urut, $idpet, $idrole_user);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    // Ambil daftar pertemuan hari ini
    public function getDaftarHariIni(): array
    {
        $stmt = $this->db->prepare("
            SELECT td.idreservasi_dokter, td.no_urut, td.waktu_daftar, td.status, p.nama AS nama_pet, u.nama AS nama_pemilik
            FROM temu_dokter td
            JOIN pet p ON td.idpet = p.idpet
            JOIN pemilik pm ON p.idpemilik = pm.idpemilik
            JOIN user u ON pm.iduser = u.iduser
            WHERE DATE(td.waktu_daftar) = CURDATE()
            ORDER BY td.no_urut ASC
        ");
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();
        $daftar = [];
        while ($row = $result->fetch_assoc()) {
            $daftar[] = $row;
        }
        $stmt->close();
        return $daftar;
    }
}
