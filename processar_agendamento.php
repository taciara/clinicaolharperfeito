<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
        // Score mínimo de 0.5 (você pode ajustar conforme necessário)
        $is_valid = $response['score'] >= 0.5;
        error_log("Score reCAPTCHA: " . $response['score'] . " - Válido: " . ($is_valid ? 'sim' : 'não'));
        return $is_valid;
    }
    
    error_log("reCAPTCHA falhou: success=" . ($response['success'] ?? 'null') . ", score=" . ($response['score'] ?? 'null'));
    return false;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Recebe os dados do formulário
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

// Log para debug
error_log("Dados recebidos: " . $raw_input);

if (!$input) {
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

    // Valida reCAPTCHA (opcional se não carregou)
    if (!empty($recaptcha_token)) {
        error_log("Validando reCAPTCHA token: " . substr($recaptcha_token, 0, 20) . "...");
        $recaptcha_valid = validarRecaptcha($recaptcha_token);
        error_log("Resultado reCAPTCHA: " . ($recaptcha_valid ? 'válido' : 'inválido'));
        if (!$recaptcha_valid) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Verificação reCAPTCHA falhou']);
            exit;
        }
    } else {
        error_log("Nenhum token reCAPTCHA fornecido");
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

// Chaves do reCAPTCHA já definidas no início do arquivo

// Função para enviar dados para Google Sheets via Google Apps Script
function enviarParaGoogleSheets($dados, $spreadsheet_id) {
    // URL do Google Apps Script (você precisará criar este script)
    $script_url = 'https://script.google.com/macros/s/AKfycbxNUWLSooaDm11BVMokvGngAwJ5GF83lgvLSECa0PnNmojfesZ3WjXdZK86YGH2zFMypA/exec'; // Será criado depois
    
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

// Tenta enviar para Google Sheets
$resultado_sheets = enviarParaGoogleSheets($dados, $spreadsheet_id);

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

// Resposta de sucesso
$response = [
    'success' => true,
    'message' => 'Agendamento realizado com sucesso!',
    'sheets_success' => $resultado_sheets !== false,
    'dados' => $dados
];

echo json_encode($response);
?> 