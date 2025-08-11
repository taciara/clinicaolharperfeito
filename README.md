# Clínica - Projeto Organizado

Este projeto foi completamente reorganizado para seguir as melhores práticas de desenvolvimento web, separando CSS e JavaScript inline em arquivos externos.

## 📁 Estrutura do Projeto

```
https://clinicaolharperfeito.com.br
├── agendamento.php          # Página principal de agendamento
├── conheca.php              # Página "Conheça"
├── localidades.json         # Dados das localidades
├── notificacoes.json        # Dados para notificações
├── assets/
│   ├── css/
│   │   ├── global.css       # Estilos globais compartilhados
│   │   ├── agendamento.css  # Estilos específicos do agendamento
│   │   ├── conheca.css      # Estilos específicos do conheca
│   │   ├── frontend.min.css # CSS principal (existente)
│   │   ├── banner.min.css   # CSS do banner (existente)
│   │   ├── itens_icone.min.css # CSS dos ícones (existente)
│   │   ├── img_texto.min.css # CSS imagem/texto (existente)
│   │   ├── depoimentos.min.css # CSS depoimentos (existente)
│   │   ├── accordion.min.css # CSS accordion (existente)
│   │   └── bloco_servicos.min.css # CSS blocos (existente)
│   ├── js/
│   │   ├── frontend.js      # JavaScript principal (existente)
│   │   └── agendamento.js   # JavaScript específico do agendamento
│   ├── lib/                 # Bibliotecas externas
│   ├── fonts/               # Fontes
│   └── images/              # Imagens
└── README.md               # Este arquivo
```

## 🔧 Organização Implementada

### CSS Separado
- **`global.css`**: Estilos compartilhados entre todas as páginas
  - Botão WhatsApp flutuante
  - Estilos de formulários
  - Estilos de botões
  - Estilos de accordion
  - Estilos do Select2
  - Estilos responsivos

- **`agendamento.css`**: Estilos específicos da página de agendamento
  - Botão pulsante
  - Formulário sticky
  - Notificações de agendamento

- **`conheca.css`**: Estilos específicos da página conheca
  - Estilos específicos podem ser adicionados aqui

### JavaScript Separado
- **`agendamento.js`**: Funcionalidades específicas do agendamento
  - Inicialização do Select2
  - Eventos de mudança de estado/cidade
  - Máscaras de input
  - Sistema de notificações

### Remoção de Código Inline
- ✅ Todos os estilos `<style>` inline foram removidos
- ✅ Todos os scripts `<script>` inline foram removidos
- ✅ Código organizado em arquivos externos
- ✅ Manutenção facilitada

## 🚀 Benefícios da Organização

1. **Manutenibilidade**: Código mais fácil de manter e atualizar
2. **Reutilização**: Estilos e scripts podem ser reutilizados
3. **Performance**: Melhor cache do navegador
4. **Legibilidade**: Código mais limpo e organizado
5. **Escalabilidade**: Fácil adição de novas funcionalidades

## 📝 Como Usar

### Para adicionar novos estilos:
1. Para estilos globais: edite `assets/css/global.css`
2. Para estilos específicos de uma página: edite o arquivo CSS correspondente

### Para adicionar novas funcionalidades JavaScript:
1. Para funcionalidades globais: edite `assets/js/frontend.js`
2. Para funcionalidades específicas: edite o arquivo JS correspondente

### Para incluir em novas páginas:
```html
<!-- CSS -->
<link rel="stylesheet" href="assets/css/global.css">
<link rel="stylesheet" href="assets/css/[pagina].css">

<!-- JavaScript -->
<script src="assets/js/[pagina].js"></script>
```

## 🚀 Funcionalidades Implementadas

### ✅ Sistema de Agendamento
- **Formulário funcional** com validação completa
- **Modal de agradecimento** após envio bem-sucedido
- **Integração com Google Sheets** para armazenamento
- **Log local** como backup dos dados
- **Validação de campos** em tempo real
- **Feedback visual** durante o envio

### 📊 Estrutura de Dados
Os agendamentos são salvos com as seguintes informações:
- **Data** e **Hora** (colunas separadas)
- Nome completo do cliente
- Telefone do cliente
- Email do cliente
- Unidade selecionada
- **Estado** e **Cidade** (colunas separadas)
- **Localidade URL** (da página onde foi preenchido)
- **Campanha** (utm_campaign ou parâmetro personalizado)
- **Origem do Clique** (utm_source ou parâmetro personalizado)
- **URL Completa** (página exata onde foi enviado)
- IP do cliente
- User Agent (navegador)

### 🔒 Segurança
- **reCAPTCHA v3 invisível** integrado
- Validação de campos em tempo real
- Sanitização de dados
- Proteção contra spam
- Score de confiança automático

## 🔄 Próximos Passos

- [ ] Minificar os arquivos CSS e JS para produção
- [ ] Implementar sistema de cache
- [ ] Adicionar compressão gzip
- [ ] Otimizar carregamento de imagens
- [ ] Implementar lazy loading
- [ ] Adicionar notificações por email
- [ ] Implementar dashboard administrativo

## 📞 Suporte

Para dúvidas ou sugestões sobre a organização do projeto, consulte a documentação ou entre em contato com a equipe de desenvolvimento. 