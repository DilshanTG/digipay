<?php

namespace App\Services;

class Logger
{
    protected static string $logPath;

    public static function init()
    {
        self::$logPath = __DIR__ . '/../../storage/logs/app.log';

        $logDir = dirname(self::$logPath);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public static function log(string $level, string $message, array $context = [])
    {
        if (!isset(self::$logPath)) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logLine = "[{$timestamp}] [{$level}] {$message}{$contextStr}\n";

        file_put_contents(self::$logPath, $logLine, FILE_APPEND);
    }

    public static function info(string $message, array $context = [])
    {
        self::log('INFO', $message, $context);
    }

    public static function error(string $message, array $context = [])
    {
        self::log('ERROR', $message, $context);
    }

    public static function warning(string $message, array $context = [])
    {
        self::log('WARNING', $message, $context);
    }

    public static function debug(string $message, array $context = [])
    {
        self::log('DEBUG', $message, $context);
    }
}
