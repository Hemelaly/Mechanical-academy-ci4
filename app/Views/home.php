<?php

$isLoggedIn = auth()->loggedIn();
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

$courses = $courses ?? [];
$courseCount = count($courses);
$formatMzn = static function ($value): string {
    return number_format((float) $value, 0, ',', '.') . ' MT';
};
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mechanical Academy</title>

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
      --surface-2: #1c1c1c;
      --line: rgba(255, 255, 255, 0.09);
      --accent: #0d6efd;
      --accent-soft: rgba(13, 110, 253, 0.14);
      --accent-border: rgba(13, 110, 253, 0.38);
      --accent-glow: rgba(13, 110, 253, 0.28);
      --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
      --ease-spring: cubic-bezier(0.34, 1.3, 0.64, 1);
    }

    * { box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Sora', sans-serif;
      color: var(--ink);
      background:
        radial-gradient(900px 520px at 85% -10%, var(--accent-glow) 0%, transparent 55%),
        radial-gradient(700px 420px at 0% 40%, rgba(13, 110, 253, 0.08) 0%, transparent 50%),
        var(--page-bg);
      -webkit-font-smoothing: antialiased;
      margin: 0;
    }

    a { color: inherit; }

    .container-mech {
      width: 100%;
      max-width: 1140px;
      margin: 0 auto;
      padding-left: max(1.25rem, env(safe-area-inset-left, 0px));
      padding-right: max(1.25rem, env(safe-area-inset-right, 0px));
    }

    .container-mech.site-nav__inner,
    .container-mech.hero__inner {
      padding-left: max(1.25rem, env(safe-area-inset-left, 0px));
      padding-right: max(1.25rem, env(safe-area-inset-right, 0px));
    }

    /* ---------- Preloader ---------- */
    #preloader {
      position: fixed;
      inset: 0;
      z-index: 9999;
      display: grid;
      place-items: center;
      background: #12151a;
      transition: none;
    }

    #preloader.is-hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .preloader__inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.25rem;
      width: min(280px, 70vw);
    }

    .preloader__logo {
      width: 160px;
      height: auto;
      opacity: 0.95;
      animation: none;
    }

    .preloader__bar {
      width: 100%;
      height: 2px;
      background: rgba(255, 255, 255, 0.12);
      border-radius: 0.375rem;
      overflow: hidden;
    }

    .preloader__bar__fill {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, var(--accent), #4d8fff);
      border-radius: 0.375rem;
      transition: none;
    }

    .preloader__text {
      margin: 0;
      font-size: 0.78rem;
      font-weight: 500;
      letter-spacing: 0.08em;
      color: rgba(255, 255, 255, 0.55);
    }

    @keyframes brandPulse {
      0%, 100% { opacity: 0.7; transform: scale(1); }
      50% { opacity: 1; transform: scale(1.015); }
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
      transition: none;
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
      padding-top: 0.9rem;
      padding-bottom: 0.9rem;
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
      transition: none;
    }

    .site-nav__links a:hover { color: #fff; }

    .site-nav__cta {
      color: #fff !important;
      border: 1px solid rgba(255, 255, 255, 0.28);
      border-radius: 0.375rem;
      padding: 0.5rem 1.15rem !important;
      transition: none;
    }

    .site-nav__cta:hover {
      border-color: rgba(255, 255, 255, 0.6);
      transform: translateY(-1px);
    }

    .nav-avatar {
      width: 32px;
      height: 32px;
      min-width: 32px;
      border-radius: 50%;
      object-fit: cover;
      display: block;
      background: #1c2028;
      border: 1px solid rgba(255, 255, 255, 0.22);
    }

    .site-nav__toggle {
      display: none;
      background: transparent;
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 0.375rem;
      color: #fff;
      padding: 0.4rem 0.6rem;
      font-size: 1.05rem;
    }

    .site-nav__user {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      text-decoration: none;
      color: #fff;
      font-weight: 600;
      font-size: 0.9rem;
    }

    /* ---------- Buttons ---------- */
    .btn-mech {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      border-radius: 0.375rem;
      padding: 0.9rem 1.85rem;
      font-weight: 600;
      font-size: 0.98rem;
      text-decoration: none;
      border: 1px solid transparent;
      transition: none;
      cursor: pointer;
      line-height: 1.2;
    }

    .btn-mech:active { transform: scale(0.97); }

    .btn-mech-primary {
      background: var(--accent);
      color: #fff;
      box-shadow: 0 10px 28px -12px rgba(13, 110, 253, 0.65);
    }

    .btn-mech-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 32px -12px rgba(13, 110, 253, 0.7);
      color: #fff;
      filter: brightness(1.06);
    }

    .btn-mech-outline-invert {
      background: transparent;
      color: #fff;
      border-color: rgba(255, 255, 255, 0.35);
    }

    .btn-mech-outline-invert:hover {
      border-color: rgba(255, 255, 255, 0.85);
      background: rgba(255, 255, 255, 0.06);
      color: #fff;
      transform: translateY(-2px);
    }

    .btn-mech-light {
      background: #fff;
      color: #0a0a0a;
    }

    .btn-mech-light:hover {
      transform: translateY(-2px);
      color: #0a0a0a;
      box-shadow: 0 12px 28px -14px rgba(255, 255, 255, 0.25);
    }

    .btn-mech-dark {
      background: var(--accent);
      color: #fff;
    }

    .btn-mech-dark:hover {
      transform: translateY(-2px);
      color: #fff;
      filter: brightness(1.08);
      box-shadow: 0 12px 28px -14px rgba(13, 110, 253, 0.55);
    }

    /* ---------- Hero ---------- */
    .hero {
      position: relative;
      min-height: 92vh;
      display: flex;
      align-items: flex-end;
      overflow: hidden;
      color: #fff;
    }

    .hero__media {
      position: absolute;
      inset: 0;
      background: url(<?= base_url('assets/img/banner.jpeg') ?>) center / cover no-repeat;
      transform: scale(1.04);
      animation: none;
    }

    .hero__gradient {
      position: absolute;
      inset: 0;
      background:
        radial-gradient(880px 620px at 78% 12%, var(--accent-glow) 0%, transparent 58%),
        linear-gradient(180deg, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.72) 48%, rgba(5, 5, 5, 0.98) 100%);
    }

    .hero__inner {
      position: relative;
      z-index: 2;
      padding-top: 8rem;
      padding-bottom: 4.75rem;
      width: 100%;
    }

    .hero__content { max-width: 720px; }

    .hero__brand {
      margin: 0 0 1.1rem;
      font-size: clamp(0.78rem, 1.4vw, 0.88rem);
      font-weight: 700;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.78);
    }

    .hero__title {
      margin: 0 0 1.15rem;
      font-size: clamp(2.35rem, 5.6vw, 4.15rem);
      font-weight: 700;
      line-height: 1.08;
      letter-spacing: -0.03em;
    }

    .hero__title span { color: #6ea8fe; }

    .hero__lead {
      margin: 0 0 2rem;
      max-width: 34rem;
      font-size: clamp(1rem, 1.6vw, 1.15rem);
      line-height: 1.65;
      color: rgba(255, 255, 255, 0.78);
      font-weight: 400;
    }

    .hero__actions {
      display: flex;
      flex-wrap: wrap;
      gap: 0.85rem;
    }

    .hero-anim > * {
      opacity: 1;
      transform: none;
      animation: none;
    }

    .hero-anim > *:nth-child(1) {  }
    .hero-anim > *:nth-child(2) {  }
    .hero-anim > *:nth-child(3) {  }
    .hero-anim > *:nth-child(4) {  }

    @keyframes riseIn {
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes heroKen {
      to { transform: scale(1); }
    }

    /* ---------- Sections ---------- */
    .section {
      padding: 5.5rem 0;
    }

    .section-ink {
      background: #0a0a0a;
      color: #fff;
      border-top: 1px solid var(--line);
      border-bottom: 1px solid var(--line);
    }

    .section-blue {
      background:
        radial-gradient(900px 420px at 10% 0%, rgba(255, 255, 255, 0.14) 0%, transparent 55%),
        linear-gradient(145deg, #0b5ed7 0%, #0d6efd 48%, #0a58ca 100%);
      color: #fff;
    }

    .section-blue .section__eyebrow {
      color: rgba(255, 255, 255, 0.85);
    }

    .section-blue .section__title,
    .section-blue .step__title {
      color: #fff;
    }

    .section-blue .section__lead,
    .section-blue .step__text {
      color: rgba(255, 255, 255, 0.82);
    }

    .section__eyebrow {
      margin: 0 0 0.75rem;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--accent);
    }

    .section-ink .section__eyebrow { color: #6ea8fe; }

    .section__title {
      margin: 0 0 0.85rem;
      font-size: clamp(1.75rem, 3.4vw, 2.45rem);
      font-weight: 700;
      letter-spacing: -0.025em;
      line-height: 1.15;
      color: #fff;
    }

    .section__lead {
      margin: 0;
      max-width: 36rem;
      color: var(--ink-soft);
      font-size: 1.05rem;
      line-height: 1.65;
    }

    .section-ink .section__lead { color: rgba(255, 255, 255, 0.68); }

    .section__head {
      margin-bottom: 2.75rem;
    }

    /* ---------- Trust strip ---------- */
    .trust {
      padding: 2.25rem 0;
      border-bottom: 1px solid var(--line);
      background: rgba(20, 20, 20, 0.85);
      backdrop-filter: blur(10px);
    }

    .trust__grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }

    .trust__item {
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.55rem;
    }

    .trust__icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #6ea8fe;
      font-size: 1.75rem;
      line-height: 1;
      background: none;
      border: 0;
      width: auto;
      height: auto;
      padding: 0;
      border-radius: 0;
    }

    .trust__value {
      margin: 0;
      font-size: 1.65rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      color: #fff;
    }

    .trust__label {
      margin: 0;
      font-size: 0.88rem;
      color: var(--ink-soft);
    }

    /* ---------- Course grid (compact dark cards) ---------- */
    .course-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.25rem;
    }

    .course-tile {
      display: flex;
      flex-direction: column;
      min-height: 100%;
      padding: 1.45rem 1.4rem 1.35rem;
      background: #161616;
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      text-decoration: none;
      color: inherit;
      transition: none;
      position: relative;
      overflow: hidden;
    }

    .course-tile::before {
      content: '';
      position: absolute;
      inset: 0 0 auto 0;
      height: 3px;
      background: var(--tile-accent, var(--accent));
      opacity: 0;
      transition: none;
      z-index: 2;
    }

    .course-tile:hover {
      box-shadow: 0 28px 48px -28px rgba(0, 0, 0, 0.75);
      border-color: rgba(13, 110, 253, 0.35);
      color: inherit;
    }

    .course-tile:hover::before {
      opacity: 1;
    }

    .course-tile__top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: 1.2rem;
    }

    .course-tile__meta {
      display: flex;
      flex-wrap: wrap;
      gap: 0.45rem;
      margin-bottom: 0;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.28rem 0.65rem;
      border-radius: 0.375rem;
      font-size: 0.72rem;
      font-weight: 600;
      letter-spacing: 0.02em;
      background: var(--accent-soft);
      color: #8bb9ff;
      border: 1px solid var(--accent-border);
    }

    .pill-muted {
      background: rgba(255, 255, 255, 0.05);
      color: var(--ink-soft);
      border-color: var(--line);
    }

    .course-tile__icon {
      width: 48px;
      height: 48px;
      border-radius: 0.375rem;
      object-fit: cover;
      background: #222;
      flex-shrink: 0;
      border: 1px solid var(--line);
    }

    .course-tile__title {
      margin: 0 0 0.85rem;
      font-size: 1.15rem;
      font-weight: 650;
      letter-spacing: -0.02em;
      line-height: 1.3;
      color: #fff;
    }

    .course-tile__price {
      margin-top: auto;
      display: flex;
      align-items: baseline;
      gap: 0.55rem;
      flex-wrap: wrap;
    }

    .course-tile__price-now {
      font-size: 1.15rem;
      font-weight: 700;
      letter-spacing: -0.02em;
      color: #fff;
    }

    .course-tile__price-was {
      font-size: 0.88rem;
      color: #ef4444;
      text-decoration: line-through;
      font-weight: 600;
    }

    .pill-discount {
      background: rgba(239, 68, 68, 0.16);
      border-color: rgba(239, 68, 68, 0.35);
      color: #fca5a5;
      font-weight: 700;
      letter-spacing: 0.02em;
    }

    .course-tile__promo-time {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      margin-top: 0.55rem;
      padding: 0.28rem 0.55rem;
      border-radius: 0.375rem;
      background: rgba(13, 110, 253, 0.14);
      border: 1px solid rgba(13, 110, 253, 0.35);
      color: #9ec5fe;
      font-size: 0.72rem;
      font-weight: 600;
      font-variant-numeric: tabular-nums;
      width: fit-content;
    }

    .course-tile__cta {
      margin-top: 1rem;
      font-size: 0.88rem;
      font-weight: 600;
      color: #6ea8fe;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      transition: none;
    }

    .course-tile:hover .course-tile__cta { gap: 0.55rem; }

    /* ---------- Steps (illustrative) ---------- */
    .steps {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
      counter-reset: step;
      position: relative;
    }

    .step {
      counter-increment: step;
      position: relative;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.18);
      border-radius: 0.375rem;
      padding: 1.6rem 1.4rem 1.5rem;
      backdrop-filter: blur(8px);
      transition: none;
    }

    .step:hover {
      transform: translateY(-4px);
      background: rgba(255, 255, 255, 0.14);
    }

    .step__visual {
      width: 64px;
      height: 64px;
      border-radius: 0.375rem;
      display: grid;
      place-items: center;
      margin-bottom: 1.15rem;
      background: rgba(255, 255, 255, 0.16);
      border: 1px solid rgba(255, 255, 255, 0.22);
      color: #fff;
      font-size: 1.55rem;
    }

    .step__num {
      display: block;
      margin-bottom: 0.55rem;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.16em;
      color: rgba(255, 255, 255, 0.78);
    }

    .step__num::before {
      content: "PASSO 0" counter(step);
    }

    .step__title {
      margin: 0 0 0.55rem;
      font-size: 1.2rem;
      font-weight: 650;
      letter-spacing: -0.02em;
      color: #fff;
    }

    .step__text {
      margin: 0;
      color: rgba(255, 255, 255, 0.82);
      line-height: 1.65;
      font-size: 0.95rem;
    }

    /* ---------- YouTube ---------- */
    .yt {
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: 3rem;
      align-items: center;
      width: 100%;
      max-width: 100%;
    }

    .yt__visual {
      position: relative;
      border-radius: 0.375rem;
      overflow: hidden;
      aspect-ratio: 16 / 10;
      width: 100%;
      background: #0d1014 url(<?= base_url('assets/img/youtube.png') ?>) center / cover no-repeat;
      box-shadow: 0 30px 60px -36px rgba(0, 0, 0, 0.55);
      transition: none;
    }

    .yt__visual:hover { transform: scale(1.015); }

    .yt__visual img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
      mix-blend-mode: luminosity;
      opacity: 0.92;
    }

    .yt__play {
      position: absolute;
      inset: 0;
      display: grid;
      place-items: center;
      background: rgba(0, 0, 0, 0.28);
      text-decoration: none;
    }

    .yt__play-btn {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: #fff;
      color: #0a0a0a;
      display: grid;
      place-items: center;
      font-size: 1.45rem;
      box-shadow: 0 16px 40px -16px rgba(0, 0, 0, 0.45);
      transition: none;
    }

    .yt__play:hover .yt__play-btn { transform: scale(1.08); }

    .yt__copy { min-width: 0; }

    .yt__stats {
      display: flex;
      gap: 2rem;
      margin: 1.75rem 0 2rem;
      flex-wrap: wrap;
    }

    .yt__stat strong {
      display: block;
      font-size: 1.45rem;
      letter-spacing: -0.02em;
      color: #fff;
    }

    .yt__stat span {
      font-size: 0.88rem;
      color: rgba(255, 255, 255, 0.62);
    }

    /* ---------- FAQ ---------- */
    .faq-wrap { max-width: 760px; margin: 0 auto; }

    .faq-item {
      border-bottom: 1px solid var(--line);
    }

    .faq-item summary {
      list-style: none;
      cursor: pointer;
      padding: 1.25rem 2.2rem 1.25rem 0;
      font-weight: 600;
      font-size: 1.02rem;
      position: relative;
      transition: none;
      color: #fff;
    }

    .faq-item summary::-webkit-details-marker { display: none; }

    .faq-item summary::after {
      content: '+';
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.35rem;
      font-weight: 400;
      color: var(--ink-soft);
      transition: none;
    }

    .faq-item[open] summary::after {
      content: '−';
      color: var(--accent);
    }

    .faq-item summary:hover { color: #6ea8fe; }

    .faq-item__body {
      padding: 0 0 1.35rem;
      color: var(--ink-soft);
      line-height: 1.7;
      font-size: 0.98rem;
      max-width: 62ch;
    }

    .faq-item__body a {
      color: #6ea8fe;
      text-decoration: none;
      font-weight: 600;
    }

    /* ---------- Newsletter ---------- */
    .news {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      align-items: center;
      padding: 2.5rem;
      background: linear-gradient(135deg, rgba(13, 110, 253, 0.16) 0%, rgba(20, 20, 20, 0.95) 45%), var(--surface);
      border: 1px solid rgba(13, 110, 253, 0.28);
      border-radius: 0.375rem;
    }

    .news__form {
      display: flex;
      gap: 0.65rem;
      flex-wrap: wrap;
    }

    .news__input {
      flex: 1 1 180px;
      min-width: 0;
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      padding: 0.85rem 1.2rem;
      font-family: inherit;
      font-size: 0.95rem;
      outline: none;
      transition: none;
      background: #0a0a0a;
      color: #fff;
    }

    .news__input::placeholder { color: rgba(255, 255, 255, 0.4); }

    .news__input:focus {
      border-color: var(--accent-border);
      box-shadow: 0 0 0 4px var(--accent-soft);
    }

    .news__msg {
      margin: 0.85rem 0 0;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.85);
    }

    .news__msg.is-ok { color: #7dd3a7; }
    .news__msg.is-err { color: #f0a0a0; }

    /* ---------- Footer ---------- */
    .site-footer {
      background: #000;
      color: rgba(255, 255, 255, 0.7);
      padding: 2.5rem 0;
      border-top: 1px solid var(--line);
    }

    .site-footer__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
    }

    .site-footer__brand img {
      height: 40px;
      width: auto;
    }

    .site-footer__copy {
      margin: 0;
      font-size: 0.88rem;
    }

    .site-footer__social {
      display: flex;
      gap: 0.85rem;
    }

    .site-footer__social a {
      color: rgba(255, 255, 255, 0.7);
      font-size: 1.1rem;
      transition: none;
      text-decoration: none;
    }

    .site-footer__social a:hover {
      color: #fff;
      transform: translateY(-2px);
    }

    /* ---------- Motion system ---------- */
    .reveal {
      opacity: 1;
      transform: none;
      transition: none;
    }

    .reveal.is-in {
      opacity: 1;
      transform: none;
    }

    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .reveal,
      .hero-anim > *,
      .hero__media,
      .preloader__logo {
        animation: none;
        transition: none;
        opacity: 1 !important;
        transform: none !important;
      }
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 991.98px) {
      .site-nav__inner { position: relative; }
      .site-nav__toggle { display: inline-flex; align-items: center; justify-content: center; }

      .site-nav__links {
        display: none;
        position: absolute;
        top: calc(100% + 0.25rem);
        left: 0;
        right: 0;
        flex-direction: column;
        align-items: stretch;
        gap: 0.15rem;
        padding: 0.65rem;
        background: rgba(12, 12, 12, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.09);
        border-radius: 0.375rem;
        box-shadow: 0 18px 40px -20px rgba(0, 0, 0, 0.7);
        max-height: min(70vh, 420px);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        z-index: 1100;
      }

      .site-nav__links.is-open { display: flex; }

      .site-nav__links li { padding: 0; width: 100%; }
      .site-nav__links a {
        display: flex;
        align-items: center;
        padding: 0.7rem 0.85rem;
        border-radius: 0.375rem;
      }
      .site-nav__links a:hover { background: rgba(255, 255, 255, 0.06); }

      .hero { min-height: auto; align-items: center; }
      .hero__inner { padding-top: 5.75rem; padding-bottom: 2.75rem; }
      .section { padding: 3.5rem 0; }

      .course-grid,
      .steps,
      .trust__grid {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
      }

      .yt,
      .news {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 767.98px) {
      .container-mech,
      .container-mech.site-nav__inner,
      .container-mech.hero__inner {
        padding-left: max(1.25rem, env(safe-area-inset-left, 0px));
        padding-right: max(1.25rem, env(safe-area-inset-right, 0px));
      }

      .site-nav__inner { padding-top: 0.7rem; padding-bottom: 0.7rem; }
      .site-nav__brand img { height: 28px; }

      .hero__inner { padding-top: 5.25rem; padding-bottom: 2.25rem; }
      .hero__title { font-size: clamp(1.85rem, 8vw, 2.4rem); }
      .section { padding: 2.75rem 0; }
      .section-head { margin-bottom: 1.75rem; }
      .section-head h2 { font-size: 1.55rem; }

      .course-grid,
      .steps,
      .trust__grid {
        grid-template-columns: 1fr;
      }

      .hero__actions { flex-direction: column; align-items: stretch; }
      .btn-mech { width: 100%; }

      .news { padding: 1.25rem; }
      .site-footer__inner { justify-content: center; text-align: center; }
      .site-footer__social { width: 100%; justify-content: center; }
    }

    @media (max-width: 479.98px) {
      .hero__inner { padding-top: 4.75rem; padding-bottom: 2rem; }
      .trust__item { padding: 1.1rem; }
      .course-card__body { padding: 1.15rem; }
    }

    /* Gutters: keep last so nav/hero never lose horizontal padding */
    .container-mech,
    .container-mech.site-nav__inner,
    .container-mech.hero__inner,
    .container-mech.site-footer__inner {
      padding-left: max(1.25rem, env(safe-area-inset-left, 0px)) !important;
      padding-right: max(1.25rem, env(safe-area-inset-right, 0px)) !important;
    }

    @media (min-width: 768px) {
      .container-mech,
      .container-mech.site-nav__inner,
      .container-mech.hero__inner,
      .container-mech.site-footer__inner {
        padding-left: max(1.5rem, env(safe-area-inset-left, 0px)) !important;
        padding-right: max(1.5rem, env(safe-area-inset-right, 0px)) !important;
      }
    }
  </style>
</head>

<body>
  <div id="preloader" role="status" aria-live="polite" aria-label="Carregando">
    <div class="preloader__inner">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy" class="preloader__logo">
      <div class="preloader__bar">
        <div class="preloader__bar__fill" id="preloaderFill"></div>
      </div>
      <p class="preloader__text"><span id="preloaderPct">0</span>%</p>
    </div>
  </div>

  <nav class="site-nav" id="siteNav">
    <div class="container-mech site-nav__inner">
      <a class="site-nav__brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
      </a>

      <button class="site-nav__toggle" type="button" id="navToggle" aria-label="Abrir menu" aria-expanded="false">
        <i class="bi bi-list"></i>
      </button>

      <ul class="site-nav__links" id="navLinks">
        <?php if ($isLoggedIn && $user): ?>
          <li><a href="<?= base_url($user->role === 'student' ? 'student/dashboard/inscricoes' : ($user->role === 'instructor' ? 'instructor/dashboard/meus_cursos' : $user->role . '/dashboard')) ?>">Meus Cursos</a></li>
          <li><a href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer">YouTube</a></li>
          <li>
            <a class="site-nav__user" href="<?= base_url($user->role . '/dashboard/perfil') ?>">
              <img src="<?= esc($userAvatarUrl) ?>" alt="" class="nav-avatar" onerror="this.onerror=null;this.src='<?= esc($defaultAvatarUrl) ?>';">
              <span><?= esc($user->username) ?></span>
            </a>
          </li>
        <?php else: ?>
          <li><a href="#cursos">Cursos</a></li>
          <li><a href="#como-funciona">Como funciona</a></li>
          <li><a href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer">YouTube</a></li>
          <li><a class="site-nav__cta" href="<?= base_url('login') ?>">Entrar</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <header class="hero">
    <div class="hero__media" aria-hidden="true"></div>
    <div class="hero__gradient" aria-hidden="true"></div>
    <div class="container-mech hero__inner">
      <div class="hero__content hero-anim">
        <p class="hero__brand">Mechanical Academy</p>
        <h1 class="hero__title">Aprenda a fazer do jeito <span>certo</span>.</h1>
        <p class="hero__lead">Cursos práticos, objectivos e sem enrolação.</p>
        <div class="hero__actions">
          <a href="#cursos" class="btn-mech btn-mech-primary">Explorar cursos</a>
          <a href="#como-funciona" class="btn-mech btn-mech-outline-invert">Como funciona</a>
        </div>
      </div>
    </div>
  </header>

  <section class="trust">
    <div class="container-mech">
      <div class="trust__grid">
        <div class="trust__item">
          <span class="trust__icon" aria-hidden="true"><i class="bi bi-journal-bookmark-fill"></i></span>
          <p class="trust__value"><?= max($courseCount, 3) ?>+</p>
          <p class="trust__label">Cursos activos</p>
        </div>
        <div class="trust__item">
          <span class="trust__icon" aria-hidden="true"><i class="bi bi-clock-fill"></i></span>
          <p class="trust__value">20+</p>
          <p class="trust__label">Horas de conteúdo</p>
        </div>
        <div class="trust__item">
          <span class="trust__icon" aria-hidden="true"><i class="bi bi-award-fill"></i></span>
          <p class="trust__value">Certificado</p>
          <p class="trust__label">Ao concluir cada curso</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="cursos">
    <div class="container-mech">
      <div class="section__head">
        <p class="section__eyebrow">Catálogo</p>
        <h2 class="section__title">Cursos</h2>
        <p class="section__lead">Escolha um curso e comece a aprender.</p>
      </div>

      <?php if (empty($courses)): ?>
        <p class="reveal" style="color:var(--ink-soft)">Em breve novos cursos.</p>
      <?php else: ?>
        <div class="course-grid">
          <?php foreach ($courses as $i => $course): ?>
            <?php
              $accent = trim((string) ($course->color_course ?? '')) ?: '#0d6efd';
              $iconPath = !empty($course->icon_course)
                  ? base_url('assets/img/' . $course->icon_course)
                  : base_url('assets/instructor/img/courses/' . ($course->image_course ?? ''));
              $freeLessons = (int) ($course->free_lessons ?? 0);
              $delay = ($i % 3) * 80;
            ?>
            <a
              href="<?= base_url('courses/' . $course->id_course) ?>"
              class="course-tile reveal"
              style="--tile-accent: <?= esc($accent) ?>; --d: <?= (int) $delay ?>ms"
            >
              <div class="course-tile__top">
                <div class="course-tile__meta">
                  <span class="pill pill-muted"><i class="bi bi-clock"></i> <?= esc($course->total_hours_label ?? '0 Horas') ?></span>
                  <?php if ($freeLessons > 0): ?>
                    <span class="pill"><?= $freeLessons ?> aulas grátis</span>
                  <?php endif; ?>
                </div>
                <img class="course-tile__icon" src="<?= esc($iconPath) ?>" alt="" loading="lazy">
              </div>
              <h3 class="course-tile__title"><?= esc($course->title_course) ?></h3>
              <div class="course-tile__price">
                <?php if (!empty($course->has_promo)): ?>
                  <span class="course-tile__price-was"><?= esc($formatMzn($course->list_price)) ?></span>
                  <span class="course-tile__price-now"><?= esc($formatMzn($course->effective_price)) ?></span>
                  <?php if (!empty($course->discount_percent)): ?>
                    <span class="pill pill-discount">−<?= (int) $course->discount_percent ?>% OFF</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="course-tile__price-now"><?= esc($formatMzn($course->effective_price ?? $course->price_course ?? 0)) ?></span>
                <?php endif; ?>
              </div>
              <?php if (!empty($course->has_promo) && (int) ($course->promo_remaining_seconds ?? 0) > 0): ?>
                <div class="course-tile__promo-time">
                  <i class="bi bi-hourglass-split"></i>
                  Oferta termina em
                  <span class="js-home-promo-countdown" data-left="<?= (int) $course->promo_remaining_seconds ?>">--:--:--</span>
                </div>
              <?php endif; ?>
              <span class="course-tile__cta">Ver curso <i class="bi bi-arrow-right"></i></span>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="section section-blue" id="como-funciona">
    <div class="container-mech">
      <div class="section__head">
        <p class="section__eyebrow">Processo</p>
        <h2 class="section__title">Como funciona</h2>
        <p class="section__lead">Do curso ao certificado, em 3 passos.</p>
      </div>

      <div class="steps">
        <div class="step">
          <div class="step__visual" aria-hidden="true"><i class="bi bi-journal-bookmark"></i></div>
          <span class="step__num"></span>
          <h3 class="step__title">Escolha o curso</h3>
          <p class="step__text">Veja o programa e as horas.</p>
        </div>
        <div class="step">
          <div class="step__visual" aria-hidden="true"><i class="bi bi-play-circle"></i></div>
          <span class="step__num"></span>
          <h3 class="step__title">Aprenda na prática</h3>
          <p class="step__text">Aulas objectivas e exercícios.</p>
        </div>
        <div class="step">
          <div class="step__visual" aria-hidden="true"><i class="bi bi-award"></i></div>
          <span class="step__num"></span>
          <h3 class="step__title">Receba o certificado</h3>
          <p class="step__text">Emita o certificado ao concluir.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-ink" id="youtube">
    <div class="container-mech">
      <div class="yt">
        <div class="yt__visual reveal">
          <img src="<?= base_url('assets/img/frame-youtube.png') ?>" alt="Mechanical Tecnologia no YouTube">
          <a class="yt__play" href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer" aria-label="Abrir canal no YouTube">
            <span class="yt__play-btn"><i class="bi bi-play-fill"></i></span>
          </a>
        </div>
        <div class="yt__copy">
          <p class="section__eyebrow">Comunidade</p>
          <h2 class="section__title">YouTube</h2>
          <p class="section__lead">Tutoriais gratuitos para complementar o curso.</p>
          <div class="yt__stats">
            <div class="yt__stat">
              <strong>2M+</strong>
              <span>Assinantes</span>
            </div>
            <div class="yt__stat">
              <strong>1000+</strong>
              <span>Tutoriais</span>
            </div>
          </div>
          <a href="https://www.youtube.com/@MechanicalTecnologia" class="btn-mech btn-mech-outline-invert" target="_blank" rel="noopener noreferrer">
            Ver canal <i class="bi bi-box-arrow-up-right"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="faq">
    <div class="container-mech">
      <div class="section__head text-center" style="max-width:36rem;margin-left:auto;margin-right:auto">
        <p class="section__eyebrow">FAQ</p>
        <h2 class="section__title">Perguntas frequentes</h2>
        <p class="section__lead">Respostas rápidas sobre a Academy.</p>
      </div>

      <div class="faq-wrap">
        <details class="faq-item reveal">
          <summary>O que é Mechanical Academy?</summary>
          <div class="faq-item__body">
            É a plataforma de ensino online da <a href="https://mechanical.co.mz" target="_blank" rel="noopener noreferrer">Mechanical Tecnologia</a>, com cursos exclusivos, conteúdos actualizados e suporte da nossa equipa.
          </div>
        </details>
        <details class="faq-item reveal" style="--d:60ms">
          <summary>Como me inscrevo num curso?</summary>
          <div class="faq-item__body">
            Abra a página do curso, clique em inscrever-se, preencha os dados e conclua o pagamento. O acesso é libertado após a confirmação.
          </div>
        </details>
        <details class="faq-item reveal" style="--d:120ms">
          <summary>Posso estudar no telemóvel?</summary>
          <div class="faq-item__body">
            Sim. A plataforma é responsiva — computador, tablet ou telemóvel, em qualquer navegador moderno.
          </div>
        </details>
        <details class="faq-item reveal" style="--d:180ms">
          <summary>Os cursos têm certificado?</summary>
          <div class="faq-item__body">
            Sim. Ao concluir o curso e as actividades obrigatórias, pode emitir o certificado digital na plataforma.
          </div>
        </details>
        <details class="faq-item reveal" style="--d:240ms">
          <summary>Como funciona o suporte?</summary>
          <div class="faq-item__body">
            Tem área de mensagens e fórum para tirar dúvidas com instrutores e interagir com outros alunos. Também pode contactar via WhatsApp nos cursos que o disponibilizam.
          </div>
        </details>
        <details class="faq-item reveal" style="--d:300ms">
          <summary>Que formas de pagamento aceitam?</summary>
          <div class="faq-item__body">
            Aceitamos M-Pesa, transferência e contacto via WhatsApp. Os detalhes aparecem no checkout de cada curso.
          </div>
        </details>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:0">
    <div class="container-mech">
      <div class="news">
        <div>
          <p class="section__eyebrow">Novidades</p>
          <h2 class="section__title" style="font-size:1.55rem">Newsletter</h2>
          <p class="section__lead">Avisos de novos cursos.</p>
        </div>
        <div>
          <form class="news__form" id="newsletterForm" action="<?= site_url('newsletter/subscribe') ?>" method="post">
            <input class="news__input" type="email" name="email" placeholder="O seu email" aria-label="Email" required autocomplete="email">
            <button class="btn-mech btn-mech-dark" type="submit" id="newsletterBtn">Notificar-me</button>
          </form>
          <p class="news__msg" id="newsletterMsg" hidden role="status"></p>
        </div>
      </div>
    </div>
  </section>

  <footer class="site-footer">
    <div class="container-mech site-footer__inner">
      <a class="site-footer__brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
      </a>
      <p class="site-footer__copy">&copy; <?= date('Y') ?> Mechanical Academy · +258 84 272 6761</p>
      <div class="site-footer__social">
        <a href="https://wa.me/258842726761" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
        <a href="https://www.youtube.com/@MechanicalTecnologia" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </footer>

  <script>
    (function () {
      const nav = document.getElementById('siteNav');
      const toggle = document.getElementById('navToggle');
      const links = document.getElementById('navLinks');

      const onScroll = () => {
        if (!nav) return;
        nav.classList.toggle('is-scrolled', window.scrollY > 12);
      };
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });

      if (toggle && links) {
        toggle.addEventListener('click', () => {
          const open = links.classList.toggle('is-open');
          toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
          toggle.innerHTML = open ? '<i class="bi bi-x-lg"></i>' : '<i class="bi bi-list"></i>';
        });
        links.querySelectorAll('a').forEach((a) => {
          a.addEventListener('click', () => {
            links.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.innerHTML = '<i class="bi bi-list"></i>';
          });
        });
      }

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

      // Preloader
      const preloader = document.getElementById('preloader');
      const fillEl = document.getElementById('preloaderFill');
      const pctEl = document.getElementById('preloaderPct');
      if (!preloader || !fillEl || !pctEl) return;

      const MIN = 180;
      const start = Date.now();
      let target = 8;
      let current = 0;
      let done = false;
      let raf = 0;

      document.documentElement.style.overflow = 'hidden';

      const render = () => {
        const pct = Math.round(current);
        fillEl.style.width = pct + '%';
        pctEl.textContent = String(pct);
      };

      const tick = () => {
        current += (target - current) * 0.14;
        render();
        if (!done) raf = requestAnimationFrame(tick);
      };

      const bump = (n) => { target = Math.min(100, target + n); };

      if (document.readyState !== 'loading') bump(25);
      else document.addEventListener('DOMContentLoaded', () => bump(25), { once: true });

      if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => bump(15)).catch(() => bump(10));
      } else bump(10);

      const imgs = Array.from(document.images || []);
      let loaded = 0;
      const onImg = () => {
        loaded += 1;
        const frac = imgs.length ? loaded / imgs.length : 1;
        target = Math.max(target, 40 + Math.round(45 * frac));
      };
      if (!imgs.length) bump(45);
      else imgs.forEach((img) => {
        if (img.complete) onImg();
        else {
          img.addEventListener('load', onImg, { once: true });
          img.addEventListener('error', onImg, { once: true });
        }
      });

      const hide = () => {
        if (done) return;
        done = true;
        target = 100;
        current = 100;
        render();
        preloader.classList.add('is-hidden');
        document.documentElement.style.overflow = '';
        if (raf) cancelAnimationFrame(raf);
        setTimeout(() => { preloader.style.display = 'none'; }, 450);
      };

      const force = setTimeout(hide, MIN + 400);
      window.addEventListener('load', () => {
        bump(20);
        clearTimeout(force);
        const wait = Math.max(0, MIN - (Date.now() - start));
        setTimeout(hide, wait);
      }, { once: true });

      raf = requestAnimationFrame(tick);
    })();

    (function () {
      const nodes = document.querySelectorAll('.js-home-promo-countdown');
      if (!nodes.length) return;
      const pad = (n) => String(n).padStart(2, '0');
      const fmt = (secs) => {
        const d = Math.floor(secs / 86400);
        const h = Math.floor((secs % 86400) / 3600);
        const m = Math.floor((secs % 3600) / 60);
        const s = secs % 60;
        if (d > 0) return d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s);
        return pad(h) + ':' + pad(m) + ':' + pad(s);
      };
      nodes.forEach((el) => {
        let left = parseInt(el.getAttribute('data-left') || '0', 10);
        const tick = () => {
          el.textContent = fmt(Math.max(0, left));
          if (left <= 0) return;
          left -= 1;
          setTimeout(tick, 1000);
        };
        tick();
      });
    })();

    (function () {
      const form = document.getElementById('newsletterForm');
      const msg = document.getElementById('newsletterMsg');
      const btn = document.getElementById('newsletterBtn');
      if (!form || !msg || !btn) return;

      const show = (text, ok) => {
        msg.hidden = false;
        msg.textContent = text;
        msg.classList.toggle('is-ok', !!ok);
        msg.classList.toggle('is-err', !ok);
      };

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = (form.email?.value || '').trim();
        if (!email) {
          show('Indique um email válido.', false);
          return;
        }

        btn.disabled = true;
        const prev = btn.textContent;
        btn.textContent = 'A enviar…';

        try {
          const body = new FormData(form);
          const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body,
          });
          const data = await res.json().catch(() => ({}));
          if (res.ok && data.ok) {
            show(data.message || 'Subscrição registada.', true);
            form.reset();
          } else {
            show(data.message || 'Não foi possível registar. Tente novamente.', false);
          }
        } catch (_) {
          show('Falha de ligação. Tente novamente.', false);
        } finally {
          btn.disabled = false;
          btn.textContent = prev;
        }
      });
    })();
  </script>
  <script>window.ANALYTICS_COLLECT_URL = <?= json_encode(site_url('analytics/collect')) ?>;</script>
  <script src="<?= base_url('assets/js/analytics-tracker.js') ?>" defer></script>
  <?= view('partials/posthog') ?>
</body>

</html>
