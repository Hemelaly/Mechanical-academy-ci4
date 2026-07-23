<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar nova senha · Mechanical Academy</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" type="image/x-icon">
  <style>
    :root {
      --ink: #f5f7fa;
      --ink-soft: rgba(245, 247, 250, 0.62);
      --page-bg: #050505;
      --surface: #141414;
      --line: rgba(255, 255, 255, 0.09);
      --accent: #0d6efd;
      --accent-soft: rgba(13, 110, 253, 0.16);
      --accent-border: rgba(13, 110, 253, 0.38);
    }

    * { box-sizing: border-box; }

    body {
      min-height: 100vh;
      margin: 0;
      font-family: 'Sora', sans-serif;
      color: var(--ink);
      background:
        radial-gradient(900px 480px at 50% -20%, rgba(13, 110, 253, 0.22) 0%, transparent 55%),
        var(--page-bg);
      -webkit-font-smoothing: antialiased;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1.25rem;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      padding: 2.25rem 1.75rem 1.85rem;
      box-shadow: 0 28px 60px -36px rgba(0, 0, 0, 0.75);
      text-align: center;
    }

    .login-card__logo {
      display: inline-flex;
      margin: 0 auto 1.75rem;
      text-decoration: none;
    }

    .login-card__logo img {
      height: 42px;
      width: auto;
      display: block;
    }

    .login-card__title {
      margin: 0 0 0.55rem;
      font-size: 1.35rem;
      font-weight: 650;
      letter-spacing: -0.02em;
      color: #fff;
    }

    .login-card__subtitle {
      margin: 0 0 1.5rem;
      font-size: 0.88rem;
      line-height: 1.45;
      color: var(--ink-soft);
    }

    .login-alert {
      margin-bottom: 1rem;
      padding: 0.75rem 0.9rem;
      border-radius: 0.375rem;
      font-size: 0.88rem;
      line-height: 1.4;
      text-align: left;
    }

    .login-alert--error {
      background: rgba(220, 53, 69, 0.14);
      border: 1px solid rgba(220, 53, 69, 0.3);
      color: #ff8a95;
    }

    .login-field {
      margin-bottom: 1rem;
      text-align: left;
    }

    .login-field label {
      display: block;
      margin-bottom: 0.4rem;
      font-size: 0.82rem;
      font-weight: 500;
      color: var(--ink-soft);
    }

    .login-input-wrap {
      position: relative;
    }

    .login-field input {
      width: 100%;
      border: 1px solid var(--line);
      border-radius: 0.375rem;
      padding: 0.85rem 2.75rem 0.85rem 1rem;
      background: #0a0a0a;
      color: #fff;
      font-family: inherit;
      font-size: 0.95rem;
      outline: none;
      transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .login-field input::placeholder {
      color: rgba(255, 255, 255, 0.3);
    }

    .login-field input:focus {
      border-color: var(--accent-border);
      box-shadow: 0 0 0 4px var(--accent-soft);
    }

    .login-field input.is-invalid {
      border-color: rgba(220, 53, 69, 0.55);
    }

    .login-toggle {
      position: absolute;
      right: 0.55rem;
      top: 50%;
      transform: translateY(-50%);
      border: 0;
      background: transparent;
      color: var(--ink-soft);
      cursor: pointer;
      padding: 0.35rem;
      font-size: 1rem;
      line-height: 1;
    }

    .login-toggle:hover { color: #fff; }

    .password-strength {
      height: 4px;
      border-radius: 999px;
      margin-top: 0.55rem;
      background: rgba(255, 255, 255, 0.08);
      overflow: hidden;
    }

    .password-strength-bar {
      height: 100%;
      width: 0;
      border-radius: 999px;
      transition: width 0.25s ease, background-color 0.25s ease;
    }

    .password-requirements {
      margin-top: 0.65rem;
      font-size: 0.78rem;
      color: var(--ink-soft);
      text-align: left;
    }

    .password-requirements ul {
      margin: 0;
      padding-left: 1.1rem;
    }

    .password-requirements li { margin-bottom: 0.15rem; }
    .requirement-met { color: #86efac; }

    .field-error {
      display: none;
      margin-top: 0.4rem;
      font-size: 0.78rem;
      color: #ff8a95;
    }

    .login-submit {
      width: 100%;
      border: 0;
      border-radius: 0.375rem;
      padding: 0.9rem 1.25rem;
      margin-top: 0.35rem;
      background: var(--accent);
      color: #fff;
      font-family: inherit;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.15s ease, filter 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
      box-shadow: 0 12px 28px -14px rgba(13, 110, 253, 0.65);
    }

    .login-submit:hover:not(:disabled) {
      filter: brightness(1.06);
      transform: translateY(-1px);
    }

    .login-submit:disabled {
      opacity: 0.45;
      cursor: not-allowed;
      box-shadow: none;
    }

    .login-back {
      display: inline-block;
      margin-top: 1.35rem;
      color: var(--ink-soft);
      text-decoration: none;
      font-size: 0.84rem;
      font-weight: 500;
    }

    .login-back:hover { color: #fff; }
  </style>
</head>
<body>
  <div class="login-card">
    <a class="login-card__logo" href="<?= base_url('/') ?>">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
    </a>

    <h1 class="login-card__title">Criar nova senha</h1>
    <p class="login-card__subtitle">Defina uma palavra-passe segura para a sua conta.</p>

    <?php if (session('error')): ?>
      <div class="login-alert login-alert--error" role="alert"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('reset-password') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= esc($token) ?>">
      <input type="hidden" name="next" value="<?= esc($next ?? '') ?>">
      <input type="hidden" name="course" value="<?= esc($course ?? '') ?>">

      <div class="login-field">
        <label for="password">Nova senha</label>
        <div class="login-input-wrap">
          <input type="password" id="password" name="password" required placeholder="••••••••" autocomplete="new-password">
          <button class="login-toggle" type="button" id="togglePassword" aria-label="Mostrar senha"><i class="bi bi-eye"></i></button>
        </div>
        <div class="password-strength"><div class="password-strength-bar" id="passwordStrengthBar"></div></div>
        <div class="password-requirements">
          <ul>
            <li id="lengthReq">Pelo menos 8 caracteres</li>
            <li id="uppercaseReq">Pelo menos uma letra maiúscula</li>
            <li id="numberReq">Pelo menos um número</li>
            <li id="specialReq">Pelo menos um caractere especial</li>
          </ul>
        </div>
      </div>

      <div class="login-field">
        <label for="confirmPassword">Confirmar senha</label>
        <div class="login-input-wrap">
          <input type="password" id="confirmPassword" name="password_confirm" required placeholder="••••••••" autocomplete="new-password">
          <button class="login-toggle" type="button" id="toggleConfirmPassword" aria-label="Mostrar confirmação"><i class="bi bi-eye"></i></button>
        </div>
        <div class="field-error" id="passwordMatchFeedback">As senhas não coincidem.</div>
      </div>

      <button type="submit" class="login-submit" id="submitButton" disabled>Criar nova senha</button>
    </form>

    <a class="login-back" href="<?= site_url('login') ?>">← Voltar para login</a>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const passwordInput = document.getElementById('password');
      const confirmPasswordInput = document.getElementById('confirmPassword');
      const togglePasswordButton = document.getElementById('togglePassword');
      const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');
      const passwordStrengthBar = document.getElementById('passwordStrengthBar');
      const submitButton = document.getElementById('submitButton');
      const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');
      const lengthReq = document.getElementById('lengthReq');
      const uppercaseReq = document.getElementById('uppercaseReq');
      const numberReq = document.getElementById('numberReq');
      const specialReq = document.getElementById('specialReq');

      const bindToggle = (btn, input) => {
        btn.addEventListener('click', () => {
          const show = input.getAttribute('type') === 'password';
          input.setAttribute('type', show ? 'text' : 'password');
          btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
      };
      bindToggle(togglePasswordButton, passwordInput);
      bindToggle(toggleConfirmPasswordButton, confirmPasswordInput);

      passwordInput.addEventListener('input', () => {
        checkPasswordStrength(passwordInput.value);
        validateForm();
      });
      confirmPasswordInput.addEventListener('input', validateForm);

      function checkPasswordStrength(password) {
        let strength = 0;
        const rules = [
          [password.length >= 8, lengthReq],
          [/[A-Z]/.test(password), uppercaseReq],
          [/[0-9]/.test(password), numberReq],
          [/[^A-Za-z0-9]/.test(password), specialReq],
        ];
        rules.forEach(([ok, el]) => {
          if (ok) { strength += 25; el.classList.add('requirement-met'); }
          else el.classList.remove('requirement-met');
        });
        passwordStrengthBar.style.width = strength + '%';
        passwordStrengthBar.style.backgroundColor = strength < 50 ? '#dc3545' : (strength < 75 ? '#f59e0b' : '#22c55e');
      }

      function validateForm() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const isPasswordStrong = password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password);
        const passwordsMatch = password === confirmPassword;
        submitButton.disabled = !(isPasswordStrong && passwordsMatch && password.length > 0);

        if (confirmPassword.length > 0 && !passwordsMatch) {
          confirmPasswordInput.classList.add('is-invalid');
          passwordMatchFeedback.style.display = 'block';
        } else {
          confirmPasswordInput.classList.remove('is-invalid');
          passwordMatchFeedback.style.display = 'none';
        }
      }
    });
  </script>
</body>
</html>
