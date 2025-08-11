<?php
// Debug do redirecionamento
echo "=== DEBUG REDIRECIONAMENTO ===\n";

// Simula exatamente a lógica do index.php
$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
echo "REQUEST_URI original: " . $path . "\n";

$path = preg_replace('#^/#', '', $path);
echo "Após remover / inicial: " . $path . "\n";

$path = preg_replace('#[\?\#].*$#', '', $path);
echo "Após remover query/hash: " . $path . "\n";

$path = trim($path, '/');
echo "Após trim: " . $path . "\n";

$partes = explode('/', $path);
echo "Partes: " . implode(',', $partes) . "\n";
echo "Count: " . count($partes) . "\n";

if (count($partes) === 0) {
    echo "*** REDIRECIONAMENTO DEVE SER ATIVADO ***\n";
    echo "Deve redirecionar para: /sao-paulo\n";
} else {
    echo "Nenhum redirecionamento necessário\n";
}

echo "\n=== FIM DEBUG ===\n";
?> 