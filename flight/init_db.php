#!/usr/bin/env php
<?php

/**
 * Database Initialization Script
 * Run this to create the database schema
 */

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$config = require __DIR__ . '/config/database.php';

echo "Initializing database...\n";

if ($config['driver'] === 'sqlite') {
    $dbPath = $config['sqlite']['database'];
    $dbDir = dirname($dbPath);

    if (!file_exists($dbDir)) {
        mkdir($dbDir, 0755, true);
    }

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

// Create tables
echo "Creating tables...\n";

// Payments table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS payments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        merchant_id INTEGER,
        order_id TEXT UNIQUE NOT NULL,
        client_order_id TEXT,
        amount DECIMAL(10, 2) NOT NULL,
        currency TEXT DEFAULT 'LKR',
        status TEXT DEFAULT 'PENDING',
        redirect_url TEXT,
        notify_url TEXT,
        mode TEXT DEFAULT 'api',
        payment_method TEXT,
        payhere_ref TEXT,
        customer_email TEXT,
        customer_phone TEXT,
        meta_data TEXT,
        real_description TEXT,
        fake_description TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

// Merchants table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS merchants (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        api_key TEXT UNIQUE NOT NULL,
        secret_key TEXT NOT NULL,
        allowed_domains TEXT,
        return_url TEXT,
        cancel_url TEXT,
        notify_url TEXT,
        is_active INTEGER DEFAULT 1,
        sandbox_mode INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

// Settings table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key TEXT UNIQUE NOT NULL,
        value TEXT
    )
");

// Users table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

echo "Database initialized successfully!\n";

// Create a default merchant if none exists
$stmt = $pdo->query("SELECT COUNT(*) FROM merchants");
$count = $stmt->fetchColumn();

if ($count == 0) {
    echo "Creating default merchant...\n";

    $apiKey = 'sk_test_' . bin2hex(random_bytes(16));
    $secretKey = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare("
        INSERT INTO merchants (name, api_key, secret_key, allowed_domains, is_active)
        VALUES (?, ?, ?, ?, 1)
    ");

    $stmt->execute([
        'Default Merchant',
        $apiKey,
        $secretKey,
        json_encode(['*'])
    ]);

    echo "Default merchant created!\n";
    echo "API Key: {$apiKey}\n";
    echo "Secret Key: {$secretKey}\n";
}

echo "\nDone! Your database is ready.\n";
