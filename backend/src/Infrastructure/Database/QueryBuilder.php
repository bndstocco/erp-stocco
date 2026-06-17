<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Database;

use PDO;
use Closure;

class QueryBuilder
{
    private PDO $pdo;
    private string $table;
    private array $selects = ['*'];
    private array $wheres = [];
    private array $params = [];
    private ?string $orderBy = null;
    private ?string $groupBy = null;
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];

    public function __construct(PDO $pdo, string $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function select(array $columns): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function where($column, $operator = '=', $value = null): self
    {
        if ($column instanceof Closure) {
            $subQb = new self($this->pdo, $this->table);
            $column($subQb);
            if (!empty($subQb->wheres)) {
                $subSql = $subQb->buildWhereConditions();
                $this->wheres[] = ['sql' => '(' . $subSql . ')', 'boolean' => 'AND'];
                $this->params = array_merge($this->params, $subQb->params);
            }
            return $this;
        }

        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = 'where_' . count($this->params);
        $this->wheres[] = ['sql' => "{$column} {$operator} :{$placeholder}", 'boolean' => 'AND'];
        $this->params[$placeholder] = $value;
        return $this;
    }

    public function orWhere(string $column, $operator = '=', $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = 'or_where_' . count($this->params);
        $this->wheres[] = ['sql' => "{$column} {$operator} :{$placeholder}", 'boolean' => 'OR'];
        $this->params[$placeholder] = $value;
        return $this;
    }

    public function whereLike(string $column, string $value): self
    {
        $placeholder = 'like_' . count($this->params);
        $this->wheres[] = ['sql' => "{$column} LIKE :{$placeholder}", 'boolean' => 'AND'];
        $this->params[$placeholder] = "%{$value}%";
        return $this;
    }

    public function orWhereLike(string $column, string $value): self
    {
        $placeholder = 'or_like_' . count($this->params);
        $this->wheres[] = ['sql' => "{$column} LIKE :{$placeholder}", 'boolean' => 'OR'];
        $this->params[$placeholder] = "%{$value}%";
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = "in_{$column}_{$i}";
            $placeholders[] = ":{$placeholder}";
            $this->params[$placeholder] = $value;
        }
        $this->wheres[] = ['sql' => "{$column} IN (" . implode(', ', $placeholders) . ")", 'boolean' => 'AND'];
        return $this;
    }

    public function whereColumn(string $column, string $operator = '=', string $column2 = ''): self
    {
        if ($column2 === '') {
            $column2 = $operator;
            $operator = '=';
        }
        $this->wheres[] = ['sql' => "{$column} {$operator} {$column2}", 'boolean' => 'AND'];
        return $this;
    }

    public function whereBetween(string $column, $start, $end): self
    {
        $startPlaceholder = 'between_start_' . count($this->params);
        $endPlaceholder = 'between_end_' . count($this->params);
        $this->wheres[] = ['sql' => "{$column} BETWEEN :{$startPlaceholder} AND :{$endPlaceholder}", 'boolean' => 'AND'];
        $this->params[$startPlaceholder] = $start;
        $this->params[$endPlaceholder] = $end;
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = ['sql' => "{$column} IS NULL", 'boolean' => 'AND'];
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = ['sql' => "{$column} IS NOT NULL", 'boolean' => 'AND'];
        return $this;
    }

    public function join(string $table, string $first, string $operator = '=', string $second = '', string $type = 'LEFT'): self
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy = "{$column} {$direction}";
        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->groupBy = $column;
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    private function buildWhereConditions(): string
    {
        if (empty($this->wheres)) return '';

        $parts = [];
        foreach ($this->wheres as $i => $where) {
            if ($i === 0) {
                $parts[] = $where['sql'];
            } else {
                $parts[] = $where['boolean'] . ' ' . $where['sql'];
            }
        }
        return implode(' ', $parts);
    }

    public function buildSelect(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }

        $whereClause = $this->buildWhereConditions();
        if ($whereClause !== '') {
            $sql .= " WHERE " . $whereClause;
        }

        if ($this->groupBy) {
            $sql .= " GROUP BY {$this->groupBy}";
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    public function get(): array
    {
        $stmt = $this->pdo->prepare($this->buildSelect());
        $stmt->execute($this->params);
        return $stmt->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit(1);
        $stmt = $this->pdo->prepare($this->buildSelect());
        $stmt->execute($this->params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function count(): int
    {
        $this->selects = ['COUNT(*) as count'];
        $stmt = $this->pdo->prepare($this->buildSelect());
        $stmt->execute($this->params);
        return (int) $stmt->fetch()['count'];
    }

    public function insert(array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":{$key}", array_keys($data)));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->pdo->lastInsertId();
    }

    public function update(array $data): int
    {
        $sets = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$sets}";

        $whereClause = $this->buildWhereConditions();
        if ($whereClause !== '') {
            $sql .= " WHERE " . $whereClause;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($data, $this->params));

        return $stmt->rowCount();
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}";

        $whereClause = $this->buildWhereConditions();
        if ($whereClause !== '') {
            $sql .= " WHERE " . $whereClause;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->params);

        return $stmt->rowCount();
    }

    public function paginate(int $page = 1, int $perPage = 15): array
    {
        $originalSelects = $this->selects;
        $total = $this->count();
        $this->selects = $originalSelects;

        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $lastPage));

        $this->limit($perPage);
        $this->offset(($page - 1) * $perPage);

        return [
            'data' => $this->get(),
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
        ];
    }
}
