<?php

declare(strict_types=1);

namespace ErpStocco\Infrastructure\Database;

use PDO;
use PDOException;

class Connection
{
    private static ?Connection $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../../../config/database.php';

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            throw new PDOException("Erro de conexao com o banco: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    private function __clone() {}
    public function __wakeup() { throw new \RuntimeException('Cannot unserialize singleton'); }
}
