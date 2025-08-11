<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://clinicaolharperfeito.com.br');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Inclui configurações do FTP (opcional)
$ftp_config_loaded = false;
if (file_exists('config_ftp.php')) {
    try {
        include_once 'config_ftp.php';
        $ftp_config_loaded = true;
    } catch (Exception $e) {
        error_log("Erro ao carregar config_ftp.php: " . $e->getMessage());
    }
} else {
    error_log("Arquivo config_ftp.php não encontrado - FTP desabilitado");
}

// Chaves do reCAPTCHA
$recaptcha_secret_key = '6LfD-osrAAAAAM2YpTVIw9ak4qL5rWJob4FTLNZx';



// Função para validar reCAPTCHA v3

function validarRecaptcha($token) {

    global $recaptcha_secret_key;

    

    if (empty($token)) {

        return false;

    }

    

    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $data = [

        'secret' => $recaptcha_secret_key,

        'response' => $token,

        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''

    ];

    

    $options = [

        'http' => [

            'header' => "Content-type: application/x-www-form-urlencoded\r\n",

            'method' => 'POST',

            'content' => http_build_query($data)

        ]

    ];

    

    $context = stream_context_create($options);

    $result = file_get_contents($url, false, $context);

    $response = json_decode($result, true);

    

    error_log("Resposta reCAPTCHA: " . json_encode($response));

    

    // Para reCAPTCHA v3, verificamos o score (0.0 = bot, 1.0 = humano)

    if ($response['success'] && isset($response['score'])) {

        // Score mínimo de 0.3 (mais permissivo)

        $is_valid = $response['score'] >= 0.3;

        error_log("Score reCAPTCHA: " . $response['score'] . " - Válido: " . ($is_valid ? 'sim' : 'não'));

        return $is_valid;

    }

    

    error_log("reCAPTCHA falhou: success=" . ($response['success'] ?? 'null') . ", score=" . ($response['score'] ?? 'null'));

    return false;

}



// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Recebe os dados do formulário
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

if (!$input) {
    error_log("Dados inválidos recebidos");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Valida os campos obrigatórios

$nome = trim($input['nome'] ?? '');

$telefone = trim($input['telefone'] ?? '');

$email = trim($input['email'] ?? '');

$unidade = trim($input['unidade'] ?? '');

$estado = trim($input['estado'] ?? '');

$cidade = trim($input['cidade'] ?? '');

$localidade_url = trim($input['localidade_url'] ?? '');

$campanha = trim($input['campanha'] ?? '');

$origem_clique = trim($input['origem_clique'] ?? '');

$url_completa = trim($input['url_completa'] ?? '');

$recaptcha_token = trim($input['recaptcha_token'] ?? '');



if (empty($nome) || empty($telefone) || empty($email) || empty($unidade)) {

    http_response_code(400);

    echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);

    exit;

}



// Valida reCAPTCHA (opcional - permite envio sem token)

if (!empty($recaptcha_token)) {

    error_log("Validando reCAPTCHA token: " . substr($recaptcha_token, 0, 20) . "...");

    $recaptcha_valid = validarRecaptcha($recaptcha_token);

    error_log("Resultado reCAPTCHA: " . ($recaptcha_valid ? 'válido' : 'inválido'));
    
    // Se o reCAPTCHA falhar, ainda permite o envio mas loga

    if (!$recaptcha_valid) {

        error_log("reCAPTCHA falhou, mas permitindo envio do formulário");

    }

} else {

    error_log("Nenhum token reCAPTCHA fornecido - permitindo envio");

}



// Valida email

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode(['success' => false, 'message' => 'Email inválido']);

    exit;

}



// Data e hora atual

$data = date('d/m/Y');

$hora = date('H:i:s');

$ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';



// Dados para enviar para o Google Sheets

$dados = [

    'data' => $data,

    'hora' => $hora,

    'nome' => $nome,

    'telefone' => $telefone,

    'email' => $email,

    'unidade' => $unidade,

    'estado' => $estado,

    'cidade' => $cidade,

    'localidade_url' => $localidade_url,

    'campanha' => $campanha,

    'origem_clique' => $origem_clique,

    'url_completa' => $url_completa,

    'ip' => $ip,

    'user_agent' => $user_agent

];



// Log dos dados que serão enviados

error_log("Dados para Google Sheets: " . json_encode($dados));



// ID da sua planilha do Google Drive

$spreadsheet_id = '1W9OAplQEFABJFWlbhNbhw4bwwuBCXZWw6upQ5lCSx0Q';



// Função para enviar dados para Google Sheets via Google Apps Script

function enviarParaGoogleSheets($dados, $spreadsheet_id) {

    // URL do Google Apps Script

    $script_url = 'https://script.google.com/macros/s/AKfycbxNUWLSooaDm11BVMokvGngAwJ5GF83lgvLSECa0PnNmojfesZ3WjXdZK86YGH2zFMypA/exec';

    

    // Dados para enviar

    $post_data = [

        'spreadsheet_id' => $spreadsheet_id,

        'data' => $dados

    ];

    

    // Log do que está sendo enviado

    error_log("Enviando para Google Sheets: " . json_encode($post_data));

    

    // Configuração do cURL

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $script_url);

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        'Content-Type: application/json',

        'Content-Length: ' . strlen(json_encode($post_data))

    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    

    $response = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    

    // Log da resposta

    error_log("Resposta Google Sheets - HTTP Code: " . $http_code . ", Response: " . $response);

    

    if ($http_code === 200) {

        return json_decode($response, true);

    }

    

    return false;

}

// Função para salvar dados em CSV
function salvarCSV($dados) {
    $csv_file = 'agendamentos.csv';
    $csv_line = [
        $dados['data'],
        $dados['hora'],
        $dados['nome'],
        $dados['telefone'],
        $dados['email'],
        $dados['unidade'],
        $dados['estado'],
        $dados['cidade'],
        $dados['localidade_url'],
        $dados['campanha'],
        $dados['origem_clique'],
        $dados['url_completa'],
        $dados['ip'],
        $dados['user_agent']
    ];
    
    // Se o arquivo não existe, cria o cabeçalho
    if (!file_exists($csv_file)) {
        $header = [
            'Data',
            'Hora',
            'Nome',
            'Telefone',
            'Email',
            'Unidade',
            'Estado',
            'Cidade',
            'Localidade_URL',
            'Campanha',
            'Origem_Clique',
            'URL_Completa',
            'IP',
            'User_Agent'
        ];
        file_put_contents($csv_file, implode(',', $header) . "\n");
    }
    
    // Adiciona a nova linha
    $csv_content = implode(',', array_map(function($field) {
        return '"' . str_replace('"', '""', $field) . '"';
    }, $csv_line)) . "\n";
    
    return file_put_contents($csv_file, $csv_content, FILE_APPEND | LOCK_EX);
}

// Função para salvar dados em JSON
function salvarJSON($dados) {
    $json_file = 'agendamentos.json';
    $timestamp = time();
    
    // Carrega dados existentes ou cria array vazio
    $json_data = [];
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        if ($json_content) {
            $json_data = json_decode($json_content, true) ?: [];
        }
    }
    
    // Adiciona novo registro com timestamp
    $json_data[] = [
        'timestamp' => $timestamp,
        'data_hora' => date('Y-m-d H:i:s', $timestamp),
        'dados' => $dados
    ];
    
    // Salva o arquivo JSON
    return file_put_contents($json_file, json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Função para fazer upload para FTP
function uploadParaFTP($arquivo_local, $arquivo_remoto) {
    global $ftp_config_loaded;
    
    // Verifica se o FTP está configurado
    if (!$ftp_config_loaded) {
        error_log("Configuração do FTP não carregada - upload cancelado");
        return false;
    }
    
    // Verifica se as funções FTP estão disponíveis
    if (!function_exists('ftp_connect') || !function_exists('validateFTPConfig')) {
        error_log("Funções FTP não disponíveis - upload cancelado");
        return false;
    }
    
    // Valida configuração do FTP
    if (!validateFTPConfig()) {
        error_log("Configuração do FTP inválida - upload cancelado");
        return false;
    }
    
    $config = getFTPConfig();
    $directories = getFTPDirectories();
    
    // Conecta ao FTP
    $conn_id = ftp_connect($config['server'], $config['port']);
    if (!$conn_id) {
        error_log("Erro ao conectar ao FTP: " . $config['server']);
        return false;
    }
    
    // Faz login
    $login = ftp_login($conn_id, $config['username'], $config['password']);
    if (!$login) {
        error_log("Erro no login FTP");
        ftp_close($conn_id);
        return false;
    }
    
    // Ativa modo passivo se configurado
    if ($config['passive']) {
        ftp_pasv($conn_id, true);
    }
    
    // Define timeout
    ftp_set_option($conn_id, FTP_TIMEOUT_SEC, $config['timeout']);
    
    // Determina o diretório baseado no tipo de arquivo
    $remote_dir = '';
    if (strpos($arquivo_local, '.csv') !== false) {
        $remote_dir = $directories['csv'];
    } elseif (strpos($arquivo_local, '.json') !== false) {
        $remote_dir = $directories['json'];
    } else {
        $remote_dir = $directories['logs'];
    }
    
    // Cria diretório remoto se não existir
    $remote_path = $remote_dir . basename($arquivo_local);
    
    // Faz upload do arquivo
    $upload = ftp_put($conn_id, $remote_path, $arquivo_local, FTP_ASCII);
    
    // Fecha conexão
    ftp_close($conn_id);
    
    if ($upload) {
        error_log("Upload FTP bem-sucedido: $arquivo_local -> $remote_path");
        return true;
    } else {
        error_log("Erro no upload FTP: $arquivo_local -> $remote_path");
        return false;
    }
}



// Tenta enviar para Google Sheets
$resultado_sheets = enviarParaGoogleSheets($dados, $spreadsheet_id);

// Salva em CSV local
$resultado_csv = salvarCSV($dados);

// Salva em JSON local
$resultado_json = salvarJSON($dados);

// Salva também em arquivo local como backup
$log_file = 'agendamentos_log.txt';
$log_entry = date('Y-m-d H:i:s') . " | " . 
             "Nome: $nome | " .
             "Telefone: $telefone | " .
             "Email: $email | " .
             "Unidade: $unidade | " .
             "Estado: $estado | " .
             "Cidade: $cidade | " .
             "Campanha: $campanha | " .
             "IP: $ip\n";

file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Faz upload para FTP se configurado corretamente
$upload_csv = false;
$upload_json = false;

if ($ftp_config_loaded) { // Use $ftp_config_loaded aqui
    $upload_csv = uploadParaFTP('agendamentos.csv', 'agendamentos.csv');
    $upload_json = uploadParaFTP('agendamentos.json', 'agendamentos.json');
} else {
    error_log("Upload FTP desabilitado - configure config_ftp.php");
}

// Resposta de sucesso
$response = [
    'success' => true,
    'message' => 'Agendamento realizado com sucesso!',
    'sheets_success' => $resultado_sheets !== false,
    'csv_success' => $resultado_csv !== false,
    'json_success' => $resultado_json !== false,
    'ftp_csv_success' => $upload_csv,
    'ftp_json_success' => $upload_json,
    'dados' => $dados
];

echo json_encode($response);
?> 