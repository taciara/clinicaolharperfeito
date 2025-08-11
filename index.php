<?php
// Função para normalizar strings (remover acentos, minúsculas, etc)
function normalize_slug($str) {
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace('/[^a-zA-Z0-9\s]/', '', $str);
    $str = strtolower($str);
    $str = str_replace(' ', '-', $str);
    return $str;
}

// Pega a URL após /
$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$path = preg_replace('#^/#', '', $path);
$path = preg_replace('#[\?\#].*$#', '', $path); // remove query/hash
$path = trim($path, '/');
$partes = explode('/', $path); // [estado, cidade]

// Debug: log da URL e partes
error_log("URL: " . $_SERVER['REQUEST_URI'] . " | Path: '$path' | Partes: " . implode(',', $partes) . " | Count: " . count($partes));

// Lê o JSON de localidades
$localidades = [];
$padrao = null;
if (file_exists(__DIR__ . '/localidades.json')) {
    $json = file_get_contents(__DIR__ . '/localidades.json');
    $data = json_decode($json, true);
    if (isset($data['localidades'])) {
        $localidades = $data['localidades'];
    }
    if (isset($data['padrao'])) {
        $padrao = $data['padrao'];
    }
}

// Função para redirecionar
function redirect($url) {
    error_log("Executando redirect para: " . $url);
    
    // Se a URL não começar com http/https, adiciona o protocolo atual
    if (!preg_match('/^https?:\/\//', $url)) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $url = $protocol . '://' . $host . $url;
    }
    
    header('Location: ' . $url);
    exit;
}

// Se não tem localidade na URL ou localidade inválida, redireciona para padrão
$slug_estado_padrao = $padrao ? normalize_slug($padrao['estado']) : '';
$slug_cidade_padrao = $padrao && isset($padrao['cidade']) ? normalize_slug($padrao['cidade']) : '';

$nome = '';
$localidade_valida = false;

// Se acessar apenas /, redireciona para /sao-paulo
if (count($partes) === 0) {
    error_log("Redirecionando de / para /sao-paulo");
    // Redirecionamento direto sem função
    header('Location: /sao-paulo');
    exit;
}

if (count($partes) === 1) {
    $slug = $partes[0];
    foreach ($localidades as $estado) {
        if (normalize_slug($estado['estado']) === $slug) {
            $nome = $estado['estado'];
            $localidade_valida = true;
            break;
        }
        foreach ($estado['cidades'] as $cidade) {
            if (normalize_slug($cidade) === $slug) {
                $nome = $cidade;
                $localidade_valida = true;
                break 2;
            }
        }
    }
    // Removido redirecionamento automático para evitar loops
} elseif (count($partes) === 2) {
    $estado_slug = $partes[0];
    $cidade_slug = $partes[1];
    
    foreach ($localidades as $estado) {
        if (normalize_slug($estado['estado']) === $estado_slug) {
            foreach ($estado['cidades'] as $cidade) {
                if (normalize_slug($cidade) === $cidade_slug) {
                    $nome = $cidade;
                    $localidade_valida = true;
                    break 2;
                }
            }
        }
    }
    // Removido redirecionamento automático para evitar loops
}

if (!$nome && count($partes) > 0) {
    // fallback: capitaliza slug
    $nome = ucwords(str_replace('-', ' ', $partes[count($partes)-1]));
}

// Gera arrays de estados e cidades para o select
$estados = [];
$cidades_por_estado = [];
foreach ($localidades as $estado) {
    $slug_estado = normalize_slug($estado['estado']);
    $estados[$slug_estado] = $estado['estado'];
    $cidades_por_estado[$slug_estado] = [];
    foreach ($estado['cidades'] as $cidade) {
        $cidades_por_estado[$slug_estado][] = [
            'slug' => normalize_slug($cidade),
            'nome' => $cidade
        ];
    }
}

// Estado/cidade selecionados
$estado_atual = '';
$cidade_atual = '';
if (count($partes) === 1) {
    // /{estado} ou /{cidade}
    $slug = $partes[0];
    foreach ($localidades as $estado) {
        if (normalize_slug($estado['estado']) === $slug) {
            $estado_atual = $slug;
            // Se for um estado que também é uma cidade no formulário, define como cidade também
            $estado_nome = $estado['estado'];
            if (in_array($estado_nome, ['Belo Horizonte', 'Jardim Ângela', 'Mogi das Cruzes', 'Guarujá', 'Suzano', 'São Vicente', 'Mauá', 'São Miguel', 'Guarulhos', 'Osasco', 'São Mateus', 'Pirajussara', 'Santo Amaro', 'Santo André'])) {
                $cidade_atual = $slug;
            } else {
                // Se não for uma cidade do formulário, não define cidade_atual
                $cidade_atual = '';
            }
            break;
        }
        foreach ($estado['cidades'] as $cidade) {
            if (normalize_slug($cidade) === $slug) {
                $cidade_atual = $slug;
                break 2;
            }
        }
    }
} elseif (count($partes) === 2) {
    // /{estado}/{cidade}
    $estado_slug = $partes[0];
    $cidade_slug = $partes[1];
    foreach ($localidades as $estado) {
        if (normalize_slug($estado['estado']) === $estado_slug) {
            $estado_atual = $estado_slug;
            foreach ($estado['cidades'] as $cidade) {
                if (normalize_slug($cidade) === $cidade_slug) {
                    $cidade_atual = $cidade_slug;
                    break;
                }
            }
            break;
        }
    }
}
?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- SEO Meta Tags -->
  <title>Exame de Vista Acessível em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> | Clínica de Optometria Olhar Perfeito</title>
  <meta name="description" content="Clínica em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> especializada com equipe qualificada atendimento humanizado e urgente. Clínica de Optometria Olhar Perfeito.">
  <meta name="keywords" content="clínica especializada, Clínica de Optometria Olhar Perfeito">
  <meta name="author" content="Clínica de Optometria Olhar Perfeito">
  <meta name="robots" content="index, follow">
  <meta name="language" content="pt-BR">
  <meta name="revisit-after" content="7 days">
  <meta name="distribution" content="global">
  
  <!-- Canonical URL -->
  <link rel="canonical" href="https://clinicaolharperfeito.com.br/">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="assets/images/favicon.png">
  <link rel="icon" type="image/x-icon" href="assets/images/favicon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon.png">
  <link rel="manifest" href="assets/images/site.webmanifest">
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://clinicaolharperfeito.com.br/">
  <meta property="og:title" content="Clínica de Optometria em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> Olhar Perfeito">
  <meta property="og:description" content="Clínica em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> especializada com equipe qualificada atendimento humanizado e urgente. Clínica de Optometria Olhar Perfeito.">
  <meta property="og:image" content="https://clinicaolharperfeito.com.br/assets/images/2.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:site_name" content="Clínica de Optometria em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> Olhar Perfeito.">
  <meta property="og:locale" content="pt_BR">
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="https://clinicaolharperfeito.com.br/">
  <meta property="twitter:title" content="Clínica em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> Especializada Vista Acessível | Clinica de Optometria Olhar Perfeito">
  <meta property="twitter:description" content="Clínica em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> especializada com equipe qualificada atendimento humanizado e urgente. Clínica de Optometria Olhar Perfeito.">
  <meta property="twitter:image" content="https://clinicaolharperfeito.com.br/assets/images/2.png">
  
  <!-- Additional SEO -->
  <meta name="theme-color" content="#44B77B">
  <meta name="msapplication-TileColor" content="#44B77B">
  <meta name="msapplication-config" content="assets/images/browserconfig.xml">

  <!-- Css -->
  <link rel="stylesheet" href="/assets/lib/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/lib/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="/assets/fonts/inter/inter.css">
  <link rel="stylesheet" href="/assets/css/frontend.min.css">
  <link rel="stylesheet" href="/assets/css/banner.min.css">
  <link rel="stylesheet" href="/assets/css/itens_icone.min.css">
  <link rel="stylesheet" href="/assets/css/img_texto.min.css">
  <link rel="stylesheet" href="/assets/css/depoimentos.min.css">
  <link rel="stylesheet" href="/assets/css/accordion.min.css">
  <link rel="stylesheet" href="/assets/css/bloco_servicos.min.css">
  <link rel="stylesheet" href="/assets/css/agendamento.css">
  <link rel="stylesheet" href="/assets/css/global.css">
  
  <!-- reCAPTCHA v3 -->
  <script src="https://www.google.com/recaptcha/api.js?render=6LfD-osrAAAAAOnzFKB8oSQkS_ADQvKGGq82CfR4"></script>

  <!-- Meta Pixel Code -->
  <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src='https://connect.facebook.net/en_US/fbevents.js';
    s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script');
    
    fbq('init', '740439735615253');
    fbq('track', 'PageView');
  </script>
  <noscript>
    <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=740439735615253&ev=PageView&noscript=1"/>
  </noscript>
  <!-- End Meta Pixel Code -->

  <!-- TikTok Pixel Code -->
<script>
  !function (w, d, t) {
    w.TiktokAnalyticsObject = t;
    var ttq = w[t] = w[t] || [];
    ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "enableCookie", "disableCookie"],
    ttq.setAndDefer = function (t, e) {
      t[e] = function () {
        t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
      }
    };
    for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
    ttq.instance = function (t) {
      for (var e = ttq._i[t] || [], n = 0; n < ttq.methods.length; n++
      ) ttq.setAndDefer(e, ttq.methods[n]);
      return e
    };
    ttq.load = function (e, n) {
      var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
      ttq._i = ttq._i || {};
      ttq._i[e] = [];
      ttq._i[e]._u = i;
      ttq._t = ttq._t || {};
      ttq._t[e] = +new Date;
      ttq._o = ttq._o || {};
      ttq._o[e] = n || {};
      var o = document.createElement("script");
      o.type = "text/javascript";
      o.async = !0;
      o.src = i + "?sdkid=" + e + "&lib=" + t;
      var a = document.getElementsByTagName("script")[0];
      a.parentNode.insertBefore(o, a)
    };
    
    ttq.load('D29M4HJC77U9B02LO3M0');
    ttq.page();
  }(window, document, 'ttq');
</script>
<!-- End TikTok Pixel Code -->


</head>
<body>

<!-- Formulário de Agendamento no topo -->
<section class="form-agendamento form-agendamento-sticky py-lg-2 bg-light border-bottom">
  <!-- Versão Desktop (acima de 980px) -->
  <div class="form-desktop">
    <div class="container">
      <h3 class="h5 mb-0"><small>Agende seu exame de vista 100% Acessível em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>!</small></h3>
      <form class="row g-3 justify-content-center align-items-end" method="post" action="#">
        <div class="col-md-3">
          <input type="text" class="form-control" id="nome-desktop" name="nome" placeholder="Seu nome" required>
        </div>
        <div class="col-md-2">
          <input type="tel" class="form-control phone" id="telefone-desktop" name="telefone" placeholder="(11) 99999-9999" required>
        </div>
        <div class="col-md-3">
          <input type="email" class="form-control" id="email-desktop" name="email" placeholder="seu@email.com" required>
        </div>
        <div class="col-md-2">
          <select class="form-select" id="unidade-desktop" name="unidade" required>
            <option value="" disabled <?php echo (!$cidade_atual) ? 'selected' : ''; ?>>Selecione a cidade</option>
            <option value="Belo Horizonte" <?php echo ($cidade_atual && normalize_slug('Belo Horizonte') === $cidade_atual) ? 'selected' : ''; ?>>Belo Horizonte</option>
            <option value="Jardim Ângela" <?php echo ($cidade_atual && normalize_slug('Jardim Ângela') === $cidade_atual) ? 'selected' : ''; ?>>Jardim Ângela</option>
            <option value="Mogi das Cruzes" <?php echo ($cidade_atual && normalize_slug('Mogi das Cruzes') === $cidade_atual) ? 'selected' : ''; ?>>Mogi das Cruzes</option>
            <option value="Guarujá" <?php echo ($cidade_atual && normalize_slug('Guarujá') === $cidade_atual) ? 'selected' : ''; ?>>Guarujá</option>
            <option value="Suzano" <?php echo ($cidade_atual && normalize_slug('Suzano') === $cidade_atual) ? 'selected' : ''; ?>>Suzano</option>
            <option value="São Vicente" <?php echo ($cidade_atual && normalize_slug('São Vicente') === $cidade_atual) ? 'selected' : ''; ?>>São Vicente</option>
            <option value="Mauá" <?php echo ($cidade_atual && normalize_slug('Mauá') === $cidade_atual) ? 'selected' : ''; ?>>Mauá</option>
            <option value="São Miguel" <?php echo ($cidade_atual && normalize_slug('São Miguel') === $cidade_atual) ? 'selected' : ''; ?>>São Miguel</option>
            <option value="Guarulhos" <?php echo ($cidade_atual && normalize_slug('Guarulhos') === $cidade_atual) ? 'selected' : ''; ?>>Guarulhos</option>
            <option value="Osasco" <?php echo ($cidade_atual && normalize_slug('Osasco') === $cidade_atual) ? 'selected' : ''; ?>>Osasco</option>
            <option value="São Mateus" <?php echo ($cidade_atual && normalize_slug('São Mateus') === $cidade_atual) ? 'selected' : ''; ?>>São Mateus</option>
            <option value="Pirajussara" <?php echo ($cidade_atual && normalize_slug('Pirajussara') === $cidade_atual) ? 'selected' : ''; ?>>Pirajussara</option>
            <option value="Santo Amaro" <?php echo ($cidade_atual && normalize_slug('Santo Amaro') === $cidade_atual) ? 'selected' : ''; ?>>Santo Amaro</option>
          </select>
        </div>
        <div class="col-md-2 d-grid">
          <button type="submit" class="btn btn-primary btn-lg btn-pulsante">Agendar</button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Versão Mobile (abaixo de 980px) -->
  <div class="form-mobile">
    <div class="form-container">
      <div class="form-header">
        <h3>Agendar Exame</h3>
        <button type="button" class="close-btn" id="closeFormMobile">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="form-content">
        <form method="post" action="#">
          <div class="form-group">
            <label for="nome-mobile">Nome Completo</label>
            <input type="text" class="form-control" id="nome-mobile" name="nome" placeholder="Seu nome completo" required>
          </div>
          <div class="form-group">
            <label for="telefone-mobile">Telefone</label>
            <input type="tel" class="form-control phone" id="telefone-mobile" name="telefone" placeholder="(11) 99999-9999" required>
          </div>
          <div class="form-group">
            <label for="email-mobile">Email</label>
            <input type="email" class="form-control" id="email-mobile" name="email" placeholder="seu@email.com" required>
          </div>
          <div class="form-group">
            <label for="unidade-mobile">Unidade</label>
            <select class="form-control" id="unidade-mobile" name="unidade" required>
              <option value="" disabled <?php echo (!$cidade_atual) ? 'selected' : ''; ?>>Selecione a cidade</option>
              <option value="Belo Horizonte" <?php echo ($cidade_atual && normalize_slug('Belo Horizonte') === $cidade_atual) ? 'selected' : ''; ?>>Belo Horizonte</option>
              <option value="Jardim Ângela" <?php echo ($cidade_atual && normalize_slug('Jardim Ângela') === $cidade_atual) ? 'selected' : ''; ?>>Jardim Ângela</option>
              <option value="Mogi das Cruzes" <?php echo ($cidade_atual && normalize_slug('Mogi das Cruzes') === $cidade_atual) ? 'selected' : ''; ?>>Mogi das Cruzes</option>
              <option value="Guarujá" <?php echo ($cidade_atual && normalize_slug('Guarujá') === $cidade_atual) ? 'selected' : ''; ?>>Guarujá</option>
              <option value="Suzano" <?php echo ($cidade_atual && normalize_slug('Suzano') === $cidade_atual) ? 'selected' : ''; ?>>Suzano</option>
              <option value="São Vicente" <?php echo ($cidade_atual && normalize_slug('São Vicente') === $cidade_atual) ? 'selected' : ''; ?>>São Vicente</option>
              <option value="Mauá" <?php echo ($cidade_atual && normalize_slug('Mauá') === $cidade_atual) ? 'selected' : ''; ?>>Mauá</option>
              <option value="São Miguel" <?php echo ($cidade_atual && normalize_slug('São Miguel') === $cidade_atual) ? 'selected' : ''; ?>>São Miguel</option>
              <option value="Guarulhos" <?php echo ($cidade_atual && normalize_slug('Guarulhos') === $cidade_atual) ? 'selected' : ''; ?>>Guarulhos</option>
              <option value="Osasco" <?php echo ($cidade_atual && normalize_slug('Osasco') === $cidade_atual) ? 'selected' : ''; ?>>Osasco</option>
              <option value="São Mateus" <?php echo ($cidade_atual && normalize_slug('São Mateus') === $cidade_atual) ? 'selected' : ''; ?>>São Mateus</option>
              <option value="Pirajussara" <?php echo ($cidade_atual && normalize_slug('Pirajussara') === $cidade_atual) ? 'selected' : ''; ?>>Pirajussara</option>
              <option value="Santo Amaro" <?php echo ($cidade_atual && normalize_slug('Santo Amaro') === $cidade_atual) ? 'selected' : ''; ?>>Santo Amaro</option>
            </select>
          </div>
          <button type="submit" class="submit-btn btn-pulsante">
            <i class="fas fa-calendar-check me-2"></i>
            Agendar Exame
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Botão fixo mobile para abrir formulário -->
<button class="mobile-agendamento-btn" id="openFormMobile">
  <i class="fas fa-calendar-plus btn-icon"></i>
  <span class="btn-text">Agendar Exame</span>
</button>



  <section class="banner mb-5 mt-lg-5 py-lg-5">
    <div class="container">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-5 col-xl-5">
          <img src="/assets/images/logo-clinica.png" alt="Clínica de Optometria Olhar Perfeito." width="270">
          <h1>Exame de Vista Acessível com Optometrista em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>?</h1>
          <p>Você foi selecionado para participar do Projeto Olhar Perfeito em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>! Faça seu <strong>exame de vista 100% Acessível</strong> com optometristas especializados em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>. <strong>Restam poucas vagas</strong> — garanta já a sua!</p>
          <div class="wrapper d-flex flex-column flex-lg-row gap-2 align-items-center">
            <button type="button" class="btn-padrao btn-pulsante" onclick="scrollToForm()">Agendar Exame</button>
            <p>Restam poucas vagas para agendamento!</p>
          </div>
        </div>
        <figure class="mb-0 col-lg-7 col-xl-6 text-center">
          <span class="tag-image-1">+200 mil atendimentos</span>
          <span class="tag-image-2">Exame 100% Acessível</span>
          <span class="icone-1"><i class="fa-solid fa-user-plus"></i></span>
          <span class="icone-2"><i class="fa-solid fa-ambulance"></i></span>
          <img class="main-img" src="/assets/images/2.png" alt="">
        </figure>
      </div>
    </div>
  </section>

  <section class="itens_icone my-5 py-lg-5">
    <div class="container">
      <div class="wrapper d-flex justify-content-lg-between">
        <div class="item">
          <img class="icone" src="/assets/images/check.svg" alt="">
          <h3>Exame 100% Acessível em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h3>
          <p>Sem custo em nenhuma etapa. Projeto exclusivo para quem precisa cuidar da visão agora em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>.</p>
        </div>
        <div class="item">
          <img class="icone" src="/assets/images/check.svg" alt="">
          <h3>+200 mil Atendimentos em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h3>
          <p>Centenas de milhares de pessoas já passaram pelo nosso projeto em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>. Você pode ser o(a) próximo(a) da sua cidade.</p>
        </div>
        <div class="item">
          <img class="icone" src="/assets/images/check.svg" alt="">
          <h3>Optometristas Qualificados em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h3>
          <p>Exame completo com profissionais de optometria capacitados e acolhimento humanizado em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>.</p>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="img_texto py-5 my-4">
    <div class="container">
      <div class="row align-items-center gx-lg-5">
        <div class="texto col-lg-7 order-1 order-lg-1">
        <h2>Exame de Vista Acessível com Atendimento Rápido e Sem Burocracia em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h2>
        <p>No Projeto Olhar Perfeito em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>, você realiza seu <strong>exame de vista 100% Acessível</strong> com optometrista de forma simples e rápida. <strong>Vagas limitadas por cidade</strong> — agende enquanto ainda há disponibilidade em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>!</p>
        <ul>
          <li>Sem custo, sem pegadinhas, sem compromisso de compra em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Atendimento com profissionais experientes em optometria em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Estrutura confortável e segura em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Mais de 200 mil pessoas atendidas em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
        </ul>
        <button type="button" class="btn-padrao btn-pulsante" onclick="scrollToForm()">Agendar Exame</button>

        </div>
        <figure class="col-lg-5 mb-0 order-2 order-lg-2 mt-5 mt-lg-0">
          <img class="w-100" src="/assets/images/1.png" alt="">
        </figure>
      </div>
    </div>
  </section>

  <section class="img_texto py-5 my-4">
    <div class="container">
      <div class="row align-items-center gx-lg-5">
        <div class="texto col-lg-7 order-1 order-lg-1">
        <h2>Exame de Vista 100% Acessível com Atendimento Humanizado em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h2>
        <p>Na Clínica Olhar Perfeito em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>, o exame de vista é totalmente <strong>Acessível</strong>. Nosso atendimento vai além da triagem ocular: acolhemos cada pessoa com respeito, empatia e atenção individual. Aqui, você encontra cuidado de verdade pagando um valor acessível pelo exame em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>.</p>
        <ul>
          <li>Atendimento acessível e humanizado desde o primeiro contato em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Optometristas capacitados prontos para te atender em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Ambiente seguro, confortável e acessível em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Equipe treinada para ouvir, orientar e cuidar de você em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
          <li>Mais de 200 mil pessoas já atendidas em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
        </ul>
        <a href="https://api.whatsapp.com/send?phone=5511937023409&text=Ol%C3%A1,%20gostaria%20de%20agendar%20meu%20exame." target="_blank" class="btn-padrao btn-pulsante">Agendar Exame</a>

        </div>
        <figure class="col-lg-5 mb-0 order-2 order-lg-0 mt-5 mt-lg-0">
          <img class="w-100" src="/assets/images/3.png" alt="">
        </figure>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="depoimentos py-5 my-4">
    <div class="container">
      <div class="wrapper d-flex">
        <div class="depoimento">
          <img src="/assets/images/eart.svg" alt="">
          <p>"Ótimo atendimento, super atenciosos. Fui tratada com respeito e atenção desde o começo em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>!"</p>
          <figure class="depoimento-pessoa">
            <img class="object-fit-cover" src="/assets/images/depoimento-juliana.jpg" alt="">
          </figure>
          <span class="nome">Juliana R.</span>
        </div>
        <div class="depoimento">
          <img src="/assets/images/eart.svg" alt="">
          <p>"Fiz meu exame de vista totalmente acessível e já saí sabendo meu grau. Atendimento excelente em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>!"</p>
          <figure class="depoimento-pessoa">
            <img class="object-fit-cover" src="/assets/images/depoimento-marcelo.jpg" alt="">
          </figure>
          <span class="nome">Marcelo S.</span>
        </div>
        <div class="depoimento">
          <img src="/assets/images/eart.svg" alt="">
          <p>"Muito bom! Fiz o exame e fui super bem atendida em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>. Já estou usando meus óculos novos. Recomendo!"</p>
          <figure class="depoimento-pessoa">
            <img class="object-fit-cover" src="/assets/images/depoimento-renata.jpg" alt="">
          </figure>
          <span class="nome">Renata M.</span>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <section class="accordion-wrapper py-5 my-5">
    <div class="container">
              <div class="texto text-center">
        <h2>Perguntas Frequentes em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h2>
        <p class="text-center mb-4">Essas são dúvidas comuns de quem está agendando o <strong>exame acessível com optometrista</strong> em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>:</p>
      </div>

      <div class="accordion accordion-flush col-lg-7 mx-auto mt-5" id="accordionFAQ">
        <div class="accordion-item mb-2">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
            Quem pode participar do Projeto Olhar Perfeito em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>?
            </button>
          </h3>
          <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
          <div class="accordion-body">
            Pessoas de baixa renda ou que não possuem acesso fácil a consultas visuais em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>. O projeto é social e sem custo.
          </div>
          </div>
        </div>
        <div class="accordion-item mb-2">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo">
            O exame realmente é acessível em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>?
            </button>
          </h3>
          <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
          <div class="accordion-body">
            Sim! Todo o processo de triagem visual com optometrista é 100% acessível em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?> — sem cobranças escondidas.
          </div>
          </div>
        </div>
        <div class="accordion-item mb-2">
          <h3 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree">
            O que preciso levar no dia do exame em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>?
            </button>
          </h3>
          <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
          <div class="accordion-body">
            Documento com foto e, se possível, seus óculos antigos ou uma receita anterior. Isso ajuda na análise do optometrista em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>.
          </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-final bg-primary text-white py-5">
  <div class="container text-center">
    <h2 class="fw-bold">Agende Seu Exame de Vista 100% Acessível Agora em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h2>
    <p class="lead">Vagas limitadas em sua região de <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>. Garanta seu atendimento com optometrista ainda hoje.</p>
    <a href="https://api.whatsapp.com/send?phone=5511937023409&text=Ol%C3%A1,%20gostaria%20de%20agendar%20meu%20exame." target="_blank" class="btn btn-light px-4 py-2 btn-pulsante">Agendar Exame</a>
  </div>
</section>

  <section class="bloco_servicos py-5 my-5">
    <div class="container">
      <div class="row justify-content-lg-between align-items-center gy-5">
        <div class="texto col-lg-7 col-xl-7">
          <img src="/assets/images/logo-clinica.png" alt="Clínica de Optometria Olhar Perfeito." width="270">
          <span class="d-block mt-3 w-100">Vagas Limitadas</span>
          <h2>Exame de Vista Acessível com quem realmente se importa em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></h2>
          <p>Agende agora mesmo seu exame de vista <strong>100% Acessível</strong> pela Clínica Olhar Perfeito. Estamos prontos para te atender com acolhimento, cuidado e equipe especializada em saúde visual. Atendemos em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>, ABC e interior com estrutura completa e profissionais qualificados.</p>
        </div>
        <div class="col-lg-5 col-xl-4">
          <div class="plano">
            <h3>Vagas Limitadas</h3>
            <ul>
              <li>Exame 100% Acessível</li>
              <li>Atendimento rápido e humanizado</li>
              <li>Unidade próximo a <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?></li>
            </ul>
            <a href="https://api.whatsapp.com/send?phone=5511937023409&text=Ol%C3%A1,%20gostaria%20de%20agendar%20meu%20exame." target="_blank" class="btn-padrao btn-pulsante">Agendar Exame</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="divider"></div>

  <footer class="py-4 py-lg-5">
    <div class="container">
      <div class="d-flex flex-column flex-lg-row justify-content-lg-between gap-2 px-0">
        <span>Clínica de Optometria Olhar Perfeito. <br>Todos os direitos reservados</span>
      <!-- Selects de Estado e Cidade no rodapé, sutil -->
      <div class="d-flex justify-content-end mt-3 mt-lg-0">
          <form id="form-localidade-rodape" class="d-flex flex-wrap align-items-center gap-2 small form-localidade-rodape mobile-selects-container">
            <select id="select-estado-rodape" class="form-select form-select-sm select-estado-rodape" name="estado">
              <option value="">Estado</option>
              <?php foreach ($estados as $slug => $nome): ?>
                <option value="<?= htmlspecialchars($slug) ?>" <?= $estado_atual === $slug ? 'selected' : '' ?>><?= htmlspecialchars($nome) ?></option>
              <?php endforeach; ?>
            </select>
            <select id="select-cidade-rodape" class="form-select form-select-sm select-cidade-rodape" name="cidade">
              <option value="">Cidade</option>
              <?php if ($estado_atual && isset($cidades_por_estado[$estado_atual])): ?>
                <?php foreach ($cidades_por_estado[$estado_atual] as $cidade): ?>
                  <option value="<?= htmlspecialchars($cidade['slug']) ?>" <?= $cidade_atual === $cidade['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($cidade['nome']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </form>
      </div>
      </div>
    </div>
  </footer>

<!-- Scripts -->
<script src="/assets/lib/jquery/jquery-3.7.1.min.js?07ago25"></script>
<script src="/assets/lib/bootstrap/js/bootstrap.bundle.min.js?07ago25"></script>
<script src="/assets/lib/mask/jquery.mask.js?07ago25"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/assets/js/frontend.js?07ago25"></script>
<script>
// Dados de cidades por estado vindos do PHP
var cidadesPorEstado = <?php echo json_encode($cidades_por_estado); ?>;
// Localidade atual para o formulário
window.currentLocation = <?php echo json_encode($nome ? htmlspecialchars($nome) : '[location]'); ?>;
window.currentEstado = <?php echo json_encode($estado_atual ? htmlspecialchars($estado_atual) : ''); ?>;
window.currentCidade = <?php echo json_encode($cidade_atual ? htmlspecialchars($cidade_atual) : ''); ?>;
</script>
<script src="/assets/js/agendamento.js?07ago25"></script>
    <a href="https://api.whatsapp.com/send?phone=5511937023409&text=Ol%C3%A1,%20gostaria%20de%20agendar%20meu%20exame." target="_blank" id="btn_chat_whatsapp"><span>Agendar Exame</span><i><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" class="whatsapp-icon"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></i></a>
  <!-- Box de notificações de agendamento -->
  <div id="notificacao-agendamento">
    <span class="icon"><i class="fa-solid fa-check-circle"></i></span>
    <span class="texto"></span>
  </div>

  <!-- Modal de Agradecimento -->
  <div class="modal fade" id="modalAgradecimento" tabindex="-1" aria-labelledby="modalAgradecimentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center py-4">
          <div class="mb-4">
            <i class="fa-solid fa-check-circle text-success" style="font-size: 4rem;"></i>
          </div>
          <h4 class="modal-title mb-3" id="modalAgradecimentoLabel">Agendamento Realizado com Sucesso!</h4>
          <p class="text-muted mb-4" id="modalMensagem">Obrigado por agendar seu exame de vista em <?php echo $nome ? htmlspecialchars($nome) : '[location]'; ?>! Entraremos em contato em breve para confirmar os detalhes.</p>
          <div class="d-grid gap-2">
            <a href="#" target="_blank" class="btn btn-success disabled" id="btnWhatsAppConfirmar" style="display: none;">
              <i class="fab fa-whatsapp me-2"></i>Confirmar no WhatsApp
            </a>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>