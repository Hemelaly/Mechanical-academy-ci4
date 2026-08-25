/**
 * Adiciona botão mostrar/ocultar em todos os inputs type=password.
 */
(function () {
  'use strict';

  var STYLE_ID = 'ma-pw-toggle-style';
  var EYE =
    '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><circle cx="12" cy="12" r="3"/></svg>';
  var EYE_OFF =
    '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.585 10.585A3 3 0 0012 15a3 3 0 002.121-.879M9.88 4.24A9.87 9.87 0 0112 4c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-4.132 5.111M6.228 6.228A10.02 10.02 0 002.458 12c1.274 4.057 5.064 7 9.542 7 1.26 0 2.47-.22 3.59-.624"/></svg>';

  function injectStyle() {
    if (document.getElementById(STYLE_ID)) return;
    var style = document.createElement('style');
    style.id = STYLE_ID;
    style.textContent =
      '.pw-toggle-wrap{position:relative;display:block;width:100%}' +
      '.pw-toggle-wrap>input[type="password"],.pw-toggle-wrap>input[type="text"]{padding-right:2.6rem!important}' +
      '.pw-toggle-btn{position:absolute;right:.55rem;top:50%;transform:translateY(-50%);display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;padding:0;border:0;background:transparent;color:inherit;opacity:.55;cursor:pointer;border-radius:.35rem;line-height:0}' +
      '.pw-toggle-btn:hover,.pw-toggle-btn:focus-visible{opacity:1;outline:none}' +
      '.pw-toggle-btn:focus-visible{box-shadow:0 0 0 2px rgba(13,110,253,.45)}';
    document.head.appendChild(style);
  }

  function enhance(input) {
    if (!(input instanceof HTMLInputElement)) return;
    if (input.type !== 'password' && input.dataset.pwWasPassword !== '1') return;
    if (input.dataset.pwToggle === '1') return;
    if (input.closest('[data-no-pw-toggle]')) return;
    if (input.closest('.pw-toggle-wrap')) {
      input.dataset.pwToggle = '1';
      return;
    }
    // Já tem botão de revelar (ex.: reset_password.php)
    if (input.parentElement && input.parentElement.querySelector('.login-toggle, .pw-toggle-btn, [data-pw-toggle-btn]')) {
      input.dataset.pwToggle = '1';
      return;
    }

    input.dataset.pwToggle = '1';
    input.dataset.pwWasPassword = '1';

    var wrap = document.createElement('div');
    wrap.className = 'pw-toggle-wrap';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'pw-toggle-btn';
    btn.setAttribute('aria-label', 'Mostrar senha');
    btn.setAttribute('tabindex', '0');
    btn.innerHTML = EYE;

    btn.addEventListener('click', function () {
      var show = input.getAttribute('type') === 'password';
      input.setAttribute('type', show ? 'text' : 'password');
      btn.setAttribute('aria-label', show ? 'Ocultar senha' : 'Mostrar senha');
      btn.innerHTML = show ? EYE_OFF : EYE;
    });

    wrap.appendChild(btn);
  }

  function scan(root) {
    injectStyle();
    (root || document).querySelectorAll('input[type="password"]').forEach(enhance);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      scan(document);
    });
  } else {
    scan(document);
  }

  // Modais / formulários injectados dinamicamente
  if (typeof MutationObserver !== 'undefined') {
    var timer = null;
    var obs = new MutationObserver(function () {
      if (timer) clearTimeout(timer);
      timer = setTimeout(function () {
        scan(document);
      }, 80);
    });
    obs.observe(document.documentElement, { childList: true, subtree: true });
  }
})();
