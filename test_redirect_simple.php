<?php
// Teste simples de redirecionamento
echo "Testando redirecionamento...\n";

// Simula a URL raiz
$_SERVER['REQUEST_URI'] = '/';

$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$path = preg_replace('#^/#', '', $path);
$path = preg_replace('#[\?\#].*$#', '', $path);
$path = trim($path, '/');
$partes = explode('/', $path);

echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Path: '$path'\n";
echo "Partes: " . implode(',', $partes) . "\n";
echo "Count: " . count($partes) . "\n";

if (count($partes) === 0) {
    echo "REDIRECIONAMENTO ATIVADO!\n";
    echo "URL de destino: /sao-paulo\n";
} else {
    echo "Nenhum redirecionamento necessário\n";
}

echo "\nTeste concluído!\n";
?> 