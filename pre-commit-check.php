<?php
/**
 * Verificação Pré-Commit para GitHub
 * 
 * Execute este script antes de fazer commit para garantir que
 * nenhum arquivo sensível será enviado ao repositório.
 * 
 * Uso: php pre-commit-check.php
 * 
 * @package Sistema MVC08
 * @version 1.0.0
 */

echo "===========================================\n";
echo "  VERIFICAÇÃO PRÉ-COMMIT - GITHUB\n";
echo "  Sistema MVC08\n";
echo "===========================================\n\n";

$errors = [];
$warnings = [];
$checks = [];

// 1. Verifica se .env existe (não deve ser commitado)
echo "[1/10] Verificando arquivo .env...\n";
if (file_exists('.env')) {
    $checks[] = "✓ .env existe localmente";
    
    // Verifica se está no .gitignore
    $gitignore = file_get_contents('.gitignore');
    if (strpos($gitignore, '.env') !== false) {
        $checks[] = "✓ .env está no .gitignore";
    } else {
        $errors[] = "✗ .env NÃO está no .gitignore";
    }
    
    // Verifica se está sendo rastreado pelo git
    exec('git ls-files .env', $output, $return);
    if (empty($output)) {
        $checks[] = "✓ .env NÃO está sendo rastreado pelo git";
    } else {
        $errors[] = "✗ .env ESTÁ sendo rastreado pelo git (CRÍTICO!)";
    }
} else {
    $warnings[] = "⚠ .env não existe (use .env.example)";
}

// 2. Verifica vendor/
echo "[2/10] Verificando diretório vendor/...\n";
if (is_dir('vendor')) {
    $checks[] = "✓ vendor/ existe localmente";
    
    $gitignore = file_get_contents('.gitignore');
    if (strpos($gitignore, '/vendor/') !== false || strpos($gitignore, 'vendor/') !== false) {
        $checks[] = "✓ vendor/ está no .gitignore";
    } else {
        $errors[] = "✗ vendor/ NÃO está no .gitignore";
    }
    
    exec('git ls-files vendor/', $output, $return);
    if (empty($output)) {
        $checks[] = "✓ vendor/ NÃO está sendo rastreado";
    } else {
        $errors[] = "✗ vendor/ ESTÁ sendo rastreado (pode causar problemas)";
    }
}

// 3. Verifica storage/logs/
echo "[3/10] Verificando storage/logs/...\n";
exec('git ls-files storage/logs/*.log', $output, $return);
if (empty($output)) {
    $checks[] = "✓ Logs NÃO estão sendo rastreados";
} else {
    $errors[] = "✗ Arquivos de log ESTÃO sendo rastreados";
}

// 4. Verifica storage/cache/
echo "[4/10] Verificando storage/cache/...\n";
exec('git ls-files storage/cache/*', $output, $return);
if (empty($output)) {
    $checks[] = "✓ Cache NÃO está sendo rastreado";
} else {
    $warnings[] = "⚠ Arquivos de cache estão sendo rastreados";
}

// 5. Verifica storage/sessions/
echo "[5/10] Verificando storage/sessions/...\n";
exec('git ls-files storage/sessions/*', $output, $return);
if (empty($output)) {
    $checks[] = "✓ Sessões NÃO estão sendo rastreadas";
} else {
    $errors[] = "✗ Arquivos de sessão ESTÃO sendo rastreados";
}

// 6. Verifica uploads/
echo "[6/10] Verificando public/uploads/...\n";
exec('git ls-files public/uploads/*', $output, $return);
if (empty($output)) {
    $checks[] = "✓ Uploads NÃO estão sendo rastreados";
} else {
    $warnings[] = "⚠ Arquivos de upload estão sendo rastreados";
}

// 7. Verifica composer.lock
echo "[7/10] Verificando composer.lock...\n";
$gitignore = file_get_contents('.gitignore');
if (strpos($gitignore, 'composer.lock') !== false) {
    $checks[] = "✓ composer.lock está no .gitignore";
    $warnings[] = "⚠ Considere commitar composer.lock para garantir versões";
} else {
    $checks[] = "✓ composer.lock será commitado (recomendado)";
}

// 8. Verifica arquivos grandes (>50MB)
echo "[8/10] Verificando arquivos grandes...\n";
$largeFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('.', RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getSize() > 50 * 1024 * 1024) {
        $largeFiles[] = $file->getPathname() . ' (' . round($file->getSize() / 1024 / 1024, 2) . ' MB)';
    }
}

if (empty($largeFiles)) {
    $checks[] = "✓ Nenhum arquivo maior que 50MB";
} else {
    foreach ($largeFiles as $file) {
        $errors[] = "✗ Arquivo grande: {$file}";
    }
}

// 9. Verifica .gitignore
echo "[9/10] Verificando .gitignore...\n";
if (file_exists('.gitignore')) {
    $checks[] = "✓ .gitignore existe";
    
    $required = ['.env', 'vendor', 'storage/logs', 'storage/cache', 'storage/sessions'];
    $gitignore = file_get_contents('.gitignore');
    
    foreach ($required as $item) {
        if (strpos($gitignore, $item) !== false) {
            $checks[] = "✓ .gitignore contém: {$item}";
        } else {
            $errors[] = "✗ .gitignore NÃO contém: {$item}";
        }
    }
} else {
    $errors[] = "✗ .gitignore NÃO existe";
}

// 10. Verifica README.md
echo "[10/10] Verificando README.md...\n";
if (file_exists('README.md')) {
    $checks[] = "✓ README.md existe";
    
    $readme = file_get_contents('README.md');
    if (strlen($readme) > 100) {
        $checks[] = "✓ README.md tem conteúdo";
    } else {
        $warnings[] = "⚠ README.md está muito curto";
    }
} else {
    $warnings[] = "⚠ README.md não existe";
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

// Status final
echo "\n===========================================\n";
if (empty($errors)) {
    echo "  STATUS: ✅ PRONTO PARA COMMIT\n";
    echo "===========================================\n\n";
    
    echo "Comandos sugeridos:\n";
    echo "  git add .\n";
    echo "  git commit -m \"feat: auditoria de segurança completa (v1.6.0)\"\n";
    echo "  git push origin main\n\n";
    
    exit(0);
} else {
    echo "  STATUS: ❌ NÃO PRONTO PARA COMMIT\n";
    echo "===========================================\n\n";
    
    echo "Corrija os erros críticos antes de commitar!\n\n";
    
    if (in_array("✗ .env ESTÁ sendo rastreado pelo git (CRÍTICO!)", $errors)) {
        echo "Para remover .env do git:\n";
        echo "  git rm --cached .env\n";
        echo "  git commit -m \"chore: remove .env do repositório\"\n\n";
    }
    
    if (in_array("✗ vendor/ ESTÁ sendo rastreado (pode causar problemas)", $errors)) {
        echo "Para remover vendor/ do git:\n";
        echo "  git rm -r --cached vendor/\n";
        echo "  git commit -m \"chore: remove vendor do repositório\"\n\n";
    }
    
    exit(1);
}
