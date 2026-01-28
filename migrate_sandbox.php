#!/usr/bin/env php
<?php

/**
 * Migration to add sandbox_mode to existing merchants table
 * Run this if you already have an existing database
 */

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/config/database.php';

echo "Adding sandbox_mode column to merchants table...\n";

if ($config['driver'] === 'sqlite') {
    $dbPath = $config['sqlite']['database'];
    $pdo = new PDO('sqlite:' . $dbPath);
} elseif ($config['driver'] === 'mysql') {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['mysql']['host'],
        $config['mysql']['port'],
        $config['mysql']['database'],
        $config['mysql']['charset']
    );

    $pdo = new PDO(
        $dsn,
        $config['mysql']['username'],
        $config['mysql']['password'],
        $config['mysql']['options']
    );
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Check if column already exists
    $stmt = $pdo->query("PRAGMA table_info(merchants)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasColumn = false;

    foreach ($columns as $column) {
        if ($column['name'] === 'sandbox_mode') {
            $hasColumn = true;
            break;
        }
    }

    if ($hasColumn) {
        echo "✓ sandbox_mode column already exists!\n";
    } else {
        $pdo->exec("ALTER TABLE merchants ADD COLUMN sandbox_mode INTEGER DEFAULT 0");
        echo "✓ sandbox_mode column added successfully!\n";
    }

    echo "\nMigration completed! You can now enable sandbox mode for merchants.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
