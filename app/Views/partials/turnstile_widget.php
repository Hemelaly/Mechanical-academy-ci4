<?php

/**
 * Widget Cloudflare Turnstile para formulários.
 *
 * @var string $theme light|dark|auto
 */
$cf = config(\Config\Cloudflare::class);
if (! $cf->isTurnstileReady()) {
    return;
}
$theme = $theme ?? 'dark';
?>
<div class="cf-turnstile-wrap" style="margin:0.75rem 0;display:flex;justify-content:flex-start;">
    <div
        class="cf-turnstile"
        data-sitekey="<?= esc($cf->turnstileSiteKey) ?>"
        data-theme="<?= esc($theme) ?>"
        data-size="normal"
    ></div>
</div>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
