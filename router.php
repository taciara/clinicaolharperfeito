<?php
// Se for um arquivo real (imagem, JS, CSS etc.), serve normalmente
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if (is_file($file)) {
        return false;
    }
}

// Se for um diretório real, também ignora
if (is_dir(__DIR__ . $_SERVER["REQUEST_URI"])) {
    return false;
}

// SPA: tudo vai para index.php
require_once __DIR__ . '/index.php';
