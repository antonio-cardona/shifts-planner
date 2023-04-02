<?php

abstract class GatewayBase
{
    private PDO $conn;

    private string $table;

    public function __construct(protected Database $database)
    {
        $this->conn = $database->getConnection();
    }


    public function getAll(): array
    {
    }

    public function create(array $data): string
    {
    }

    public function get(string $id): array|false
    {
    }

    public function update(array $current, array $new): int
    {
    }

    public function delete(string $id): int
    {
    }
}