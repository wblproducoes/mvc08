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

// Configurações de erro
if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mudar para 1 em HTTPS

// Timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

// Carrega rotas
$router = new Router();
require_once __DIR__ . '/../routes/web.php';

// Executa roteamento
$router->dispatch();
