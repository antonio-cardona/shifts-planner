<?php

class ShiftGateway extends GatewayBase
{
    private PDO $conn;

    private string $table = "shift";

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        $statement = $this->conn->query($sql);
        $data = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql =
            "INSERT INTO {$this->table} (worker_id, shift_type, shift_date)
            VALUES (:worker_id, :shift_type, :shift_date)";

        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":worker_id", $data["worker_id"], PDO::PARAM_INT);
        $statement->bindValue(":shift_type", $data["shift_type"] ?? 0, PDO::PARAM_INT);
        $statement->bindValue(":shift_date", $data["shift_date"] ?? "1977-04-20", PDO::PARAM_STR);

        $statement->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array|false
    {
        $sql =
            "SELECT * 
            FROM {$this->table}
            WHERE id = :id";

        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql =
            "UPDATE {$this->table}
            SET worker_id = :worker_id, shift_type = :shift_type, shift_date = :shift_date
            WHERE id = :id";

        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":worker_id", $new["worker_id"] ?? $current["worker_id"], PDO::PARAM_INT);
        $statement->bindValue(":shift_type", $new["shift_type"] ?? $current["shift_type"], PDO::PARAM_INT);
        $statement->bindValue(":shift_date", $new["shift_date"] ?? $current["shift_date"], PDO::PARAM_STR);
        $statement->bindValue(":id", $current["id"], PDO::PARAM_INT);
        
        $statement->execute();

        return $statement->rowCount();
    }

    public function delete(string $id): int
    {
        $sql =
            "DELETE FROM {$this->table}
            WHERE id = :id";

        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount();
    }
}