<?php
// Teste de processamento de URL
echo "=== TESTE DE PROCESSAMENTO DE URL ===\n";

// Simula diferentes URLs
$test_urls = [
    '/',
    '/sao-paulo',
    '/sao-paulo/campinas',
    '/clinica/agendamento/',
    '/clinica/agendamento/sao-paulo'
];

foreach ($test_urls as $test_url) {
    echo "\n--- Testando URL: $test_url ---\n";
    
    // Simula a lógica atual
    $path = $test_url;
    $path = preg_replace('#^/#', '', $path);
    $path = preg_replace('#[\?\#].*$#', '', $path);
    $path = trim($path, '/');
    $partes = explode('/', $path);
    
    echo "Path processado: '$path'\n";
    echo "Partes: " . implode(',', $partes) . "\n";
    echo "Count: " . count($partes) . "\n";
    
    if (count($partes) === 0) {
        echo "*** REDIRECIONAMENTO ATIVADO ***\n";
    } else {
        echo "Nenhum redirecionamento\n";
    }
}

echo "\n=== FIM TESTE ===\n";
?> 