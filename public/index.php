<?php

/**
 * Ponto de entrada do sistema
 */

// Verifica se o autoload existe
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('Erro: Execute "composer install" para instalar as dependências.');
}

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Função para verificar se as tabelas existem
function checkSystemInstalled() {
    if (!file_exists(__DIR__ . '/../.env')) {
        return false;
    }
    
    try {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        
        $dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS']);
        
        $prefix = $_ENV['DB_PREFIX'];
        $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}users'");
        
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Redireciona para instalador se sistema não estiver instalado
if (!checkSystemInstalled()) {
    // Usa caminho relativo para funcionar em qualquer configuração
    if (basename($_SERVER['PHP_SELF']) !== 'install.php') {
        // Obtém o diretório base da aplicação
        $baseDir = dirname($_SERVER['PHP_SELF']);
        $installUrl = rtrim($baseDir, '/') . '/install.php';
        header('Location: ' . $installUrl);
        exit;
    }
}

// Se chegou aqui mas não tem .env, algo está errado
if (!file_exists(__DIR__ . '/../.env')) {
    die('Erro: Sistema não instalado. <a href="install.php">Clique aqui para instalar</a>');
}

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Headers de Segurança
header('X-Frame-Options: DENY'); // Previne clickjacking
header('X-Content-Type-Options: nosniff'); // Previne MIME sniffing
header('X-XSS-Protection: 1; mode=block'); // Proteção XSS
header('Referrer-Policy: strict-origin-when-cross-origin'); // Controla referrer
header('Permissions-Policy: geolocation=(), microphone=(), camera=()'); // Permissões

// Content Security Policy (CSP)
if ($_ENV['APP_DEBUG'] !== 'true') {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net;");
}

// Configurações de erro
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    // Log de erros em produção
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../storage/logs/php_errors.log');
}

// Configurações de sessão seguras
ini_set('session.cookie_httponly', 1); // Previne acesso via JavaScript
ini_set('session.use_only_cookies', 1); // Usa apenas cookies
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0); // HTTPS
ini_set('session.cookie_samesite', 'Strict'); // Proteção CSRF
ini_set('session.use_strict_mode', 1); // Modo estrito
// session.sid_length e session.sid_bits_per_character foram removidos no PHP 8.4+
// O PHP 8.4+ usa automaticamente IDs de sessão seguros

// Regenera ID de sessão periodicamente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) { // 30 minutos
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Verifica integridade do sistema (usuário master existe)
$integrityMiddleware = new \App\Middlewares\SystemIntegrityMiddleware();
if (!$integrityMiddleware->handle()) {
    exit; // Sistema bloqueado
}

// Carrega rotas
$router = new Router();
require_once __DIR__ . '/../routes/web.php';

// Executa roteamento
$router->dispatch();
