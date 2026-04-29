<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';

    protected function db(): PDO
    {
        return Database::connection();
    }

    public function all(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        $orderBy = $this->identifier($orderBy);
        $direction = $this->direction($direction);

        return $this->get("SELECT * FROM {$this->tableName()} ORDER BY {$orderBy} {$direction}");
    }

    public function find(int $id): ?array
    {
        return $this->first(
            "SELECT * FROM {$this->tableName()} WHERE {$this->column($this->primaryKey)} = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function where(string $column, mixed $value, string $operator = '='): array
    {
        $column = $this->column($column);
        $operator = $this->operator($operator);

        return $this->get(
            "SELECT * FROM {$this->tableName()} WHERE {$column} {$operator} :value",
            ['value' => $value]
        );
    }

    public function firstWhere(string $column, mixed $value, string $operator = '='): ?array
    {
        $column = $this->column($column);
        $operator = $this->operator($operator);

        return $this->first(
            "SELECT * FROM {$this->tableName()} WHERE {$column} {$operator} :value LIMIT 1",
            ['value' => $value]
        );
    }

    public function create(array $data): int
    {
        if ($data === []) {
            throw new InvalidArgumentException('Cannot insert an empty row.');
        }

        $columns = array_keys($data);
        $escapedColumns = array_map(fn (string $column): string => $this->column($column), $columns);
        $placeholders = array_map(fn (string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tableName(),
            implode(', ', $escapedColumns),
            implode(', ', $placeholders)
        );

        $this->query($sql, $data);

        return (int) $this->db()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if ($data === []) {
            return false;
        }

        $assignments = [];
        foreach (array_keys($data) as $column) {
            $assignments[] = $this->column($column) . ' = :' . $column;
        }

        $data[$this->primaryKey] = $id;

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :%s',
            $this->tableName(),
            implode(', ', $assignments),
            $this->column($this->primaryKey),
            $this->primaryKey
        );

        return $this->query($sql, $data)->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        return $this->query(
            "DELETE FROM {$this->tableName()} WHERE {$this->column($this->primaryKey)} = :id",
            ['id' => $id]
        )->rowCount() > 0;
    }

    public function exists(int $id): bool
    {
        return $this->first(
            "SELECT 1 FROM {$this->tableName()} WHERE {$this->column($this->primaryKey)} = :id LIMIT 1",
            ['id' => $id]
        ) !== null;
    }

    public function countWhere(string $column, mixed $value): int
    {
        $column = $this->column($column);
        $row = $this->first(
            "SELECT COUNT(*) AS total FROM {$this->tableName()} WHERE {$column} = :value",
            ['value' => $value]
        );

        return (int) ($row['total'] ?? 0);
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $statement = $this->db()->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    public function get(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function first(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();

        return $result === false ? null : $result;
    }

    protected function tableName(): string
    {
        return $this->identifier($this->table);
    }

    protected function column(string $column): string
    {
        return $this->identifier($column);
    }

    protected function identifier(string $identifier): string
    {
        if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException("Invalid SQL identifier: {$identifier}");
        }

        return "`{$identifier}`";
    }

    protected function direction(string $direction): string
    {
        $direction = strtoupper($direction);

        if (! in_array($direction, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException("Invalid sort direction: {$direction}");
        }

        return $direction;
    }

    protected function operator(string $operator): string
    {
        $allowed = ['=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE'];
        $operator = strtoupper($operator);

        if (! in_array($operator, $allowed, true)) {
            throw new InvalidArgumentException("Invalid SQL operator: {$operator}");
        }

        return $operator;
    }
}
