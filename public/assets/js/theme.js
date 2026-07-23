/**
 * Theme switcher partilhado (dashboard + player).
 * Ícone = tema para o qual se muda: sol no dark, lua no light.
 */
(function () {
  const STORAGE_KEY = 'theme';

  function getStoredTheme() {
    try {
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved === 'dark' || saved === 'light') return saved;
    } catch (_) {}
    return 'dark';
  }

  function updateIcons(theme) {
    document.querySelectorAll('#theme-toggle-icon, [data-theme-icon]').forEach((icon) => {
      icon.classList.remove('bi-sun', 'bi-moon-stars', 'bi-moon');
      // Em dark → mostrar sol (ir para claro); em light → mostrar lua (ir para escuro)
      icon.classList.add(theme === 'dark' ? 'bi-sun' : 'bi-moon-stars');
    });
  }

  function updateLogos(theme) {
    const logoLight = document.getElementById('logo-light');
    const logoDark = document.getElementById('logo-dark');
    if (!logoLight || !logoDark) return;
    const isDark = theme === 'dark';
    // logo-light = marca clara (usada no dark); logo-dark = marca azul (usada no light)
    logoLight.classList.toggle('hidden', !isDark);
    logoDark.classList.toggle('hidden', isDark);
  }

  function applyTheme(theme) {
    const next = theme === 'light' ? 'light' : 'dark';
    document.documentElement.classList.toggle('dark', next === 'dark');
    document.documentElement.style.colorScheme = next;

    try {
      localStorage.setItem(STORAGE_KEY, next);
    } catch (_) {}

    updateIcons(next);
    updateLogos(next);
    document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: next } }));
  }

  function toggleTheme() {
    const isDark = document.documentElement.classList.contains('dark');
    applyTheme(isDark ? 'light' : 'dark');
  }

  // Expor para perfil / outros botões
  window.AcademyTheme = { apply: applyTheme, toggle: toggleTheme, get: getStoredTheme };

  applyTheme(getStoredTheme());

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('#theme-toggle, [data-theme-toggle]');
    if (!btn) return;
    e.preventDefault();
    toggleTheme();
  });
})();
