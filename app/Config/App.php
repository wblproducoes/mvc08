<?php

namespace App\Config;

/**
 * Configurações gerais da aplicação
 * 
 * @package App\Config
 */
class App
{
    /**
     * Retorna configuração da aplicação
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
    
    /**
     * Verifica se está em modo debug
     * 
     * @return bool
     */
    public static function isDebug(): bool
    {
        return filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Retorna a URL base da aplicação
     * 
     * @return string
     */
    public static function url(string $path = ''): string
    {
        $baseUrl = rtrim($_ENV['APP_URL'], '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}
