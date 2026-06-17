<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$config = require __DIR__ . '/../../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}",
        $config['user'],
        $config['password'],
        $config['options']
    );

    $sql = file_get_contents(__DIR__ . '/001_create_tables.sql');
    $pdo->exec($sql);

    // Run additional migrations
    $files = glob(__DIR__ . '/0*.sql');
    sort($files);
    foreach ($files as $file) {
        if (basename($file) === '001_create_tables.sql') continue;
        try {
            $alterSql = file_get_contents($file);
            $pdo->exec($alterSql);
            echo "  Migracao " . basename($file) . " executada" . PHP_EOL;
        } catch (PDOException $e) {
            echo "  Aviso (" . basename($file) . "): " . $e->getMessage() . PHP_EOL;
        }
    }

    echo "Migracoes executadas com sucesso!" . PHP_EOL;
} catch (PDOException $e) {
    echo "Erro ao executar migracoes: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
