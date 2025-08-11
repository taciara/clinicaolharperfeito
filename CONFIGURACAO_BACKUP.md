# 📋 Configuração do Sistema de Backup

## 🎯 **Funcionalidades Implementadas**

O sistema agora salva os dados de agendamento em **4 locais diferentes**:

1. **Google Sheets** (principal)
2. **Arquivo CSV local** (`agendamentos.csv`)
3. **Arquivo JSON local** (`agendamentos.json`)
4. **Upload FTP** (opcional)

---

## ⚙️ **Configuração do FTP**

### **Passo 1: Editar `config_ftp.php`**

Abra o arquivo `config_ftp.php` e substitua os valores:

```php
$ftp_config = [
    'server' => 'ftp.seudominio.com',     // Seu servidor FTP
    'username' => 'seu-usuario',          // Seu usuário FTP
    'password' => 'sua-senha',            // Sua senha FTP
    'port' => 21,                         // Porta (21 para FTP, 22 para SFTP)
    'passive' => true,                    // Modo passivo (recomendado)
    'timeout' => 30,                      // Timeout em segundos
    'ssl' => false                        // true para FTPS
];
```

### **Passo 2: Configurar Diretórios FTP**

```php
$ftp_directories = [
    'csv' => '/backup/csv/',              // Para arquivos CSV
    'json' => '/backup/json/',            // Para arquivos JSON
    'logs' => '/backup/logs/'             // Para logs
];
```

---

## 📁 **Arquivos Gerados**

### **1. `agendamentos.csv`**
- Formato: CSV com cabeçalhos
- Localização: Raiz do projeto
- Estrutura:
```csv
Data,Hora,Nome,Telefone,Email,Unidade,Estado,Cidade,Localidade_URL,Campanha,Origem_Clique,URL_Completa,IP,User_Agent
```

### **2. `agendamentos.json`**
- Formato: JSON estruturado
- Localização: Raiz do projeto
- Estrutura:
```json
[
  {
    "timestamp": 1234567890,
    "data_hora": "2024-01-15 14:30:00",
    "dados": {
      "data": "15/01/2024",
      "hora": "14:30:00",
      "nome": "João Silva",
      "telefone": "(11) 99999-9999",
      "email": "joao@email.com",
      "unidade": "Vila Mariana",
      "estado": "sao-paulo",
      "cidade": "sao-paulo",
      "localidade_url": "São Paulo",
      "campanha": "Orgânico",
      "origem_clique": "Direto",
      "url_completa": "https://clinicaolharperfeito.com.br/sao-paulo",
      "ip": "192.168.1.1",
      "user_agent": "Mozilla/5.0..."
    }
  }
]
```

### **3. `agendamentos_log.txt`**
- Formato: Texto simples
- Localização: Raiz do projeto
- Estrutura:
```
2024-01-15 14:30:00 | Nome: João Silva | Telefone: (11) 99999-9999 | Email: joao@email.com | Unidade: Vila Mariana | Estado: sao-paulo | Cidade: sao-paulo | Campanha: Orgânico | IP: 192.168.1.1
```

---

## 🔧 **Configurações Avançadas**

### **Backup Configurações**
```php
$backup_config = [
    'max_file_size' => 10 * 1024 * 1024, // 10MB máximo por arquivo
    'max_records_per_file' => 10000,      // Máximo de registros por arquivo
    'compress_files' => true,             // Comprimir arquivos antes do upload
    'delete_local_after_upload' => false, // Deletar arquivo local após upload
    'retry_attempts' => 3                 // Tentativas de upload
];
```

---

## 🚀 **Como Testar**

### **1. Teste Local**
```bash
# Verifique se os arquivos foram criados
ls -la agendamentos.*
```

### **2. Teste FTP**
1. Configure o `config_ftp.php`
2. Faça um agendamento de teste
3. Verifique os logs do servidor
4. Confirme se os arquivos aparecem no FTP

### **3. Verificar Logs**
```bash
# Ver logs do PHP
tail -f /var/log/apache2/error.log

# Ver logs do sistema
tail -f agendamentos_log.txt
```

---

## 📊 **Resposta da API**

A API agora retorna informações detalhadas:

```json
{
  "success": true,
  "message": "Agendamento realizado com sucesso!",
  "sheets_success": true,
  "csv_success": true,
  "json_success": true,
  "ftp_csv_success": true,
  "ftp_json_success": true,
  "dados": {
    "data": "15/01/2024",
    "hora": "14:30:00",
    "nome": "João Silva",
    "telefone": "(11) 99999-9999",
    "email": "joao@email.com",
    "unidade": "Vila Mariana",
    "estado": "sao-paulo",
    "cidade": "sao-paulo",
    "localidade_url": "São Paulo",
    "campanha": "Orgânico",
    "origem_clique": "Direto",
    "url_completa": "https://clinicaolharperfeito.com.br/sao-paulo",
    "ip": "192.168.1.1",
    "user_agent": "Mozilla/5.0..."
  }
}
```

---

## 🔒 **Segurança**

### **Recomendações:**
1. **Use SFTP** quando possível (porta 22)
2. **Mantenha `config_ftp.php` seguro**
3. **Não compartilhe credenciais**
4. **Use senhas fortes**
5. **Monitore logs regularmente**

### **Permissões de Arquivo:**
```bash
chmod 600 config_ftp.php
chmod 644 agendamentos.*
```

---

## 🆘 **Troubleshooting**

### **Problema: FTP não conecta**
- Verifique servidor, usuário e senha
- Teste conexão manualmente
- Verifique firewall

### **Problema: Arquivos não são criados**
- Verifique permissões de escrita
- Verifique espaço em disco
- Verifique logs do PHP

### **Problema: Upload FTP falha**
- Verifique configuração do FTP
- Teste conexão FTP manualmente
- Verifique diretórios remotos

---

## 📞 **Suporte**

Se precisar de ajuda:
1. Verifique os logs do servidor
2. Teste cada componente separadamente
3. Confirme configurações do FTP
4. Verifique permissões de arquivo

**Sistema de backup configurado com sucesso! 🎉** 