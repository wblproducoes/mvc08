<?php
/**
 * Script Simples para Criar Tabelas
 * Use este se o instalador principal não funcionar
 */

// Configurações - EDITE AQUI
$config = [
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => 'mvc08',
    'db_user' => 'mvc08',
    'db_pass' => 'sua_senha_aqui',
    'db_prefix' => 'sa_'
];

try {
    $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Criando Tabelas...</h2>";
    
    $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
    
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
    
    // Executa
    $statements = explode(';', $cleanedSql);
    $executedCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        if (strlen($statement) < 10) continue;
        
        try {
            $pdo->exec($statement);
            $executedCount++;
            echo ".";
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (stripos($msg, 'already exists') === false && 
                stripos($msg, 'Duplicate') === false) {
                echo "<br><span style='color:red'>Erro: " . htmlspecialchars(substr($msg, 0, 200)) . "</span><br>";
            }
        }
    }
    
    echo "<br><br><h3 style='color:green'>✓ Sucesso! {$executedCount} comandos executados</h3>";
    echo "<p><a href='install.php'>Voltar ao instalador</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color:red'>Erro: " . htmlspecialchars($e->getMessage()) . "</h3>";
}
