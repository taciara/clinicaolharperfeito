# Configuração da Integração com Google Sheets

## 📋 Passo a Passo para Configurar

### 1. Configurar o Google Apps Script

1. **Acesse o Google Apps Script:**
   - Vá para [script.google.com](https://script.google.com)
   - Faça login com sua conta Google

2. **Crie um novo projeto:**
   - Clique em "Novo projeto"
   - Dê o nome: "Clinica Agendamentos"

3. **Cole o código do Google Apps Script:**
   - Abra o arquivo `google_apps_script.js` deste projeto
   - Copie todo o conteúdo
   - Cole no editor do Google Apps Script

4. **Salve o projeto:**
   - Clique em "Salvar" (ícone de disquete)
   - Dê um nome ao projeto

### 2. Configurar a Planilha

1. **Abra sua planilha:**
   - Acesse: https://docs.google.com/spreadsheets/d/1W9OAplQEFABJFWlbhNbhw4bwwuBCXZWw6upQ5lCSx0Q/edit

2. **Execute a função de configuração:**
   - No Google Apps Script, selecione a função `setupSpreadsheet`
   - Clique no botão "Executar" (▶️)
   - Autorize o acesso quando solicitado

3. **Verifique se os cabeçalhos foram criados:**
   - A planilha deve ter as colunas:
     - Data/Hora
     - Nome
     - Telefone
     - Email
     - Unidade
     - Localidade
     - IP
     - User Agent

### 3. Publicar o Web App

1. **Configure o deploy:**
   - No Google Apps Script, clique em "Deploy" > "New deployment"
   - Tipo: "Web app"
   - Execute as: "Me"
   - Who has access: "Anyone"
   - Clique em "Deploy"

2. **Copie a URL do Web App:**
   - Após o deploy, você receberá uma URL como:
   - `https://script.google.com/macros/s/AKfycbz.../exec`
   - **Copie esta URL!**

3. **Informação da imaplantação**
    - Código de implantação:
    - `AKfycbwtcNYh_abXCAdpH9vkgpWCcIgY5zBM3zavSfyb_L-nxrGOo7mIJfQLCDPh_6lfzlAwcQ`
    - `AKfycbxNUWLSooaDm11BVMokvGngAwJ5GF83lgvLSECa0PnNmojfesZ3WjXdZK86YGH2zFMypA` V2
    - App da Web:
    - `https://script.google.com/macros/s/AKfycbwtcNYh_abXCAdpH9vkgpWCcIgY5zBM3zavSfyb_L-nxrGOo7mIJfQLCDPh_6lfzlAwcQ/exec`
    - `https://script.google.com/macros/s/AKfycbxNUWLSooaDm11BVMokvGngAwJ5GF83lgvLSECa0PnNmojfesZ3WjXdZK86YGH2zFMypA/exec` V2

### 4. Atualizar o PHP

1. **Edite o arquivo `processar_agendamento.php`:**
   - Localize a linha com `$script_url`
   - Substitua `'https://script.google.com/macros/s/AKfycbz.../exec'`
   - Pela URL que você copiou no passo anterior

```php
$script_url = 'https://script.google.com/macros/s/AKfycbwtcNYh_abXCAdpH9vkgpWCcIgY5zBM3zavSfyb_L-nxrGOo7mIJfQLCDPh_6lfzlAwcQ/exec';
```

### 5. Testar a Integração

1. **Teste o formulário:**
   - Acesse sua página de agendamento
   - Preencha e envie o formulário
   - Verifique se o modal aparece
   - Verifique se os dados chegam na planilha

2. **Verificar logs:**
   - Os dados também são salvos em `agendamentos_log.txt`
   - Verifique este arquivo para debug

## 🔧 Estrutura da Planilha

A planilha será configurada automaticamente com as seguintes colunas:

| Coluna | Descrição |
|--------|-----------|
| Data/Hora | Data e hora do agendamento |
| Nome | Nome completo do cliente |
| Telefone | Telefone do cliente |
| Email | Email do cliente |
| Unidade | Unidade selecionada |
| Localidade | Localidade da página |
| IP | IP do cliente |
| User Agent | Navegador do cliente |

## 🚨 Troubleshooting

### Erro 403 - Acesso Negado
- Verifique se o Google Apps Script está publicado como "Anyone"
- Verifique se a planilha está compartilhada com sua conta

### Erro 404 - URL não encontrada
- Verifique se a URL do Web App está correta no PHP
- Verifique se o deploy foi feito corretamente

### Dados não aparecem na planilha
- Verifique os logs em `agendamentos_log.txt`
- Verifique o console do navegador para erros JavaScript
- Teste a URL do Web App diretamente

### Modal não aparece
- Verifique se o Bootstrap está carregado
- Verifique se não há erros JavaScript no console

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs do servidor
2. Verifique o console do navegador
3. Teste a URL do Web App diretamente
4. Verifique as permissões da planilha e do script 