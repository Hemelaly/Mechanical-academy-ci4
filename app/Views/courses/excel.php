<?php

$isLoggedIn   = auth()->loggedIn();

$user = service('auth')->user();

// dd($course)


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($course->title_course) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .bg-primary {
      background: <?= esc($course->color_course) ?> !important;
    }

    .text-primary {
      color: <?= esc($course->color_course) ?> !important;
    }

    .btn-primary {
      background: <?= esc($course->color_course) ?> !important;
      border: 0;
    }

    .bg-yellow {
      background: rgba(226, 226, 128, 1);
    }

    .bg-darkblue {
      background: #161e2a;
    }

    .bg-blue {
      background: #1b232f;
    }

    .check-icon {
      color: <?= esc($course->color_course) ?>;
    }

    .feature-bold {
      font-weight: 700;
    }
  </style>
</head>

<body style="font-family: 'Poppins';">

  <nav class="navbar navbar-expand-lg sticky-top bg-black navbar-dark py-3">
    <div class="container">
      <a class="navbar-brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('./assets/img/logo.png') ?>" alt="Logo" style="width: 150px;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
          <?php if ($isLoggedIn): ?>
            <li class="nav-item me-3">
              <a class="nav-link active" href="<?= base_url($user->role . '/dashboard/meus_cursos') ?>">Meus Cursos</a>
            </li>
            <li class="nav-item d-flex align-items-center">
              <a href="<?= base_url($user->role . '/dashboard/perfil') ?>" class="d-flex align-items-center text-decoration-none">
                <img src="<?= base_url('assets/img/user-default.png') ?>" alt="User" class="rounded-circle me-2" width="35" height="35">
                <span class="text-white fw-semibold text-nowrap"><?= $user->username ?></span>
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item me-3">
              <a class="nav-link active" href="#cursos">Cursos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="<?= base_url('login') ?>">Entrar</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <section id="banner"
    style="background-image: url('<?= base_url('assets/img/' . ($course->bg_course ?? 'bg.webp')) ?>'); height:100%; width:100%; background-size:cover; background-position:center center; background-repeat:no-repeat;">
    <div class="overlay" style="background: rgba(0, 0, 0, 0.9); height: 100%; width: 100%;">
      <div class="container py-5">
        <div class="content text-center py-3">
          <div class="badged d-flex justify-content-center">
            <p class="text-dark bg-yellow px-3 rounded">Curso mais vendido e recém-renovado</p>
          </div>
          <div class="title d-flex justify-content-center">
            <p class="title fs-1 fw-bold text-white">Curso de Excel Para Todos</p>
          </div>
          <div class="course-image d-flex justify-content-center">
            <img src="<?= base_url('assets/img/' . $course->image_course) ?>" class="img-fluid rounded h-auto" width="500px" alt="">
          </div>
          <div class="description text-center mt-3">
            <p class="title fs-2 fw-bold text-white">Aprenda Excel moderno desde o início</p>
            <p class="text-white" style="margin-top: -0.8rem;">Um curso de mestrado de 40 horas para levá-lo
              do iniciante ao avançado</p>
            <a href="<?= base_url('/checkout/' . $course->id_course) ?>" class="btn btn-primary py-3 px-5 fw-bold">Compre Agora</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="numbers" class="bg-darkblue">
    <div class="container py-4">
      <div class="content">
        <div class="row">
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-user-graduate text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white">100,000+</p>
              <p class="text-white">Alunos matriculados</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-star-half-stroke text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white">4.7+</p>
              <p class="text-white">Avaliado</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-language text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white">Português</p>
              <p class="text-white">Idioma do Curso</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-arrows-rotate text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white">Ultima atualização</p>
              <p class="text-white">10/2025</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="video" class="bg-blue">
    <div class="container py-lg-5 py-0">
      <div class="content py-lg-4 py-0">
        <p class="fs-2 fw-bold text-center text-white">Vídeo de visão geral do curso</p>

        <div class="video-area mx-auto mt-lg-3 mt-0" style="width: 65%; height: 500px;">
          <iframe title="vimeo-player" class="rounded mt-lg-3 mt-0" src="<?= esc($course->url_video_course) ?>" width="100%" height="100%" frameborder="0" referrerpolicy="strict-origin-when-cross-origin" allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" allowfullscreen></iframe>
        </div>
      </div>
  </section>

  <section id="features" class="bg-darkblue py-5">
    <div class="container">
      <div class="content">
        <div class="row row-cols-1 row-cols-md-4 g-4 align-items-stretch">
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center">
              <div class="icon">
                <i class="fa-solid fa-photo-film text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4">40+ horas de vídeo sob demanda</p>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center">
              <div class="icon">
                <i class="fa-solid fa-download text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4">20+ recursos e documentos para download</p>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center">
              <div class="icon">
                <i class="fa-solid fa-calendar-days text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4">Acesso vitalício completo</p>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center">
              <div class="icon">
                <i class="fa-solid fa-graduation-cap text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4">Certificado de conclusão</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="details" class="py-5">
    <div class="container">
      <div class="row justify-content-center align-items-center">
        <!-- Lista de aprendizado -->
        <div class="col-lg-8 mb-4">
          <div class="bg-white shadow rounded-3 p-4 p-md-5 h-100">
            <h2 class="fw-bold text-primary mb-4 text-center">O que você aprenderá</h2>
            <ul class="list-unstyled feature-text">
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Introdução ao Excel:
                interface, células, planilhas e menus</li>
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Formatação de dados:
                estilos, formatação condicional e tabelas</li>
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Fórmulas básicas:
                SOMA, MÉDIA, MÍNIMO, MÁXIMO, CONT.SE</li>
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Funções avançadas:
                PROCV, ÍNDICE, CORRESP, SE, SOMASE</li>
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Gráficos: criação e
                personalização de gráficos profissionais</li>
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Tabelas dinâmicas:
                criação, segmentação e análise de dados</li>
              <li class="d-flex mb-3"><i class="bi bi-check-lg check-icon me-3"></i>Validação de dados e
                proteção de planilhas</li>
              <li class="d-flex"><i class="bi bi-check-lg check-icon me-3"></i><span><span
                    class="feature-bold">Bônus:</span> Automatização com Macros e introdução ao
                  VBA</span></li>
            </ul>
          </div>
        </div>

        <!-- Card de compra -->
        <div class="col-lg-4">
          <div class="bg-white shadow rounded-3 p-4 text-center">
            <h4 class="fw-bold text-primary mb-3">Excel Básico</h4>
            <span class="fw-bold display-6 mb-2"><?= number_format(esc($course->price_course), 2, ",", ".") ?></span><sub class="fw-bold">MZN</sub>
            <p class="text-muted mb-4">Compra única</p>
            <a href="<?= base_url('/checkout/' . $course->id_course) ?>" class="btn btn-primary btn-lg fw-bold px-4">Compre Agora</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="description" class="py-5 text-white bg-darkblue">
    <div class="container text-center">
      <h2 class="fw-bold mb-4">Descrição do Curso</h2>
      <p class="fs-5 mb-4">
        Este é um curso aprofundado de 40+ horas que o levará desde o início absoluto do Excel, aprendendo desde
        fórmulas básicas até recursos avançados como tabelas dinâmicas, gráficos e automação com macros.
        Você aprenderá a criar planilhas profissionais, organizar e analisar grandes volumes de dados e aplicar
        técnicas de produtividade para o uso no ambiente corporativo.
      </p>
      <p class="fs-5 mb-4">
        Também exploraremos o uso de funções complexas como PROCX, SOMASES, ÍNDICE e CORRESP, além de recursos
        de validação de dados, proteção de planilhas e dashboards interativos.
        No final, você dominará as ferramentas para transformar dados brutos em insights claros e visuais
        profissionais.
      </p>
      <p class="fw-bold fs-5">Confira o currículo abaixo!</p>

      <div class="bg-blue text-white rounded p-4 mt-5 mx-auto" style="max-width: 1000px;">
        <p class="fs-5 mb-0">
          <strong>BÔNUS:</strong> Os alunos que adquirirem este curso receberão documentação detalhada com
          exemplos práticos, planilhas-modelo e exercícios organizados por módulo.
          Você poderá baixar e consultar todos os materiais para revisar cada tema facilmente durante o curso.
        </p>
      </div>
    </div>
  </section>

  <section id="modules" class="bg-blue py-5">
    <div class="container">
      <div class="accordion mx-auto w-75" id="excelAccordion">

        <?php foreach ($modules as $key => $module): ?>
          <div class="accordion-item mb-3 border-0 shadow-sm p-2">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                data-bs-target="#mod<?= $module->id_module ?>">
                <?= esc($module->title_module) ?>
              </button>
            </h2>
            <div id="mod<?= $module->id_module ?>" class="accordion-collapse collapse" data-bs-parent="#excelAccordion">
              <div class="accordion-body">
                <?= esc($module->description_module) ?>
              </div>
            </div>
          </div>
        <?php endforeach ?>

      </div>
    </div>
  </section>

  <section id="projects" class="text-center text-white">
    <div class="bg-darkblue pt-5 pb-2">
      <h2 class="fw-bold mb-2">Projetos de Curso</h2>
      <p class="fs-5 mb-5">Vamos dar uma olhada em alguns dos projetos práticos desenvolvidos durante o curso
      </p>
    </div>

    <div class="py-5" style="background: #eee;">
      <div class="container pb-3">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="bg-white text-dark px-5 py-4 rounded h-100">
              <img src="<?= base_url('assets/img/Excell.jpg') ?>" alt="Dashboard Financeiro" class="img-fluid rounded mb-3">
              <h5 class="fw-bold">Dashboard Financeiro</h5>
              <p class="mb-0">
                Projeto de análise financeira com tabelas dinâmicas, gráficos e métricas automáticas.
                Permite visualizar lucros, despesas e tendências de forma interativa.
              </p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="bg-white text-dark px-5 py-4 rounded h-100">
              <img src="<?= base_url('assets/img/Excell.jpg') ?>" alt="Planilha de controle" class="img-fluid rounded mb-3">
              <h5 class="fw-bold">Controle de Estoque</h5>
              <p class="mb-0">
                Planilha automatizada com fórmulas e validações para controle de produtos e níveis de
                estoque.
                Inclui alertas e relatórios de movimentação.
              </p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="bg-white text-dark px-5 py-4 rounded h-100">
              <img src="<?= base_url('assets/img/Excell.jpg') ?>" alt="Tela de macros" class="img-fluid rounded mb-3">
              <h5 class="fw-bold">Automação com VBA</h5>
              <p class="mb-0">
                Projeto completo de automação usando VBA para geração de relatórios automáticos e
                consolidação de dados de múltiplas planilhas.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- <section id="rating" class="text-white">
    <div class="title bg-darkblue py-5">
      <div class="container text-center">
        <h2 class="fw-bold mb-2">Classificações do Curso</h2>
        <p class="text-secondary mb-5">Algumas das últimas avaliações dos nossos alunos</p>
      </div>
    </div>

    <div class="bg-blue">
      <div class="container text-center">
        <div class="row g-5 pb-3">
          <div class="col-md-4">
            <div class="d-flex flex-column align-items-center">
              <div class="rounded-circle border border-primary d-flex align-items-center justify-content-center mb-3"
                style="width:60px; height:60px;">
                <span class="fw-bold text-primary">MS</span>
              </div>
              <div class="text-warning mb-2">
                ★★★★★
              </div>
              <h5 class="fw-bold text-white">Mariana S.</h5>
              <p class="text-light">
                O curso de Excel é excelente! As explicações são diretas e os exemplos práticos ajudam
                muito.
                Aprendi a criar dashboards e automatizar planilhas em poucas semanas. Recomendo para
                todos!
              </p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="d-flex flex-column align-items-center">
              <div class="rounded-circle border border-primary d-flex align-items-center justify-content-center mb-3"
                style="width:60px; height:60px;">
                <span class="fw-bold text-primary">RC</span>
              </div>
              <div class="text-warning mb-2">
                ★★★★★
              </div>
              <h5 class="fw-bold text-white">Ricardo C.</h5>
              <p class="text-light">
                Gostei muito da didática e da clareza dos exemplos.
                As aulas de tabelas dinâmicas e fórmulas avançadas foram incríveis.
                Agora uso o Excel de forma muito mais profissional no trabalho.
              </p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="d-flex flex-column align-items-center">
              <div class="rounded-circle border border-primary d-flex align-items-center justify-content-center mb-3"
                style="width:60px; height:60px;">
                <span class="fw-bold text-primary">TP</span>
              </div>
              <div class="text-warning mb-2">
                ★★★★★
              </div>
              <h5 class="fw-bold text-white">Tatiane P.</h5>
              <p class="text-light">
                O curso superou minhas expectativas. As explicações sobre VBA e automação foram
                fantásticas.
                Já consegui criar relatórios automáticos e economizar horas de trabalho!
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <footer class="py-3 text-white" style="background-color:#111827;">
    <div
      class="container d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-start">

      <!-- Logotipo circular -->
      <div class="mb-3 mb-md-0">
        <div class="" style="width:150px; height:auto;">
          <a href="">
            <img src="<?= base_url('assets/img/logo.png') ?>" class="img-fluid h-auto" width="100%" alt="">
          </a>
        </div>
      </div>

      <!-- Texto central -->
      <div class="mb-3 mb-md-0">
        <small>© 2025 Mechanical Academy. Todos os direitos reservados</small>
      </div>

      <!-- Ícones sociais -->
      <div>
        <a href="#" class="text-white me-3"><i class="bi bi-twitter-x"></i></a>
        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
        <a href="#" class="text-white me-3"><i class="bi bi-youtube"></i></a>
        <a href="#" class="text-white me-3"><i class="bi bi-github"></i></a>
        <a href="#" class="text-white"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </footer>




  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>
</body>

</html>