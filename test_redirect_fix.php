<?php
// Teste de redirecionamento corrigido
echo "=== TESTE DE REDIRECIONAMENTO CORRIGIDO ===\n";

// Simula a URL raiz
$_SERVER['REQUEST_URI'] = '/';

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
    
    // Simula o redirecionamento
    echo "Simulando redirecionamento...\n";
    echo "Status: 302 Found\n";
    echo "Location: /sao-paulo\n";
    
    // Aqui seria o redirecionamento real
    // header('Location: /sao-paulo');
    // exit;
} else {
    echo "Nenhum redirecionamento necessário\n";
}

echo "\n=== FIM TESTE ===\n";
?> 