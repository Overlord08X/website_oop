<?php
require_once(__DIR__ . "/config.php");

class DBConnection
{
    private ?mysqli $dbconn = null;

    public function init_connect(): void
    {
        $this->dbconn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($this->dbconn->connect_error) {
            // Log error secara internal, jangan tampilkan ke user di produksi
            error_log("Koneksi database gagal: " . $this->dbconn->connect_error);
            die("Terjadi kesalahan koneksi database. Silakan coba lagi nanti.");
        }
    }

    public function getConnection(): mysqli
    {
        if ($this->dbconn === null) {
            $this->init_connect(); // Pastikan koneksi sudah diinisialisasi
        }
        return $this->dbconn;
    }

    public function prepare(string $sql): ?mysqli_stmt
    {
        if ($this->dbconn === null) {
            $this->init_connect(); // Pastikan koneksi sudah diinisialisasi
        }
        $stmt = $this->dbconn->prepare($sql);
        if (!$stmt) {
            error_log("DBConnection prepare failed for SQL: '$sql' - Error: " . $this->dbconn->error);
        }
        return $stmt;
    }

    public function close_connection(): void
    {
        if ($this->dbconn) {
            $this->dbconn->close();
            $this->dbconn = null; // Set kembali ke null setelah ditutup
        }
    }
}
