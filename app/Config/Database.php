<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Classe de configuração e conexão com banco de dados
 * 
 * @package App\Config
 */
class Database
{
    private static ?PDO $connection = null;
    
    /**
     * Obtém a conexão com o banco de dados (Singleton)
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $host = $_ENV['DB_HOST'];
                $port = $_ENV['DB_PORT'];
                $dbname = $_ENV['DB_NAME'];
                $charset = $_ENV['DB_CHARSET'];
                
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
                
                self::$connection = new PDO(
                    $dsn,
                    $_ENV['DB_USER'],
                    $_ENV['DB_PASS'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                throw new PDOException("Erro ao conectar com o banco de dados");
            }
        }
        
        return self::$connection;
    }
    
    /**
     * Retorna o prefixo das tabelas
     * 
     * @return string
     */
    public static function getPrefix(): string
    {
        return $_ENV['DB_PREFIX'] ?? '';
    }
}
