<?php

namespace App\Middlewares;

use App\Config\Database;

/**
 * Middleware de Integridade do Sistema
 * Verifica se o usuário master (ID 1) existe no banco de dados
 * 
 * @package App\Middlewares
 */
class SystemIntegrityMiddleware
{
    /**
     * Executa o middleware
     * 
     * @return bool
     */
    public function handle(): bool
    {
        try {
            $db = Database::getConnection();
            $prefix = Database::getPrefix();
            
            // Verifica se o usuário ID 1 existe
            $stmt = $db->prepare("SELECT id FROM {$prefix}users WHERE id = 1");
            $stmt->execute();
            $user = $stmt->fetch();
            
            if (!$user) {
                // Usuário master não existe - ERRO CRÍTICO
                $this->showCriticalError();
                return false;
            }
            
            return true;
        } catch (\Exception $e) {
            // Erro ao verificar - permite continuar para não travar em caso de problema de conexão
            return true;
        }
    }
    
    /**
     * Exibe tela de erro crítico
     */
    private function showCriticalError(): void
    {
        http_response_code(500);
        
        $html = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro Crítico do Sistema</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            padding: 50px;
            text-align: center;
        }
        .error-icon {
            font-size: 80px;
            color: #ef4444;
            margin-bottom: 20px;
        }
        h1 {
            color: #1f2937;
            font-size: 32px;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .error-code {
            color: #ef4444;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .error-details {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            border-radius: 8px;
        }
        .error-details h3 {
            color: #991b1b;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .error-details ul {
            list-style: none;
            padding-left: 0;
        }
        .error-details li {
            color: #7f1d1d;
            padding: 8px 0;
            font-size: 14px;
        }
        .error-details li:before {
            content: "→ ";
            color: #ef4444;
            font-weight: bold;
            margin-right: 8px;
        }
        .contact-info {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .contact-info p {
            color: #374151;
            font-size: 14px;
            margin: 0;
        }
        .shield-icon {
            display: inline-block;
            width: 100px;
            height: 100px;
            background: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
        }
        .shield-icon svg {
            width: 50px;
            height: 50px;
            fill: #ef4444;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="shield-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
            </svg>
        </div>
        
        <h1>Sistema Bloqueado</h1>
        <div class="error-code">Erro Crítico de Integridade</div>
        
        <p>O sistema detectou uma violação crítica de integridade e foi bloqueado por segurança.</p>
        
        <div class="error-details">
            <h3>Detalhes do Problema:</h3>
            <ul>
                <li>O usuário administrador principal (ID: 1) foi removido do banco de dados</li>
                <li>Este usuário é essencial para o funcionamento do sistema</li>
                <li>A remoção deste usuário compromete a segurança e integridade do sistema</li>
            </ul>
        </div>
        
        <p><strong>O que fazer?</strong></p>
        <p>Entre em contato com o administrador do sistema ou com o suporte técnico para restaurar o usuário administrador principal.</p>
        
        <div class="contact-info">
            <p><strong>Ação necessária:</strong> Restaurar o usuário ID 1 no banco de dados ou executar novamente o instalador do sistema.</p>
        </div>
    </div>
</body>
</html>';
        
        echo $html;
        exit;
    }
}
