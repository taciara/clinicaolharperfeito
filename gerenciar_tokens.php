<?php
// Sistema de Gerenciamento de Tokens para Página de Agradecimento
// Este arquivo gerencia a criação e validação de tokens de acesso

class TokenManager {
    private $tokens_file = 'tokens_agendamento.json';
    private $tokens = [];
    private $token_expiry = 3600; // 1 hora em segundos
    
    public function __construct() {
        $this->loadTokens();
        $this->cleanExpiredTokens();
    }
    
    // Carrega tokens do arquivo
    private function loadTokens() {
        if (file_exists($this->tokens_file)) {
            $content = file_get_contents($this->tokens_file);
            $this->tokens = json_decode($content, true) ?: [];
        }
    }
    
    // Salva tokens no arquivo
    private function saveTokens() {
        file_put_contents($this->tokens_file, json_encode($this->tokens, JSON_PRETTY_PRINT));
    }
    
    // Cria um novo token
    public function createToken($unidade) {
        $token = bin2hex(random_bytes(32)); // Token mais seguro
        $expiry = time() + $this->token_expiry;
        
        $this->tokens[$token] = [
            'unidade' => $unidade,
            'created' => time(),
            'expires' => $expiry,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $this->saveTokens();
        return $token;
    }
    
    // Valida um token
    public function validateToken($token, $unidade = null) {
        if (!isset($this->tokens[$token])) {
            return false;
        }
        
        $token_data = $this->tokens[$token];
        
        // Verifica se expirou
        if (time() > $token_data['expires']) {
            unset($this->tokens[$token]);
            $this->saveTokens();
            return false;
        }
        
        // Verifica se a unidade corresponde (se fornecida)
        if ($unidade && $token_data['unidade'] !== $unidade) {
            return false;
        }
        
        return true;
    }
    
    // Remove um token usado
    public function removeToken($token) {
        if (isset($this->tokens[$token])) {
            unset($this->tokens[$token]);
            $this->saveTokens();
            return true;
        }
        return false;
    }
    
    // Limpa tokens expirados
    private function cleanExpiredTokens() {
        $current_time = time();
        $cleaned = false;
        
        foreach ($this->tokens as $token => $data) {
            if ($current_time > $data['expires']) {
                unset($this->tokens[$token]);
                $cleaned = true;
            }
        }
        
        if ($cleaned) {
            $this->saveTokens();
        }
    }
    
    // Retorna estatísticas dos tokens
    public function getStats() {
        return [
            'total' => count($this->tokens),
            'expired' => count(array_filter($this->tokens, function($data) {
                return time() > $data['expires'];
            }))
        ];
    }
}

// Função para criar token via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $tokenManager = new TokenManager();
    
    switch ($_POST['action']) {
        case 'create':
            if (isset($_POST['unidade'])) {
                $token = $tokenManager->createToken($_POST['unidade']);
                echo json_encode(['success' => true, 'token' => $token]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Unidade não fornecida']);
            }
            break;
            
        case 'validate':
            if (isset($_POST['token']) && isset($_POST['unidade'])) {
                $valid = $tokenManager->validateToken($_POST['token'], $_POST['unidade']);
                echo json_encode(['success' => true, 'valid' => $valid]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Token ou unidade não fornecidos']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Ação inválida']);
    }
    exit;
}
?>
