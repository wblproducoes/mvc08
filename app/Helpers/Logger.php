<?php

namespace App\Helpers;

/**
 * Helper de logging
 * 
 * @package App\Helpers
 */
class Logger
{
    /**
     * Registra log
     * 
     * @param string $message
     * @param string $level
     */
    public static function log(string $message, string $level = 'info'): void
    {
        $logPath = __DIR__ . '/../../storage/logs/';
        $logFile = $logPath . date('Y-m-d') . '.log';
        
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Log de erro
     * 
     * @param string $message
     */
    public static function error(string $message): void
    {
        self::log($message, 'ERROR');
    }
    
    /**
     * Log de info
     * 
     * @param string $message
     */
    public static function info(string $message): void
    {
        self::log($message, 'INFO');
    }
    
    /**
     * Log de warning
     * 
     * @param string $message
     */
    public static function warning(string $message): void
    {
        self::log($message, 'WARNING');
    }
}
