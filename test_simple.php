<?php
// Teste simples de redirecionamento
echo "Testando redirecionamento...\n";

// Simula a lógica do index.php
$path = '';
$path = preg_replace('#^/#', '', $path);
$path = preg_replace('#[\?\#].*$#', '', $path);
$path = trim($path, '/');
$partes = explode('/', $path);

echo "Path: '$path'\n";
echo "Partes: " . implode(',', $partes) . "\n";
echo "Count: " . count($partes) . "\n";

if (count($partes) === 0) {
    echo "REDIRECIONAMENTO ATIVADO!\n";
    echo "Deve redirecionar para: /sao-paulo\n";
} else {
    echo "Nenhum redirecionamento necessário\n";
}

echo "\nTeste concluído!\n";
?> 