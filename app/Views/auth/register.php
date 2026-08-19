<?php
$isLoggedIn = auth()->loggedIn();
$user = service('auth')->user();
?>
<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>Criar conta · Mechanical Academy<?= $this->endSection() ?>

<?= $this->section('main3') ?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap');

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
    min-height: 100vh !important;
    height: auto !important;
    margin: 0;
    display: block !important;
    font-family: 'Sora', sans-serif !important;
    color: var(--ink);
    background:
      radial-gradient(900px 480px at 50% -20%, rgba(13, 110, 253, 0.22) 0%, transparent 55%),
      var(--page-bg) !important;
    background-image: none !important;
    -webkit-font-smoothing: antialiased;
  }

  .overlay { min-height: 100vh; width: 100%; background: transparent !important; }

  .login {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.25rem;
  }

  .login-stack { width: 100%; max-width: 400px; }

  .login-card {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 0.375rem;
    padding: 2.1rem 1.75rem 1.7rem;
    box-shadow: 0 28px 60px -36px rgba(0, 0, 0, 0.75);
    text-align: center;
  }

  .login-brand { display: flex; justify-content: center; margin: 0 auto 1.2rem; text-decoration: none; }
  .login-brand img { height: 42px; width: auto; display: block; }

  .login-card__title {
    margin: 0 0 0.35rem;
    font-size: 1.35rem;
    font-weight: 650;
    letter-spacing: -0.02em;
    color: #fff;
  }

  .login-card__subtitle {
    margin: 0 0 1.25rem;
    color: var(--ink-soft);
    font-size: 0.88rem;
    line-height: 1.45;
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

  .login-alert--ok {
    background: rgba(22, 163, 74, 0.14);
    border: 1px solid rgba(22, 163, 74, 0.3);
    color: #86efac;
  }

  .login-field { margin-bottom: 0.85rem; text-align: left; }
  .login-field label {
    display: block;
    margin-bottom: 0.4rem;
    font-size: 0.82rem;
    font-weight: 500;
    color: var(--ink-soft);
  }

  .login-field input {
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 0.375rem;
    padding: 0.85rem 1rem;
    background: #0a0a0a;
    color: #fff;
    font-family: inherit;
    font-size: 0.95rem;
    outline: none;
  }

  .login-field input:focus {
    border-color: var(--accent-border);
    box-shadow: 0 0 0 4px var(--accent-soft);
  }

  .login-submit {
    width: 100%;
    border: 0;
    border-radius: 0.375rem;
    padding: 0.9rem 1.25rem;
    background: var(--accent);
    color: #fff;
    font-family: inherit;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
  }

  .login-submit:hover { filter: brightness(1.06); }

  .login-switch, .login-back {
    display: block;
    margin-top: 1.1rem;
    color: var(--ink-soft);
    text-decoration: none;
    font-size: 0.84rem;
    font-weight: 500;
  }

  .login-switch a, .login-back:hover { color: #fff; }

  .oauth-google {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    width: 100%;
    border: 1px solid var(--line);
    border-radius: 0.375rem;
    padding: 0.78rem 1rem;
    background: #fff;
    color: #1f1f1f;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
  }

  .oauth-google:hover { filter: brightness(0.97); color: #1f1f1f; }

  .oauth-divider {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 1rem 0 1.1rem;
    color: var(--ink-soft);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
  }

  .oauth-divider::before, .oauth-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--line);
  }
</style>

<div class="login">
  <div class="login-stack">
    <a class="login-brand" href="<?= base_url('/') ?>">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
    </a>
    <div class="login-card">
      <h1 class="login-card__title">Criar conta</h1>
      <p class="login-card__subtitle">Grátis. Depois escolhe o curso e paga só quando quiseres.</p>

      <?php if (session('error') !== null) : ?>
        <div class="login-alert login-alert--error" role="alert"><?= esc(session('error')) ?></div>
      <?php elseif (session('errors') !== null) : ?>
        <div class="login-alert login-alert--error" role="alert">
          <?php if (is_array(session('errors'))) : ?>
            <?php foreach (session('errors') as $error) : ?>
              <?= esc($error) ?><br>
            <?php endforeach ?>
          <?php else : ?>
            <?= esc(session('errors')) ?>
          <?php endif ?>
        </div>
      <?php endif ?>

      <?php if (session('message') !== null) : ?>
        <div class="login-alert login-alert--ok" role="alert"><?= esc(session('message')) ?></div>
      <?php endif ?>

      <?= view('partials/google_auth_button', ['label' => 'Criar conta com Google']) ?>

      <form action="<?= url_to('register') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="role" value="student">

        <div class="login-field">
          <label for="floatingEmailInput">E-mail</label>
          <input type="email" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="nome@email.com" value="<?= old('email') ?>" required>
        </div>

        <div class="login-field">
          <label for="floatingUsernameInput">Nome</label>
          <input type="text" id="floatingUsernameInput" name="username" autocomplete="name" placeholder="O seu nome" value="<?= old('username') ?>" required>
        </div>

        <div class="login-field">
          <label for="floatingPasswordInput">Senha</label>
          <input type="password" id="floatingPasswordInput" name="password" autocomplete="new-password" placeholder="••••••••" required>
        </div>

        <div class="login-field">
          <label for="floatingPasswordConfirmInput">Confirmar senha</label>
          <input type="password" id="floatingPasswordConfirmInput" name="password_confirm" autocomplete="new-password" placeholder="••••••••" required>
        </div>

        <?= view('partials/turnstile_widget', ['theme' => 'dark']) ?>

        <button type="submit" class="login-submit">Criar conta grátis</button>
      </form>

      <p class="login-switch">Já tem conta? <a href="<?= url_to('login') ?>">Entrar</a></p>
      <a class="login-back" href="<?= base_url('/') ?>">← Voltar ao início</a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
