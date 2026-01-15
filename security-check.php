<?php
/**
 * Script de Verificação de Segurança
 * 
 * Execute este script para verificar se todas as medidas de segurança
 * estão corretamente implementadas no sistema.
 * 
 * Uso: php security-check.php
 * 
 * @package Sistema MVC08
 * @version 1.0.0
 */

echo "===========================================\n";
echo "  VERIFICAÇÃO DE SEGURANÇA - MVC08\n";
echo "  Colégio São Gonçalo\n";
echo "===========================================\n\n";

$checks = [];
$warnings = [];
$errors = [];

// 1. Verifica arquivo .env
echo "[1/15] Verificando arquivo .env...\n";
if (file_exists('.env')) {
    $checks[] = "✓ Arquivo .env existe";
    
    // Verifica se .env está protegido
    $htaccess = file_get_contents('.htaccess');
    if (strpos($htaccess, '.env') !== false) {
        $checks[] = "✓ .env protegido no .htaccess";
    } else {
        $errors[] = "✗ .env NÃO está protegido no .htaccess";
    }
    
    // Verifica configurações críticas
    $env = parse_ini_file('.env');
    if (isset($env['APP_DEBUG']) && $env['APP_DEBUG'] === 'false') {
        $checks[] = "✓ APP_DEBUG está false (produção)";
    } else {
        $warnings[] = "⚠ APP_DEBUG está true (desenvolvimento)";
    }
} else {
    $errors[] = "✗ Arquivo .env NÃO existe";
}

// 2. Verifica .htaccess raiz
echo "[2/15] Verificando .htaccess raiz...\n";
if (file_exists('.htaccess')) {
    $htaccess = file_get_contents('.htaccess');
    
    if (strpos($htaccess, 'Options -Indexes') !== false) {
        $checks[] = "✓ Listagem de diretórios desabilitada";
    } else {
        $errors[] = "✗ Listagem de diretórios NÃO desabilitada";
    }
    
    if (strpos($htaccess, 'ServerSignature Off') !== false) {
        $checks[] = "✓ Assinatura do servidor desabilitada";
    } else {
        $warnings[] = "⚠ Assinatura do servidor não desabilitada";
    }
} else {
    $errors[] = "✗ .htaccess raiz NÃO existe";
}

// 3. Verifica .htaccess public
echo "[3/15] Verificando .htaccess public...\n";
if (file_exists('public/.htaccess')) {
    $htaccess = file_get_contents('public/.htaccess');
    
    if (strpos($htaccess, 'X-Frame-Options') !== false) {
        $checks[] = "✓ Headers de segurança configurados";
    } else {
        $warnings[] = "⚠ Headers de segurança não configurados";
    }
} else {
    $errors[] = "✗ public/.htaccess NÃO existe";
}

// 4. Verifica RateLimiter
echo "[4/15] Verificando RateLimiter...\n";
if (file_exists('app/Helpers/RateLimiter.php')) {
    $checks[] = "✓ RateLimiter implementado";
    
    // Verifica se está sendo usado no AuthController
    if (file_exists('app/Controllers/AuthController.php')) {
        $auth = file_get_contents('app/Controllers/AuthController.php');
        if (strpos($auth, 'RateLimiter') !== false) {
            $checks[] = "✓ RateLimiter integrado ao login";
        } else {
            $errors[] = "✗ RateLimiter NÃO integrado ao login";
        }
    }
} else {
    $errors[] = "✗ RateLimiter NÃO implementado";
}

// 5. Verifica Security Helper
echo "[5/15] Verificando Security Helper...\n";
if (file_exists('app/Helpers/Security.php')) {
    $security = file_get_contents('app/Helpers/Security.php');
    
    if (strpos($security, 'generateCsrfToken') !== false) {
        $checks[] = "✓ Geração de token CSRF implementada";
    }
    
    if (strpos($security, 'validateCsrfToken') !== false) {
        $checks[] = "✓ Validação de token CSRF implementada";
    }
    
    if (strpos($security, 'sanitize') !== false) {
        $checks[] = "✓ Sanitização de inputs implementada";
    }
    
    if (strpos($security, 'hashPassword') !== false) {
        $checks[] = "✓ Hash de senhas implementado";
    }
} else {
    $errors[] = "✗ Security Helper NÃO existe";
}

// 6. Verifica Model Base (Prepared Statements)
echo "[6/15] Verificando Model Base...\n";
if (file_exists('app/Core/Model.php')) {
    $model = file_get_contents('app/Core/Model.php');
    
    if (strpos($model, 'prepare(') !== false && strpos($model, 'execute(') !== false) {
        $checks[] = "✓ Prepared Statements implementados";
    } else {
        $errors[] = "✗ Prepared Statements NÃO implementados";
    }
} else {
    $errors[] = "✗ Model Base NÃO existe";
}

// 7. Verifica SystemIntegrityMiddleware
echo "[7/15] Verificando SystemIntegrityMiddleware...\n";
if (file_exists('app/Middlewares/SystemIntegrityMiddleware.php')) {
    $checks[] = "✓ SystemIntegrityMiddleware implementado";
    
    // Verifica se está sendo usado
    if (file_exists('public/index.php')) {
        $index = file_get_contents('public/index.php');
        if (strpos($index, 'SystemIntegrityMiddleware') !== false) {
            $checks[] = "✓ SystemIntegrityMiddleware ativo";
        } else {
            $errors[] = "✗ SystemIntegrityMiddleware NÃO ativo";
        }
    }
} else {
    $errors[] = "✗ SystemIntegrityMiddleware NÃO implementado";
}

// 8. Verifica CsrfMiddleware
echo "[8/15] Verificando CsrfMiddleware...\n";
if (file_exists('app/Middlewares/CsrfMiddleware.php')) {
    $checks[] = "✓ CsrfMiddleware implementado";
} else {
    $errors[] = "✗ CsrfMiddleware NÃO implementado";
}

// 9. Verifica AuthMiddleware
echo "[9/15] Verificando AuthMiddleware...\n";
if (file_exists('app/Middlewares/AuthMiddleware.php')) {
    $checks[] = "✓ AuthMiddleware implementado";
} else {
    $errors[] = "✗ AuthMiddleware NÃO implementado";
}

// 10. Verifica configurações de sessão
echo "[10/15] Verificando configurações de sessão...\n";
if (file_exists('public/index.php')) {
    $index = file_get_contents('public/index.php');
    
    if (strpos($index, 'session.cookie_httponly') !== false) {
        $checks[] = "✓ HttpOnly configurado";
    } else {
        $errors[] = "✗ HttpOnly NÃO configurado";
    }
    
    if (strpos($index, 'session.cookie_samesite') !== false) {
        $checks[] = "✓ SameSite configurado";
    } else {
        $errors[] = "✗ SameSite NÃO configurado";
    }
    
    if (strpos($index, 'session_regenerate_id') !== false) {
        $checks[] = "✓ Regeneração de ID implementada";
    } else {
        $warnings[] = "⚠ Regeneração de ID não implementada";
    }
}

// 11. Verifica headers de segurança
echo "[11/15] Verificando headers de segurança...\n";
if (file_exists('public/index.php')) {
    $index = file_get_contents('public/index.php');
    
    $headers = [
        'X-Frame-Options' => 'Clickjacking',
        'X-Content-Type-Options' => 'MIME Sniffing',
        'X-XSS-Protection' => 'XSS',
        'Referrer-Policy' => 'Referrer',
        'Permissions-Policy' => 'Permissões',
        'Content-Security-Policy' => 'CSP'
    ];
    
    foreach ($headers as $header => $protection) {
        if (strpos($index, $header) !== false) {
            $checks[] = "✓ Header {$header} configurado";
        } else {
            $warnings[] = "⚠ Header {$header} não configurado";
        }
    }
}

// 12. Verifica diretório de uploads
echo "[12/15] Verificando diretório de uploads...\n";
if (is_dir('public/uploads')) {
    $checks[] = "✓ Diretório de uploads existe";
    
    // Verifica se PHP está desabilitado
    $htaccess = file_get_contents('.htaccess');
    if (strpos($htaccess, 'php_flag engine off') !== false || 
        strpos($htaccess, 'uploads') !== false) {
        $checks[] = "✓ PHP desabilitado em uploads";
    } else {
        $errors[] = "✗ PHP NÃO desabilitado em uploads";
    }
} else {
    $warnings[] = "⚠ Diretório de uploads não existe";
}

// 13. Verifica diretórios de storage
echo "[13/15] Verificando diretórios de storage...\n";
$storageDirs = ['storage/logs', 'storage/cache', 'storage/sessions'];
foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $checks[] = "✓ Diretório {$dir} existe";
        
        if (is_writable($dir)) {
            $checks[] = "✓ Diretório {$dir} tem permissão de escrita";
        } else {
            $errors[] = "✗ Diretório {$dir} NÃO tem permissão de escrita";
        }
    } else {
        $errors[] = "✗ Diretório {$dir} NÃO existe";
    }
}

// 14. Verifica documentação
echo "[14/15] Verificando documentação...\n";
if (file_exists('SECURITY.md')) {
    $checks[] = "✓ Documentação de segurança existe";
} else {
    $warnings[] = "⚠ Documentação de segurança não existe";
}

// 15. Verifica vendor (composer)
echo "[15/15] Verificando dependências...\n";
if (is_dir('vendor')) {
    $checks[] = "✓ Dependências instaladas";
    
    // Verifica se vendor está protegido
    $htaccess = file_get_contents('.htaccess');
    if (strpos($htaccess, 'vendor') !== false) {
        $checks[] = "✓ Diretório vendor protegido";
    } else {
        $errors[] = "✗ Diretório vendor NÃO protegido";
    }
} else {
    $errors[] = "✗ Dependências NÃO instaladas (execute: composer install)";
}

// Exibe resultados
echo "\n===========================================\n";
echo "  RESULTADOS\n";
echo "===========================================\n\n";

echo "✓ VERIFICAÇÕES APROVADAS (" . count($checks) . "):\n";
foreach ($checks as $check) {
    echo "  {$check}\n";
}

if (!empty($warnings)) {
    echo "\n⚠ AVISOS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "  {$warning}\n";
    }
}

if (!empty($errors)) {
    echo "\n✗ ERROS CRÍTICOS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  {$error}\n";
    }
}

// Score de segurança
$total = count($checks) + count($warnings) + count($errors);
$score = ($total > 0) ? round((count($checks) / $total) * 100) : 0;

echo "\n===========================================\n";
echo "  SCORE DE SEGURANÇA: {$score}%\n";
echo "===========================================\n";

if ($score >= 90) {
    echo "  Status: EXCELENTE ✓\n";
} elseif ($score >= 70) {
    echo "  Status: BOM ⚠\n";
} elseif ($score >= 50) {
    echo "  Status: REGULAR ⚠\n";
} else {
    echo "  Status: CRÍTICO ✗\n";
}

echo "\n";

// Recomendações
if (!empty($errors) || !empty($warnings)) {
    echo "===========================================\n";
    echo "  RECOMENDAÇÕES\n";
    echo "===========================================\n\n";
    
    if (!empty($errors)) {
        echo "1. Corrija os ERROS CRÍTICOS imediatamente\n";
        echo "2. Revise o arquivo SECURITY.md para instruções\n";
    }
    
    if (!empty($warnings)) {
        echo "3. Revise os AVISOS antes de ir para produção\n";
    }
    
    echo "4. Execute este script regularmente\n";
    echo "5. Mantenha backups atualizados\n";
    echo "\n";
}

echo "Verificação concluída em " . date('Y-m-d H:i:s') . "\n\n";

// Retorna código de saída
exit(empty($errors) ? 0 : 1);
