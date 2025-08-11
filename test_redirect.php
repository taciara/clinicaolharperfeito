<?php
// Teste do redirecionamento
echo "Testando redirecionamento...\n";

// Simula a URL raiz
$path = '';
$path = preg_replace('#^/#', '', $path);
$path = preg_replace('#[\?\#].*$#', '', $path);
$path = trim($path, '/');
$partes = explode('/', $path);

echo "Path: '$path'\n";
echo "Partes: ";
print_r($partes);
echo "Count: " . count($partes) . "\n";

if (count($partes) === 0) {
    echo "REDIRECIONAMENTO ATIVADO: /sao-paulo\n";
} else {
    echo "Nenhum redirecionamento necessário\n";
}

// Testa com URL com conteúdo
$path2 = '/sao-paulo/campinas';
$path2 = preg_replace('#^/#', '', $path2);
$path2 = preg_replace('#[\?\#].*$#', '', $path2);
$path2 = trim($path2, '/');
$partes2 = explode('/', $path2);

echo "\nTeste com URL completa:\n";
echo "Path2: '$path2'\n";
echo "Partes2: ";
print_r($partes2);
echo "Count2: " . count($partes2) . "\n";
?> 