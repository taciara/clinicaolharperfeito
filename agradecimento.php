<?php
// Página de Agradecimento - Clínica Olhar Perfeito

// Verificação de segurança - apenas para quem preencheu o formulário
require_once 'gerenciar_tokens.php';

// Pegar parâmetros da URL
$token = isset($_GET['token']) ? $_GET['token'] : '';
$unidade = isset($_GET['unidade']) ? $_GET['unidade'] : '';

// Verifica se o token é válido
if (empty($token)) {
    // Se não há token, redireciona para a página principal
    header('Location: /');
    exit();
}

// Valida o token usando o sistema de gerenciamento
$tokenManager = new TokenManager();
if (!$tokenManager->validateToken($token, $unidade)) {
    // Token inválido ou expirado, redireciona para a página principal
    header('Location: /');
    exit();
}

// Configurações básicas
$titulo = "Agendamento Realizado com Sucesso!";
$descricao = "Obrigado por agendar seu exame de vista! Entraremos em contato em breve para confirmar os detalhes.";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?> - Clínica Olhar Perfeito</title>
    <meta name="description" content="<?php echo $descricao; ?>">
    
    <!-- Bootstrap CSS -->
    <link href="/assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/assets/lib/fontawesome/css/all.min.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <link href="/assets/css/frontend.css" rel="stylesheet">
    
    <style>
        .agradecimento-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0081d0 0%, #004066 100%);
            padding: 2rem 1rem;
        }
        
        .agradecimento-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #0081d0;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .agradecimento-titulo {
            color: #333;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        
        .agradecimento-mensagem {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .btn-voltar {
            background: linear-gradient(135deg, #0081d0 0%, #004066 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-voltar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        
        .unidade-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin: 1.5rem 0;
            border-left: 4px solid #0081d0;
        }
        
        .unidade-info strong {
            color: #0081d0;
        }
        
        @media (max-width: 768px) {
            .agradecimento-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .success-icon {
                font-size: 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="agradecimento-container">
        <div class="agradecimento-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="agradecimento-titulo"><?php echo $titulo; ?></h1>
            
            <p class="agradecimento-mensagem">
                <?php echo $descricao; ?>
            </p>
            
            <?php if ($unidade): ?>
            <div class="unidade-info">
                <strong>Unidade selecionada:</strong> <?php echo htmlspecialchars($unidade); ?>
            </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="/" class="btn-voltar">
                    <i class="fas fa-home me-2"></i>Voltar ao Início
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/lib/jquery/jquery-3.7.1.min.js"></script>
    <script src="/assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Verificação adicional de segurança no lado do cliente
    document.addEventListener('DOMContentLoaded', function() {
        // Pega o token da URL
        var urlParams = new URLSearchParams(window.location.search);
        var urlToken = urlParams.get('token');
        
        // Verifica se há token na URL
        if (!urlToken) {
            console.log('Token não encontrado - redirecionando para página principal');
            window.location.href = '/';
            return;
        }
        
        console.log('Acesso autorizado à página de agradecimento');
        
        // Opcional: Remove o token da URL para maior segurança
        // window.history.replaceState({}, document.title, window.location.pathname);
    });
    </script>
</body>
</html>
