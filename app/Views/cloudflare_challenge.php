<!DOCTYPE html>
<html lang="pt" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de segurança · Mechanical Academy</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <style>
        :root {
            --ink: #f5f7fa;
            --ink-soft: rgba(245, 247, 250, 0.62);
            --page-bg: #050505;
            --surface: #141414;
            --line: rgba(255, 255, 255, 0.09);
            --accent: #0d6efd;
            --danger: #f43f5e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Sora', system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(900px 480px at 50% -20%, rgba(13, 110, 253, 0.22) 0%, transparent 55%),
                var(--page-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            -webkit-font-smoothing: antialiased;
        }
        .card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 0.375rem;
            padding: 2.25rem 1.75rem 1.85rem;
            box-shadow: 0 28px 60px -36px rgba(0, 0, 0, 0.75);
            text-align: center;
        }
        .logo {
            display: inline-flex;
            margin: 0 auto 1.5rem;
        }
        .logo img {
            height: 42px;
            width: auto;
            display: block;
        }
        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.2rem;
            font-weight: 650;
            letter-spacing: -0.02em;
        }
        p {
            margin: 0 0 1.5rem;
            font-size: 0.88rem;
            line-height: 1.5;
            color: var(--ink-soft);
        }
        .widget {
            display: flex;
            justify-content: center;
            min-height: 65px;
            margin-bottom: 1rem;
        }
        .error {
            margin: 0 0 1rem;
            padding: 0.65rem 0.75rem;
            border: 1px solid rgba(244, 63, 94, 0.35);
            background: rgba(244, 63, 94, 0.1);
            color: #fda4af;
            border-radius: 0.25rem;
            font-size: 0.8rem;
        }
        .hint {
            margin: 1rem 0 0;
            font-size: 0.72rem;
            color: rgba(245, 247, 250, 0.4);
        }
        .submit {
            display: none;
            width: 100%;
            margin-top: 0.75rem;
            border: 0;
            border-radius: 0.25rem;
            padding: 0.7rem 1rem;
            background: var(--accent);
            color: #fff;
            font-family: inherit;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
        }
        .submit.is-on { display: inline-flex; justify-content: center; }
        .submit:disabled { opacity: 0.55; cursor: wait; }
    </style>
</head>
<body>
    <div class="card">
        <a class="logo" href="<?= esc(site_url('/')) ?>">
            <img src="<?= esc(base_url('assets/img/logo.png')) ?>" alt="Mechanical Academy">
        </a>
        <h1>Verificação de segurança</h1>
        <p>Confirme que é um humano para continuar a usar a Mechanical Academy. Esta verificação é feita pela Cloudflare.</p>

        <?php if (! empty($error)): ?>
            <div class="error"><?= esc($error) ?></div>
        <?php endif; ?>

        <form id="cf-form" method="post" action="<?= esc(site_url('cf-challenge/verify')) ?>">
            <?= csrf_field() ?>
            <div class="widget">
                <div
                    class="cf-turnstile"
                    data-sitekey="<?= esc($siteKey) ?>"
                    data-theme="dark"
                    data-size="normal"
                    data-callback="onTurnstileSuccess"
                    data-error-callback="onTurnstileError"
                    data-expired-callback="onTurnstileExpired"
                ></div>
            </div>
            <button type="submit" class="submit" id="cf-submit">Continuar</button>
        </form>
        <p class="hint">Protegido por Cloudflare Turnstile</p>
    </div>

    <script>
        function onTurnstileSuccess() {
            var btn = document.getElementById('cf-submit');
            if (btn) {
                btn.classList.add('is-on');
                btn.disabled = true;
            }
            document.getElementById('cf-form').submit();
        }
        function onTurnstileError() {
            var btn = document.getElementById('cf-submit');
            if (btn) {
                btn.classList.add('is-on');
                btn.disabled = false;
                btn.textContent = 'Tentar novamente';
            }
        }
        function onTurnstileExpired() {
            var btn = document.getElementById('cf-submit');
            if (btn) {
                btn.classList.remove('is-on');
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
