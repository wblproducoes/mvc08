<?php

namespace App\Helpers;

/**
 * Helper de URLs
 * 
 * @package App\Helpers
 */
class Url
{
    /**
     * Gera URL completa com base path
     * 
     * @param string $path
     * @return string
     */
    public static function to(string $path = ''): string
    {
        // Remove barra inicial se existir
        $path = ltrim($path, '/');
        
        // Pega o base path da aplicação
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $basePath = str_replace('/public/index.php', '', $scriptName);
        
        // Remove barra final do base path
        $basePath = rtrim($basePath, '/');
        
        // Retorna URL completa
        return $basePath . '/' . $path;
    }
    
    /**
     * Gera URL de asset (CSS, JS, imagens)
     * 
     * @param string $path
     * @return string
     */
    public static function asset(string $path): string
    {
        // Remove barra inicial se existir
        $path = ltrim($path, '/');
        
        // Pega o base path da aplicação
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $basePath = str_replace('/index.php', '', $scriptName);
        
        // Remove barra final do base path
        $basePath = rtrim($basePath, '/');
        
        // Retorna URL completa
        return $basePath . '/' . $path;
    }
}
