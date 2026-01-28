#!/usr/bin/env php
<?php

/**
 * Database Initialization Script
 * Run this to create the database schema
 */

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
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

$is_mysql = ($config['driver'] === 'mysql');
$pk = $is_mysql ? "BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY" : "INTEGER PRIMARY KEY AUTOINCREMENT";
$timestamp = $is_mysql ? "TIMESTAMP NULL DEFAULT NULL" : "DATETIME DEFAULT NULL";
$text = $is_mysql ? "VARCHAR(255)" : "TEXT";
$longtext = $is_mysql ? "LONGTEXT" : "TEXT";
$json = $is_mysql ? "LONGTEXT" : "TEXT"; // SQLite uses TEXT for JSON, MySQL uses LONGTEXT with CHECK

// Merchants table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS merchants (
        id $pk,
        name VARCHAR(255) NOT NULL,
        api_key VARCHAR(255) NOT NULL,
        secret_key VARCHAR(255) DEFAULT NULL,
        allowed_domains $json DEFAULT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at $timestamp,
        updated_at $timestamp,
        return_url VARCHAR(255) DEFAULT NULL,
        cancel_url VARCHAR(255) DEFAULT NULL,
        notify_url VARCHAR(255) DEFAULT NULL,
        sandbox_mode TINYINT(1) NOT NULL DEFAULT 0
    )
");

// Payments table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS payments (
        id $pk,
        merchant_id BIGINT UNSIGNED NOT NULL,
        order_id VARCHAR(255) NOT NULL,
        client_order_id VARCHAR(255) DEFAULT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        currency VARCHAR(255) NOT NULL DEFAULT 'LKR',
        status VARCHAR(255) NOT NULL DEFAULT 'PENDING',
        redirect_url $longtext DEFAULT NULL,
        notify_url VARCHAR(255) DEFAULT NULL,
        mode VARCHAR(255) NOT NULL DEFAULT 'api',
        payment_method VARCHAR(255) DEFAULT NULL,
        payhere_ref VARCHAR(255) DEFAULT NULL,
        customer_email VARCHAR(255) DEFAULT NULL,
        customer_phone VARCHAR(255) DEFAULT NULL,
        meta_data $json DEFAULT NULL,
        real_description VARCHAR(500) DEFAULT NULL,
        fake_description VARCHAR(500) DEFAULT NULL,
        created_at $timestamp,
        updated_at $timestamp
    )
");

// Settings table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS settings (
        id $pk,
        `key` VARCHAR(255) NOT NULL,
        value $longtext DEFAULT NULL,
        created_at $timestamp,
        updated_at $timestamp
    )
");

// Users table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id $pk,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        email_verified_at $timestamp,
        password VARCHAR(255) NOT NULL,
        remember_token VARCHAR(100) DEFAULT NULL,
        created_at $timestamp,
        updated_at $timestamp
    )
");

// Unique indexes for consistent key lookups
if ($is_mysql) {
    try { $pdo->exec("ALTER TABLE merchants ADD UNIQUE (api_key)"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE payments ADD UNIQUE (order_id)"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE settings ADD UNIQUE (`key`)"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE users ADD UNIQUE (email)"); } catch(Exception $e) {}
} else {
    // Unique indexing is usually handled in the CREATE TABLE for SQLite but we ensure it here if needed
}

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
