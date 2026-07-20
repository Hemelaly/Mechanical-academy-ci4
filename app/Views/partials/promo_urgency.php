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

$urgencyCopy = $d >= 2
    ? 'Ainda tem alguns dias — mas o preço volta ao normal quando o tempo acabar.'
    : ($d === 1
        ? 'Falta menos de 2 dias. Garanta o desconto antes que expire.'
        : ($h >= 6
            ? 'Últimas horas desta promoção. O preço normal regressa em breve.'
            : 'Atenção: a promoção está a acabar. Não perca o preço especial.'));
?>
<style>
  .promo-urgency-bar {
    position: sticky;
    top: 0;
    z-index: 1100;
    background: linear-gradient(90deg, #0a58ca 0%, #0d6efd 45%, #3d8bfd 100%);
    color: #fff;
    text-align: center;
    padding: 0.65rem 1rem;
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
    gap: 0.45rem 0.75rem;
  }
  .promo-urgency-bar strong {
    font-variant-numeric: tabular-nums;
    font-weight: 700;
  }
  body.has-promo-urgency .site-nav,
  body.has-promo-urgency .topbar {
    top: 2.65rem;
  }
  .promo-popup-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1200;
    background: rgba(0, 0, 0, 0.72);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    opacity: 0;
    transition: opacity 0.28s ease;
  }
  .promo-popup-backdrop.is-open {
    display: flex;
    opacity: 1;
  }
  .promo-popup {
    width: 100%;
    max-width: 440px;
    background: linear-gradient(180deg, #1a1a1a 0%, #121212 100%);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 22px;
    padding: 1.85rem 1.5rem 1.5rem;
    color: #fff;
    font-family: 'Sora', sans-serif;
    text-align: center;
    box-shadow: 0 40px 80px -40px rgba(0,0,0,0.9), 0 0 0 1px rgba(13,110,253,0.12);
    transform: translateY(12px) scale(0.98);
    transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1);
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
    background: rgba(13, 110, 253, 0.18);
    border: 1px solid rgba(13, 110, 253, 0.4);
    color: #9ec5fe;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-bottom: 0.9rem;
  }
  .promo-popup h3 {
    margin: 0 0 0.45rem;
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.25;
  }
  .promo-popup__lead {
    margin: 0 0 1.15rem;
    color: rgba(255,255,255,0.68);
    font-size: 0.92rem;
    line-height: 1.55;
  }
  .promo-popup__units {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.45rem;
    margin-bottom: 0.85rem;
  }
  .promo-popup__unit {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 0.65rem 0.25rem 0.55rem;
  }
  .promo-popup__unit strong {
    display: block;
    font-size: 1.35rem;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
    color: #6ea8fe;
    letter-spacing: 0.02em;
    line-height: 1.1;
  }
  .promo-popup__unit span {
    display: block;
    margin-top: 0.2rem;
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.45);
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
    margin-bottom: 1.2rem;
  }
  .promo-popup__prices .now {
    font-size: 1.45rem;
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
    background: rgba(13,110,253,0.2);
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
    border-radius: 999px;
    padding: 0.75rem 1.35rem;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    border: 0;
    cursor: pointer;
    font-family: inherit;
  }
  .promo-popup .btn-primary {
    background: #0d6efd;
    color: #fff;
  }
  .promo-popup .btn-primary:hover { background: #0b5ed7; color: #fff; }
  .promo-popup .btn-ghost {
    background: transparent;
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.18);
  }
  @media (max-width: 420px) {
    .promo-popup__unit strong { font-size: 1.1rem; }
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
    <h3 id="promoPopupTitle"><?= $discountPercent > 0 ? 'Desconto de ' . $discountPercent . '% a expirar' : 'Promoção a expirar' ?></h3>
    <p class="promo-popup__lead"><?= esc($urgencyCopy) ?></p>

    <div class="promo-popup__units" aria-label="Tempo restante">
      <div class="promo-popup__unit"><strong id="promoUnitD"><?= (int) $d ?></strong><span>Dias</span></div>
      <div class="promo-popup__unit"><strong id="promoUnitH"><?= str_pad((string) $h, 2, '0', STR_PAD_LEFT) ?></strong><span>Horas</span></div>
      <div class="promo-popup__unit"><strong id="promoUnitM"><?= str_pad((string) $m, 2, '0', STR_PAD_LEFT) ?></strong><span>Min</span></div>
      <div class="promo-popup__unit"><strong id="promoUnitS"><?= str_pad((string) $s, 2, '0', STR_PAD_LEFT) ?></strong><span>Seg</span></div>
    </div>

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
    if (unitD) unitD.textContent = String(d);
    if (unitH) unitH.textContent = pad(h);
    if (unitM) unitM.textContent = pad(m);
    if (unitS) unitS.textContent = pad(s);
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
  };
  const closePopup = () => {
    if (!backdrop) return;
    backdrop.classList.remove('is-open');
    backdrop.setAttribute('aria-hidden', 'true');
    setTimeout(() => { backdrop.style.display = 'none'; }, 280);
    try { sessionStorage.setItem(storageKey, '1'); } catch (e) {}
  };

  closeBtn?.addEventListener('click', closePopup);
  backdrop?.addEventListener('click', (e) => { if (e.target === backdrop) closePopup(); });
  cta?.addEventListener('click', () => { try { sessionStorage.setItem(storageKey, '1'); } catch (e) {} });

  let shown = false;
  try { shown = sessionStorage.getItem(storageKey) === '1'; } catch (e) {}
  if (!shown) setTimeout(openPopup, 1200);
})();
</script>
