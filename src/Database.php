<?php

namespace App;

use PDO;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require __DIR__ . '/../config/database.php';
        $driver = $config['driver'];

        if ($driver === 'sqlite') {
            $dbPath = $config['sqlite']['database'];
            $dbDir = dirname($dbPath);

            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            self::$pdo = new PDO('sqlite:' . $dbPath);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } elseif ($driver === 'mysql') {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['mysql']['host'],
                $config['mysql']['port'],
                $config['mysql']['database'],
                $config['mysql']['charset']
            );

            self::$pdo = new PDO(
                $dsn,
                $config['mysql']['username'],
                $config['mysql']['password'],
                $config['mysql']['options']
            );
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } else {
            throw new \Exception("Unsupported database driver: {$driver}");
        }

        return self::$pdo;
    }

    public static function getPdo(): PDO
    {
        return self::connect();
    }
}
