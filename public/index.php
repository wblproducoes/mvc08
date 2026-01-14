<?php

/**
 * Ponto de entrada do sistema
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Carrega variáveis de ambiente
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
