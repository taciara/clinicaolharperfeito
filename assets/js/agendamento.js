// Scripts específicos para a página de agendamento

// Função para fazer scroll para o topo da página
function scrollToForm() {
    console.log('Função scrollToForm executada - fazendo scroll para o topo');
    
    // Scroll simples para o topo da página
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
    
    // Se for mobile, abre o modal do formulário
    if (window.innerWidth <= 980) {
        console.log('Dispositivo mobile detectado, abrindo modal');
        // Verifica se jQuery está disponível
        if (typeof $ !== 'undefined') {
            $('.form-mobile').addClass('active');
            $('body').addClass('no-scroll');
        } else {
            console.log('jQuery não está disponível, usando JavaScript puro');
            const mobileForm = document.querySelector('.form-mobile');
            const body = document.body;
            if (mobileForm) mobileForm.classList.add('active');
            if (body) body.classList.add('no-scroll');
        }
    } else {
        console.log('Dispositivo desktop detectado - scroll para o topo');
    }
}

// Dados de cidades por estado (será preenchido pelo PHP)

var cidadesPorEstado = {};



// Inicialização dos selects de localidade

$(function() {

    // Função para verificar se deve usar Select2

    function shouldUseSelect2() {

        return window.innerWidth > 980;

    }

    

    // Inicializa Select2 apenas em telas maiores que 980px

    if (shouldUseSelect2()) {

        $('#select-estado').select2({ width: '100%' });

        $('#select-cidade').select2({ width: '100%' });

        $('#select-estado-rodape').select2({ 

            width: '140px', 

            minimumResultsForSearch: 10, 

            dropdownPosition: 'above' 

        });

        $('#select-cidade-rodape').select2({ 

            width: '160px', 

            minimumResultsForSearch: 10, 

            dropdownPosition: 'above' 

        });

    }



    // Evento de mudança do estado (formulário principal)

    $('#select-estado').on('change', function() {

        var estado = $(this).val();

        var cidades = cidadesPorEstado[estado] || [];

        var $cidade = $('#select-cidade');

        $cidade.empty().append('<option value="">Selecione a cidade</option>');

        cidades.forEach(function(c) {

            $cidade.append('<option value="'+c.slug+'">'+c.nome+'</option>');

        });

        $cidade.val('').trigger('change');

    });



    // Evento de mudança da cidade (formulário principal)

    $('#select-cidade').on('change', function() {

        var estado = $('#select-estado').val();

        var cidade = $(this).val();

        if (estado && cidade) {

            window.location.href = '/' + estado + '/' + cidade;

        } else if (estado) {

            window.location.href = '/' + estado;

        }

    });



    // Evento de mudança do estado (rodapé)

    $('#select-estado-rodape').on('change', function() {

        var estado = $(this).val();

        var cidades = cidadesPorEstado[estado] || [];

        var $cidade = $('#select-cidade-rodape');

        $cidade.empty().append('<option value="">Cidade</option>');

        cidades.forEach(function(c) {

            $cidade.append('<option value="'+c.slug+'">'+c.nome+'</option>');

        });

        $cidade.val('').trigger('change');

    });



    // Evento de mudança da cidade (rodapé)

    $('#select-cidade-rodape').on('change', function() {

        var estado = $('#select-estado-rodape').val();

        var cidade = $(this).val();

        if (estado && cidade) {

            window.location.href = '/' + estado + '/' + cidade;

        } else if (estado) {

            window.location.href = '/' + estado;

        }

    });

    

    // Listener para redimensionamento da janela

    $(window).on('resize', function() {

        var shouldUse = shouldUseSelect2();

        var hasSelect2 = $('#select-estado').hasClass('select2-hidden-accessible');

        

        if (shouldUse && !hasSelect2) {

            // Aplica Select2 se não estiver aplicado

            $('#select-estado').select2({ width: '100%' });

            $('#select-cidade').select2({ width: '100%' });

            $('#select-estado-rodape').select2({ 

                width: '140px', 

                minimumResultsForSearch: 10, 

                dropdownPosition: 'above' 

            });

            $('#select-cidade-rodape').select2({ 

                width: '160px', 

                minimumResultsForSearch: 10, 

                dropdownPosition: 'above' 

            });

        } else if (!shouldUse && hasSelect2) {

            // Remove Select2 se estiver aplicado

            $('#select-estado').select2('destroy');

            $('#select-cidade').select2('destroy');

            $('#select-estado-rodape').select2('destroy');

            $('#select-cidade-rodape').select2('destroy');

        }

    });

    

    // Máscara do telefone

    $('#telefone').mask('(00) 00000-0000');
    
    // Máscaras adicionais para garantir funcionamento
    $('#telefone-desktop, #telefone-mobile').mask('(00) 00000-0000');
    $('input[placeholder*="telefone"], input[placeholder*="Telefone"]').mask('(00) 00000-0000');
    $('input[name="telefone"]').mask('(00) 00000-0000');

    

    // Envio do formulário desktop

    $('.form-desktop form').on('submit', function(e) {

        e.preventDefault();

        

        // Desabilita o botão e mostra loading

        var $submitBtn = $(this).find('button[type="submit"]');

        var originalText = $submitBtn.text();

        $submitBtn.prop('disabled', true).text('Enviando...');

        

        // Função para enviar formulário

        function enviarFormulario(recaptchaToken) {

            // Pega os dados do formulário desktop

                            var utmParams = getUtmParameters();

                var formData = {

                    nome: $('#nome-desktop').val().trim(),

                    telefone: $('#telefone-desktop').val().trim(),

                    email: $('#email-desktop').val().trim(),

                    unidade: $('#unidade-desktop').val(),

                    estado: window.currentEstado || '',

                    cidade: window.currentCidade || '',

                    localidade_url: window.formatLocalidade(),

                    campanha: utmParams.utm_campaign || getUrlParameter('campanha') || 'Orgânico',

                    origem_clique: utmParams.utm_source || getUrlParameter('origem') || 'Direto',

                    url_completa: window.location.href,

                    recaptcha_token: recaptchaToken || ''

                };

                

                // Log dos dados que serão enviados

                console.log('Dados do formulário desktop:', formData);

                console.log('Parâmetros UTM:', utmParams);



            // Validação básica

            if (!formData.nome || !formData.telefone || !formData.email || !formData.unidade) {

                alert('Por favor, preencha todos os campos obrigatórios.');

                $submitBtn.prop('disabled', false).text(originalText);

                return false;

            }

            

            // Validação de email

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(formData.email)) {

                alert('Por favor, insira um email válido.');

                $submitBtn.prop('disabled', false).text(originalText);

                return false;

            }

            

            // Envia os dados via AJAX

            $.ajax({

                url: 'https://clinicaolharperfeito.com.br/processar_agendamento.php',

                type: 'POST',

                dataType: 'json',

                contentType: 'application/json',

                data: JSON.stringify(formData),

                success: function(response) {

                    if (response.success) {

                        // Limpa o formulário

                        $('.form-agendamento form')[0].reset();

                        

                        // Prepara e atualiza link do WhatsApp

                        var mensagemWhatsApp = prepararMensagemWhatsAppSimples(formData);

                        var whatsappLink = 'https://api.whatsapp.com/send?phone=5511937023409&text=' + mensagemWhatsApp;

                        $('#btnWhatsAppConfirmar')

                            .attr('href', whatsappLink)

                            .removeClass('disabled')

                            .show();

                        

                        // Log para debug

                        console.log('Link WhatsApp atualizado (desktop):', whatsappLink);

                        console.log('Elemento encontrado:', $('#btnWhatsAppConfirmar').length > 0);
                        
                        // Cria token de segurança via AJAX
                        $.ajax({
                            url: '/gerenciar_tokens.php',
                            type: 'POST',
                            data: {
                                action: 'create',
                                unidade: formData.unidade
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Redireciona para página de agradecimento com token
                                    window.location.href = '/agradecimento.php?token=' + response.token + '&unidade=' + encodeURIComponent(formData.unidade);
                                } else {
                                    // Em caso de erro, redireciona sem token (será bloqueado)
                                    window.location.href = '/agradecimento.php?unidade=' + encodeURIComponent(formData.unidade);
                                }
                            },
                            error: function() {
                                // Em caso de erro, redireciona sem token (será bloqueado)
                                window.location.href = '/agradecimento.php?unidade=' + encodeURIComponent(formData.unidade);
                            }
                        });
                        
                        // Log de sucesso
                        console.log('Agendamento realizado:', response);

                    } else {

                        alert('Erro: ' + response.message);

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Erro no envio:', error);

                    alert('Erro ao enviar o formulário. Tente novamente.');

                },

                complete: function() {

                    // Reabilita o botão

                    $submitBtn.prop('disabled', false).text(originalText);

                }

            });

        }

        

        // Tenta usar reCAPTCHA, se não conseguir, envia sem

        if (typeof grecaptcha !== 'undefined' && grecaptcha) {

            // Timeout de 5 segundos para o reCAPTCHA

            var recaptchaTimeout = setTimeout(function() {

                console.log('Timeout do reCAPTCHA, enviando sem validação');

                enviarFormulario('');

            }, 5000);

            

            grecaptcha.ready(function() {

                grecaptcha.execute('6LfD-osrAAAAAOnzFKB8oSQkS_ADQvKGGq82CfR4', {action: 'agendamento'}).then(function(token) {

                    clearTimeout(recaptchaTimeout);

                    enviarFormulario(token);

                }).catch(function(error) {

                    clearTimeout(recaptchaTimeout);

                    console.error('Erro no reCAPTCHA:', error);

                    enviarFormulario(''); // Envia sem token

                });

            });

        } else {

            console.log('reCAPTCHA não disponível, enviando sem validação');

            enviarFormulario(''); // Envia sem token

        }

    });

});







// Função para pegar parâmetros da URL

function getUrlParameter(name) {

    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');

    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');

    var results = regex.exec(location.search);

    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));

}



// Função para capturar todos os parâmetros UTM

function getUtmParameters() {

    var utmParams = {};

    var utmFields = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_id', 'utm_term', 'utm_content'];

    

    utmFields.forEach(function(field) {

        var value = getUrlParameter(field);

        if (value) {

            utmParams[field] = value;

        }

    });

    

    return utmParams;

}



// Função alternativa para gerar a URL do WhatsApp de forma mais robusta
function prepararMensagemWhatsAppAlternativa(formData) {
    var nome = formData.nome;
    var unidade = formData.unidade;
    var url = window.location.href;
    var telefone = formData.telefone;
    var email = formData.email;
    
    // Mensagem ainda mais simples, sem caracteres especiais
    var mensagem = 'Ola! Me chamo ' + nome + ' e gostaria de confirmar meu agendamento de exame de vista.\n\n' +
                   'Dados do agendamento:\n' +
                   'Nome: ' + nome + '\n' +
                   'Telefone: ' + telefone + '\n' +
                   'Email: ' + email + '\n' +
                   'Unidade: ' + unidade + '\n' +
                   'Site: ' + url + '\n\n' +
                   'Por favor, confirme se receberam meu agendamento e me informe os proximos passos. Obrigado!';
    
    // Remove possíveis caracteres problemáticos
    mensagem = mensagem.replace(/[^\x00-\x7F]/g, function(char) {
        var replacements = {
            'á': 'a', 'à': 'a', 'ã': 'a', 'â': 'a', 'ä': 'a',
            'é': 'e', 'è': 'e', 'ê': 'e', 'ë': 'e',
            'í': 'i', 'ì': 'i', 'î': 'i', 'ï': 'i',
            'ó': 'o', 'ò': 'o', 'õ': 'o', 'ô': 'o', 'ö': 'o',
            'ú': 'u', 'ù': 'u', 'û': 'u', 'ü': 'u',
            'ç': 'c', 'ñ': 'n'
        };
        return replacements[char] || char;
    });
    
    console.log('Mensagem WhatsApp (limpa):', mensagem);
    
    return encodeURIComponent(mensagem);
}

// Função para preparar mensagem do WhatsApp (versão mais simples)
function prepararMensagemWhatsAppSimples(formData) {
    var nome = formData.nome;
    var unidade = formData.unidade;
    var url = window.location.href;
    var telefone = formData.telefone;
    var email = formData.email;
    
    // Mensagem ultra-simples, apenas ASCII
    var mensagem = 'Ola! Me chamo ' + nome + ' e gostaria de confirmar meu agendamento de exame de vista.\n\n' +
                   'Dados do agendamento:\n' +
                   'Nome: ' + nome + '\n' +
                   'Telefone: ' + telefone + '\n' +
                   'Email: ' + email + '\n' +
                   'Unidade: ' + unidade + '\n' +
                   'Site: ' + url + '\n\n' +
                   'Por favor, confirme se receberam meu agendamento e me informe os proximos passos. Obrigado!';
    
    // Remove TODOS os caracteres especiais
    mensagem = mensagem.replace(/[^\x20-\x7E\n]/g, function(char) {
        var replacements = {
            'á': 'a', 'à': 'a', 'ã': 'a', 'â': 'a', 'ä': 'a', 'Á': 'A', 'À': 'A', 'Ã': 'A', 'Â': 'A', 'Ä': 'A',
            'é': 'e', 'è': 'e', 'ê': 'e', 'ë': 'e', 'É': 'E', 'È': 'E', 'Ê': 'E', 'Ë': 'E',
            'í': 'i', 'ì': 'i', 'î': 'i', 'ï': 'i', 'Í': 'I', 'Ì': 'I', 'Î': 'I', 'Ï': 'I',
            'ó': 'o', 'ò': 'o', 'õ': 'o', 'ô': 'o', 'ö': 'o', 'Ó': 'O', 'Ò': 'O', 'Õ': 'O', 'Ô': 'O', 'Ö': 'O',
            'ú': 'u', 'ù': 'u', 'û': 'u', 'ü': 'u', 'Ú': 'U', 'Ù': 'U', 'Û': 'U', 'Ü': 'U',
            'ç': 'c', 'Ç': 'C', 'ñ': 'n', 'Ñ': 'N'
        };
        return replacements[char] || ' ';
    });
    
    console.log('Mensagem WhatsApp (ultra-simples):', mensagem);
    
    return encodeURIComponent(mensagem);
}

// Função para atualizar mensagem do modal - EM STANDBY
/*
function atualizarMensagemModal(unidade) {
    var mensagem = 'Obrigado por agendar seu exame de vista em ' + unidade + '! Entraremos em contato em breve para confirmar os detalhes.';
    $('#modalMensagem').text(mensagem);
}
*/



// Controles mobile

$(function() {

    // Reset do botão WhatsApp quando modal é fechado - EM STANDBY
    /*
    $('#modalAgradecimento').on('hidden.bs.modal', function() {

        $('#btnWhatsAppConfirmar')

            .addClass('disabled')

            .hide()

            .attr('href', '#');

    });
    */

    

    // Abrir formulário mobile

    $('#openFormMobile').on('click', function() {

        $('.form-mobile').addClass('active');

        $('body').addClass('no-scroll');

    });

    

    // Fechar formulário mobile

    $('#closeFormMobile').on('click', function() {

        $('.form-mobile').removeClass('active');

        $('body').removeClass('no-scroll');

    });

    

    // Fechar ao clicar fora do formulário

    $('.form-mobile').on('click', function(e) {

        if (e.target === this) {

            $(this).removeClass('active');

            $('body').removeClass('no-scroll');

        }

    });

    

    // Envio do formulário mobile

    $('.form-mobile form').on('submit', function(e) {

        e.preventDefault();

        

        // Desabilita o botão e mostra loading

        var $submitBtn = $(this).find('.submit-btn');

        var originalText = $submitBtn.html();

        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Enviando...');

        

        // Função para enviar formulário mobile

        function enviarFormularioMobile(recaptchaToken) {

            // Pega os dados do formulário mobile

            var utmParams = getUtmParameters();

                            var formData = {

                    nome: $('#nome-mobile').val().trim(),

                    telefone: $('#telefone-mobile').val().trim(),

                    email: $('#email-mobile').val().trim(),

                    unidade: $('#unidade-mobile').val(),

                    estado: window.currentEstado || '',

                    cidade: window.currentCidade || '',

                    localidade_url: window.formatLocalidade(),

                campanha: utmParams.utm_campaign || getUrlParameter('campanha') || 'Orgânico',

                origem_clique: utmParams.utm_source || getUrlParameter('origem') || 'Direto',

                url_completa: window.location.href,

                recaptcha_token: recaptchaToken || ''

            };

            

            // Validação básica

            if (!formData.nome || !formData.telefone || !formData.email || !formData.unidade) {

                alert('Por favor, preencha todos os campos obrigatórios.');

                $submitBtn.prop('disabled', false).html(originalText);

                return false;

            }

            

            // Validação de email

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(formData.email)) {

                alert('Por favor, insira um email válido.');

                $submitBtn.prop('disabled', false).html(originalText);

                return false;

            }

            

            // Envia os dados via AJAX

            $.ajax({

                url: 'https://clinicaolharperfeito.com.br/processar_agendamento.php',

                type: 'POST',

                dataType: 'json',

                contentType: 'application/json',

                data: JSON.stringify(formData),

                success: function(response) {

                    if (response.success) {

                        // Limpa o formulário

                        $('.form-mobile form')[0].reset();

                        

                        // Fecha o formulário mobile

                        $('.form-mobile').removeClass('active');

                        $('body').removeClass('no-scroll');

                        

                        // Prepara e atualiza link do WhatsApp

                        var mensagemWhatsApp = prepararMensagemWhatsAppSimples(formData);

                        var whatsappLink = 'https://api.whatsapp.com/send?phone=5511937023409&text=' + mensagemWhatsApp;

                        $('#btnWhatsAppConfirmar')

                            .attr('href', whatsappLink)

                            .removeClass('disabled')

                            .show();

                        

                        // Log para debug

                        console.log('Link WhatsApp atualizado (mobile):', whatsappLink);

                        console.log('Elemento encontrado:', $('#btnWhatsAppConfirmar').length > 0);
                        
                        // Cria token de segurança via AJAX
                        $.ajax({
                            url: '/gerenciar_tokens.php',
                            type: 'POST',
                            data: {
                                action: 'create',
                                unidade: formData.unidade
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Redireciona para página de agradecimento com token
                                    window.location.href = '/agradecimento.php?token=' + response.token + '&unidade=' + encodeURIComponent(formData.unidade);
                                } else {
                                    // Em caso de erro, redireciona sem token (será bloqueado)
                                    window.location.href = '/agradecimento.php?unidade=' + encodeURIComponent(formData.unidade);
                                }
                            },
                            error: function() {
                                // Em caso de erro, redireciona sem token (será bloqueado)
                                window.location.href = '/agradecimento.php?unidade=' + encodeURIComponent(formData.unidade);
                            }
                        });
                        
                        // Log de sucesso
                        console.log('Agendamento realizado (mobile):', response);

                    } else {

                        alert('Erro: ' + response.message);

                    }

                },

                error: function(xhr, status, error) {

                    console.error('Erro no envio (mobile):', error);

                    alert('Erro ao enviar o formulário. Tente novamente.');

                },

                complete: function() {

                    // Reabilita o botão

                    $submitBtn.prop('disabled', false).html(originalText);

                }

            });

        }

        

        // Tenta usar reCAPTCHA, se não conseguir, envia sem

        if (typeof grecaptcha !== 'undefined' && grecaptcha) {

            // Timeout de 5 segundos para o reCAPTCHA

            var recaptchaTimeout = setTimeout(function() {

                console.log('Timeout do reCAPTCHA mobile, enviando sem validação');

                enviarFormularioMobile('');

            }, 5000);

            

            grecaptcha.ready(function() {

                grecaptcha.execute('6LfD-osrAAAAAOnzFKB8oSQkS_ADQvKGGq82CfR4', {action: 'agendamento'}).then(function(token) {

                    clearTimeout(recaptchaTimeout);

                    enviarFormularioMobile(token);

                }).catch(function(error) {

                    clearTimeout(recaptchaTimeout);

                    console.error('Erro no reCAPTCHA mobile:', error);

                    enviarFormularioMobile(''); // Envia sem token

                });

            });

        } else {

            console.log('reCAPTCHA não disponível no mobile, enviando sem validação');

            enviarFormularioMobile(''); // Envia sem token

        }

    });

    

    // Máscara do telefone mobile

    $('#telefone-mobile').mask('(00) 00000-0000');

    

    // Máscaras adicionais para garantir funcionamento
    $('#telefone-desktop, #telefone-mobile').mask('(00) 00000-0000');
    $('input[placeholder*="telefone"], input[placeholder*="Telefone"]').mask('(00) 00000-0000');
    $('input[name="telefone"]').mask('(00) 00000-0000');

    

    // Garante que o botão fixo mobile esteja sempre visível

    $('.mobile-agendamento-btn').removeClass('hidden');

    

    // Sistema de notificações de agendamento

    let nomes = [];

    let localidades = [];

    let box = document.getElementById('notificacao-agendamento');

    let texto = box ? box.querySelector('.texto') : null;

    

    console.log('Notificação - Box encontrado:', !!box);

    console.log('Notificação - Texto encontrado:', !!texto);

    

    // Carrega nomes

    fetch('/notificacoes.json')

        .then(r => r.json())

        .then(data => { 

            nomes = data; 

        })

        .catch(err => {

            console.log('Erro ao carregar notificações:', err);

        });

    

    // Carrega localidades

    fetch('/localidades.json')

        .then(r => r.json())

        .then(data => { 

            localidades = data.localidades || []; 

        })

        .catch(err => {

            console.log('Erro ao carregar localidades:', err);

        });

    

    function sortearNotificacao() {

        if (!nomes.length || !localidades.length) return null;

        const nome = nomes[Math.floor(Math.random() * nomes.length)];

        const estado = localidades[Math.floor(Math.random() * localidades.length)];

        const cidade = estado.cidades[Math.floor(Math.random() * estado.cidades.length)];

        return { nome, cidade, estado: estado.estado };

    }

    

    function mostrarNotificacao() {

        const n = sortearNotificacao();

        console.log('Tentando mostrar notificação:', n);

        if (!n) return setTimeout(mostrarNotificacao, 1000);



        // Notificação original (funciona em desktop e mobile)

        if (!box || !texto) {

            console.log('Box ou texto não encontrado');

            return setTimeout(mostrarNotificacao, 1000);

        }

        

        console.log('Mostrando notificação:', `${n.nome} de ${n.cidade}/${n.estado} acabou de agendar!`);

        texto.textContent = `${n.nome} de ${n.cidade}/${n.estado} acabou de agendar!`;

        box.style.display = 'flex';

        box.style.opacity = '1';

        box.style.transform = 'translateY(0)';



        setTimeout(() => {

            box.style.opacity = '0';

            box.style.transform = 'translateY(40px)';

            setTimeout(() => {

                box.style.display = 'none';

                setTimeout(mostrarNotificacao, 36000); // 36s de intervalo para totalizar 40s

            }, 500);

        }, 4000); // 4 segundos visível

    }

    

    setTimeout(mostrarNotificacao, 2000);

});



 