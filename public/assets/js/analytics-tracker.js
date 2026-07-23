/**
 * Recolhe cliques relevantes e envia para /analytics/collect.
 * Usa fetch (com X-Requested-With) para não contaminar previous_url do CI.
 */
(function () {
  'use strict';

  var ENDPOINT = (typeof window.ANALYTICS_COLLECT_URL === 'string' && window.ANALYTICS_COLLECT_URL)
    ? window.ANALYTICS_COLLECT_URL
    : '/analytics/collect';

  var queue = [];
  var flushTimer = null;
  var MAX_BATCH = 20;

  function labelFromEl(el) {
    if (!el || el.nodeType !== 1) return '';
    var explicit = el.getAttribute('data-analytics') || el.getAttribute('data-track') || '';
    if (explicit) return String(explicit).trim().slice(0, 160);

    var aria = (el.getAttribute('aria-label') || '').trim();
    if (aria) return aria.slice(0, 160);

    var text = (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim();
    if (text) return text.slice(0, 120);

    var href = el.getAttribute('href') || '';
    if (href) return ('link:' + href).slice(0, 160);

    var name = el.getAttribute('name') || el.id || el.tagName.toLowerCase();
    return String(name).slice(0, 120);
  }

  function interestingTarget(el) {
    if (!el || el.nodeType !== 1) return null;
    var node = el.closest('a, button, [role="button"], input[type="submit"], input[type="button"], [data-analytics], [data-track]');
    if (!node) return null;
    if (node.closest('[data-analytics-ignore]')) return null;
    return node;
  }

  function enqueue(event) {
    queue.push(event);
    if (queue.length >= MAX_BATCH) {
      flush();
      return;
    }
    if (flushTimer) clearTimeout(flushTimer);
    flushTimer = setTimeout(function () { flush(); }, 1200);
  }

  function flush() {
    if (flushTimer) {
      clearTimeout(flushTimer);
      flushTimer = null;
    }
    if (!queue.length) return;

    var batch = queue.splice(0, MAX_BATCH);
    var body = JSON.stringify({ events: batch });

    try {
      fetch(ENDPOINT, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: body,
        keepalive: true,
        credentials: 'same-origin'
      }).catch(function () {});
    } catch (e) { /* silent */ }
  }

  document.addEventListener('click', function (e) {
    var target = interestingTarget(e.target);
    if (!target) return;

    enqueue({
      event_type: target.getAttribute('data-analytics-type') || 'click',
      path: window.location.pathname || '/',
      referrer: document.referrer || '',
      element: labelFromEl(target),
      meta: {
        tag: target.tagName.toLowerCase(),
        href: target.getAttribute('href') || null,
        id: target.id || null,
        classes: (target.className && typeof target.className === 'string')
          ? target.className.split(/\s+/).slice(0, 6).join(' ')
          : null
      }
    });
  }, true);

  window.addEventListener('pagehide', function () { flush(); });
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'hidden') flush();
  });
})();
