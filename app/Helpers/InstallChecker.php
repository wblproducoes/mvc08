<?php

namespace App\Helpers;

use PDO;
use PDOException;

/**
 * Helper para verificar instalação do sistema
 * 
 * @package App\Helpers
 */
class InstallChecker
{
    /**
     * Verifica se o sistema está instalado
     * 
     * @return array ['installed' => bool, 'env_exists' => bool, 'tables_exist' => bool]
     */
    public static function checkInstallation(): array
    {
        $envExists = file_exists(__DIR__ . '/../../.env');
        $tablesExist = false;
        
        if ($envExists) {
            $tablesExist = self::checkTables();
        }
        
        return [
            'installed' => $envExists && $tablesExist,
            'env_exists' => $envExists,
            'tables_exist' => $tablesExist
        ];
    }
    
    /**
     * Verifica se as tabelas existem
     * 
     * @return bool
     */
    private static function checkTables(): bool
    {
        try {
            $envConfig = self::loadEnvConfig();
            
            $dsn = "mysql:host={$envConfig['DB_HOST']};port={$envConfig['DB_PORT']};dbname={$envConfig['DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $envConfig['DB_USER'], $envConfig['DB_PASS']);
            
            $prefix = $envConfig['DB_PREFIX'];
            $requiredTables = ['users', 'user_access_logs', 'status', 'levels', 'genders'];
            
            foreach ($requiredTables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}{$table}'");
                if ($stmt->rowCount() === 0) {
                    return false;
                }
            }
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Carrega configurações do .env
     * 
     * @return array
     */
    private static function loadEnvConfig(): array
    {
        $envContent = file_get_contents(__DIR__ . '/../../.env');
        $envLines = explode("\n", $envContent);
        $config = [];
        
        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1], '"');
                $config[$key] = $value;
            }
        }
        
        return $config;
    }
}
