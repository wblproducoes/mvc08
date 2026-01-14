<?php
// Backup do código de criação de tabelas - versão segura

$config = $_SESSION['db_config'];
$dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$prefix = $config['db_prefix'];

// Lê o schema SQL
$schemaFile = __DIR__ . '/../database/schema.sql';
$sql = file_get_contents($schemaFile);

// Substitui prefixo
$sql = str_replace('`sa_', '`' . $prefix, $sql);
$sql = str_replace('sa_', $prefix, $sql);

// Remove blocos DELIMITER linha por linha
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
$errors = [];

foreach ($statements as $statement) {
    $statement = trim($statement);
    
    if (strlen($statement) < 10) continue;
    
    try {
        $pdo->exec($statement);
        $executedCount++;
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        if (stripos($msg, 'already exists') === false && 
            stripos($msg, 'Duplicate') === false) {
            $errors[] = substr($msg, 0, 100);
        }
    }
}

if (!empty($errors)) {
    $error = "Alguns comandos falharam: " . implode('; ', array_slice($errors, 0, 3));
} else {
    $success = "Tabelas criadas com sucesso! ({$executedCount} comandos executados)";
}
