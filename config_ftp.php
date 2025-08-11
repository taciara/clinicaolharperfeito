<?php
/**
 * Configurações do FTP para backup de agendamentos
 * 
 * IMPORTANTE: 
 * 1. Substitua os valores abaixo pelos seus dados reais do FTP
 * 2. Mantenha este arquivo seguro e não o compartilhe
 * 3. Use SFTP quando possível para maior segurança
 */

// Configurações do FTP
$ftp_config = [
    'server' => 'seu-ftp-server.com',     // Ex: ftp.seudominio.com
    'username' => 'seu-usuario',          // Seu usuário FTP
    'password' => 'sua-senha',            // Sua senha FTP
    'port' => 21,                         // Porta padrão FTP (21) ou 22 para SFTP
    'passive' => true,                    // Modo passivo (recomendado)
    'timeout' => 30,                      // Timeout em segundos
    'ssl' => false                        // Use true para FTPS
];

// Diretórios no FTP
$ftp_directories = [
    'csv' => '/backup/csv/',              // Diretório para arquivos CSV
    'json' => '/backup/json/',            // Diretório para arquivos JSON
    'logs' => '/backup/logs/'             // Diretório para logs
];

// Configurações de backup
$backup_config = [
    'max_file_size' => 10 * 1024 * 1024, // 10MB máximo por arquivo
    'max_records_per_file' => 10000,      // Máximo de registros por arquivo
    'compress_files' => true,             // Comprimir arquivos antes do upload
    'delete_local_after_upload' => false, // Deletar arquivo local após upload
    'retry_attempts' => 3                 // Tentativas de upload
];

// Função para obter configuração
function getFTPConfig() {
    global $ftp_config;
    return $ftp_config;
}

// Função para obter diretórios
function getFTPDirectories() {
    global $ftp_directories;
    return $ftp_directories;
}

// Função para obter configuração de backup
function getBackupConfig() {
    global $backup_config;
    return $backup_config;
}

// Função para validar configuração
function validateFTPConfig() {
    $config = getFTPConfig();
    
    if (empty($config['server']) || $config['server'] === 'seu-ftp-server.com') {
        error_log("ERRO: Configuração do FTP não definida em config_ftp.php");
        return false;
    }
    
    if (empty($config['username']) || $config['username'] === 'seu-usuario') {
        error_log("ERRO: Usuário do FTP não definido em config_ftp.php");
        return false;
    }
    
    if (empty($config['password']) || $config['password'] === 'sua-senha') {
        error_log("ERRO: Senha do FTP não definida em config_ftp.php");
        return false;
    }
    
    return true;
}
?> 