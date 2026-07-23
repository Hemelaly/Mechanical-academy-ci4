<?php

$isLoggedIn = auth()->loggedIn();
$user = service('auth')->user();
?>
<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> · Mechanical Academy<?= $this->endSection() ?>

<?= $this->section('main2') ?>

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
    align-items: stretch !important;
    font-family: 'Sora', sans-serif !important;
    color: var(--ink);
    background:
      radial-gradient(900px 480px at 50% -20%, rgba(13, 110, 253, 0.22) 0%, transparent 55%),
      var(--page-bg) !important;
    background-image: none !important;
    -webkit-font-smoothing: antialiased;
  }

  .overlay {
    min-height: 100vh;
    width: 100%;
    background: transparent !important;
  }

  .login {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.25rem;
  }

  .login-stack {
    width: 100%;
    max-width: 400px;
  }

  .login-card {
    width: 100%;
    max-width: none;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 0.375rem;
    padding: 2.25rem 1.75rem 1.85rem;
    box-shadow: 0 28px 60px -36px rgba(0, 0, 0, 0.75);
    text-align: center;
  }

  .login-brand {
    display: flex;
    justify-content: center;
    margin: 0 auto 1.35rem;
    text-decoration: none;
  }

  .login-brand img {
    height: 42px;
    width: auto;
    display: block;
  }

  .login-card__title {
    margin: 0 0 1.5rem;
    font-size: 1.35rem;
    font-weight: 650;
    letter-spacing: -0.02em;
    color: #fff;
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

  .login-field {
    margin-bottom: 0.95rem;
    text-align: left;
  }

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
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .login-field input::placeholder {
    color: rgba(255, 255, 255, 0.3);
  }

  .login-field input:focus {
    border-color: var(--accent-border);
    box-shadow: 0 0 0 4px var(--accent-soft);
  }

  .login-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin: 0.35rem 0 1.35rem;
    text-align: left;
  }

  .login-remember {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin: 0;
    color: var(--ink-soft);
    font-size: 0.82rem;
    cursor: pointer;
  }

  .login-remember input {
    width: 0.9rem;
    height: 0.9rem;
    accent-color: var(--accent);
  }

  .login-forgot {
    color: #6ea8fe;
    text-decoration: none;
    font-size: 0.82rem;
    font-weight: 500;
  }

  .login-forgot:hover {
    color: #fff;
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
    transition: transform 0.15s ease, filter 0.15s ease, box-shadow 0.15s ease;
    box-shadow: 0 12px 28px -14px rgba(13, 110, 253, 0.65);
  }

  .login-submit:hover {
    filter: brightness(1.06);
    transform: translateY(-1px);
  }

  .login-submit:active {
    transform: scale(0.98);
  }

  .login-back {
    display: inline-block;
    margin-top: 1.35rem;
    color: var(--ink-soft);
    text-decoration: none;
    font-size: 0.84rem;
    font-weight: 500;
  }

  .login-back:hover {
    color: #fff;
  }
</style>

<div class="login">
  <div class="login-stack">
    <a class="login-brand" href="<?= base_url('/') ?>">
      <img src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy">
    </a>
    <div class="login-card">
    <h1 class="login-card__title"><?= lang('Auth.login') ?></h1>

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

    <form action="<?= url_to('login') ?>" method="post">
      <?= csrf_field() ?>

      <div class="login-field">
        <label for="floatingEmailInput"><?= lang('Email') ?></label>
        <input type="email" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="nome@email.com" value="<?= old('email') ?>" required>
      </div>

      <div class="login-field">
        <label for="floatingPasswordInput"><?= lang('Senha') ?></label>
        <input type="password" id="floatingPasswordInput" name="password" autocomplete="current-password" placeholder="••••••••" required>
      </div>

      <div class="login-row">
        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
          <label class="login-remember">
            <input type="checkbox" name="remember" <?php if (old('remember')): ?>checked<?php endif ?>>
            <?= lang('Manter logado') ?>
          </label>
        <?php else: ?>
          <span></span>
        <?php endif; ?>
        <a class="login-forgot" href="<?= base_url('reset-password') ?>">Esqueci a senha</a>
      </div>

      <?= view('partials/turnstile_widget', ['theme' => 'dark']) ?>

      <button type="submit" class="login-submit"><?= lang('Auth.login') ?></button>
    </form>

    <a class="login-back" href="<?= base_url('/') ?>">← Voltar ao início</a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
