<?php
/**
 * Teste de Criação de Tabelas com Debug Completo
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

// CONFIGURE AQUI
$config = [
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => 'mvc08',
    'db_user' => 'mvc08',
    'db_pass' => '', // COLOQUE SUA SENHA
    'db_prefix' => 'sys08_'
];

echo "<h2>Teste de Criação de Tabelas</h2>";
echo "<p><strong>Prefixo:</strong> {$config['db_prefix']}</p>";

try {
    $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green'>✓ Conexão OK</p>";
    
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    echo "<p>Tamanho do schema: " . strlen($sql) . " bytes</p>";
    
    // Substitui prefixo
    $sql = str_replace('`sa_', '`' . $config['db_prefix'], $sql);
    $sql = str_replace('sa_', $config['db_prefix'], $sql);
    
    // Remove blocos DELIMITER
    $lines = explode("\n", $sql);
    $cleanedSql = '';
    $skipBlock = false;
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        if (stripos($trimmed, 'DELIMITER $$') !== false) {
            $skipBlock = true;
            continue;
        }
        if (stripos($trimmed, 'DELIMITER ;') !== false) {
            $skipBlock = false;
            continue;
        }
        
        if ($skipBlock) continue;
        
        if (stripos($trimmed, 'ANALYZE TABLE') !== false ||
            stripos($trimmed, 'OPTIMIZE TABLE') !== false) {
            continue;
        }
        
        $cleanedSql .= $line . "\n";
    }
    
    echo "<p>Tamanho limpo: " . strlen($cleanedSql) . " bytes</p>";
    
    // Executa
    $statements = explode(';', $cleanedSql);
    $executedCount = 0;
    $errors = [];
    
    echo "<h3>Executando comandos...</h3>";
    echo "<div style='max-height:400px; overflow-y:scroll; border:1px solid #ccc; padding:10px; background:#f5f5f5;'>";
    
    foreach ($statements as $i => $statement) {
        $statement = trim($statement);
        
        if (strlen($statement) < 10) continue;
        
        $preview = substr($statement, 0, 80);
        
        try {
            $pdo->exec($statement);
            $executedCount++;
            echo "<div style='color:green'>✓ [{$executedCount}] " . htmlspecialchars($preview) . "...</div>";
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (stripos($msg, 'already exists') === false && 
                stripos($msg, 'Duplicate') === false) {
                $errors[] = $msg;
                echo "<div style='color:red'>✗ " . htmlspecialchars($preview) . "<br>&nbsp;&nbsp;&nbsp;" . htmlspecialchars($msg) . "</div>";
            } else {
                echo "<div style='color:orange'>⚠ " . htmlspecialchars($preview) . " (já existe)</div>";
            }
        }
    }
    
    echo "</div>";
    
    echo "<h3>Resultado:</h3>";
    echo "<p><strong>Comandos executados:</strong> {$executedCount}</p>";
    echo "<p><strong>Erros:</strong> " . count($errors) . "</p>";
    
    // Verifica tabelas criadas
    echo "<h3>Tabelas Criadas:</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        $highlight = (strpos($table, $config['db_prefix']) === 0) ? 'style="color:green; font-weight:bold"' : '';
        echo "<li {$highlight}>{$table}</li>";
    }
    echo "</ul>";
    
    // Verifica especificamente a tabela users
    $userTable = $config['db_prefix'] . 'users';
    $stmt = $pdo->query("SHOW TABLES LIKE '{$userTable}'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green; font-size:18px'>✓ Tabela {$userTable} foi criada com sucesso!</p>";
    } else {
        echo "<p style='color:red; font-size:18px'>✗ Tabela {$userTable} NÃO foi criada!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'><strong>Erro Fatal:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
