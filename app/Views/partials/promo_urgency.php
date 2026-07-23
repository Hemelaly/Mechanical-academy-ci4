<?php
/**
 * Urgência de promoção: barra superior + popup.
 *
 * @var bool   $hasPromo
 * @var int    $promoRemainingSeconds
 * @var int    $discountPercent
 * @var string|null $promoEndsAt
 * @var string $promoCtaHref
 * @var string $promoCtaLabel
 * @var float|null $listPrice
 * @var float|null $promoPrice
 * @var string|null $courseTitle
 */
$hasPromo = !empty($hasPromo);
$promoRemainingSeconds = (int) ($promoRemainingSeconds ?? 0);
$discountPercent = (int) ($discountPercent ?? 0);
if (! $hasPromo || $promoRemainingSeconds <= 0) {
    return;
}

$popupKey = 'promo_popup_' . md5((string) ($promoEndsAt ?? '') . '_' . (string) ($discountPercent ?? 0));
$promoCtaHref = (string) ($promoCtaHref ?? '#');
$promoCtaLabel = (string) ($promoCtaLabel ?? 'Garantir oferta');
$listPrice = isset($listPrice) ? (float) $listPrice : null;
$promoPrice = isset($promoPrice) ? (float) $promoPrice : null;
$courseTitle = trim((string) ($courseTitle ?? ''));

$endsLabel = '';
if (! empty($promoEndsAt)) {
    $ts = strtotime((string) $promoEndsAt);
    if ($ts) {
        $endsLabel = date('d/m/Y \à\s H:i', $ts);
    }
}

$d = intdiv($promoRemainingSeconds, 86400);
$h = intdiv($promoRemainingSeconds % 86400, 3600);
$m = intdiv($promoRemainingSeconds % 3600, 60);
$s = $promoRemainingSeconds % 60;
?>
<style>
  :root { --promo-bar-h: 2.65rem; }
  .promo-urgency-bar {
    position: sticky;
    top: 0;
    z-index: 1100;
    background: linear-gradient(105deg, #0a58ca 0%, #0d6efd 48%, #4ea1ff 100%);
    color: #fff;
    text-align: center;
    padding: 0.55rem 0.85rem;
    padding-top: calc(0.55rem + env(safe-area-inset-top, 0px));
    font-family: 'Sora', sans-serif;
    font-size: 0.88rem;
    font-weight: 600;
    letter-spacing: -0.01em;
    box-shadow: 0 10px 28px -18px rgba(13, 110, 253, 0.85);
  }
  .promo-urgency-bar__inner {
    display: inline-flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.35rem 0.65rem;
  }
  .promo-urgency-bar strong {
    font-variant-numeric: tabular-nums;
    font-weight: 700;
    background: rgba(255,255,255,0.16);
    border: 1px solid rgba(255,255,255,0.22);
    border-radius: 999px;
    padding: 0.15rem 0.65rem;
    letter-spacing: 0.04em;
  }
  body.has-promo-urgency .site-nav,
  body.has-promo-urgency .topbar { top: var(--promo-bar-h); }

  .promo-popup-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1200;
    background: rgba(0, 0, 0, 0.78);
    backdrop-filter: blur(6px);
    display: none;
    align-items: center;
    justify-content: center;
    padding: max(1rem, env(safe-area-inset-top, 0px)) 1rem max(1rem, env(safe-area-inset-bottom, 0px));
    opacity: 0;
    transition: opacity .28s ease;
  }
  .promo-popup-backdrop.is-open {
    display: flex;
    opacity: 1;
  }
  .promo-popup {
    width: 100%;
    max-width: 440px;
    max-height: min(92vh, 640px);
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    background:
      radial-gradient(520px 220px at 50% -10%, rgba(13,110,253,0.35) 0%, transparent 60%),
      #12151c;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 0.5rem;
    padding: 1.6rem 1.3rem 1.35rem;
    color: #fff;
    font-family: 'Sora', sans-serif;
    text-align: center;
    box-shadow: 0 30px 70px -28px rgba(0,0,0,0.85);
    transform: translateY(14px) scale(0.97);
    transition: transform .32s cubic-bezier(.2,.8,.2,1);
  }
  .promo-popup-backdrop.is-open .promo-popup {
    transform: translateY(0) scale(1);
  }
  .promo-popup__badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.28rem 0.75rem;
    border-radius: 999px;
    background: rgba(239, 68, 68, 0.18);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #fda4af;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 0.85rem;
    animation: promoBadgePulse 1.8s ease-in-out infinite;
  }
  @keyframes promoBadgePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.25); }
    50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
  }
  .promo-popup h3 {
    margin: 0 0 1.1rem;
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: -0.03em;
    line-height: 1.2;
  }
  .promo-popup__lead { display: none; }

  .promo-popup__units {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.5rem;
    margin-bottom: 1rem;
  }
  .promo-popup__unit {
    position: relative;
    overflow: hidden;
    background: linear-gradient(180deg, rgba(255,255,255,0.07) 0%, rgba(255,255,255,0.02) 100%);
    border: 1px solid rgba(110,168,254,0.28);
    border-radius: 0.45rem;
    padding: 0.75rem 0.25rem 0.6rem;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
  }
  .promo-popup__unit::before {
    content: '';
    position: absolute;
    left: 8%;
    right: 8%;
    top: 48%;
    height: 1px;
    background: rgba(255,255,255,0.08);
    pointer-events: none;
  }
  .promo-popup__digit {
    display: block;
    font-size: clamp(1.25rem, 4.5vw, 1.55rem);
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    color: #8bb8ff;
    letter-spacing: 0.03em;
    line-height: 1.1;
    transition: transform .28s cubic-bezier(.2,.8,.2,1), color .2s ease, text-shadow .2s ease;
  }
  .promo-popup__digit.is-tick {
    transform: translateY(-4px) scale(1.08);
    color: #fff;
    text-shadow: 0 0 18px rgba(110,168,254,0.55);
  }
  .promo-popup__unit span {
    display: block;
    margin-top: 0.35rem;
    font-size: 0.62rem;
    font-weight: 650;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.48);
  }
  .promo-popup__seconds-ring {
    margin: 0 auto 1rem;
    width: 64px;
    height: 4px;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    overflow: hidden;
  }
  .promo-popup__seconds-ring > i {
    display: block;
    height: 100%;
    width: 100%;
    transform-origin: left center;
    background: linear-gradient(90deg, #0d6efd, #6ea8fe);
    animation: promoSecondSweep 1s linear infinite;
  }
  @keyframes promoSecondSweep {
    from { transform: scaleX(0); }
    to { transform: scaleX(1); }
  }

  .promo-popup__ends {
    margin: 0 0 1rem;
    font-size: 0.78rem;
    color: rgba(255,255,255,0.45);
  }
  .promo-popup__prices {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.55rem;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
  }
  .promo-popup__prices .now {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
  }
  .promo-popup__prices .was {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.4);
    text-decoration: line-through;
  }
  .promo-popup__prices .off {
    font-size: 0.75rem;
    font-weight: 700;
    color: #9ec5fe;
    background: rgba(13,110,253,0.22);
    border-radius: 999px;
    padding: 0.2rem 0.55rem;
  }
  .promo-popup .actions {
    display: flex;
    gap: 0.55rem;
    justify-content: center;
    flex-wrap: wrap;
  }
  .promo-popup button,
  .promo-popup a {
    border-radius: 0.375rem;
    padding: 0.8rem 1.35rem;
    font-weight: 650;
    font-size: 0.9rem;
    text-decoration: none;
    border: 0;
    cursor: pointer;
    font-family: inherit;
  }
  .promo-popup .btn-primary {
    background: linear-gradient(180deg, #3d8bfd 0%, #0d6efd 100%);
    color: #fff;
    box-shadow: 0 12px 28px -14px rgba(13,110,253,0.9);
  }
  .promo-popup .btn-primary:hover { filter: brightness(1.06); color: #fff; }
  .promo-popup .btn-ghost {
    background: transparent;
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.18);
  }
  @media (max-width: 575.98px) {
    .promo-urgency-bar { font-size: 0.8rem; }
    .promo-popup .actions { flex-direction: column; align-items: stretch; }
    .promo-popup .actions .btn-primary,
    .promo-popup .actions .btn-ghost { width: 100%; text-align: center; }
  }
  @media (prefers-reduced-motion: reduce) {
    .promo-popup__badge,
    .promo-popup__seconds-ring > i,
    .promo-popup__digit { animation: none !important; transition: none !important; }
  }
</style>

<div class="promo-urgency-bar" id="promoUrgencyBar" role="status" aria-live="polite">
  <span class="promo-urgency-bar__inner">
    <span>Oferta<?= $discountPercent > 0 ? ' (−' . $discountPercent . '%)' : '' ?> termina em</span>
    <strong id="promoBarCountdown" data-promo-left="<?= (int) $promoRemainingSeconds ?>">--:--:--</strong>
  </span>
</div>

<div class="promo-popup-backdrop" id="promoPopup" aria-hidden="true">
  <div class="promo-popup" role="dialog" aria-modal="true" aria-labelledby="promoPopupTitle">
    <div class="promo-popup__badge">Tempo limitado</div>
    <h3 id="promoPopupTitle"><?= $discountPercent > 0 ? '−' . $discountPercent . '% OFF' : 'Promoção' ?></h3>

    <div class="promo-popup__units" aria-label="Tempo restante">
      <div class="promo-popup__unit"><strong class="promo-popup__digit" id="promoUnitD"><?= (int) $d ?></strong><span>Dias</span></div>
      <div class="promo-popup__unit"><strong class="promo-popup__digit" id="promoUnitH"><?= str_pad((string) $h, 2, '0', STR_PAD_LEFT) ?></strong><span>Horas</span></div>
      <div class="promo-popup__unit"><strong class="promo-popup__digit" id="promoUnitM"><?= str_pad((string) $m, 2, '0', STR_PAD_LEFT) ?></strong><span>Min</span></div>
      <div class="promo-popup__unit"><strong class="promo-popup__digit" id="promoUnitS"><?= str_pad((string) $s, 2, '0', STR_PAD_LEFT) ?></strong><span>Seg</span></div>
    </div>
    <div class="promo-popup__seconds-ring" aria-hidden="true"><i></i></div>

    <?php if ($endsLabel !== ''): ?>
      <p class="promo-popup__ends">Válida até <?= esc($endsLabel) ?></p>
    <?php endif; ?>

    <?php if ($promoPrice !== null && $promoPrice > 0): ?>
      <div class="promo-popup__prices">
        <span class="now"><?= number_format($promoPrice, 0, ',', '.') ?> MZN</span>
        <?php if ($listPrice !== null && $listPrice > $promoPrice): ?>
          <span class="was"><?= number_format($listPrice, 0, ',', '.') ?> MZN</span>
        <?php endif; ?>
        <?php if ($discountPercent > 0): ?>
          <span class="off">−<?= $discountPercent ?>%</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="actions">
      <a class="btn-primary" href="<?= esc($promoCtaHref) ?>" id="promoPopupCta"><?= esc($promoCtaLabel) ?></a>
      <button type="button" class="btn-ghost" id="promoPopupClose">Agora não</button>
    </div>
  </div>
</div>

<script>
(function () {
  document.body.classList.add('has-promo-urgency');
  let left = <?= (int) $promoRemainingSeconds ?>;
  const barRoot = document.getElementById('promoUrgencyBar');
  const barEl = document.getElementById('promoBarCountdown');
  const unitD = document.getElementById('promoUnitD');
  const unitH = document.getElementById('promoUnitH');
  const unitM = document.getElementById('promoUnitM');
  const unitS = document.getElementById('promoUnitS');
  const backdrop = document.getElementById('promoPopup');
  const closeBtn = document.getElementById('promoPopupClose');
  const cta = document.getElementById('promoPopupCta');
  const storageKey = <?= json_encode($popupKey) ?>;
  const pad = (n) => String(n).padStart(2, '0');
  const prev = { d: null, h: null, m: null, s: null };

  const syncPromoBarHeight = () => {
    if (!barRoot) return;
    const h = Math.ceil(barRoot.getBoundingClientRect().height);
    if (h > 0) document.documentElement.style.setProperty('--promo-bar-h', h + 'px');
  };
  syncPromoBarHeight();
  window.addEventListener('resize', syncPromoBarHeight, { passive: true });
  if (typeof ResizeObserver !== 'undefined' && barRoot) {
    new ResizeObserver(syncPromoBarHeight).observe(barRoot);
  }

  const tickDigit = (el, value) => {
    if (!el) return;
    const next = String(value);
    if (el.textContent !== next) {
      el.textContent = next;
      el.classList.remove('is-tick');
      void el.offsetWidth;
      el.classList.add('is-tick');
      setTimeout(() => el.classList.remove('is-tick'), 280);
    }
  };

  const fmtBar = (secs) => {
    const d = Math.floor(secs / 86400);
    const h = Math.floor((secs % 86400) / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = secs % 60;
    if (d > 0) return d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s);
    return pad(h) + ':' + pad(m) + ':' + pad(s);
  };

  const render = () => {
    const secs = Math.max(0, left);
    const d = Math.floor(secs / 86400);
    const h = Math.floor((secs % 86400) / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = secs % 60;
    const barText = fmtBar(secs);
    if (barEl) barEl.textContent = barText;
    tickDigit(unitD, d);
    tickDigit(unitH, pad(h));
    tickDigit(unitM, pad(m));
    tickDigit(unitS, pad(s));
    document.querySelectorAll('.js-promo-inline-countdown').forEach((el) => {
      el.textContent = barText;
    });
  };

  const tick = () => {
    if (left <= 0) {
      render();
      window.location.reload();
      return;
    }
    render();
    left -= 1;
  };

  tick();
  setInterval(tick, 1000);

  const openPopup = () => {
    if (!backdrop) return;
    backdrop.style.display = 'flex';
    requestAnimationFrame(() => backdrop.classList.add('is-open'));
    backdrop.setAttribute('aria-hidden', 'false');
    document.documentElement.style.overflow = 'hidden';
  };

  const closePopup = () => {
    if (!backdrop) return;
    backdrop.classList.remove('is-open');
    backdrop.setAttribute('aria-hidden', 'true');
    document.documentElement.style.overflow = '';
    setTimeout(() => { backdrop.style.display = 'none'; }, 280);
    try { sessionStorage.setItem(storageKey, '1'); } catch (e) {}
  };

  closeBtn?.addEventListener('click', closePopup);
  backdrop?.addEventListener('click', (e) => { if (e.target === backdrop) closePopup(); });

  cta?.addEventListener('click', (e) => {
    closePopup();
    const href = cta.getAttribute('href') || '';
    if (href.startsWith('#')) {
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        setTimeout(() => {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          try { target.focus({ preventScroll: true }); } catch (err) {}
        }, 120);
      }
    }
  });

  let shown = false;
  try { shown = sessionStorage.getItem(storageKey) === '1'; } catch (e) {}
  if (!shown) setTimeout(openPopup, 900);
})();
</script>
