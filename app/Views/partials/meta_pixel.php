<?php

/**
 * Meta Pixel — incluir no <head> do checkout.
 *
 * @var bool $trackInitiateCheckout
 * @var array|null $initiatePayload content_ids, value, currency…
 */
$meta = config(\Config\MetaPixel::class);
if (! $meta->isReady()) {
    return;
}

$pixelId = $meta->pixelId;
$trackInitiate = ! empty($trackInitiateCheckout);
$initiatePayload = is_array($initiatePayload ?? null) ? $initiatePayload : [];
?>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', <?= json_encode($pixelId) ?>);
fbq('track', 'PageView');
<?php if ($trackInitiate): ?>
fbq('track', 'InitiateCheckout', <?= json_encode($initiatePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
<?php endif; ?>
window.MetaPixel = {
  ready: true,
  purchase: function (payload) {
    try { fbq('track', 'Purchase', payload || {}); } catch (e) {}
  },
  addPaymentInfo: function (payload) {
    try { fbq('track', 'AddPaymentInfo', payload || {}); } catch (e) {}
  }
};
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=<?= esc($pixelId, 'url') ?>&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
