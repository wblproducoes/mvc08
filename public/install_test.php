<?php
// Teste do processamento do schema.sql

$schemaFile = __DIR__ . '/../database/schema.sql';
$sql = file_get_contents($schemaFile);

echo "Tamanho original: " . strlen($sql) . " bytes<br><br>";

// Método simples: remove linha por linha
$lines = explode("\n", $sql);
$cleanedSql = '';
$skipBlock = false;

foreach ($lines as $line) {
    $trimmed = trim($line);
    
    // Detecta início de bloco DELIMITER
    if (stripos($trimmed, 'DELIMITER $$') !== false) {
        $skipBlock = true;
        continue;
    }
    
    // Detecta fim de bloco DELIMITER
    if (stripos($trimmed, 'DELIMITER ;') !== false) {
        $skipBlock = false;
        continue;
    }
    
    // Pula linhas dentro do bloco
    if ($skipBlock) {
        continue;
    }
    
    // Pula ANALYZE e OPTIMIZE
    if (stripos($trimmed, 'ANALYZE TABLE') !== false ||
        stripos($trimmed, 'OPTIMIZE TABLE') !== false) {
        continue;
    }
    
    $cleanedSql .= $line . "\n";
}

echo "Tamanho limpo: " . strlen($cleanedSql) . " bytes<br><br>";

// Conta statements
$statements = explode(';', $cleanedSql);
$count = 0;
foreach ($statements as $stmt) {
    if (strlen(trim($stmt)) > 10) {
        $count++;
    }
}

echo "Statements encontrados: " . $count . "<br><br>";

echo "<h3>Primeiros 5 statements:</h3>";
$i = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (strlen($stmt) > 10) {
        echo "<pre>" . htmlspecialchars(substr($stmt, 0, 200)) . "...</pre><hr>";
        $i++;
        if ($i >= 5) break;
    }
}
