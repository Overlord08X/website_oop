<?php
// php/role_user.php
require_once(__DIR__ . "/dbconnection.php");

class RoleUser
{
    private mysqli $conn;

    public function __construct(DBConnection $db)
    {
        $this->conn = $db->getConnection();
    }

    // Ambil semua role yang tersedia di sistem
    public function getAllRoles(): array
    {
        $sql = "SELECT idrole, nama_role FROM role ORDER BY nama_role ASC";
        $res = $this->conn->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        error_log("Error getAllRoles: " . $this->conn->error);
        return [];
    }

    // Ambil semua role yang dimiliki oleh user tertentu beserta statusnya
    public function getRolesById(int $iduser): array
    {
        $stmt = $this->conn->prepare("
            SELECT ru.idrole_user, r.idrole, r.nama_role, ru.status
            FROM role_user ru
            JOIN role r ON ru.idrole = r.idrole
            WHERE ru.iduser = ?
            ORDER BY r.nama_role ASC
        ");
        if (!$stmt) {
            error_log("Prepare failed getRolesById: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $iduser);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Assign role baru ke user (biasanya saat tambah user)
     *
     * @param int $iduser ID user
     * @param int $idrole ID role
     * @param int $status Default 1 (aktif)
     * @return bool true jika berhasil
     */
    public function assignRoleToUser(int $iduser, int $idrole, int $status = 1): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO role_user (iduser, idrole, status) VALUES (?, ?, ?)");
        if (!$stmt) {
            error_log("Prepare failed assignRoleToUser: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("iii", $iduser, $idrole, $status);
        return $stmt->execute();
    }

    /**
     * Memperbarui role user.
     *
     * @param int $iduser ID user yang akan diupdate role-nya
     * @param array $selectedRoleIds Array idrole yang dipilih (aktif)
     * @param int $newStatusForSelected Status (1=aktif, 0=nonaktif) untuk role yang dipilih
     * @return bool true jika berhasil, false jika gagal
     */
    public function updateUserRoles(int $iduser, array $selectedRoleIds, int $newStatusForSelected = 1): bool
    {
        $this->conn->begin_transaction();

        try {
            // Ambil role_user yang sudah ada untuk user ini
            $existingUserRoles = $this->getRolesById($iduser);
            $existingRoleMap = []; // idrole => idrole_user
            $existingRoleStatusMap = []; // idrole => status
            foreach ($existingUserRoles as $role) {
                $existingRoleMap[$role['idrole']] = $role['idrole_user'];
                $existingRoleStatusMap[$role['idrole']] = $role['status'];
            }

            // Ambil semua role sistem
            $allSystemRoles = $this->getAllRoles();

            foreach ($allSystemRoles as $systemRole) {
                $idrole = $systemRole['idrole'];
                $isSelected = in_array($idrole, $selectedRoleIds);

                if (isset($existingRoleMap[$idrole])) {
                    // Role sudah ada, update status jika perlu
                    $idrole_user = $existingRoleMap[$idrole];
                    $currentStatus = $existingRoleStatusMap[$idrole];
                    $targetStatus = $isSelected ? $newStatusForSelected : 0;

                    if ($currentStatus != $targetStatus) {
                        $stmtUpdate = $this->conn->prepare("UPDATE role_user SET status = ? WHERE idrole_user = ?");
                        if (!$stmtUpdate) throw new Exception("Prepare failed update status: " . $this->conn->error);
                        $stmtUpdate->bind_param("ii", $targetStatus, $idrole_user);
                        if (!$stmtUpdate->execute()) throw new Exception("Execute failed update status: " . $stmtUpdate->error);
                    }
                } else {
                    // Role belum ada, insert jika dipilih
                    if ($isSelected) {
                        $stmtInsert = $this->conn->prepare("INSERT INTO role_user (iduser, idrole, status) VALUES (?, ?, ?)");
                        if (!$stmtInsert) throw new Exception("Prepare failed insert new role: " . $this->conn->error);
                        $stmtInsert->bind_param("iii", $iduser, $idrole, $newStatusForSelected);
                        if (!$stmtInsert->execute()) throw new Exception("Execute failed insert new role: " . $stmtInsert->error);
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error updateUserRoles for user ID $iduser: " . $e->getMessage());
            return false;
        }
    }
}
