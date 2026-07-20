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

$accentColor = trim((string) ($course->color_course ?? '')) ?: '#0d6efd';

if (!function_exists('mechHexToRgba')) {
    function mechHexToRgba(string $hex, float $alpha = 1): string
    {
        $hex = ltrim(trim($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
            return "rgba(26, 95, 122, {$alpha})";
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba({$r}, {$g}, {$b}, {$alpha})";
    }
}

$accentSoft   = mechHexToRgba($accentColor, 0.16);
$accentBorder = mechHexToRgba($accentColor, 0.38);
$accentGlow   = mechHexToRgba($accentColor, 0.28);

$learnPreviewItems = array_slice($normalizedLearningItems, 0, 6);
$learnMoreCount = max(0, count($normalizedLearningItems) - count($learnPreviewItems));
?>

<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($course->title_course) ?> · Mechanical Academy</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></noscript>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" type="image/x-icon">

  <style>
    :root {
      --ink: #f5f7fa;
      --ink-soft: rgba(245, 247, 250, 0.62);
      --page-bg: #050505;
      --surface: #141414;
      --line: rgba(255, 255, 255, 0.09);
      --accent: <?= esc($accentColor) ?>;
      --accent-soft: <?= esc($accentSoft) ?>;
      --accent-border: <?= esc($accentBorder) ?>;
      --accent-glow: <?= esc($accentGlow) ?>;
    }

    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Sora', sans-serif;
      color: var(--ink);
      background:
        radial-gradient(1100px 480px at 88% 0%, var(--accent-glow) 0%, transparent 55%),
        radial-gradient(900px 420px at 4% 30%, rgba(13, 110, 253, 0.06) 0%, transparent 55%),
        var(--page-bg);
      -webkit-font-smoothing: antialiased;
    }

    a {
      color: inherit;
    }

    .container-mech {
      width: 100%;
      max-width: 1140px;
      margin: 0 auto;
      padding: 0 1.5rem;
    }

    /* ---------- Nav ---------- */
    .site-nav {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: rgba(18, 21, 26, 0.82);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      transition: background 0.25s ease, box-shadow 0.25s ease;
    }

    .site-nav.is-scrolled {
      background: rgba(18, 21, 26, 0.94);
      box-shadow: 0 8px 28px -18px rgba(0, 0, 0, 0.45);
    }

    .site-nav__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 0.9rem 0;
    }

    .site-nav__brand {
      display: flex;
      align-items: center;
      text-decoration: none;
    }

    .site-nav__brand img {
      height: 42px;
      width: auto;
      display: block;
    }

    .site-nav__links {
      display: flex;
      align-items: center;
      gap: 1.6rem;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .site-nav__links a {
      color: rgba(255, 255, 255, 0.82);
      text-decoration: none;
      font-size: 0.92rem;
      font-weight: 500;
      transition: color 0.15s ease;
    }

    .site-nav__links a:hover {
      color: #fff;
    }

    .site-nav__cta {
      color: #fff !important;
      border: 1px solid rgba(255, 255, 255, 0.28);
      border-radius: 999px;
      padding: 0.5rem 1.15rem !important;
    }

    .site-nav__cta:hover {
      border-color: rgba(255, 255, 255, 0.6);
    }

    .nav-avatar {
      width: 32px;
      height: 32px;
      min-width: 32px;
      border-radius: 50%;
      object-fit: cover;
      object-position: center;
      display: block;
      background: #1c2028;
      border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .site-nav__toggle {
      display: none;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 8px;
      color: #fff;
      padding: 0.4rem 0.6rem;
      font-size: 1.05rem;
    }

    /* ---------- Buttons ---------- */
    .btn-mech {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      border-radius: 999px;
      padding: 0.85rem 1.75rem;
      font-weight: 600;
      font-size: 0.98rem;
      text-decoration: none;
      border: 1px solid transparent;
      transition: transform 0.16s ease, box-shadow 0.16s ease, background-color 0.16s ease, border-color 0.16s ease, color 0.16s ease;
      cursor: pointer;
      line-height: 1.2;
    }

    .btn-mech-primary {
      background: var(--accent);
      color: #fff;
      box-shadow: 0 8px 20px -10px rgba(18, 21, 26, 0.28);
    }

    .btn-mech-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 24px -10px rgba(18, 21, 26, 0.32);
      color: #fff;
    }

    .btn-mech-outline-invert {
      background: transparent;
      color: #fff;
      border-color: rgba(255, 255, 255, 0.4);
    }

    .btn-mech-outline-invert:hover {
      border-color: rgba(255, 255, 255, 0.85);
      background: rgba(255, 255, 255, 0.06);
      color: #fff;
    }

    .btn-mech-outline {
      background: transparent;
      color: #fff;
      border-color: rgba(255, 255, 255, 0.22);
    }

    .btn-mech-outline:hover {
      border-color: rgba(255, 255, 255, 0.55);
      background: rgba(255, 255, 255, 0.06);
      color: #fff;
    }

    .btn-mech-block {
      width: 100%;
    }

    .btn-mech-sm {
      padding: 0.65rem 1.2rem;
      font-size: 0.88rem;
    }

    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      min-height: 92vh;
      display: flex;
      align-items: center;
      overflow: hidden;
      color: #fff;
    }

    .hero__media {
      position: absolute;
      inset: 0;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      transform: scale(1.02);
    }

    .hero__gradient {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(880px 620px at 82% 8%, var(--accent-glow) 0%, transparent 58%),
        linear-gradient(180deg, rgba(18, 21, 26, 0.55) 0%, rgba(18, 21, 26, 0.78) 55%, rgba(18, 21, 26, 0.96) 100%);
    }

    .hero__inner {
      position: relative;
      z-index: 2;
      padding: 7.5rem 0 4.5rem;
      width: 100%;
    }

    .hero__content {
      max-width: 720px;
    }

    .hero__badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      border: 1px solid var(--accent-border);
      background: rgba(255, 255, 255, 0.04);
      color: rgba(255, 255, 255, 0.92);
      border-radius: 999px;
      padding: 0.4rem 0.9rem 0.4rem 0.7rem;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.02em;
      margin-bottom: 1.75rem;
    }

    .hero__badge .dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: var(--accent);
      display: inline-block;
    }

    .hero__kicker {
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.55);
      margin-bottom: 1.1rem;
    }

    .hero__title {
      font-weight: 700;
      font-size: clamp(2.1rem, 4.6vw, 3.4rem);
      line-height: 1.12;
      letter-spacing: -0.01em;
      margin-bottom: 1.15rem;
    }

    .hero__subtitle {
      font-size: clamp(1rem, 1.6vw, 1.2rem);
      line-height: 1.6;
      color: rgba(255, 255, 255, 0.78);
      font-weight: 400;
      max-width: 560px;
      margin-bottom: 2.2rem;
    }

    .hero__cta {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 0.9rem;
      margin-bottom: 1.9rem;
    }

    .hero__whatsapp {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: rgba(255, 255, 255, 0.68);
      text-decoration: none;
      font-size: 0.92rem;
      font-weight: 500;
      border-bottom: 1px solid transparent;
      transition: color 0.15s ease, border-color 0.15s ease;
    }

    .hero__whatsapp:hover {
      color: #fff;
      border-bottom-color: rgba(255, 255, 255, 0.5);
    }

    .hero__meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.55rem 1.1rem;
      color: rgba(255, 255, 255, 0.78);
      font-size: 0.92rem;
      font-weight: 500;
      padding-top: 1.6rem;
      border-top: 1px solid rgba(255, 255, 255, 0.14);
    }

    .hero__meta span.item {
      display: inline-flex;
      align-items: center;
      gap: 0.45rem;
      white-space: nowrap;
    }

    .hero__meta .sep {
      color: rgba(255, 255, 255, 0.3);
    }

    .hero__meta i {
      color: var(--accent);
      font-size: 1rem;
    }

    /* ---------- Sections ---------- */
    .section {
      padding: 5.5rem 0;
    }

    .section-tight {
      padding: 3.5rem 0;
    }

    .section-heading {
      max-width: 620px;
      margin-bottom: 3rem;
    }

    .section-heading.centered {
      margin-left: auto;
      margin-right: auto;
      text-align: center;
    }

    .kicker {
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 0.85rem;
    }

    .section-heading h2 {
      font-weight: 700;
      font-size: clamp(1.6rem, 3vw, 2.15rem);
      letter-spacing: -0.01em;
      color: var(--ink);
      margin-bottom: 0;
    }

    .section-lead {
      color: var(--ink-soft);
      font-size: 1.02rem;
      line-height: 1.75;
      max-width: 640px;
      margin-bottom: 3rem;
    }

    .section-lead p {
      margin-bottom: 0.9rem;
    }

    .section-lead p:last-child {
      margin-bottom: 0;
    }

    /* ---------- Learning checklist ---------- */
    .learn-grid {
      list-style: none;
      margin: 0;
      padding: 0;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem 2.5rem;
    }

    .learn-grid li {
      display: flex;
      align-items: flex-start;
      gap: 0.85rem;
      padding: 1.1rem 0;
      border-bottom: 1px solid var(--line);
      line-height: 1.5;
      color: var(--ink);
      font-size: 0.98rem;
    }

    .learn-grid li i {
      flex: 0 0 auto;
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: var(--accent-soft);
      color: var(--accent);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      margin-top: 0.05rem;
    }

    .learn-more-note {
      margin-top: 1.5rem;
      font-size: 0.92rem;
      color: var(--ink-soft);
    }

    /* ---------- Overview video ---------- */
    .video-frame {
      width: 100%;
      max-width: 860px;
      aspect-ratio: 16 / 9;
      border-radius: 16px;
      overflow: hidden;
      background: #05070b;
      box-shadow: 0 30px 70px -30px rgba(18, 21, 26, 0.35);
    }

    .video-frame iframe,
    .video-frame .video-fallback {
      width: 100%;
      height: 100%;
      display: block;
      border: 0;
    }

    /* ---------- Curriculum layout ---------- */
    .curriculum-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      gap: 2.5rem;
      align-items: stretch;
    }

    @media (min-width: 992px) {
      .curriculum-layout {
        grid-template-columns: minmax(0, 1fr) 340px;
      }

      .curriculum-layout > aside.purchase-aside {
        align-self: stretch;
        height: 100%;
        position: relative;
      }
    }

    .preview-helper {
      font-size: 0.92rem;
      color: var(--ink-soft);
      margin-bottom: 1.5rem;
    }

    .preview-helper i {
      color: var(--accent);
      margin-right: 0.4rem;
    }

    .accordion-mech .accordion-item {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 14px !important;
      overflow: hidden;
      margin-bottom: 0.9rem;
    }

    .accordion-mech .accordion-item:last-child {
      margin-bottom: 0;
    }

    .accordion-mech .accordion-button {
      background: var(--surface);
      font-family: 'Sora', sans-serif;
      font-weight: 600;
      font-size: 1.02rem;
      color: var(--ink);
      box-shadow: none;
      padding: 1.15rem 1.35rem;
    }

    .accordion-mech .accordion-button::after {
      background-size: 1.1rem;
      filter: invert(1) brightness(1.4);
    }

    .accordion-mech .accordion-button:not(.collapsed) {
      color: var(--ink);
      background: var(--surface);
    }

    .accordion-mech .accordion-button:focus {
      box-shadow: none;
      border-color: var(--line);
    }

    .accordion-mech .module-index {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      min-width: 28px;
      border-radius: 50%;
      background: var(--accent-soft);
      color: var(--accent);
      font-size: 0.82rem;
      font-weight: 700;
      margin-right: 0.9rem;
    }

    .accordion-mech .accordion-body {
      padding: 0 1.1rem 1.1rem;
    }

    .module-lesson-item {
      background: transparent;
      color: var(--ink);
      padding: 0.85rem 0.6rem;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      line-height: 1.35;
      font-size: 0.94rem;
      border: 0;
      width: 100%;
      text-align: left;
      text-decoration: none;
      transition: background-color 0.16s ease;
    }

    .module-lesson-item:hover {
      background: var(--accent-soft);
    }

    .module-lesson-locked {
      opacity: 0.82;
    }

    .lesson-main {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: 0.85rem;
      flex: 1;
    }

    .lesson-icon {
      color: var(--accent);
      font-size: 1.05rem;
      flex: 0 0 auto;
    }

    .lesson-copy {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 0.1rem;
    }

    .lesson-title {
      display: block;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .lesson-meta {
      color: var(--ink-soft);
      font-size: 0.76rem;
      font-weight: 500;
    }

    .lesson-status {
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.82rem;
      font-weight: 600;
    }

    .lesson-status-open {
      color: #15803d;
    }

    .lesson-status-locked {
      color: var(--ink-soft);
    }

    .module-lessons {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 0.15rem;
    }

    /* ---------- Purchase card ---------- */
    .purchase-card {
      position: -webkit-sticky;
      position: sticky;
      top: 5.5rem;
      z-index: 30;
      background: #161616;
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 1.75rem;
      box-shadow: 0 24px 60px -30px rgba(0, 0, 0, 0.65);
    }

    @media (max-width: 991.98px) {
      .purchase-card {
        position: static;
        top: auto;
        z-index: auto;
      }
    }

    .purchase-card__title {
      font-weight: 700;
      font-size: 1.08rem;
      margin-bottom: 1.1rem;
      line-height: 1.35;
      color: #fff;
    }

    .purchase-card__price-row {
      display: flex;
      align-items: baseline;
      gap: 0.6rem;
      flex-wrap: wrap;
      margin-bottom: 0.3rem;
    }

    .purchase-card__price {
      font-weight: 700;
      font-size: 2rem;
      letter-spacing: -0.01em;
      color: #fff;
    }

    .purchase-card__price-unit {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--ink-soft);
    }

    .purchase-card__list-price {
      text-decoration: line-through;
      color: var(--ink-soft);
      font-size: 0.95rem;
    }

    .purchase-card__badge {
      display: inline-flex;
      background: rgba(220, 53, 69, 0.18);
      color: #ff8a95;
      font-size: 0.78rem;
      font-weight: 700;
      padding: 0.15rem 0.55rem;
      border-radius: 999px;
    }

    .purchase-card__promo-note {
      color: #ff8a95;
      font-size: 0.85rem;
      font-weight: 600;
      margin-bottom: 1.1rem;
    }

    .purchase-card__single {
      color: var(--ink-soft);
      font-size: 0.84rem;
      margin: 0.9rem 0 1.1rem;
      text-align: center;
    }

    .purchase-card__actions {
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    .purchase-card__facts {
      list-style: none;
      margin: 1.35rem 0 0;
      padding: 1.2rem 0 0;
      border-top: 1px solid var(--line);
      display: flex;
      flex-direction: column;
      gap: 0.65rem;
    }

    .purchase-card__facts li {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      font-size: 0.86rem;
      color: var(--ink-soft);
    }

    .purchase-card__facts i {
      color: var(--accent);
      width: 18px;
      text-align: center;
    }

    .purchase-card__rating {
      color: var(--ink-soft);
      font-size: 0.85rem;
      margin-top: 0.9rem;
    }

    .purchase-card__rating .stars {
      color: #fbbf24;
      margin-right: 0.35rem;
    }

    /* ---------- Mobile sticky CTA ---------- */
    .mobile-sticky-cta {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 900;
      display: none;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      background: rgba(10, 10, 10, 0.94);
      backdrop-filter: blur(12px);
      border-top: 1px solid var(--line);
      padding: 0.75rem 1rem;
      box-shadow: 0 -12px 30px -20px rgba(0, 0, 0, 0.65);
    }

    .mobile-sticky-cta .price {
      font-weight: 700;
      font-size: 1.05rem;
      color: var(--ink);
      line-height: 1.15;
    }

    .mobile-sticky-cta .price small {
      display: block;
      font-weight: 500;
      font-size: 0.72rem;
      color: var(--ink-soft);
      text-decoration: none;
    }

    @media (max-width: 991.98px) {
      .mobile-sticky-cta {
        display: flex;
      }

      body {
        padding-bottom: 76px;
      }
    }

    /* ---------- Preview modal ---------- */
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

    /* ---------- Footer ---------- */
    .site-footer {
      background: #000;
      color: rgba(255, 255, 255, 0.65);
      padding: 2.75rem 0;
      border-top: 1px solid var(--line);
    }

    .site-footer__row {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 1.25rem;
    }

    .site-footer img {
      height: 40px;
      width: auto;
    }

    .site-footer small {
      font-size: 0.82rem;
    }

    .site-footer__social {
      display: flex;
      gap: 1.1rem;
    }

    .site-footer__social a {
      color: rgba(255, 255, 255, 0.65);
      font-size: 1.05rem;
      text-decoration: none;
      transition: color 0.15s ease;
    }

    .site-footer__social a:hover {
      color: #fff;
    }

    /* ---------- Motion (Apple-like restraint) ---------- */
    :root {
      --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
      --ease-spring: cubic-bezier(0.34, 1.3, 0.64, 1);
    }

    .btn-mech:active {
      transform: scale(0.97);
    }

    .hero__media {
      animation: heroKen 18s ease-out forwards;
    }

    .hero-anim > * {
      opacity: 0;
      transform: translateY(20px);
      animation: riseIn 0.8s var(--ease-out) forwards;
    }

    .hero-anim > *:nth-child(1) { animation-delay: 0.06s; }
    .hero-anim > *:nth-child(2) { animation-delay: 0.14s; }
    .hero-anim > *:nth-child(3) { animation-delay: 0.22s; }
    .hero-anim > *:nth-child(4) { animation-delay: 0.3s; }
    .hero-anim > *:nth-child(5) { animation-delay: 0.38s; }
    .hero-anim > *:nth-child(6) { animation-delay: 0.46s; }
    .hero-anim > *:nth-child(7) { animation-delay: 0.54s; }

    @keyframes riseIn {
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes heroKen {
      from { transform: scale(1.04); }
      to { transform: scale(1); }
    }

    .reveal {
      opacity: 0;
      transform: translateY(24px);
      transition: opacity 0.7s var(--ease-out), transform 0.7s var(--ease-out);
      transition-delay: var(--d, 0ms);
    }

    .reveal.is-in {
      opacity: 1;
      transform: translateY(0);
    }

    .learn-grid li {
      transition: transform 0.22s var(--ease-out), background-color 0.22s ease;
    }

    .learn-grid li:hover {
      transform: translateX(4px);
    }

    .module-item,
    .accordion-item {
      transition: border-color 0.2s ease, box-shadow 0.25s ease, transform 0.25s var(--ease-spring);
    }

    .buy-bar,
    .sticky-cta {
      transition: transform 0.28s var(--ease-out), opacity 0.28s ease;
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal,
      .hero-anim > *,
      .hero__media {
        animation: none !important;
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
      }
    }

    @media (max-width: 767.98px) {
      .hero {
        min-height: 100vh;
      }

      .hero__inner {
        padding: 6.5rem 0 3rem;
      }

      .section {
        padding: 3.75rem 0;
      }

      .learn-grid {
        grid-template-columns: 1fr;
      }

      .site-nav__links {
        display: none;
      }
    }
  </style>
</head>

<body>

  <?= view('partials/promo_urgency', [
      'hasPromo' => !empty($hasPromo),
      'promoRemainingSeconds' => (int) ($promoRemainingSeconds ?? 0),
      'discountPercent' => (int) ($discountPercent ?? 0),
      'promoEndsAt' => $promoEndsAt ?? null,
      'listPrice' => $listPrice ?? null,
      'promoPrice' => $promoPrice ?? null,
      'courseTitle' => $course->title_course ?? '',
      'promoCtaHref' => site_url('checkout/' . (int) ($course->id_course ?? 0)),
      'promoCtaLabel' => 'Inscrever-me agora',
  ]) ?>

  <nav class="site-nav">
    <div class="container-mech site-nav__inner">
      <a class="site-nav__brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
      </a>
      <ul class="site-nav__links">
        <?php if ($isLoggedIn): ?>
          <li><a href="<?= base_url($user->role . '/dashboard/meus_cursos') ?>">Meus cursos</a></li>
          <li><a href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer">Youtube</a></li>
          <li>
            <a href="<?= base_url($user->role . '/dashboard/perfil') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
              <img src="<?= esc($userAvatarUrl) ?>" alt="User" class="nav-avatar" onerror="this.onerror=null;this.src='<?= esc($defaultAvatarUrl) ?>';">
              <span class="text-white fw-semibold text-nowrap"><?= esc($user->username) ?></span>
            </a>
          </li>
        <?php else: ?>
          <li><a href="<?= base_url('/') ?>#cursos">Cursos</a></li>
          <li><a href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer">Youtube</a></li>
          <li><a class="site-nav__cta" href="<?= base_url('login') ?>">Entrar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero__media" style="background-image:url('<?= base_url('assets/img/' . ($course->bg_course ?? 'bg.webp')) ?>');"></div>
    <div class="hero__gradient"></div>
    <div class="container-mech hero__inner">
      <div class="hero__content hero-anim">
        <span class="hero__badge"><span class="dot"></span> Curso mais vendido &middot; conteúdo atualizado</span>
        <p class="hero__kicker">MECHANICAL</p>
        <h1 class="hero__title">Curso de <?= esc($course->title_course) ?></h1>
        <p class="hero__subtitle"><?= $heroSubtitle ?></p>

        <div class="hero__cta">
          <a href="<?= base_url('/checkout/' . $course->id_course) ?>" class="btn-mech btn-mech-primary">
            Comprar curso <i class="bi bi-arrow-right"></i>
          </a>

          <?php if (!empty($freeLessonsCount)): ?>
            <a href="<?= site_url('courses/' . (int) $course->id_course . '/trial') ?>" class="btn-mech btn-mech-outline-invert">
              Experimentar <?= (int) $freeLessonsCount ?> aula<?= $freeLessonsCount > 1 ? 's' : '' ?> grátis
            </a>
          <?php endif; ?>
        </div>

        <?php if (!empty($whatsappUrl)): ?>
          <a href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener" class="hero__whatsapp mb-4 d-inline-flex">
            <i class="bi bi-whatsapp"></i> Falar com a equipa comercial no WhatsApp
          </a>
        <?php endif; ?>

        <div class="hero__meta">
          <span class="item"><i class="bi bi-clock"></i> <?= esc($hoursLabel ?? (number_format($courseHours ?? 0, 1, ',', '.') . 'h')) ?> de conteúdo</span>
          <span class="sep">&middot;</span>
          <span class="item"><i class="bi bi-collection"></i> <?= (int) $moduleCount ?> módulos</span>
          <span class="sep">&middot;</span>
          <span class="item"><i class="bi bi-play-circle"></i> <?= (int) $lessonCount ?> aulas</span>
          <?php if (!empty($studentCount)): ?>
            <span class="sep">&middot;</span>
            <span class="item"><i class="bi bi-people"></i> <?= number_format($studentCount) ?> alunos</span>
          <?php endif; ?>
          <?php if (!empty($ratingSummary['total'])): ?>
            <span class="sep">&middot;</span>
            <span class="item"><i class="bi bi-star-fill"></i> <?= number_format((float) $ratingSummary['average'], 1, ',', '.') ?> (<?= (int) $ratingSummary['total'] ?>)</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- O QUE VAI APRENDER -->
  <section id="learn" class="section">
    <div class="container-mech">
      <div class="section-heading reveal">
        <p class="kicker">O que você vai aprender</p>
        <h2>Habilidades práticas, do início ao avançado</h2>
      </div>

      <?php if ($descriptionHtml !== ''): ?>
        <div class="section-lead reveal" style="--d:80ms"><?= $descriptionHtml ?></div>
      <?php endif; ?>

      <ul class="learn-grid reveal" style="--d:140ms">
        <?php foreach ($learnPreviewItems as $itemText): ?>
          <li><i class="bi bi-check-lg"></i><span><?= esc($itemText) ?></span></li>
        <?php endforeach; ?>
      </ul>

      <?php if ($learnMoreCount > 0): ?>
        <p class="learn-more-note">+ <?= $learnMoreCount ?> tópico<?= $learnMoreCount > 1 ? 's' : '' ?> adicional<?= $learnMoreCount > 1 ? 'ais' : '' ?> no currículo completo abaixo.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- VIDEO DE VISÃO GERAL -->
  <?php if ($overviewVideoId): ?>
    <section id="overview" class="section section-tight" style="background:var(--surface); border-top:1px solid var(--line); border-bottom:1px solid var(--line);">
      <div class="container-mech text-center">
        <div class="section-heading centered reveal">
          <p class="kicker">Aula de apresentação</p>
          <h2>Conheça o curso antes de comprar</h2>
        </div>
        <div class="video-frame mx-auto reveal" style="--d:100ms">
          <iframe id="vimeoPlayerOverview"
            title="Vimeo player"
            src="https://player.vimeo.com/video/<?= esc($overviewVideoId) ?>?badge=0&autopause=0&player_id=<?= esc($overviewPlayerId) ?>&app_id=58479&title=0&byline=0&portrait=0&autoplay=0"
            frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen
            referrerpolicy="no-referrer"
            loading="lazy"
            sandbox="allow-same-origin allow-scripts allow-presentation"
            oncontextmenu="return false"></iframe>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- CONTEÚDO DO CURSO -->
  <section id="curriculum" class="section">
    <div class="container-mech">
      <div class="section-heading reveal">
        <p class="kicker">Currículo completo</p>
        <h2>Conteúdo do curso</h2>
      </div>

      <div class="curriculum-layout">
        <div class="reveal" style="--d:80ms">
          <p class="preview-helper">
            <?php if ($publicPreviewLessonCount > 0): ?>
              <i class="bi bi-unlock"></i>Aulas com cadeado aberto podem ser assistidas antes da compra.
            <?php else: ?>
              <i class="bi bi-info-circle"></i>O currículo completo está listado abaixo.
            <?php endif; ?>
          </p>

          <div class="accordion accordion-mech" id="excelAccordion">
            <?php foreach ($modules as $key => $module): ?>
              <div class="accordion-item">
                <h3 class="accordion-header">
                  <button class="accordion-button <?= $key === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mod<?= $module->id_module ?>">
                    <span class="module-index"><?= $key + 1 ?></span>
                    <?= esc($module->title_module) ?>
                  </button>
                </h3>
                <div id="mod<?= $module->id_module ?>" class="accordion-collapse collapse <?= $key === 0 ? 'show' : '' ?>" data-bs-parent="#excelAccordion">
                  <div class="accordion-body">
                    <?php if (!empty($module->lessons)): ?>
                      <ul class="module-lessons">
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
                                class="module-lesson-item module-lesson-preview-button"
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
                                class="module-lesson-item module-lesson-link module-lesson-locked">
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
                      <p class="mb-0" style="color:var(--ink-soft)">
                        <?= esc($module->description_module ?: 'Sem aulas adicionadas neste modulo.') ?>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <aside class="purchase-aside">
          <div class="purchase-card">
            <p class="purchase-card__title"><?= esc($course->title_course) ?></p>

            <div class="purchase-card__price-row">
              <?php if (!empty($hasPromo)): ?>
                <span class="purchase-card__list-price"><?= number_format((float) $listPrice, 2, ',', '.') ?> MZN</span>
                <span class="purchase-card__badge">-<?= (int) $discountPercent ?>%</span>
              <?php endif; ?>
            </div>
            <div class="purchase-card__price-row">
              <span class="purchase-card__price"><?= number_format((float) ($effectivePrice ?? $course->price_course), 2, ',', '.') ?></span>
              <span class="purchase-card__price-unit">MZN</span>
            </div>
            <?php if (!empty($hasPromo)): ?>
              <?php if (!empty($promoRemainingSeconds) && (int) $promoRemainingSeconds > 0): ?>
                <p class="purchase-card__promo-note">Oferta expira em <strong class="js-promo-inline-countdown" data-left="<?= (int) $promoRemainingSeconds ?>">--:--:--</strong></p>
              <?php else: ?>
                <p class="purchase-card__promo-note">Preço promocional por tempo limitado</p>
              <?php endif; ?>
            <?php endif; ?>

            <p class="purchase-card__single">Pagamento único &middot; acesso vitalício</p>

            <div class="purchase-card__actions">
              <a href="<?= site_url('checkout/' . (int) $course->id_course) ?>" class="btn-mech btn-mech-primary btn-mech-block">
                Inscrever-me agora
              </a>
              <?php if (!empty($freeLessonsCount)): ?>
                <a href="<?= site_url('courses/' . (int) $course->id_course . '/trial') ?>" class="btn-mech btn-mech-outline btn-mech-block">
                  Experimentar <?= (int) $freeLessonsCount ?> aula<?= $freeLessonsCount > 1 ? 's' : '' ?> grátis
                </a>
              <?php endif; ?>
              <?php if (!empty($whatsappUrl)): ?>
                <a href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener" class="btn-mech btn-mech-outline btn-mech-block">
                  <i class="bi bi-whatsapp"></i> Falar no WhatsApp
                </a>
              <?php endif; ?>
            </div>

            <?php if (!empty($ratingSummary['total'])): ?>
              <p class="purchase-card__rating">
                <span class="stars">★★★★★</span><?= number_format((float) $ratingSummary['average'], 1, ',', '.') ?> &middot; <?= (int) $ratingSummary['total'] ?> avaliações
              </p>
            <?php endif; ?>

            <ul class="purchase-card__facts">
              <li><i class="bi bi-infinity"></i> Acesso vitalício ao conteúdo</li>
              <li><i class="bi bi-phone"></i> Estude no computador e no celular</li>
              <li><i class="bi bi-mortarboard"></i> Certificado digital de conclusão</li>
            </ul>
          </div>
        </aside>
      </div>
    </div>
  </section>

  <!-- MOBILE STICKY CTA -->
  <div class="mobile-sticky-cta">
    <div class="price">
      <?= number_format((float) ($effectivePrice ?? $course->price_course), 2, ',', '.') ?> MZN
      <?php if (!empty($hasPromo)): ?>
        <small class="text-decoration-line-through"><?= number_format((float) $listPrice, 2, ',', '.') ?> MZN</small>
      <?php endif; ?>
    </div>
    <a href="<?= site_url('checkout/' . (int) $course->id_course) ?>" class="btn-mech btn-mech-primary btn-mech-sm">Comprar</a>
  </div>

  <!-- LESSON PREVIEW MODAL -->
  <div class="modal fade" id="lessonPreviewModal" tabindex="-1" aria-labelledby="lessonPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg" style="font-family:'Sora',sans-serif;">
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

  <!-- FOOTER -->
  <footer class="site-footer">
    <div class="container-mech site-footer__row">
      <a href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
      </a>
      <small>&copy; <?= date('Y') ?> Mechanical Academy. Todos os direitos reservados.</small>
      <div class="site-footer__social">
        <a href="#" aria-label="X"><i class="bi bi-twitter-x"></i></a>
        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer" aria-label="Youtube"><i class="bi bi-youtube"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

    (function () {
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!reduce && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-in');
              io.unobserve(entry.target);
            }
          });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
      } else {
        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-in'));
      }

      const nav = document.querySelector('.site-nav');
      if (nav) {
        const onScroll = () => nav.classList.toggle('is-scrolled', window.scrollY > 12);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
      }
    })();
  </script>
</body>

</html>
