<?php

namespace App\Helpers;

/**
 * Helper de Rate Limiting
 * 
 * Implementa proteção contra ataques de força bruta limitando
 * o número de tentativas de login por IP em um período de tempo.
 * 
 * Funcionalidades:
 * - Limita tentativas de login por IP
 * - Bloqueia temporariamente após exceder limite
 * - Limpa tentativas antigas automaticamente
 * - Armazena dados em arquivo para persistência
 * 
 * @package App\Helpers
 * @author Sistema MVC08
 * @version 1.0.0
 * 
 * @example
 * ```php
 * $limiter = new RateLimiter();
 * 
 * // Verifica se IP está bloqueado
 * if ($limiter->isBlocked('login')) {
 *     die('Muitas tentativas. Tente novamente em alguns minutos.');
 * }
 * 
 * // Registra tentativa falha
 * $limiter->hit('login');
 * 
 * // Limpa tentativas após sucesso
 * $limiter->clear('login');
 * ```
 */
class RateLimiter
{
    /**
     * @var string Caminho do arquivo de armazenamento
     */
    private string $storagePath;
    
    /**
     * @var int Máximo de tentativas permitidas
     */
    private int $maxAttempts;
    
    /**
     * @var int Tempo de bloqueio em segundos
     */
    private int $decayMinutes;
    
    /**
     * Construtor
     * 
     * @param int $maxAttempts Máximo de tentativas (padrão: 5)
     * @param int $decayMinutes Tempo de bloqueio em minutos (padrão: 15)
     */
    public function __construct(int $maxAttempts = 5, int $decayMinutes = 15)
    {
        $this->storagePath = __DIR__ . '/../../storage/cache/rate_limiter.json';
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $decayMinutes * 60; // Converte para segundos
        
        // Cria arquivo se não existir
        if (!file_exists($this->storagePath)) {
            $dir = dirname($this->storagePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($this->storagePath, json_encode([]));
        }
    }
    
    /**
     * Verifica se o IP está bloqueado
     * 
     * @param string $key Chave de identificação (ex: 'login')
     * @return bool True se bloqueado, False caso contrário
     */
    public function isBlocked(string $key): bool
    {
        $ip = $this->getIp();
        $cacheKey = $this->getCacheKey($key, $ip);
        $data = $this->getData();
        
        if (!isset($data[$cacheKey])) {
            return false;
        }
        
        $attempts = $data[$cacheKey];
        
        // Limpa tentativas antigas
        $attempts = array_filter($attempts, function($timestamp) {
            return (time() - $timestamp) < $this->decayMinutes;
        });
        
        // Atualiza dados
        if (empty($attempts)) {
            unset($data[$cacheKey]);
            $this->saveData($data);
            return false;
        }
        
        $data[$cacheKey] = $attempts;
        $this->saveData($data);
        
        return count($attempts) >= $this->maxAttempts;
    }
    
    /**
     * Registra uma tentativa
     * 
     * @param string $key Chave de identificação (ex: 'login')
     * @return void
     */
    public function hit(string $key): void
    {
        $ip = $this->getIp();
        $cacheKey = $this->getCacheKey($key, $ip);
        $data = $this->getData();
        
        if (!isset($data[$cacheKey])) {
            $data[$cacheKey] = [];
        }
        
        $data[$cacheKey][] = time();
        $this->saveData($data);
    }
    
    /**
     * Limpa tentativas de um IP
     * 
     * @param string $key Chave de identificação (ex: 'login')
     * @return void
     */
    public function clear(string $key): void
    {
        $ip = $this->getIp();
        $cacheKey = $this->getCacheKey($key, $ip);
        $data = $this->getData();
        
        if (isset($data[$cacheKey])) {
            unset($data[$cacheKey]);
            $this->saveData($data);
        }
    }
    
    /**
     * Retorna o número de tentativas restantes
     * 
     * @param string $key Chave de identificação
     * @return int Número de tentativas restantes
     */
    public function remaining(string $key): int
    {
        $ip = $this->getIp();
        $cacheKey = $this->getCacheKey($key, $ip);
        $data = $this->getData();
        
        if (!isset($data[$cacheKey])) {
            return $this->maxAttempts;
        }
        
        $attempts = array_filter($data[$cacheKey], function($timestamp) {
            return (time() - $timestamp) < $this->decayMinutes;
        });
        
        return max(0, $this->maxAttempts - count($attempts));
    }
    
    /**
     * Retorna o tempo restante de bloqueio em segundos
     * 
     * @param string $key Chave de identificação
     * @return int Segundos até desbloquear (0 se não bloqueado)
     */
    public function availableIn(string $key): int
    {
        $ip = $this->getIp();
        $cacheKey = $this->getCacheKey($key, $ip);
        $data = $this->getData();
        
        if (!isset($data[$cacheKey]) || empty($data[$cacheKey])) {
            return 0;
        }
        
        $oldestAttempt = min($data[$cacheKey]);
        $timeElapsed = time() - $oldestAttempt;
        
        return max(0, $this->decayMinutes - $timeElapsed);
    }
    
    /**
     * Obtém o IP do cliente
     * 
     * @return string IP do cliente
     */
    private function getIp(): string
    {
        // Verifica proxies e load balancers
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        return trim($ip);
    }
    
    /**
     * Gera chave de cache
     * 
     * @param string $key Chave base
     * @param string $ip IP do cliente
     * @return string Chave de cache
     */
    private function getCacheKey(string $key, string $ip): string
    {
        return md5($key . ':' . $ip);
    }
    
    /**
     * Lê dados do arquivo
     * 
     * @return array Dados armazenados
     */
    private function getData(): array
    {
        $content = file_get_contents($this->storagePath);
        return json_decode($content, true) ?: [];
    }
    
    /**
     * Salva dados no arquivo
     * 
     * @param array $data Dados a salvar
     * @return void
     */
    private function saveData(array $data): void
    {
        file_put_contents($this->storagePath, json_encode($data));
    }
}
