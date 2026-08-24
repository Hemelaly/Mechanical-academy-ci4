<?php
/**
 * Painel promocional lateral (home) — estilo marketing / oferta.
 *
 * @var object $promoFeatured Curso com has_promo, list_price, promo_price, discount_percent, etc.
 */
$course = $promoFeatured ?? null;
if (! $course || empty($course->has_promo)) {
    return;
}

$courseId = (int) ($course->id_course ?? 0);
$listPrice = (float) ($course->list_price ?? 0);
$promoPrice = (float) ($course->promo_price ?? 0);
$discountPercent = (int) ($course->discount_percent ?? 0);
$remaining = (int) ($course->promo_remaining_seconds ?? 0);
$endsAt = (string) ($course->promo_ends_at ?? '');
$title = trim((string) ($course->title_course ?? 'Curso'));
$image = ! empty($course->image_course)
    ? base_url('assets/instructor/img/courses/' . $course->image_course)
    : base_url('assets/img/logo.png');
$checkoutUrl = site_url('checkout/' . $courseId);
$courseUrl = site_url('courses/' . $courseId);
$popupKey = 'ma_promo_side_' . md5($courseId . '_' . $endsAt . '_' . $promoPrice);

$fmt = static function (float $n): string {
    return number_format($n, 0, ',', '.');
};
?>
<style>
  .promo-side {
    --ps-bg: #0a0e16;
    --ps-card: #111827;
    --ps-border: rgba(78, 161, 255, 0.35);
    --ps-accent: #0d6efd;
    --ps-text: #f8fafc;
    --ps-muted: rgba(248, 250, 252, 0.68);
    position: fixed;
    z-index: 1200;
    inset: auto 1rem 1rem auto;
    width: min(22.5rem, calc(100vw - 1.5rem));
    max-height: calc(100vh - 2rem);
    display: flex;
    flex-direction: column;
    background: var(--ps-bg);
    border: 1px solid var(--ps-border);
    border-radius: 0.75rem;
    box-shadow: 0 28px 60px -28px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(13, 110, 253, 0.08);
    color: var(--ps-text);
    font-family: 'Sora', system-ui, sans-serif;
    overflow: hidden;
    opacity: 0;
    transform: translateY(1rem) scale(0.98);
    pointer-events: none;
    transition: opacity 0.35s ease, transform 0.35s ease;
  }

  .promo-side.is-open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
  }

  .promo-side__media {
    position: relative;
    height: 9.5rem;
    flex-shrink: 0;
    background: #05070b;
    overflow: hidden;
  }

  .promo-side__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .promo-side__media::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, transparent 35%, rgba(10, 14, 22, 0.92) 100%);
  }

  .promo-side__close {
    position: absolute;
    top: 0.65rem;
    right: 0.65rem;
    z-index: 2;
    width: 2rem;
    height: 2rem;
    border: 0;
    border-radius: 999px;
    background: rgba(0, 0, 0, 0.55);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    line-height: 1;
  }

  .promo-side__close:hover {
    background: rgba(0, 0, 0, 0.75);
  }

  .promo-side__body {
    padding: 0 1.15rem 1.15rem;
    overflow-y: auto;
    flex: 1 1 auto;
    min-height: 0;
  }

  .promo-side__badge {
    display: inline-flex;
    align-items: center;
    margin-top: -0.35rem;
    margin-bottom: 0.65rem;
    padding: 0.28rem 0.7rem;
    border-radius: 999px;
    background: var(--ps-accent);
    color: #fff;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
  }

  .promo-side__title {
    margin: 0 0 0.45rem;
    font-size: 1.28rem;
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: -0.02em;
  }

  .promo-side__lead {
    margin: 0 0 1rem;
    font-size: 0.86rem;
    line-height: 1.5;
    color: var(--ps-muted);
  }

  .promo-side__lead strong {
    color: #fff;
    font-weight: 600;
  }

  .promo-side__cards {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.55rem;
    margin-bottom: 1rem;
  }

  .promo-side__card {
    position: relative;
    padding: 0.75rem 0.7rem 0.7rem;
    border-radius: 0.5rem;
    background: var(--ps-card);
    border: 1px solid rgba(255, 255, 255, 0.08);
  }

  .promo-side__card--best {
    border-color: rgba(13, 110, 253, 0.55);
    background: linear-gradient(165deg, rgba(13, 110, 253, 0.18) 0%, #111827 55%);
  }

  .promo-side__card-label {
    display: block;
    margin-bottom: 0.35rem;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #6ea8fe;
  }

  .promo-side__card-price {
    display: block;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.2;
  }

  .promo-side__card-sub {
    display: block;
    margin-top: 0.2rem;
    font-size: 0.72rem;
    color: var(--ps-muted);
  }

  .promo-side__card-sub s {
    opacity: 0.75;
  }

  .promo-side__cta {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 0.85rem 1rem;
    border-radius: 0.5rem;
    background: var(--ps-accent);
    color: #fff !important;
    font-size: 0.95rem;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid var(--ps-accent);
    margin-bottom: 0.65rem;
  }

  .promo-side__cta:hover {
    filter: brightness(1.06);
    color: #fff !important;
  }

  .promo-side__links {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    text-align: center;
  }

  .promo-side__links a {
    color: rgba(248, 250, 252, 0.88);
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: underline;
    text-underline-offset: 0.15em;
  }

  .promo-side__links a:hover {
    color: #fff;
  }

  .promo-side__timer {
    margin-top: 0.85rem;
    padding-top: 0.75rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 0.75rem;
    color: var(--ps-muted);
    text-align: center;
  }

  .promo-side__timer strong {
    color: #fff;
    font-variant-numeric: tabular-nums;
  }

  .promo-side-backdrop {
    display: none;
  }

  @media (max-width: 767.98px) {
    .promo-side-backdrop {
      display: block;
      position: fixed;
      inset: 0;
      z-index: 1190;
      background: rgba(0, 0, 0, 0.55);
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }

    .promo-side-backdrop.is-open {
      opacity: 1;
      pointer-events: auto;
    }

    .promo-side {
      inset: auto 0.75rem 0.75rem 0.75rem;
      width: auto;
      max-height: min(88vh, 36rem);
      border-radius: 0.85rem;
    }

    .promo-side__media {
      height: 8rem;
    }

    .promo-side__title {
      font-size: 1.15rem;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .promo-side,
    .promo-side-backdrop {
      transition: none !important;
    }
  }
</style>

<div class="promo-side-backdrop" id="promoSideBackdrop" aria-hidden="true"></div>
<aside class="promo-side" id="promoSidePanel" role="dialog" aria-modal="true" aria-labelledby="promoSideTitle" aria-hidden="true">
  <div class="promo-side__media">
    <img src="<?= esc($image) ?>" alt="" loading="lazy" width="360" height="160">
    <button type="button" class="promo-side__close" id="promoSideClose" aria-label="Fechar promoção">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
  <div class="promo-side__body">
    <span class="promo-side__badge">
      <?= $discountPercent > 0 ? '−' . $discountPercent . '% OFF' : 'Preços baixaram' ?>
    </span>
    <h2 class="promo-side__title" id="promoSideTitle">
      Acesso completo a partir de <?= esc($fmt($promoPrice)) ?> MZN
    </h2>
    <p class="promo-side__lead">
      <strong><?= esc($title) ?></strong>
      <?php if ($listPrice > $promoPrice): ?>
        — estava a <?= esc($fmt($listPrice)) ?> MZN. Aproveite a oferta<?= $remaining > 0 ? ' enquanto dura' : '' ?>.
      <?php else: ?>
        — preço especial por tempo limitado.
      <?php endif; ?>
    </p>

    <div class="promo-side__cards">
      <div class="promo-side__card promo-side__card--best">
        <span class="promo-side__card-label">Melhor preço</span>
        <span class="promo-side__card-price"><?= esc($fmt($promoPrice)) ?> MZN</span>
        <span class="promo-side__card-sub">
          <?= $discountPercent > 0 ? 'Poupa ' . $discountPercent . '%' : 'Oferta activa' ?>
        </span>
      </div>
      <div class="promo-side__card">
        <span class="promo-side__card-label">Preço normal</span>
        <span class="promo-side__card-price"><s><?= esc($fmt($listPrice)) ?> MZN</s></span>
        <span class="promo-side__card-sub">Sem promoção</span>
      </div>
    </div>

    <a class="promo-side__cta" href="<?= esc($checkoutUrl) ?>" id="promoSideCta">Garantir oferta</a>
    <div class="promo-side__links">
      <a href="<?= esc($courseUrl) ?>">Ver detalhes do curso</a>
    </div>

    <?php if ($remaining > 0): ?>
      <p class="promo-side__timer">
        Termina em <strong id="promoSideCountdown" data-left="<?= $remaining ?>">--:--:--</strong>
      </p>
    <?php endif; ?>
  </div>
</aside>

<script>
(function () {
  const KEY = <?= json_encode($popupKey) ?>;
  const panel = document.getElementById('promoSidePanel');
  const backdrop = document.getElementById('promoSideBackdrop');
  const closeBtn = document.getElementById('promoSideClose');
  const countdown = document.getElementById('promoSideCountdown');
  if (!panel) return;

  try {
    if (localStorage.getItem(KEY) === '1') return;
  } catch (e) {}

  const open = () => {
    panel.classList.add('is-open');
    panel.setAttribute('aria-hidden', 'false');
    backdrop?.classList.add('is-open');
    backdrop?.setAttribute('aria-hidden', 'false');
  };

  const dismiss = () => {
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
    backdrop?.classList.remove('is-open');
    backdrop?.setAttribute('aria-hidden', 'true');
    try { localStorage.setItem(KEY, '1'); } catch (e) {}
  };

  const delay = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 1200;
  setTimeout(open, delay);

  closeBtn?.addEventListener('click', dismiss);
  backdrop?.addEventListener('click', dismiss);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && panel.classList.contains('is-open')) dismiss();
  });

  if (countdown) {
    let left = parseInt(countdown.getAttribute('data-left') || '0', 10);
    const pad = (n) => String(n).padStart(2, '0');
    const tick = () => {
      const d = Math.floor(left / 86400);
      const h = Math.floor((left % 86400) / 3600);
      const m = Math.floor((left % 3600) / 60);
      const s = left % 60;
      countdown.textContent = d > 0
        ? d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s)
        : pad(h) + ':' + pad(m) + ':' + pad(s);
      if (left <= 0) {
        dismiss();
        return;
      }
      left -= 1;
      setTimeout(tick, 1000);
    };
    tick();
  }
})();
</script>
