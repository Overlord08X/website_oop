<?php
class DBConnection
{
    private string $servername = "192.168.56.102";
    private string $username   = "admin";
    private string $password   = "1234";
    private string $dbname     = "RSHP";
    private ?mysqli $dbconn = null; // ✅ nullable biar tidak error sebelum connect

    public function init_connect(): void
    {
        $this->dbconn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->dbconn->connect_error) {
            die("Koneksi gagal: " . $this->dbconn->connect_error);
        }
    }

    public function getConnection(): mysqli
    {
        return $this->dbconn;
    }

    public function send_query(string $query): array
    {
        $result = $this->dbconn->query($query);

        if ($this->dbconn->error) {
            return ["status" => "error", "message" => $this->dbconn->error, "data" => []];
        } else if ($result === true) {
            return ["status" => "success", "message" => "OK", "data" => []];
        } else {
            return ["status" => "success", "message" => "OK", "data" => $result->fetch_all(MYSQLI_ASSOC)];
        }
    }

    public function prepare(string $sql): ?mysqli_stmt
    {
        return $this->dbconn->prepare($sql);
    }

    public function close_connection(): void
    {
        if ($this->dbconn) $this->dbconn->close();
    }
}
