// Scripts específicos para a página de agendamento

// Inicialização dos selects de localidade
$(function() {
    // Evento de mudança do estado
    $('#estado-select').on('change', function() {
        var estado = $(this).val();
        var cidades = window.cidadesPorEstado[estado] || [];
        var $cidade = $('#cidade-select');
        $cidade.empty().append('<option value="">Selecione a cidade</option>');
        cidades.forEach(function(c) {
            $cidade.append('<option value="'+c.slug+'">'+c.nome+'</option>');
        });
        $cidade.val('');
    });

    // Evento de mudança da cidade
    $('#cidade-select').on('change', function() {
        var estado = $('#estado-select').val();
        var cidade = $(this).val();
        if (estado && cidade) {
            window.location.href = '/' + estado + '/' + cidade;
        } else if (estado) {
            window.location.href = '/' + estado;
        }
    });

    // Botão agendar
    $('#agendar-btn').on('click', function() {
        var estado = $('#estado-select').val();
        var cidade = $('#cidade-select').val();
        if (estado && cidade) {
            window.location.href = '/' + estado + '/' + cidade;
        } else if (estado) {
            window.location.href = '/' + estado;
        }
    });

    // Máscara do telefone
    $('#telefone').mask('(00) 00000-0000');
    
    // Envio do formulário
    $('#agendamento-form').on('submit', function(e) {
        e.preventDefault();
        
        // Desabilita o botão e mostra loading
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Enviando...');
        
        // Função para enviar formulário
        function enviarFormulario(recaptchaToken) {
            var formData = {
                nome: $('#nome').val().trim(),
                telefone: $('#telefone').val().trim(),
                email: $('#email').val().trim(),
                unidade: $('#unidade').val(),
                estado: window.estadoAtual || '',
                cidade: window.cidadeAtual || '',
                localidade_url: window.location.href,
                campanha: 'Orgânico',
                origem_clique: 'Direto',
                url_completa: window.location.href,
                recaptcha_token: recaptchaToken || ''
            };
            
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
                        $('#agendamento-form')[0].reset();
                        
                        // Mostra o modal de sucesso
                        $('#successModal').modal('show');
                        
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