# Configuração do reCAPTCHA v3 Invisível

## 📋 Passo a Passo para Configurar

### 1. Criar conta no Google reCAPTCHA

1. **Acesse o Google reCAPTCHA:**
   - Vá para [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)
   - Faça login com sua conta Google

2. **Crie um novo site:**
   - Clique em "+" (Adicionar)
   - Dê um nome: "Clínica Agendamentos"
   - Selecione "reCAPTCHA v3"
   - Adicione seus domínios:
     - `localhost` (para desenvolvimento)
     - `seu-dominio.com` (para produção)
   - Aceite os termos e clique em "Enviar"

### 2. Obter as Chaves

Após criar, você receberá duas chaves:

- **Chave do Site (Site Key):** `6LfD-osrAAAAAOnzFKB8oSQkS_ADQvKGGq82CfR4` (pública)
- **Chave Secreta (Secret Key):** `6LfD-osrAAAAAM2YpTVIw9ak4qL5rWJob4FTLNZx` (privada)

### 3. Configurar no Código

#### No arquivo `agendamento.php`:
```html
<!-- Substitua 6Lc... pela sua chave do site -->
<script src="https://www.google.com/recaptcha/api.js?render=SUA_CHAVE_DO_SITE"></script>
```

#### No arquivo `processar_agendamento.php`:
```php
// Substitua pela sua chave secreta
$recaptcha_secret_key = 'SUA_CHAVE_SECRETA';
```

### 4. Testar

1. **Teste local:**
   - Acesse `localhost/clinica`
   - Preencha o formulário
   - Envie o formulário (reCAPTCHA é invisível)

2. **Verificar funcionamento:**
   - O reCAPTCHA v3 é completamente invisível
   - Funciona automaticamente no background
   - Analisa o comportamento do usuário
   - Não atrapalha a experiência do usuário

## 🔧 Personalização

### Score do reCAPTCHA v3
Você pode ajustar a sensibilidade no arquivo `processar_agendamento.php`:

```php
// Score mínimo de 0.5 (você pode ajustar conforme necessário)
return $response['score'] >= 0.5;
```

**Scores do reCAPTCHA v3:**
- **1.0** = Muito provavelmente humano
- **0.9** = Provavelmente humano
- **0.5** = Neutro
- **0.0** = Muito provavelmente bot

### Ações Personalizadas
Você pode criar diferentes ações para diferentes partes do site:

```javascript
grecaptcha.execute('SUA_CHAVE', {action: 'agendamento'})
grecaptcha.execute('SUA_CHAVE', {action: 'contato'})
grecaptcha.execute('SUA_CHAVE', {action: 'newsletter'})
```

## 🚨 Troubleshooting

### reCAPTCHA não funciona
- Verifique se a chave do site está correta
- Verifique se o domínio está autorizado
- Verifique se o script do reCAPTCHA está carregando
- Verifique o console do navegador para erros

### Erro de validação
- Verifique se a chave secreta está correta
- Verifique se o token está sendo enviado
- Verifique se o score está sendo validado corretamente
- Verifique os logs do servidor

### Score muito baixo
- Ajuste o score mínimo no PHP (padrão: 0.5)
- Verifique se não há muitos falsos positivos
- Monitore os scores no Google reCAPTCHA admin

### Erro de domínio
- Adicione `localhost` para desenvolvimento
- Adicione seu domínio real para produção
- Aguarde alguns minutos após adicionar domínios

## 📞 Suporte

Se encontrar problemas:
1. Verifique se as chaves estão corretas
2. Verifique se os domínios estão autorizados
3. Teste em modo incógnito
4. Verifique o console do navegador para erros 