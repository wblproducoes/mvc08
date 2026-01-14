# Correção do Erro 500 no Instalador

## Problema
Os regex complexos estão causando erro 500 ao processar o schema.sql

## Solução
Substitua as linhas 173-186 do arquivo `public/install.php`

### DE (linhas com problema):
```php
// Substitui o prefixo padrão 'sa_' pelo prefixo configurado
$sql = preg_replace('/`sa_([a-z_]+)`/i', '`' . $prefix . '$1`', $sql);
$sql = str_replace('sa_', $prefix, $sql);

// Remove blocos DELIMITER (procedures, triggers, eventos) para evitar erros
// Eles podem ser adicionados manualmente depois se necessário
$sql = preg_replace('/DELIMITER \$\$.*?DELIMITER ;/s', '', $sql);
$sql = preg_replace('/CREATE\s+(TRIGGER|PROCEDURE|EVENT).*?END\s*\$\$/si', '', $sql);

// Remove comandos problemáticos
$sql = preg_replace('/CREATE\s+OR\s+REPLACE\s+VIEW/i', 'CREATE VIEW IF NOT EXISTS', $sql);
$sql = preg_replace('/ANALYZE\s+TABLE.*?;/i', '', $sql);
$sql = preg_replace('/OPTIMIZE\s+TABLE.*?;/i', '', $sql);

// Separa comandos por ponto-e-vírgula
$statements = explode(';', $sql);
```

### PARA (código corrigido):
```php
// Substitui prefixo
$sql = str_replace('`sa_', '`' . $prefix, $sql);
$sql = str_replace('sa_', $prefix, $sql);

// Remove blocos DELIMITER linha por linha (mais seguro)
$lines = explode("\n", $sql);
$cleanedSql = '';
$skipBlock = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Detecta blocos DELIMITER
    if (stripos($trimmed, 'DELIMITER $$') !== false) {
        $skipBlock = true;
        continue;
    }
    if (stripos($trimmed, 'DELIMITER ;') !== false) {
        $skipBlock = false;
        continue;
    }
    
    // Pula linhas dentro do bloco
    if ($skipBlock) continue;
    
    // Pula comandos problemáticos
    if (stripos($trimmed, 'ANALYZE TABLE') !== false ||
        stripos($trimmed, 'OPTIMIZE TABLE') !== false) {
        continue;
    }
    
    $cleanedSql .= $line . "\n";
}

// Separa comandos
$statements = explode(';', $cleanedSql);
```

## Como Aplicar

1. Abra `public/install.php`
2. Localize a linha 173 (procure por `// Substitui o prefixo padrão`)
3. Substitua até a linha 186 pelo código corrigido acima
4. Salve o arquivo
5. Tente novamente criar as tabelas

## Explicação

A correção remove os regex complexos (`preg_replace`) que podem causar timeout ou erro de memória em arquivos grandes, e usa um método mais simples e seguro de processar linha por linha.
