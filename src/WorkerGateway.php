<?php

class WorkerGateway extends GatewayBase
{
    private PDO $conn;

    private ShiftGateway $shift_gateway;

    private string $table = "worker";

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();

        $this->shift_gateway = new ShiftGateway($database);
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";

        $statement = $this->conn->query($sql);
        $data = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $row['is_available'] = (bool) $row['is_available'];
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql =
            "INSERT INTO {$this->table} (name, document, is_available)
            VALUES (:name, :document, :is_available)";

        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $statement->bindValue(":document", $data["document"] ?? 0, PDO::PARAM_STR);
        $statement->bindValue(":is_available", (bool) ($data["is_available"] ?? false), PDO::PARAM_BOOL);

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

        if ($data !== false) {
            $data["is_available"] = (bool) $data["is_available"];
        }

        $shifts = $this->shift_gateway->getWorkerShifts($id);
        $data["shifts"] = $shifts;

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql =
            "UPDATE {$this->table}
            SET name = :name, document = :document, is_available = :is_available
            WHERE id = :id";

        $statement = $this->conn->prepare($sql);
        $statement->bindValue(":name", $new["name"] ?? $current["name"], PDO::PARAM_STR);
        $statement->bindValue(":document", $new["document"] ?? $current["document"], PDO::PARAM_STR);
        $statement->bindValue(":is_available", (bool) ($new["is_available"] ?? $current["is_available"]), PDO::PARAM_BOOL);
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