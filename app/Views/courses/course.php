<?php

$isLoggedIn   = auth()->loggedIn();

$user = service('auth')->user();
$defaultAvatarUrl = base_url('assets/img/user-default.png');
$userAvatarUrl = $defaultAvatarUrl;
if ($isLoggedIn && $user && !empty($user->img)) {
    $rawAvatar = trim((string) $user->img);
    if (preg_match('#^https?://#i', $rawAvatar) === 1) {
        $userAvatarUrl = $rawAvatar;
    } else {
        $userAvatarUrl = base_url(ltrim($rawAvatar, '/'));
    }
}

// dd($projects)

$learningHtml = trim($course->learning_course ?? ($course->what_learn_course ?? ''));
$defaultLearningList = <<<'HTML'
<li>Introdução ao Excel: interface, células, planilhas e menus</li>
<li>Formatação de dados: estilos, formatação condicional e tabelas</li>
<li>Fórmulas básicas: SOMA, MÉDIA, MÍNIMO, MÁXIMO, CONT.SE</li>
<li>Funções avançadas: PROCV, ÍNDICE, CORRESP, SE, SOMASE</li>
<li>Gráficos: criação e personalização de gráficos profissionais</li>
<li>Tabelas dinâmicas: criação, segmentação e análise de dados</li>
<li>Validação de dados e proteção de planilhas</li>
<li><span class="feature-bold">Bônus:</span> Automatização com Macros e introdução ao VBA</li>
HTML;

if ($learningHtml === '') {
    $learningHtml = $defaultLearningList;
}

if (!str_contains($learningHtml, 'fa-check')) {
    $learningHtml = preg_replace(
        '/(<li\b[^>]*>)/i',
        '$1<i class="fa-solid fa-check text-primary me-3"></i>',
        $learningHtml
    );
}

// Force learning section to render only clean <li> rows.
$normalizedLearningItems = [];
if (preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', (string) $learningHtml, $learningMatches)) {
    foreach (($learningMatches[1] ?? []) as $itemHtml) {
        $itemText = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $itemHtml)));
        if ($itemText !== '') {
            $normalizedLearningItems[] = $itemText;
        }
    }
}

if (empty($normalizedLearningItems)) {
    $plainLearningSource = preg_replace('/<\/(p|h[1-6]|li|div|br)\s*>/i', "\n", (string) $learningHtml);
    $plainLearning = strip_tags((string) $plainLearningSource);
    $chunks = preg_split('/(?:\r\n|\r|\n|;|\|)+/u', (string) $plainLearning) ?: [];
    foreach ($chunks as $chunk) {
        $itemText = trim(preg_replace('/\s+/u', ' ', (string) $chunk), " \t\n\r\0\x0B-");
        if ($itemText !== '') {
            $normalizedLearningItems[] = $itemText;
        }
    }
}

if (empty($normalizedLearningItems)) {
    $normalizedLearningItems = [
        'Introducao ao Excel: interface, celulas, planilhas e menus',
        'Formatacao de dados: estilos, formatacao condicional e tabelas',
        'Formulas basicas: SOMA, MEDIA, MINIMO, MAXIMO, CONT.SE',
        'Funcoes avancadas: PROCV, INDICE, CORRESP, SE, SOMASE',
        'Graficos: criacao e personalizacao de graficos profissionais',
        'Tabelas dinamicas: criacao, segmentacao e analise de dados',
        'Validacao de dados e protecao de planilhas',
        'Bonus: Automatizacao com Macros e introducao ao VBA',
    ];
}

$learningHtml = '';
foreach ($normalizedLearningItems as $itemText) {
    $learningHtml .= '<li><i class="fa-solid fa-check text-primary me-3"></i><span>' . esc($itemText) . '</span></li>';
}

$descriptionHtml = trim($course->description_course ?? '');
if ($descriptionHtml === '') {
    $descriptionHtml = <<<'HTML'
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
HTML;
}

$courseTitleEsc = esc($course->title_course);
$heroSubtitleRaw = trim($course->subtitle_course ?? '');
if ($heroSubtitleRaw === '') {
    $heroSubtitle = "Aprenda {$courseTitleEsc} moderno desde o início";
} else {
    $heroSubtitle = esc($heroSubtitleRaw);
}

$overviewVideoUrlRaw = trim((string) ($course->url_video_course ?? ''));
$overviewVideoId = null;

if (!function_exists('getVimeoId')) {
    function getVimeoId($url)
    {
        preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', (string) $url, $m);
        return $m[1] ?? null;
    }
}

$overviewVideoId = getVimeoId($overviewVideoUrlRaw);
$overviewPlayerId = (int) ($course->id_course ?? 0);
$publicPreviewLessonCount = 0;

foreach ($modules as &$moduleItem) {
    $moduleItem->previewable_lessons = 0;

    foreach (($moduleItem->lessons ?? []) as &$moduleLesson) {
        $lessonType = trim((string) ($moduleLesson->type_lesson ?? 'video'));
        $lessonVideoId = $lessonType === 'video'
            ? getVimeoId((string) ($moduleLesson->video_url_lesson ?? ''))
            : null;

        $moduleLesson->preview_video_id = $lessonVideoId;
        $moduleLesson->is_public_preview = (int) ($moduleLesson->is_preview_lesson ?? 0) === 1 && ! empty($lessonVideoId);

        if ($moduleLesson->is_public_preview) {
            $publicPreviewLessonCount++;
            $moduleItem->previewable_lessons++;
        }
    }
    unset($moduleLesson);
}
unset($moduleItem);
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
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" width="100%" type="image/x-icon">
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

    .feature-text li {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      margin-bottom: 0.75rem;
    }

    .feature-text li:last-child {
      margin-bottom: 0;
    }

    .feature-text li span {
      display: block;
      line-height: 1.55;
    }

    .nav-avatar {
      width: 35px;
      height: 35px;
      min-width: 35px;
      border-radius: 50%;
      object-fit: cover;
      object-position: center;
      display: block;
      background: #111827;
      border: 1px solid rgba(255, 255, 255, 0.24);
    }

    .purchase-box-sticky {
      position: sticky;
      top: 96px;
      z-index: 20;
    }

    .video-area {
      width: 100%;
      max-width: 980px;
      aspect-ratio: 16 / 9;
    }

    .video-area iframe,
    .video-area .video-fallback {
      width: 100%;
      height: 100%;
      display: block;
    }

    .modules-accordion {
      width: 100%;
      max-width: 980px;
    }

    .modules-accordion .accordion-item {
      background: #d4d4d8;
      border-radius: 8px;
      overflow: hidden;
    }

    .modules-accordion .accordion-button {
      background: #d4d4d8;
      font-size: 1.2rem;
      color: #111827;
      box-shadow: none;
    }

    .modules-accordion .accordion-button:not(.collapsed) {
      color: #111827;
      background: #d4d4d8;
    }

    .modules-accordion .accordion-body {
      background: #d4d4d8;
      padding-top: 0.35rem;
    }

    .modules-accordion .module-lesson-item {
      background: #dfdfe2ff;
      color: #1f2937;
      padding: 0.9rem 1rem;
      margin-bottom: 0.35rem;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      line-height: 1.35;
      font-size: 1.2rem;
    }

    .modules-accordion .module-lesson-item:last-child {
      margin-bottom: 0;
    }

    .modules-accordion .module-lesson-preview-button {
      border: 0;
      text-align: left;
      transition: transform 0.18s ease, background-color 0.18s ease;
      width: 100%;
    }

    .modules-accordion .module-lesson-link {
      text-decoration: none;
      transition: transform 0.18s ease, background-color 0.18s ease;
      width: 100%;
    }

    .modules-accordion .module-lesson-preview-button:hover {
      background: #b8d7ff;
      transform: translateX(2px);
    }

    .modules-accordion .module-lesson-link:hover {
      background: #dbeafe;
      transform: translateX(2px);
    }

    .modules-accordion .module-lesson-locked {
      opacity: 0.88;
    }

    .modules-accordion .lesson-main {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: 0.85rem;
      flex: 1;
    }

    .modules-accordion .lesson-icon {
      color: <?= esc($course->color_course) ?>;
      font-size: 1.15rem;
      flex: 0 0 auto;
    }

    .modules-accordion .lesson-copy {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 0.1rem;
    }

    .modules-accordion .lesson-title {
      display: block;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .modules-accordion .lesson-meta {
      color: #4b5563;
      font-size: 0.78rem;
      font-weight: 600;
    }

    .modules-accordion .lesson-status {
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      font-size: 0.9rem;
      font-weight: 700;
    }

    .modules-accordion .lesson-status-open {
      color: #15803d;
    }

    .modules-accordion .lesson-status-locked {
      color: #475569;
    }

    .modules-accordion .lesson-status-text {
      font-size: 0.8rem;
      font-weight: 700;
    }

    .modules-accordion .preview-helper {
      font-size: 0.95rem;
      color: #cbd5e1;
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .lesson-preview-player {
      aspect-ratio: 16 / 9;
      background: #020617;
      border-radius: 1rem;
      overflow: hidden;
    }

    .lesson-preview-player iframe {
      width: 100%;
      height: 100%;
      border: 0;
      display: block;
    }

    @media (min-width: 992px) {
      .video-area {
        width: 65%;
      }

      .modules-accordion {
        width: 75%;
      }
    }

    @media (max-width: 991.98px) {
      .purchase-box-sticky {
        position: static;
        top: auto;
      }
    }

    @media (max-width: 767.98px) {
      #video .container {
        padding-left: 1rem;
        padding-right: 1rem;
      }

      #video .title {
        font-size: 1.75rem !important;
      }

      .video-area {
        max-width: 100%;
      }

      .modules-accordion .accordion-button {
        font-size: 1rem;
        line-height: 1.35;
      }

      .modules-accordion .module-lesson-item {
        font-size: 1rem;
        padding: 0.8rem 0.85rem;
      }
    }

    .title {
      font-family: 'Fira Sans' !important;
    }

    :root {
      --pre-bg: #0b0f19;
      --pre-fg: #fff;
      --pre-accent: #7c5cff;
      --pre-z: 9999;
      --fade-ms: 360ms;
    }

    #preloader {
      position: fixed;
      inset: 0;
      z-index: var(--pre-z);
      display: grid;
      place-items: center;
      color: var(--pre-fg);
      background:
        radial-gradient(1000px 700px at 10% 10%, #11162a 0%, transparent 60%),
        radial-gradient(1000px 700px at 90% 90%, #0e1330 0%, transparent 60%),
        var(--pre-bg);
      transition: opacity var(--fade-ms) ease, visibility var(--fade-ms) ease;
    }

    #preloader.is-hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .preloader__inner {
      display: grid;
      gap: 14px;
      place-items: center;
      text-align: center;
    }

    .preloader__logo {
      width: 84px;
      height: auto;
      filter: drop-shadow(0 2px 10px rgba(0, 0, 0, .35));
    }

    .preloader__spinner {
      width: 56px;
      height: 56px;
    }

    .preloader__track {
      fill: none;
      stroke: rgba(255, 255, 255, .18);
      stroke-width: 6;
    }

    .preloader__arc {
      fill: none;
      stroke: var(--pre-accent);
      stroke-linecap: round;
      stroke-width: 6;
      stroke-dasharray: 110 126;
      transform-origin: 50% 50%;
      animation: spin 1.05s linear infinite, dash 1.5s ease-in-out infinite;
    }

    .preloader__bar {
      width: min(320px, 70vw);
      height: 8px;
      border-radius: 999px;
      background: rgba(255, 255, 255, .15);
      overflow: hidden;
    }

    .preloader__bar__fill {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, var(--pre-accent), #9a7bff 60%, var(--pre-accent));
      border-radius: 999px;
      transform: translateZ(0);
    }

    .preloader__text {
      font: 600 .95rem/1.2 system-ui, -apple-system, Segoe UI, Roboto, Helvetica Neue, Arial;
      opacity: .9;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    @keyframes dash {
      0% {
        stroke-dasharray: 1 235;
        stroke-dashoffset: 0;
      }

      50% {
        stroke-dasharray: 120 115;
        stroke-dashoffset: -25;
      }

      100% {
        stroke-dasharray: 1 235;
        stroke-dashoffset: -235;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .preloader__arc {
        animation: none;
      }
    }
  </style>
</head>

<body style="font-family: 'Open Sans';">

  <!-- PRELOADER -->
  <div id="preloader" role="status" aria-live="polite" aria-label="Carregando conteúdo">
    <div class="preloader__inner">
      <!-- opcional: seu logotipo -->
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Minha Marca" class="preloader__logo h-auto w-50" />


      <div class="preloader__bar">
        <div class="preloader__bar__fill" id="preloaderFill" style="width:0%"></div>
      </div>
      <p class="preloader__text"><span id="preloaderPct">0</span>%</p>
    </div>
  </div>

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
            <li class="nav-item me-3">
              <a class="nav-link active" href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer">Youtube</a>
            </li>
            <li class="nav-item d-flex align-items-center">
              <a href="<?= base_url($user->role . '/dashboard/perfil') ?>" class="d-flex align-items-center text-decoration-none">
                <img src="<?= esc($userAvatarUrl) ?>" alt="User" class="nav-avatar me-2" onerror="this.onerror=null;this.src='<?= esc($defaultAvatarUrl) ?>';">
                <span class="text-white fw-semibold text-nowrap"><?= $user->username ?></span>
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item me-3">
              <a class="nav-link active" href="<?= base_url('/') ?>#cursos">Cursos</a>
            </li>
            <li class="nav-item me-3">
              <a class="nav-link active" href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer">Youtube</a>
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
            <p class="title fs-1 fw-bold text-white">Curso de <?= esc($course->title_course) ?></p>
          </div>
          <div class="course-image d-flex justify-content-center">
            <img src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>" class="img-fluid rounded h-auto" width="500px" alt="">
          </div>
          <div class="description text-center mt-3">
            <p class="title fs-2 fw-bold text-white"><?= $heroSubtitle ?></p>
            <p class="text-white" style="margin-top: -0.8rem;"><?= esc($course->description_course) ?></p>
            <a href="<?= base_url('/checkout/' . $course->id_course) ?>" class="title btn btn-primary py-3 px-5 fw-bold">Compre Agora</a>
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
              <p class="title fs-3 my-2 fw-bold text-white"><?= number_format($studentCount) ?></p>
              <p class="text-white">Alunos matriculados</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-clock text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white"><?= number_format($courseHours ?? 0, 1, ',', '.') ?>h</p>
              <p class="text-white">Horas do curso</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-book text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white"><?= $lessonCount ?> </p>
              <p class="text-white">Total de aulas</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="info-box text-center">
              <div class="icon">
                <i class="fa-solid fa-diagram-project text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="title fs-3 my-2 fw-bold text-white"><?= $projectCount ?> </p>
              <p class="text-white">Projetos</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="video" class="bg-blue">
    <div class="container py-lg-5 py-0">
      <div class="content py-lg-4 py-0">
        <p class="title fs-2 fw-bold text-center text-white">Vídeo de visão geral do curso</p>

        <div class="video-area mx-auto mt-lg-3 mt-0">
          <?php if ($overviewVideoId): ?>
            <iframe id="vimeoPlayerOverview"
              title="Vimeo player"
              class="rounded mt-lg-3 mt-0"
              src="https://player.vimeo.com/video/<?= esc($overviewVideoId) ?>?badge=0&autopause=0&player_id=<?= esc($overviewPlayerId) ?>&app_id=58479&title=0&byline=0&portrait=0&autoplay=0"
              width="100%"
              height="100%"
              frameborder="0"
              allow="autoplay; fullscreen; picture-in-picture"
              allowfullscreen
              referrerpolicy="no-referrer"
              loading="lazy"
              sandbox="allow-same-origin allow-scripts allow-presentation"
              oncontextmenu="return false"></iframe>
          <?php else: ?>
            <div class="video-fallback rounded mt-lg-3 mt-0 d-flex align-items-center justify-content-center text-white text-center px-4" style="background:#111827;">
              <div>
                <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                <p class="mb-1 fw-semibold">Link de vídeo inválido.</p>
                <small class="text-light opacity-75">Use um link do Vimeo suportado.</small>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
  </section>

  <section id="features" class="bg-darkblue py-5">
    <div class="container">
      <div class="content">
        <div class="row row-cols-1 row-cols-md-4 g-4 align-items-stretch">
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center d-flex flex-direction-col justify-content-center align-items center">
              <div class="icon">
                <i class="fa-solid fa-photo-film text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4"><?= $lessonCount ?> aulas sob demanda</p>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center d-flex flex-direction-col justify-content-center align-items center">
              <div class="icon">
                <i class="fa-solid fa-download text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4"><?= $projectCount ?> recursos e arquivos para download</p>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center d-flex flex-direction-col justify-content-center align-items center">
              <div class="icon">
                <i class="fa-solid fa-calendar-days text-primary" style="font-size: 3rem;"></i>
              </div>
              <p class="text-white mt-4"><?= $moduleCount ?> módulos com acesso vitalício</p>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 bg-transparent border p-4 text-center d-flex flex-direction-col justify-content-center align-items center">
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
            <h2 class="title fw-bold text-primary mb-4 text-center">O que você aprenderá</h2>
            <ul class="list-unstyled feature-text">
              <?= $learningHtml ?>
            </ul>
          </div>
        </div>

        <!-- Card de compra -->
        <div class="col-lg-4">
          <div class="bg-white shadow rounded-3 p-4 text-center purchase-box-sticky">
            <h4 class="title fw-bold text-primary mb-3"><?= esc($course->title_course) ?></h4>
            <span class="title fw-bold display-6 mb-2"><?= number_format(esc($course->price_course), 2, ",", ".") ?></span><sub class="fw-bold">MZN</sub>
            <p class="text-muted mb-4">Compra única</p>
            <a href="<?= base_url('/checkout/' . $course->id_course) ?>" class="title btn btn-primary btn-lg fw-bold px-4">Compre Agora</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="description" class="py-5 text-white bg-darkblue">
    <div class="container text-center">
      <h2 class="title fw-bold mb-4">Descrição do Curso</h2>
      <?= $descriptionHtml ?>
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
      <p class="preview-helper">
        <?php if ($publicPreviewLessonCount > 0): ?>
          Aulas com cadeado aberto podem ser assistidas antes da compra.
        <?php else: ?>
          O currículo completo está listado abaixo. As aulas com prévia gratuita aparecerão com cadeado aberto.
        <?php endif; ?>
      </p>
      <div class="accordion modules-accordion mx-auto" id="excelAccordion">

        <?php foreach ($modules as $key => $module): ?>
          <div class="accordion-item mb-3 bg-white border-0 shadow-sm p-2">
            <p class="accordion-header fs-sm">
              <button class="title accordion-button bg-white fw-semibold" type="button" data-bs-toggle="collapse"
                data-bs-target="#mod<?= $module->id_module ?>">
                <?= 'Modulo ' . ($key + 1) . ': ' . esc($module->title_module) ?>
              </button>
            </p>
            <div id="mod<?= $module->id_module ?>" class="accordion-collapse collapse" data-bs-parent="#excelAccordion">
              <div class="accordion-body bg-white">
                <?php if (!empty($module->lessons)): ?>
                  <ul class="list-unstyled mb-0">
                    <?php foreach ($module->lessons as $lessonIndex => $lesson): ?>
                      <?php
                      $lessonType = trim((string) ($lesson->type_lesson ?? 'video'));
                      $lessonIconClass = match ($lessonType) {
                        'quiz' => 'bi-patch-question-fill',
                        'exercise' => 'bi-journal-check',
                        'text' => 'bi-file-earmark-text-fill',
                        default => 'bi-play-circle-fill',
                      };
                      $isPreviewLesson = !empty($lesson->is_public_preview);
                      $lessonDuration = (int) ($lesson->duration_lesson ?? 0);
                      $lessonMeta = $isPreviewLesson ? 'Previa gratuita' : 'Conteudo bloqueado';
                      if ($lessonDuration > 0) {
                        $lessonMeta .= ' - ' . $lessonDuration . ' min';
                      }
                      $previewSrc = '';
                      if ($isPreviewLesson && !empty($lesson->preview_video_id)) {
                        $previewSrc = 'https://player.vimeo.com/video/' . rawurlencode((string) $lesson->preview_video_id)
                          . '?badge=0&autopause=0&player_id=' . (int) ($lesson->id_lesson ?? 0)
                          . '&app_id=58479&title=0&byline=0&portrait=0&autoplay=1';
                      }
                      ?>
                      <li>
                        <?php if ($isPreviewLesson && $previewSrc !== ''): ?>
                          <button type="button"
                            class="module-lesson-item module-lesson-preview-button my-2"
                            data-bs-toggle="modal"
                            data-bs-target="#lessonPreviewModal"
                            data-preview-title="<?= esc($lesson->title_lesson ?? 'Aula sem titulo', 'attr') ?>"
                            data-preview-src="<?= esc($previewSrc, 'attr') ?>">
                            <span class="lesson-main">
                              <i class="bi <?= esc($lessonIconClass) ?> lesson-icon"></i>
                              <span class="lesson-copy">
                                <span class="lesson-title"><?= esc($lesson->title_lesson ?? 'Aula sem titulo') ?></span>
                                <span class="lesson-meta"><?= esc($lessonMeta) ?></span>
                              </span>
                            </span>
                            <span class="lesson-status lesson-status-open">
                              <i class="bi bi-unlock-fill"></i>
                              <span class="lesson-status-text">Assistir</span>
                            </span>
                          </button>
                        <?php else: ?>
                          <a href="<?= base_url('/checkout/' . (int) $course->id_course) ?>"
                            class="module-lesson-item module-lesson-link module-lesson-locked my-2">
                            <span class="lesson-main">
                              <i class="bi <?= esc($lessonIconClass) ?> lesson-icon"></i>
                              <span class="lesson-copy">
                                <span class="lesson-title"><?= esc($lesson->title_lesson ?? 'Aula sem titulo') ?></span>
                                <span class="lesson-meta"><?= esc($lessonMeta) ?></span>
                              </span>
                            </span>
                            <span class="lesson-status lesson-status-locked">
                              <i class="bi bi-lock-fill"></i>
                              <span class="lesson-status-text">Comprar acesso</span>
                            </span>
                          </a>
                        <?php endif; ?>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php else: ?>
                  <p class="mb-0 text-dark">
                    <?= esc($module->description_module ?: 'Sem aulas adicionadas neste modulo.') ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach ?>

      </div>
    </div>
  </section>

  <div class="modal fade" id="lessonPreviewModal" tabindex="-1" aria-labelledby="lessonPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold" id="lessonPreviewModalLabel">Pre-visualizacao da aula</h5>
            <small class="text-muted" id="lessonPreviewModalSubtitle">Assista uma aula liberada antes de comprar.</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="lesson-preview-player">
            <iframe id="lessonPreviewFrame"
              src=""
              title="Pre-visualizacao da aula"
              allow="autoplay; fullscreen; picture-in-picture"
              allowfullscreen
              referrerpolicy="no-referrer"
              loading="lazy"
              sandbox="allow-same-origin allow-scripts allow-presentation"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section id="projects" class="text-center text-white">
    <div class="bg-darkblue pt-5 pb-2">
      <h2 class="title fw-bold mb-2">Projetos de Curso</h2>
      <p class="fs-5 mb-5">Vamos dar uma olhada em alguns dos projetos práticos desenvolvidos durante o curso
      </p>
    </div>

    <div class="py-5" style="background: #eee;">
      <div class="container pb-3">
        <div class="row g-4">

          <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $key => $project): ?>
              <div class="col-md-4">
                <div class="bg-white text-dark px-4 py-4 rounded h-100">
                  <img src="<?= base_url('assets/img/' . $project->img_project) ?>" alt="<?= esc($project->title_project) ?>" class="img-fluid rounded mb-3">
                  <h5 class="fw-bold fs-4"><?= esc($project->title_project) ?></h5>
                  <p class="mb-0 text-muted">
                    <?= esc($project->description_project) ?>
                  </p>
                </div>
              </div>
            <?php endforeach ?>
          <?php else: ?>
            <div class="col-md-4 mx-auto">
              <div class="bg-white text-dark px-4 py-4 rounded h-100">
                <h5 class="fw-bold fs-4">Sem projetos</h5>
                <p class="mb-0 text-muted">
                  Nao foram adicionados projectos a este curso.
                </p>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </section>

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

  <script>
    const lessonPreviewModalEl = document.getElementById('lessonPreviewModal');
    const lessonPreviewFrame = document.getElementById('lessonPreviewFrame');
    const lessonPreviewModalLabel = document.getElementById('lessonPreviewModalLabel');

    if (lessonPreviewModalEl && lessonPreviewFrame && lessonPreviewModalLabel) {
      lessonPreviewModalEl.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        const previewTitle = trigger?.getAttribute('data-preview-title') || 'Pre-visualizacao da aula';
        const previewSrc = trigger?.getAttribute('data-preview-src') || '';

        lessonPreviewModalLabel.textContent = previewTitle;
        lessonPreviewFrame.src = previewSrc;
      });

      lessonPreviewModalEl.addEventListener('hidden.bs.modal', () => {
        lessonPreviewFrame.src = '';
      });
    }

  </script>

  <script>
    /**
     * PRELOADER com percentagem em:
     *  - #preloaderFill   (largura da barra)
     *  - #preloaderPct    (texto 0–100)
     *
     * Mantém:
     *  - MIN_PRELOAD_TIME (mínimo visível)
     *  - Esconde após window.load respeitando o mínimo
     *  - Fallback de segurança (MIN + 1000)
     */
    function initPreloader() {
      const MIN_PRELOAD_TIME = 2000; // 2s mínimo
      const startTime = Date.now();

      const preloader = document.getElementById('preloader');
      const fillEl = document.getElementById('preloaderFill');
      const pctEl = document.getElementById('preloaderPct');
      if (!preloader || !fillEl || !pctEl) return;

      // (opcional) bloquear rolagem enquanto o preloader está visível
      const lockScroll = () => {
        document.documentElement.style.overflow = 'hidden';
      };
      const unlockScroll = () => {
        document.documentElement.style.overflow = '';
      };
      lockScroll();

      // ---- Medição de progresso (realista) ----
      const WEIGHTS = {
        dom: 20,
        fonts: 10,
        images: 60,
        load: 10
      };
      let target = 0; // alvo calculado pelos eventos
      let current = 0; // valor exibido (animação)
      let rafId = 0;
      let doneFlag = false;

      const clamp = () => {
        if (target < 0) target = 0;
        if (target > 100) target = 100;
      };
      const render = () => {
        const pct = Math.round(current);
        fillEl.style.width = pct + '%';
        pctEl.textContent = pct;
      };
      const tick = () => {
        // easing suave em direção ao alvo
        current += (target - current) * 0.12;
        render();
        if (!doneFlag) rafId = requestAnimationFrame(tick);
      };

      // 1) DOM pronto
      const bumpDom = () => {
        target += WEIGHTS.dom;
        clamp();
      };
      if (document.readyState === 'interactive' || document.readyState === 'complete') bumpDom();
      else document.addEventListener('DOMContentLoaded', bumpDom, {
        once: true
      });

      // 2) Fontes
      if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => {
          target += WEIGHTS.fonts;
          clamp();
        }).catch(() => {
          target += Math.floor(WEIGHTS.fonts * 0.6);
          clamp();
        });
      } else {
        target += Math.floor(WEIGHTS.fonts * 0.6);
        clamp();
      }

      // 3) Imagens (<img> do DOM)
      const imgs = Array.from(document.images || []);
      const total = imgs.length;
      let loaded = 0;
      const onImgDone = () => {
        loaded++;
        const frac = total ? loaded / total : 1;
        const imgProgress = WEIGHTS.images * frac;
        const base = Math.min(target, WEIGHTS.dom + WEIGHTS.fonts);
        target = base + imgProgress;
        clamp();
      };
      if (total === 0) {
        target += WEIGHTS.images;
        clamp();
      } else {
        imgs.forEach(img => {
          if (img.complete) onImgDone();
          else {
            img.addEventListener('load', onImgDone, {
              once: true
            });
            img.addEventListener('error', onImgDone, {
              once: true
            });
          }
        });
      }

      // 4) load = fecha a conta (vamos a 100, mas respeitando o mínimo)
      function hidePreloader() {
        if (doneFlag) return;
        doneFlag = true;

        target = 100;
        current = 100;
        render(); // garante 100% visual

        preloader.style.opacity = '0';
        preloader.addEventListener('transitionend', () => {
          preloader.style.display = 'none';
          unlockScroll();
        }, {
          once: true
        });

        if (rafId) cancelAnimationFrame(rafId);
      }

      // fallback: força esconder após MIN + 1000 (se algo travar)
      const forceHideTimeout = setTimeout(hidePreloader, MIN_PRELOAD_TIME + 1000);

      window.addEventListener('load', () => {
        // soma o peso do load para o indicador
        target += WEIGHTS.load;
        clamp();

        const elapsed = Date.now() - startTime;
        const remainingTime = Math.max(0, MIN_PRELOAD_TIME - elapsed);

        clearTimeout(forceHideTimeout);
        setTimeout(hidePreloader, remainingTime);
      }, {
        once: true
      });

      // inicia a animação do indicador
      rafId = requestAnimationFrame(tick);
    }

    // iniciar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initPreloader, {
        once: true
      });
    } else {
      initPreloader();
    }
  </script>
</body>

</html>
