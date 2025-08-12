# Sistema de Rastreamento de Botões - Clínica Olhar Perfeito

## Visão Geral

Este sistema permite rastrear exatamente qual botão o usuário clicou antes de preencher o formulário de agendamento. Isso é útil para:

- **Análise de conversão**: Saber quais botões são mais eficazes
- **Relatórios para clientes**: "Fulano clicou no botão X e preencheu o formulário"
- **Otimização de UX**: Identificar pontos de conversão mais eficientes

## Como Funciona

### 1. Rastreamento Automático
Cada botão de "Agendar Exame" agora tem:
- **ID único** para identificação
- **Função de rastreamento** que registra o clique
- **Timestamp** para validar a relevância

### 2. Botões Rastreados

| ID do Botão | Localização | Descrição |
|--------------|-------------|-----------|
| `btn-agendar-hero` | Seção Hero (topo) | Botão principal da página |
| `btn-agendar-img-texto-1` | Seção Imagem + Texto 1 | Primeira seção de conteúdo |
| `btn-agendar-img-texto-2` | Seção Imagem + Texto 2 | Segunda seção de conteúdo |
| `btn-agendar-cta-final` | Seção CTA Final | Call-to-action antes dos serviços |
| `btn-agendar-servicos` | Seção de Serviços | Botão do card de serviços |
| `btn-mobile-fixo` | Botão Mobile Fixo | Botão flutuante em dispositivos móveis |

### 3. Formato da Localidade_URL

A coluna `localidade_url` agora retorna:
```
São Paulo - Mooca | Botão: btn-agendar-hero
```

**Estrutura:**
- **Localidade**: Estado - Cidade (ou apenas Estado, ou URL)
- **Separador**: ` | Botão: `
- **Botão**: ID do botão clicado ou "Direto" se não houver clique

## Exemplos de Dados

### Exemplo 1: Usuário clicou no botão hero
```
localidade_url: "São Paulo - Mooca | Botão: btn-agendar-hero"
```

### Exemplo 2: Usuário clicou no botão mobile fixo
```
localidade_url: "São Paulo - Mooca | Botão: btn-mobile-fixo"
```

### Exemplo 3: Usuário acessou diretamente o formulário
```
localidade_url: "São Paulo - Mooca | Botão: Direto"
```

## Implementação Técnica

### JavaScript
```javascript
// Sistema de rastreamento
window.buttonTracker = {
    lastClickedButton: null,
    lastClickedTime: null,
    
    trackClick: function(buttonId, buttonText) {
        this.lastClickedButton = buttonId;
        this.lastClickedTime = new Date().toISOString();
    },
    
    getLastButtonInfo: function() {
        if (this.lastClickedButton && this.lastClickedTime) {
            var timeDiff = Math.floor((new Date() - new Date(this.lastClickedTime)) / 1000);
            if (timeDiff <= 300) { // 5 minutos
                return this.lastClickedButton;
            }
        }
        return 'Direto';
    }
};
```

### HTML dos Botões
```html
<button type="button" class="btn-padrao btn-pulsante" 
        id="btn-agendar-hero" 
        onclick="window.buttonTracker.trackClick('btn-agendar-hero', 'Agendar Exame'); scrollToForm();">
    Agendar Exame
</button>
```

## Validação de Tempo

O sistema considera válido apenas cliques realizados nos **últimos 5 minutos** antes do preenchimento do formulário. Isso evita:

- Cliques antigos não relacionados
- Dados incorretos de rastreamento
- Confusão na análise

## Como Usar no Google Sheets

### 1. Filtros Úteis
```
// Filtrar por botão específico
localidade_url contém "btn-agendar-hero"

// Filtrar por tipo de botão
localidade_url contém "mobile"  // Botões mobile
localidade_url contém "hero"     // Botão principal
localidade_url contém "Direto"   // Acesso direto
```

### 2. Análise de Conversão
```
// Contar conversões por botão
=COUNTIF(A:A, "*btn-agendar-hero*")
=COUNTIF(A:A, "*btn-mobile-fixo*")
=COUNTIF(A:A, "*Direto*")
```

### 3. Relatórios para Clientes
```
"João Silva clicou no botão principal (btn-agendar-hero) 
e preencheu o formulário para São Paulo - Mooca"
```

## Teste do Sistema

Use o arquivo `teste_localidade.html` para:
- Testar o rastreamento de botões
- Verificar a formatação da localidade
- Simular envios de formulário
- Validar o funcionamento do sistema

## Manutenção

### Adicionar Novo Botão
1. Adicionar ID único: `id="btn-agendar-novo"`
2. Adicionar rastreamento: `onclick="window.buttonTracker.trackClick('btn-agendar-novo', 'Agendar Exame');"`
3. Documentar na tabela acima

### Modificar Botão Existente
1. Manter o ID existente para consistência dos dados
2. Atualizar apenas o texto ou estilo se necessário
3. Verificar se o rastreamento continua funcionando

## Benefícios

✅ **Rastreamento preciso** de cada interação  
✅ **Dados estruturados** para análise  
✅ **Relatórios detalhados** para clientes  
✅ **Otimização de conversão** baseada em dados reais  
✅ **Manutenção simples** e escalável  

## Suporte

Para dúvidas ou modificações, consulte a documentação ou entre em contato com a equipe de desenvolvimento.
