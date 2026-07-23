<?php

$session = session();
$checkoutStats = is_array($checkoutStats ?? null) ? $checkoutStats : [];
$moduleCount = (int) ($checkoutStats['moduleCount'] ?? 0);
$lessonCount = (int) ($checkoutStats['lessonCount'] ?? 0);
$projectCount = (int) ($checkoutStats['projectCount'] ?? 0);
$totalHoursLabel = trim((string) ($checkoutStats['totalHoursLabel'] ?? '0 Horas'));
$courseTitle = trim((string) ($course->title_course ?? ''));
$courseSubtitle = trim((string) ($course->subtitle_course ?? ''));
$isCcnaCourse = preg_match('/\bccna\b/i', $courseTitle) === 1;

$normalizeCheckoutText = static function (?string $value): string {
  $text = trim((string) $value);
  if ($text === '') {
    return '';
  }

  $text = preg_replace('/<\/(p|div|li|br|h[1-6])\s*>/i', "\n", $text);
  $text = strip_tags((string) $text);
  $text = html_entity_decode((string) $text, ENT_QUOTES, 'UTF-8');
  $text = preg_replace('/\s+/u', ' ', (string) $text);

  return trim((string) $text);
};

$buildCheckoutExcerpt = static function (?string $value, int $limit = 240) use ($normalizeCheckoutText): string {
  $text = $normalizeCheckoutText($value);
  $textLength = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
  if ($text === '' || $textLength <= $limit) {
    return $text;
  }

  $excerpt = function_exists('mb_substr')
    ? mb_substr($text, 0, $limit - 3)
    : substr($text, 0, $limit - 3);

  return rtrim((string) $excerpt) . '...';
};

$learningSource = trim((string) ($course->learning_course ?? ($course->what_learn_course ?? '')));
$learningItems = [];

if ($learningSource !== '' && preg_match_all('/<li\b[^>]*>(.*?)<\/li>/is', $learningSource, $matches)) {
  foreach (($matches[1] ?? []) as $itemHtml) {
    $itemText = $normalizeCheckoutText((string) $itemHtml);
    if ($itemText !== '') {
      $learningItems[] = $itemText;
    }
  }
}

if (empty($learningItems) && $learningSource !== '') {
  $plainLearning = preg_replace('/<\/(p|div|li|br)\s*>/i', "\n", $learningSource);
  $chunks = preg_split('/(?:\r\n|\r|\n|;|\|)+/u', strip_tags((string) $plainLearning)) ?: [];

  foreach ($chunks as $chunk) {
    $itemText = trim((string) preg_replace('/^[\-\*\x{2022}\s]+/u', '', $chunk));
    $itemText = $normalizeCheckoutText($itemText);
    if ($itemText !== '') {
      $learningItems[] = $itemText;
    }
  }
}

if (empty($learningItems) && $isCcnaCourse) {
  $learningItems = [
    'Fundamentos de rede, operacao de redes IPv4 e IPv6 e leitura de topologias',
    'Configuracao basica de switches, VLANs, trunking e roteadores',
    'IP connectivity, roteamento estatico e fundamentos de OSPF',
    'IP services, NAT, DHCP, DNS, acesso remoto e troubleshooting',
    'Seguranca basica de redes, boas praticas e hardening inicial',
    'Introducao a automacao e programabilidade em redes modernas',
  ];
}

$learningItems = array_values(array_unique(array_filter($learningItems)));
$learningItems = array_slice($learningItems, 0, 4);

$summarySource = trim((string) ($course->description_course ?? ''));
if ($summarySource === '' && $courseSubtitle !== '') {
  $summarySource = $courseSubtitle;
}
if ($summarySource === '' && $isCcnaCourse) {
  $summarySource = 'Formacao focada no CCNA 200-301 para construir base solida em redes, estudar os dominios mais importantes do exame e avancar com um roteiro mais claro.';
}
$courseSummary = $buildCheckoutExcerpt($summarySource);

$featureItems = [];
if ($totalHoursLabel !== '' && $totalHoursLabel !== '0 Horas') {
  $featureItems[] = $totalHoursLabel . ' de aulas em video para estudar no seu ritmo';
}
if ($lessonCount > 0 && $moduleCount > 0) {
  $featureItems[] = $lessonCount . ' aulas organizadas em ' . $moduleCount . ' modulos';
} elseif ($lessonCount > 0) {
  $featureItems[] = $lessonCount . ' aulas com progressao do basico ao avancado';
} elseif ($moduleCount > 0) {
  $featureItems[] = $moduleCount . ' modulos com conteudo estruturado';
}
if ($projectCount > 0) {
  $featureItems[] = $projectCount . ' projeto' . ($projectCount > 1 ? 's' : '') . ' ou laboratorios para consolidar a pratica';
}
foreach ($learningItems as $learningItem) {
  $featureItems[] = $learningItem;
}
$featureItems[] = 'Acesso pela plataforma no computador e no celular';
$featureItems[] = 'Certificado digital de conclusao emitido pela plataforma';
$featureItems = array_slice(array_values(array_unique($featureItems)), 0, 7);

$metricCards = [];
if ($totalHoursLabel !== '' && $totalHoursLabel !== '0 Horas') {
  $metricCards[] = ['label' => 'Carga horaria', 'value' => $totalHoursLabel];
}
if ($moduleCount > 0) {
  $metricCards[] = ['label' => 'Modulos', 'value' => (string) $moduleCount];
}
if ($lessonCount > 0) {
  $metricCards[] = ['label' => 'Aulas', 'value' => (string) $lessonCount];
}

$valueHeading = $isCcnaCourse ? 'Conteudo alinhado ao CCNA 200-301' : 'Conteudo pensado para aplicacao pratica';
$valueText = $isCcnaCourse
  ? 'Os topicos destacados aqui seguem os pilares oficiais do CCNA: fundamentos de rede, network access, IP connectivity, IP services, seguranca e automacao. Isso deixa o valor do curso mais claro antes da compra.'
  : 'Esta pagina agora destaca descricao, estrutura e objetivos reais do curso para a decisao de compra ser baseada em informacao concreta.';

$buyerHeading = $isCcnaCourse ? 'Para quem quer entrar ou evoluir em redes' : 'Compra guiada por informacao real';
$buyerText = $isCcnaCourse
  ? 'Se voce quer construir base para suporte, infraestrutura, NOC ou certificacao, este checkout passa a mostrar exatamente o que o aluno vai estudar e praticar.'
  : 'No lugar de depoimentos genricos, o checkout mostra o que existe de fato no curso e o que o aluno vai conseguir explorar na plataforma.';

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

$listPrice = (float) ($checkoutStats['listPrice'] ?? $course->price_course);
$effectivePrice = (float) ($checkoutStats['effectivePrice'] ?? $course->price_course);
$hasPromo = (bool) ($checkoutStats['hasPromo'] ?? false);
$discountPercent = (int) ($checkoutStats['discountPercent'] ?? 0);
$promoEndsAt = $checkoutStats['promoEndsAt'] ?? null;
$promoRemainingSeconds = (int) ($checkoutStats['promoRemainingSeconds'] ?? 0);
$whatsappUrl = (string) ($checkoutStats['whatsappUrl'] ?? '#');
$freeLessonsCount = (int) ($checkoutStats['freeLessonsCount'] ?? 0);

$overviewVideoUrlRaw = trim((string) ($course->url_video_course ?? ''));
$overviewVideoEmbedSrc = null;
$overviewPlayerId = (int) ($course->id_course ?? 0);

if ($overviewVideoUrlRaw !== '') {
  if (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $overviewVideoUrlRaw, $vimeoMatch)) {
    $overviewVideoEmbedSrc = 'https://player.vimeo.com/video/' . rawurlencode((string) $vimeoMatch[1])
      . '?badge=0&autopause=0&player_id=' . $overviewPlayerId
      . '&app_id=58479&title=0&byline=0&portrait=0&autoplay=0';
  } elseif (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{6,})/', $overviewVideoUrlRaw, $youtubeMatch)) {
    $overviewVideoEmbedSrc = 'https://www.youtube.com/embed/' . rawurlencode((string) $youtubeMatch[1])
      . '?rel=0&modestbranding=1';
  }
}

$courseImageSrc = !empty($course->image_course)
  ? base_url('assets/instructor/img/courses/' . $course->image_course)
  : base_url('assets/img/logo.png');

$courseIconBg = !empty($course->icon_course)
  ? base_url('assets/img/' . $course->icon_course)
  : $courseImageSrc;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout · <?= esc($course->title_course ?? '') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
  <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></noscript>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- kept: existing checkout JS toggles the WhatsApp button icon using a Font Awesome class -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
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

    body {
      font-family: 'Sora', sans-serif;
      color: var(--ink);
      background: var(--page-bg);
      min-height: 100vh;
      position: relative;
    }

    .checkout-icon-bg {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
      overflow: hidden;
    }

    .checkout-icon-bg img {
      position: absolute;
      width: clamp(64px, 9vw, 110px);
      height: clamp(64px, 9vw, 110px);
      object-fit: contain;
      opacity: 0.22;
      filter: saturate(0.85);
    }

    .checkout-icon-bg img:nth-child(1) { top: 8%; left: 4%; transform: rotate(-18deg); }
    .checkout-icon-bg img:nth-child(2) { top: 18%; right: 6%; transform: rotate(14deg); }
    .checkout-icon-bg img:nth-child(3) { top: 42%; left: 10%; transform: rotate(8deg); }
    .checkout-icon-bg img:nth-child(4) { top: 55%; right: 12%; transform: rotate(-12deg); }
    .checkout-icon-bg img:nth-child(5) { bottom: 18%; left: 6%; transform: rotate(22deg); }
    .checkout-icon-bg img:nth-child(6) { bottom: 10%; right: 8%; transform: rotate(-8deg); }
    .checkout-icon-bg img:nth-child(7) { top: 70%; left: 42%; transform: rotate(16deg); }
    .checkout-icon-bg img:nth-child(8) { top: 32%; left: 48%; transform: rotate(-24deg); }

    .checkout-page {
      position: relative;
      z-index: 1;
    }

    a {
      color: inherit;
    }

    .container-mech {
      width: 100%;
      max-width: 1140px;
      margin: 0 auto;
      padding-left: max(1.25rem, env(safe-area-inset-left, 0px));
      padding-right: max(1.25rem, env(safe-area-inset-right, 0px));
    }

    .container-mech.topbar__inner {
      padding-left: max(1.25rem, env(safe-area-inset-left, 0px));
      padding-right: max(1.25rem, env(safe-area-inset-right, 0px));
    }

    /* ---------- Top bar ---------- */
    .topbar {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(18, 21, 26, 0.92);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .topbar__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding-top: 0.85rem;
      padding-bottom: 0.85rem;
    }

    .topbar__brand img {
      height: 26px;
      width: auto;
      display: block;
    }

    .topbar__back {
      color: rgba(255, 255, 255, 0.75);
      text-decoration: none;
      font-size: 0.88rem;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      transition: none;
    }

    .topbar__back:hover {
      color: #fff;
    }

    .topbar__secure {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.8rem;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }

    .topbar__secure i {
      color: #22c55e;
    }

    /* ---------- Page header ---------- */
    .checkout-header {
      padding: 2.75rem 0 0.5rem;
    }

    .checkout-header .kicker {
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 0.6rem;
    }

    .checkout-header h1 {
      font-weight: 700;
      font-size: clamp(1.5rem, 3vw, 2rem);
      letter-spacing: -0.01em;
      margin-bottom: 0;
      color: #fff;
    }

    /* ---------- Layout ---------- */
    .checkout-section {
      padding: 2rem 0 5rem;
    }

    .checkout-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      gap: 2.5rem;
      align-items: stretch;
    }

    .checkout-summary {
      order: 2;
    }

    .checkout-form-col {
      order: 1;
    }

    @media (min-width: 992px) {
      .checkout-grid {
        grid-template-columns: minmax(0, 1fr) 400px;
      }

      .checkout-summary {
        order: 1;
      }

      .checkout-form-col {
        order: 2;
        align-self: stretch;
        height: 100%;
      }
    }

    /* ---------- Left: course summary ---------- */
    .course-thumb {
      border-radius: 0.375rem;
      overflow: hidden;
      margin-bottom: 1.5rem;
      background: #05070b;
      box-shadow: 0 20px 45px -25px rgba(0, 0, 0, 0.55);
      aspect-ratio: 16 / 9;
    }

    .course-thumb img,
    .course-thumb iframe {
      display: block;
      width: 100%;
      height: 100%;
      border: 0;
    }

    .course-thumb img {
      object-fit: cover;
    }

    .summary-block-heading {
      font-weight: 700;
      font-size: 1rem;
      margin-bottom: 1rem;
      color: #fff;
    }

    .summary-title {
      font-weight: 700;
      font-size: 1.5rem;
      letter-spacing: -0.01em;
      margin-bottom: 0.9rem;
      color: #fff;
    }

    .summary-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem 1.1rem;
      color: var(--ink-soft);
      font-size: 0.92rem;
      font-weight: 500;
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--line);
    }

    .summary-meta .item {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .summary-meta .item-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: none;
      border: 0;
      color: var(--accent);
      font-size: 1.55rem;
      line-height: 1;
      width: auto;
      height: auto;
      padding: 0;
      border-radius: 0;
      flex-shrink: 0;
    }

    .summary-meta i {
      color: inherit;
    }

    .course-summary {
      color: var(--ink-soft);
      font-size: 1rem;
      line-height: 1.7;
      margin-bottom: 2rem;
    }

    .learn-list-checkout {
      list-style: none;
      margin: 0 0 2rem;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 0.9rem;
    }

    .learn-list-checkout li {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      font-size: 0.96rem;
      line-height: 1.5;
      color: var(--ink);
    }

    .learn-list-checkout i {
      flex: 0 0 auto;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: var(--accent-soft);
      color: var(--accent);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.72rem;
      margin-top: 0.1rem;
    }

    .free-lessons-note {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      background: var(--accent-soft);
      border: 1px solid var(--accent-border);
      color: var(--ink);
      border-radius: 0.375rem;
      padding: 0.85rem 1.1rem;
      font-size: 0.9rem;
      font-weight: 500;
      margin-bottom: 2rem;
    }

    .free-lessons-note i {
      color: var(--accent);
      font-size: 1.1rem;
    }

    .price-line {
      display: flex;
      align-items: baseline;
      gap: 0.7rem;
      flex-wrap: wrap;
      margin-bottom: 2rem;
    }

    .price-line .list-price,
    .checkout-card .list-price {
      text-decoration: line-through;
      color: #ef4444;
      font-size: 1rem;
      font-weight: 700;
    }

    .price-line .effective-price {
      font-weight: 700;
      font-size: 1.5rem;
      color: #fff;
    }

    .price-line .promo-badge,
    .checkout-card .promo-badge {
      background: rgba(239, 68, 68, 0.18);
      color: #fca5a5;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      padding: 0.2rem 0.55rem;
      border-radius: 0.375rem;
      border: 1px solid rgba(239, 68, 68, 0.35);
    }

    .trust-row {
      display: flex;
      flex-wrap: wrap;
      gap: 1.4rem;
      padding-top: 1.75rem;
      border-top: 1px solid var(--line);
      color: var(--ink-soft);
      font-size: 0.86rem;
    }

    .trust-row .item {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .trust-row i {
      color: var(--accent);
      font-size: 1.05rem;
    }

    /* ---------- Right: purchase card / form ---------- */
    .checkout-card {
      position: sticky;
      top: 5.5rem;
      background: #161616;
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      padding: 1.85rem;
      box-shadow: 0 30px 70px -35px rgba(0, 0, 0, 0.7);
    }

    @media (max-width: 991.98px) {
      .checkout-card {
        position: static;
        top: auto;
      }
    }

    .checkout-card .price-block {
      margin-bottom: 1.4rem;
    }

    .checkout-card .price-block .list-price {
      text-decoration: line-through;
      color: var(--ink-soft);
      font-size: 0.95rem;
      margin-bottom: 0.1rem;
      display: block;
    }

    .checkout-card .price-block .effective-price {
      font-weight: 700;
      font-size: 2.1rem;
      letter-spacing: -0.01em;
      color: #fff;
      display: flex;
      align-items: baseline;
      gap: 0.6rem;
    }

    .checkout-card .price-block .effective-price small {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--ink-soft);
    }

    .checkout-card .promo-note {
      color: #ff8a95;
      font-size: 0.85rem;
      font-weight: 600;
      margin-top: 0.35rem;
      margin-bottom: 0;
    }

    .checkout-card .free-note {
      color: #6ea8fe;
      font-size: 0.85rem;
      font-weight: 600;
      margin-top: 0.5rem;
      margin-bottom: 0;
    }

    .form-control,
    .form-select {
      font-family: 'Sora', sans-serif;
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      padding: 0.65rem 0.9rem;
      font-size: 0.95rem;
      background: #0a0a0a;
      color: #fff;
    }

    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.35);
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px var(--accent-soft);
      background: #0a0a0a;
      color: #fff;
    }

    .field-label {
      font-weight: 600;
      font-size: 0.88rem;
      margin-bottom: 0.4rem;
      display: block;
      color: #fff;
    }

    .coupon-group {
      margin-bottom: 1.4rem;
    }

    .coupon-group .input-group {
      gap: 0;
    }

    .coupon-group .form-control {
      border-radius: 0.375rem 0 0 0.375rem !important;
    }

    .coupon-group .btn-mech,
    .coupon-group .btn-mech-outline {
      border-radius: 0 0.375rem 0.375rem 0 !important;
    }

    .payment-methods {
      border: 0;
      padding: 0;
      margin-bottom: 1.1rem;
    }

    .payment-methods legend {
      font-weight: 700;
      font-size: 0.94rem;
      margin-bottom: 0.85rem;
      padding: 0;
    }

    .pay-option {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      padding: 0.85rem 1rem;
      margin-bottom: 0.6rem;
      cursor: pointer;
      transition: none;
    }

    .pay-option:last-child {
      margin-bottom: 0;
    }

    .pay-option:hover {
      border-color: var(--accent-border);
      transform: translateY(-2px);
      box-shadow: 0 10px 24px -18px rgba(18, 21, 26, 0.35);
    }

    .pay-option input[type="radio"]:checked~.pay-option__body {
      color: var(--ink);
    }

    .pay-option:has(input[type="radio"]:checked) {
      border-color: var(--accent);
      background: var(--accent-soft);
    }

    .pay-option input[type="radio"] {
      margin-top: 0.25rem;
      accent-color: var(--accent);
      width: 16px;
      height: 16px;
      flex: 0 0 auto;
    }

    .pay-option__body strong {
      display: block;
      font-size: 0.92rem;
      font-weight: 700;
      margin-bottom: 0.1rem;
      color: #fff;
    }

    .pay-option__body span {
      display: block;
      font-size: 0.8rem;
      color: var(--ink-soft);
    }

    .mpesa-help,
    .transfer-help-box,
    .whatsapp-help-box {
      border-radius: 0.375rem;
      padding: 0.95rem 1.1rem;
      font-size: 0.88rem;
    }

    .btn-mech {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      border-radius: 0.375rem;
      padding: 0.85rem 1.5rem;
      font-weight: 600;
      font-size: 0.96rem;
      text-decoration: none;
      border: 1px solid transparent;
      transition: none;
      cursor: pointer;
      line-height: 1.2;
      width: 100%;
    }

    .btn-mech-primary {
      background: var(--accent);
      color: #fff;
    }

    .btn-mech-primary:hover {
      transform: translateY(-1px);
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

    .btn-mech-success {
      background: #16a34a;
      color: #fff;
    }

    .btn-mech-success:hover {
      color: #fff;
      transform: translateY(-1px);
    }

    .terms-note {
      font-size: 0.78rem;
      color: var(--ink-soft);
      text-align: center;
      margin-top: 1.1rem;
      margin-bottom: 0;
    }

    .terms-note a {
      color: #6ea8fe;
      text-decoration: none;
      font-weight: 600;
    }

    /* ---------- Alerts / success box ---------- */
    .alert-mech {
      border-radius: 0.375rem;
      border: 1px solid var(--line);
      padding: 1.5rem;
      text-align: center;
    }

    .success-checkout-box {
      background: rgba(22, 163, 74, 0.12);
      border: 1px solid rgba(22, 163, 74, 0.35);
      border-radius: 0.375rem;
      padding: 2rem 1.5rem;
      text-align: center;
    }

    .success-checkout-box .icon {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: rgba(22, 163, 74, 0.22);
      color: #86efac;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2.1rem;
      margin-bottom: 1rem;
    }

    .success-checkout-box h4 {
      font-weight: 700;
      margin-bottom: 0.6rem;
      color: #86efac;
      font-size: 1.15rem;
    }

    .success-checkout-box p {
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: 0.6rem;
      font-size: 0.94rem;
    }

    footer.site-footer {
      text-align: center;
      padding: 2rem 1rem;
      font-size: 0.85rem;
      color: var(--ink-soft);
    }

    /* ---------- Motion ---------- */
    :root {
      --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
      --ease-spring: cubic-bezier(0.34, 1.3, 0.64, 1);
    }

    .checkout-header .kicker,
    .checkout-header h1 {
      opacity: 1;
      transform: none;
      animation: none;
    }

    .checkout-header h1 {  }

    .reveal {
      opacity: 1;
      transform: none;
      transition: none;
    }

    .reveal.is-in {
      opacity: 1;
      transform: none;
    }

    .checkout-card .btn,
    .btn-mech,
    button[type="submit"] {
      transition: none;
    }

    .checkout-card .btn:active,
    button[type="submit"]:active {
      transform: scale(0.97);
    }

    .course-thumb {
      overflow: hidden;
      transition: none;
    }

    .course-thumb img {
      transition: none;
    }

    .checkout-summary:hover .course-thumb:not(:has(iframe)) img {
      transform: scale(1.03);
    }

    @keyframes riseIn {
      to { opacity: 1; transform: translateY(0); }
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal,
      .checkout-header .kicker,
      .checkout-header h1,
      .course-thumb img {
        animation: none;
        transition: none;
        opacity: 1 !important;
        transform: none !important;
      }
    }

    @media (max-width: 991.98px) {
      .checkout-section {
        padding-top: 1.5rem;
        padding-bottom: 2.5rem;
      }

      .checkout-card {
        padding: 1.35rem;
      }

      .topbar__secure {
        display: none;
      }
    }

    @media (max-width: 767.98px) {
      .checkout-icon-bg {
        display: none;
      }

      .container-mech {
        padding-left: 1.25rem;
        padding-right: 1.25rem;
      }

      .topbar__inner {
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
        gap: 0.65rem;
      }

      .topbar__brand img {
        height: 22px;
      }

      .topbar__back {
        font-size: 0.8rem;
      }

      .checkout-header h1 {
        font-size: clamp(1.45rem, 6vw, 1.85rem);
      }

      .checkout-card {
        padding: 1.15rem;
      }

      .checkout-card .price-block .effective-price {
        font-size: 1.75rem;
      }

      .coupon-group .input-group {
        flex-wrap: wrap;
        gap: 0.5rem;
      }

      .coupon-group .form-control {
        flex: 1 1 100%;
        min-width: 0;
        width: 100%;
        border-radius: 0.375rem !important;
      }

      .coupon-group .btn-mech,
      .coupon-group .btn-mech-outline {
        width: 100%;
        border-radius: 0.375rem !important;
      }

      .pay-option {
        padding: 0.85rem;
      }

      .checkout-summary {
        padding: 1.15rem;
      }
    }

    @media (max-width: 479.98px) {
      .checkout-card .btn-mech,
      .checkout-card button[type="submit"] {
        width: 100%;
      }

      .trust-row {
        gap: 0.75rem;
      }
    }

    .container-mech,
    .container-mech.topbar__inner {
      padding-left: max(1.25rem, env(safe-area-inset-left, 0px)) !important;
      padding-right: max(1.25rem, env(safe-area-inset-right, 0px)) !important;
    }

    @media (min-width: 768px) {
      .container-mech,
      .container-mech.topbar__inner {
        padding-left: max(1.5rem, env(safe-area-inset-left, 0px)) !important;
        padding-right: max(1.5rem, env(safe-area-inset-right, 0px)) !important;
      }
    }
  </style>
</head>

<body
  data-analytics-course-id="<?= (int) ($course->id_course ?? 0) ?>"
  data-analytics-course-title="<?= esc($course->title_course ?? '') ?>"
  data-analytics-amount="<?= esc((string) $effectivePrice) ?>"
>

  <div class="checkout-icon-bg" aria-hidden="true">
    <?php for ($i = 0; $i < 8; $i++): ?>
      <img src="<?= esc($courseIconBg) ?>" alt="">
    <?php endfor; ?>
  </div>

  <div class="checkout-page">

  <?= view('partials/promo_urgency', [
      'hasPromo' => $hasPromo,
      'promoRemainingSeconds' => $promoRemainingSeconds,
      'discountPercent' => $discountPercent,
      'promoEndsAt' => $promoEndsAt,
      'listPrice' => $listPrice,
      'promoPrice' => $hasPromo ? $effectivePrice : null,
      'courseTitle' => $course->title_course ?? '',
      'promoCtaHref' => '#checkout-form',
      'promoCtaLabel' => 'Garantir oferta',
  ]) ?>

  <div class="topbar">
    <div class="container-mech topbar__inner">
      <a class="topbar__back" href="<?= base_url('/courses/' . (int) ($course->id_course ?? 0)) ?>">
        <i class="bi bi-arrow-left"></i> Voltar
      </a>
      <a class="topbar__brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
      </a>
      <span class="topbar__secure d-none d-sm-inline-flex"><i class="bi bi-shield-lock-fill"></i> Seguro</span>
    </div>
  </div>

  <header class="checkout-header">
    <div class="container-mech">
      <h1><?= esc($course->title_course ?? '') ?></h1>
    </div>
  </header>

  <section class="checkout-section">
    <div class="container-mech">
      <div class="checkout-grid">

        <!-- LEFT: COURSE SUMMARY -->
        <div class="checkout-summary">
          <div class="course-thumb">
            <?php if ($overviewVideoEmbedSrc): ?>
              <iframe
                title="Vídeo de visão geral · <?= esc($course->title_course ?? 'Curso', 'attr') ?>"
                src="<?= esc($overviewVideoEmbedSrc, 'attr') ?>"
                allow="autoplay; fullscreen; picture-in-picture"
                allowfullscreen
                loading="lazy"
                referrerpolicy="no-referrer"
              ></iframe>
            <?php else: ?>
              <img src="<?= esc($courseImageSrc) ?>" alt="<?= esc($course->title_course ?? 'Curso') ?>">
            <?php endif; ?>
          </div>

          <div class="summary-meta">
            <?php if ($totalHoursLabel !== '' && $totalHoursLabel !== '0 Horas'): ?>
              <span class="item"><span class="item-icon"><i class="bi bi-clock-fill"></i></span> <?= esc($totalHoursLabel) ?></span>
            <?php endif; ?>
            <?php if ($moduleCount > 0): ?>
              <span class="item"><span class="item-icon"><i class="bi bi-folder-fill"></i></span> <?= $moduleCount ?> módulos</span>
            <?php endif; ?>
            <?php if ($lessonCount > 0): ?>
              <span class="item"><span class="item-icon"><i class="bi bi-play-btn-fill"></i></span> <?= $lessonCount ?> aulas</span>
            <?php endif; ?>
            <?php if ($projectCount > 0): ?>
              <span class="item"><span class="item-icon"><i class="bi bi-kanban-fill"></i></span> <?= $projectCount ?> projeto<?= $projectCount > 1 ? 's' : '' ?></span>
            <?php endif; ?>
          </div>

          <?php if (! empty($learningItems)): ?>
            <p class="summary-block-heading">O que você vai aprender</p>
            <ul class="learn-list-checkout">
              <?php foreach (array_slice($learningItems, 0, 6) as $learningItem): ?>
                <li><i class="bi bi-check-lg"></i><span><?= esc($learningItem) ?></span></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

          <div class="price-line">
            <?php if ($hasPromo): ?>
              <span class="list-price"><?= number_format($listPrice, 2, ",", ".") ?> MZN</span>
              <span class="effective-price"><?= number_format($effectivePrice, 2, ",", ".") ?> MZN</span>
              <span class="promo-badge">−<?= $discountPercent ?>% OFF</span>
            <?php else: ?>
              <span class="effective-price"><?= number_format($effectivePrice, 2, ",", ".") ?> MZN</span>
            <?php endif; ?>
          </div>

          <div class="trust-row">
            <span class="item"><i class="bi bi-shield-check"></i> Seguro</span>
            <span class="item"><i class="bi bi-infinity"></i> Vitalício</span>
            <span class="item"><i class="bi bi-mortarboard"></i> Certificado</span>
          </div>
        </div>

        <!-- RIGHT: FORM -->
        <div class="checkout-form-col">
          <div class="checkout-card">
            <div class="price-block">
              <?php if ($hasPromo): ?>
                <span class="list-price"><?= number_format($listPrice, 2, ",", ".") ?> MZN</span>
                <p class="effective-price mb-0">
                  <?= number_format($effectivePrice, 2, ",", ".") ?> <small>MZN</small>
                  <span class="promo-badge">−<?= $discountPercent ?>% OFF</span>
                </p>
                <?php if ($promoRemainingSeconds > 0): ?>
                  <p class="promo-note">Expira em <strong class="js-promo-inline-countdown" data-left="<?= $promoRemainingSeconds ?>">--:--:--</strong></p>
                <?php endif; ?>
              <?php else: ?>
                <p class="effective-price mb-0"><?= number_format($effectivePrice, 2, ",", ".") ?> <small>MZN</small></p>
              <?php endif; ?>
            </div>

            <?php if ($isEnrolled): ?>
              <div class="alert-mech">
                <h4 class="fw-bold mb-2" style="font-size:1.05rem;">Já inscrito</h4>
                <a href="<?= base_url('/student/dashboard/inscricoes') ?>" class="btn-mech btn-mech-primary">Meus cursos</a>
              </div>

            <?php elseif (($user) && ($user->role == "instructor")): ?>
              <div class="alert-mech">
                <h4 class="fw-bold mb-2" style="font-size:1.05rem;">Conta de instrutor</h4>
                <a href="<?= base_url('/instructor/dashboard/inscricoes') ?>" class="btn-mech btn-mech-primary">Painel</a>
              </div>

            <?php else: ?>
              <div id="checkout-wrapper">
                <form id="checkout-form" action="<?= base_url('mpesa/send') ?>" method="post" class="needs-validation" novalidate>
                  <?= csrf_field() ?>

                  <div class="coupon-group">
                    <div class="input-group">
                      <input type="text" class="form-control" id="coupon" placeholder="Código do cupom">
                      <button class="btn-mech btn-mech-outline" style="width:auto;" type="button">Aplicar</button>
                    </div>
                  </div>

                  <?php if (($user) && ($user->role !== "instructor")): ?>
                    <input type="hidden" name="email" value="<?= esc($user->email) ?>">
                    <input type="hidden" name="username" value="<?= esc($user->username) ?>">
                  <?php else: ?>
                    <div class="d-flex justify-content-end mb-2">
                      <a href="<?= base_url('/login') ?>" class="fw-bold text-decoration-none" style="color:var(--accent); font-size:0.86rem;">
                        Já tem conta? Fazer login
                      </a>
                    </div>

                    <div class="mb-3">
                      <label class="field-label" for="email">E-mail</label>
                      <input type="email" name="email" class="form-control" id="email" placeholder="seuemail@exemplo.com" required>
                      <div class="invalid-feedback">
                        Por favor, insira um e-mail válido.
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="field-label" for="name">Nome</label>
                      <input type="text" name="username" class="form-control" id="name" placeholder="O seu nome" required>
                      <div class="invalid-feedback">
                        Por favor, insira o seu nome.
                      </div>
                    </div>
                  <?php endif; ?>

                  <fieldset class="payment-methods">
                    <legend>Pagamento</legend>

                    <label class="pay-option" for="pay_mpesa">
                      <input class="form-check-input" type="radio" name="payment_method_ui" id="pay_mpesa" value="mpesa" checked>
                      <span class="pay-option__body">
                        <strong>M-Pesa</strong>
                      </span>
                    </label>

                    <label class="pay-option" for="pay_transfer">
                      <input class="form-check-input" type="radio" name="payment_method_ui" id="pay_transfer" value="transfer">
                      <span class="pay-option__body">
                        <strong>Transferência</strong>
                      </span>
                    </label>

                    <label class="pay-option" for="pay_whatsapp">
                      <input class="form-check-input" type="radio" name="payment_method_ui" id="pay_whatsapp" value="whatsapp">
                      <span class="pay-option__body">
                        <strong>WhatsApp · +258 84 272 6761</strong>
                      </span>
                    </label>
                  </fieldset>

                  <div id="mpesa-fields">
                    <label for="client_number" class="field-label">Número M-Pesa</label>
                    <input type="tel" class="form-control mb-2" id="client_number" name="client_number" placeholder="84 000 0000" required>
                    <div class="invalid-feedback">
                      Por favor, insira o seu número M-Pesa.
                    </div>
                  </div>

                  <div id="transfer-help" class="transfer-help-box mb-3 d-none" style="background:var(--accent-soft); border:1px solid var(--accent-border); color:var(--ink);">
                    <?php if ($user): ?>
                      <a class="btn-mech btn-mech-outline" style="width:auto; padding:0.5rem 1rem; font-size:0.85rem;" href="<?= site_url('student/dashboard/checkout/' . (int) $course->id_course) ?>">Enviar comprovativo</a>
                    <?php else: ?>
                      <span>Faça login e envie o comprovativo no painel.</span>
                    <?php endif; ?>
                  </div>

                  <div id="whatsapp-help" class="whatsapp-help-box mb-3 d-none" style="background:rgba(22,163,74,0.12); border:1px solid rgba(22,163,74,0.35); color:#86efac;">
                      <a class="btn-mech btn-mech-success" style="width:auto; padding:0.5rem 1rem; font-size:0.85rem;" href="<?= esc($whatsappUrl) ?>" target="_blank" rel="noopener">
                        WhatsApp +258 84 272 6761
                      </a>
                  </div>

                  <input type="hidden" name="id_course" value="<?= (int) $course->id_course ?>">
                  <input type="hidden" name="amount_payment" value="<?= $effectivePrice ?>">

                  <button type="submit" id="checkout-submit-btn" class="btn-mech btn-mech-primary">
                    Finalizar compra
                    <i class="bi bi-arrow-right"></i>
                  </button>

                  <?php if ($freeLessonsCount > 0): ?>
                    <a href="<?= site_url('courses/' . (int) $course->id_course . '/trial') ?>" class="btn-mech btn-mech-outline mt-2">
                      Experimentar <?= $freeLessonsCount ?> aula<?= $freeLessonsCount > 1 ? 's' : '' ?> grátis
                    </a>
                  <?php endif; ?>
                </form>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </section>

  <footer class="site-footer">
    &copy; <?= date('Y') ?> Mechanical Academy. Todos os direitos reservados.
  </footer>

  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    (function () {
      const radios = document.querySelectorAll('input[name="payment_method_ui"]');
      const mpesaFields = document.getElementById('mpesa-fields');
      const transferHelp = document.getElementById('transfer-help');
      const whatsappHelp = document.getElementById('whatsapp-help');
      const submitBtn = document.getElementById('checkout-submit-btn');
      const clientNumber = document.getElementById('client_number');
      const form = document.getElementById('checkout-form');
      const whatsappUrl = <?= json_encode($whatsappUrl ?? '#') ?>;

      const syncPaymentUi = () => {
        const selected = document.querySelector('input[name="payment_method_ui"]:checked')?.value || 'mpesa';
        mpesaFields?.classList.toggle('d-none', selected !== 'mpesa');
        transferHelp?.classList.toggle('d-none', selected !== 'transfer');
        whatsappHelp?.classList.toggle('d-none', selected !== 'whatsapp');
        if (clientNumber) {
          clientNumber.required = selected === 'mpesa';
          clientNumber.disabled = selected !== 'mpesa';
        }
        if (submitBtn) {
          if (selected === 'mpesa') {
            submitBtn.classList.remove('d-none');
            submitBtn.textContent = 'Finalizar a minha compra';
          } else if (selected === 'transfer') {
            submitBtn.classList.add('d-none');
          } else {
            submitBtn.classList.remove('d-none');
            submitBtn.innerHTML = 'Continuar no WhatsApp <i class="fas fa-arrow-right ms-2"></i>';
          }
        }
      };

      radios.forEach((radio) => radio.addEventListener('change', syncPaymentUi));
      syncPaymentUi();

      form?.addEventListener('submit', (event) => {
        const selected = document.querySelector('input[name="payment_method_ui"]:checked')?.value || 'mpesa';
        if (selected === 'whatsapp') {
          event.preventDefault();
          window.open(whatsappUrl, '_blank', 'noopener');
        }
        if (selected === 'transfer') {
          event.preventDefault();
        }
      });
    })();
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (session()->getFlashdata('swal')): ?>
        const swalData = <?= json_encode(session()->getFlashdata('swal')) ?>;
        Swal.fire({
          icon: swalData.icon,
          title: swalData.title,
          text: swalData.text
        });
      <?php endif; ?>

      (function() {
        'use strict';
        window.addEventListener('load', function() {
          const forms = document.getElementsByClassName('needs-validation');
          Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
              if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });
        }, false);
      })();

      const form = document.getElementById('checkout-form');
      if (!form) return;

      const checkoutWrapper = document.getElementById('checkout-wrapper');
      const submitButton = form.querySelector('button[type="submit"]');
      const csrfInput = form.querySelector('input[name="<?= csrf_token() ?>"]');

      const updateCsrf = (hash) => {
        if (csrfInput && hash) {
          csrfInput.value = hash;
        }
      };

      const renderSuccessMessage = (options = {}) => {
        if (!checkoutWrapper) return;

        const requiresPasswordSetup = options?.requiresPasswordSetup === true;
        const title = requiresPasswordSetup
          ? 'Pagamento confirmado!'
          : 'Pagamento efectuado com sucesso!';
        const description = requiresPasswordSetup
          ? 'Enviamos um e-mail com os proximos passos para concluir a sua inscricao.'
          : 'O seu pagamento foi efectuado com sucesso.';
        const nextStep = requiresPasswordSetup
          ? 'Verifique a sua caixa de entrada e siga o link enviado por e-mail para criar a sua senha e activar o acesso ao curso.'
          : 'Verifique a sua caixa de e-mails e prossiga com a criacao da senha para concluir a inscricao.';
        const actionButton = requiresPasswordSetup
          ? ''
          : `<a href="<?= base_url('/login') ?>" class="btn btn-success mt-2">
              Prosseguir para o login
            </a>`;

        checkoutWrapper.innerHTML = `
          <div class="success-checkout-box">
            <div class="icon">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <h4>${title}</h4>
            <p>${description}</p>
            <p>Verifique a sua caixa de e-mails e prossiga com a criação da senha para concluir a inscrição.</p>
            ${actionButton}
          </div>
        `;

        const successBox = checkoutWrapper.querySelector('.success-checkout-box');
        const descriptionElement = successBox?.querySelectorAll('p')?.[1];

        if (descriptionElement) {
          descriptionElement.textContent = nextStep;
        }
      };

      const showResult = async (payload) => {
        const isApproved = payload?.status === 'approved';
        const requiresPasswordSetup = payload?.status === 'password_setup_pending' || payload?.requires_password_setup === true;
        const isSuccessfulCheckout = isApproved || requiresPasswordSetup;
        const courseId = Number(document.body?.getAttribute('data-analytics-course-id') || 0) || undefined;
        const amount = Number(document.body?.getAttribute('data-analytics-amount') || 0) || undefined;
        const courseTitle = document.body?.getAttribute('data-analytics-course-title') || undefined;

        const swalData = payload?.swal || {
          icon: isSuccessfulCheckout ? 'success' : 'error',
          title: isSuccessfulCheckout ? 'Pagamento aprovado' : 'Erro',
          text: isSuccessfulCheckout
            ? 'O seu pagamento foi efectuado com sucesso.'
            : 'Não foi possível concluir o pagamento.'
        };

        await Swal.fire({
          icon: swalData.icon,
          title: swalData.title,
          text: swalData.text,
          confirmButtonText: 'OK'
        });

        if (isSuccessfulCheckout) {
          if (window.AcademyAnalytics) {
            window.AcademyAnalytics.purchase({
              course_id: courseId,
              course_title: courseTitle,
              amount: amount,
              payment_id: payload?.payment_id || undefined,
              status: payload?.status || 'approved',
              requires_password_setup: !!requiresPasswordSetup,
              method: 'mpesa'
            });
          }
          renderSuccessMessage({
            requiresPasswordSetup
          });
          return;
        }

        if (window.AcademyAnalytics) {
          window.AcademyAnalytics.paymentFailed({
            course_id: courseId,
            course_title: courseTitle,
            amount: amount,
            payment_id: payload?.payment_id || undefined,
            status: payload?.status || 'failed',
            method: 'mpesa'
          });
        }

        if (payload?.redirect_url) {
          window.location.href = payload.redirect_url;
        }
      };

      const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

      const parseJsonResponse = async (response) => {
        const rawText = await response.text();

        if (!rawText) {
          return {
            payload: null,
            rawText: ''
          };
        }

        try {
          return {
            payload: JSON.parse(rawText),
            rawText
          };
        } catch (error) {
          const start = rawText.indexOf('{');
          const end = rawText.lastIndexOf('}');

          if (start !== -1 && end > start) {
            try {
              return {
                payload: JSON.parse(rawText.slice(start, end + 1)),
                rawText
              };
            } catch (nestedError) {
              // continua para o fallback abaixo
            }
          }
        }

        return {
          payload: null,
          rawText
        };
      };

      const toReadableErrorText = (rawText) => {
        if (!rawText) {
          return 'Não foi possível concluir o pedido agora. Tente novamente dentro de instantes.';
        }

        const text = rawText
          .replace(/<[^>]*>/g, ' ')
          .replace(/\s+/g, ' ')
          .trim();

        if (!text || text === '""' || text === "''") {
          return 'Não foi possível concluir o pedido agora. Tente novamente dentro de instantes.';
        }

        return text.slice(0, 220);
      };

      const buildStatusFormData = (payload) => {
        const statusData = new FormData();
        statusData.append('payment_id', String(payload?.payment_id || ''));
        statusData.append('reference', String(payload?.reference || ''));
        statusData.append('query_reference', String(payload?.query_reference || ''));
        statusData.append('gateway_mode', String(payload?.gateway_mode || 'sync'));
        statusData.append('remote_query_enabled', payload?.remote_query_enabled === false ? '0' : '1');

        const emailInput = form.querySelector('input[name="email"]');
        const usernameInput = form.querySelector('input[name="username"]');

        if (emailInput?.value) {
          statusData.append('email', emailInput.value);
        }

        if (usernameInput?.value) {
          statusData.append('username', usernameInput.value);
        }

        if (csrfInput?.name && csrfInput?.value) {
          statusData.append(csrfInput.name, csrfInput.value);
        }

        return statusData;
      };

      const fetchPaymentStatus = async (payload) => {
        const response = await fetch(payload.status_check_url, {
          method: 'POST',
          body: buildStatusFormData(payload),
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const {
          payload: statusPayload,
          rawText
        } = await parseJsonResponse(response);

        if (statusPayload) {
          return statusPayload;
        }

        throw new Error(toReadableErrorText(rawText));
      };

      const waitForConfirmation = async (payload) => {
        if (!payload?.status_check_url) {
          await Swal.fire({
            icon: 'info',
            title: 'Pedido enviado',
            text: 'O pedido foi enviado ao M-Pesa. Confirme o PIN no celular e aguarde alguns instantes.'
          });
          return;
        }

        const maxAttempts = 18;

        Swal.fire({
          icon: 'info',
          title: 'Confirme o PIN',
          text: 'Estamos a aguardar a confirmação do pagamento no M-Pesa.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        for (let attempt = 1; attempt <= maxAttempts; attempt += 1) {
          await sleep(5000);

          const statusPayload = await fetchPaymentStatus(payload);

          if (statusPayload?.csrf) {
            updateCsrf(statusPayload.csrf);
          }

          if (statusPayload?.query_reference) {
            payload.query_reference = statusPayload.query_reference;
          }

          if (statusPayload?.gateway_mode) {
            payload.gateway_mode = statusPayload.gateway_mode;
          }

          if (typeof statusPayload?.remote_query_enabled === 'boolean') {
            payload.remote_query_enabled = statusPayload.remote_query_enabled;
          }

          if (statusPayload?.status === 'pending_confirmation') {
            const waitingText = payload?.gateway_mode === 'async'
              ? `O pedido foi aceite pelo gateway. Estamos a aguardar a confirmação final do M-Pesa. Verificação ${attempt} de ${maxAttempts}.`
              : `Aguardando a confirmação do pagamento no M-Pesa. Verificação ${attempt} de ${maxAttempts}.`;

            Swal.update({
              text: waitingText
            });
            continue;
          }

          Swal.close();
          await showResult(statusPayload);
          return;
        }

        Swal.close();
        await Swal.fire({
          icon: 'info',
          title: 'Ainda pendente',
          text: payload?.gateway_mode === 'async'
            ? 'O pedido continua pendente. O gateway aceitou a solicitação, mas a confirmação final ainda não chegou.'
            : 'O pedido continua pendente. Confirme o PIN no pop-up do celular e tente novamente dentro de instantes.'
        });
      };

      form.addEventListener('submit', async function(event) {
        event.preventDefault();

        if (!form.checkValidity()) {
          event.stopPropagation();
          form.classList.add('was-validated');

          await Swal.fire({
            icon: 'warning',
            title: 'Dados obrigatórios',
            text: 'Preencha os campos do checkout antes de continuar.'
          });
          return;
        }

        form.classList.add('was-validated');

        if (window.AcademyAnalytics) {
          window.AcademyAnalytics.paymentStart({
            course_id: Number(document.body?.getAttribute('data-analytics-course-id') || 0) || undefined,
            course_title: document.body?.getAttribute('data-analytics-course-title') || undefined,
            amount: Number(document.body?.getAttribute('data-analytics-amount') || 0) || undefined,
            method: 'mpesa'
          });
        }

        if (submitButton) {
          submitButton.disabled = true;
        }

        Swal.fire({
          icon: 'info',
          title: 'Aguardando confirmação',
          text: 'O seu pedido está a ser processado. Confirme o PIN no pop-up do celular para concluir a compra.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        try {
          const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          const {
            payload,
            rawText
          } = await parseJsonResponse(response);

          if (payload?.csrf) {
            updateCsrf(payload.csrf);
          }

          Swal.close();

          if (!payload) {
            throw new Error(toReadableErrorText(rawText));
          }

          if (payload?.status === 'pending_confirmation') {
            await waitForConfirmation(payload);
            return;
          }

          await showResult(payload);
        } catch (error) {
          Swal.close();
          if (window.AcademyAnalytics) {
            window.AcademyAnalytics.paymentFailed({
              course_id: Number(document.body?.getAttribute('data-analytics-course-id') || 0) || undefined,
              course_title: document.body?.getAttribute('data-analytics-course-title') || undefined,
              amount: Number(document.body?.getAttribute('data-analytics-amount') || 0) || undefined,
              method: 'mpesa',
              reason: 'network_or_parse'
            });
          }
          await Swal.fire({
            icon: 'error',
            title: 'Falha na comunicação',
            text: toReadableErrorText(error?.message || '')
          });
        } finally {
          if (submitButton) {
            submitButton.disabled = false;
          }
        }
      });
    });

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
        }, { threshold: 0.1, rootMargin: '0px 0px -4% 0px' });
        document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
      } else {
        document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-in'));
      }
    })();
  </script>
</div>
  <script>window.ANALYTICS_COLLECT_URL = <?= json_encode(site_url('analytics/collect')) ?>;</script>
  <script src="<?= base_url('assets/js/analytics-tracker.js') ?>" defer></script>
  <?= view('partials/posthog') ?>
</body>

</html>
