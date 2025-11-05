<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>404 • Página não encontrada</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.png') ?>" width="100%" type="image/x-icon">

    <style>
        :root {
            --bg: #0b0f19;
            --fg: #e7eaf3;
            --accent: #7c5cff;
        }

        /* === LAYOUT BASE === */
        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            color: var(--fg);
            display: grid;
            place-items: center;
            font: 500 16px/1.4 system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
            overflow: hidden;
            background: var(--bg);
        }

        /* === CENÁRIO GALÁXIA === */
        .space {
            position: fixed;
            inset: 0;
            overflow: hidden;
            /* camadas principais (nebulosa + vinheta) */
            background:
                radial-gradient(1200px 800px at 15% 20%, rgba(124, 92, 255, .18), transparent 60%),
                radial-gradient(1100px 700px at 85% 80%, rgba(78, 110, 255, .15), transparent 60%),
                radial-gradient(900px 600px at 60% 35%, rgba(180, 120, 255, .10), transparent 70%),
                #0b0f19;
        }

        /* estrelas – 2 camadas com velocidades diferentes */
        .stars,
        .stars2 {
            position: absolute;
            inset: -50% -50%;
            /* margem extra para animar sem “vazar” */
            background-repeat: repeat;
            animation: drift linear infinite;
        }

        /* camada 1: muitas estrelas pequenas */
        .stars {
            width: 200%;
            height: 200%;
            background-image:
                radial-gradient(2px 2px at 20px 30px, #fff, transparent 60%),
                radial-gradient(1px 1px at 40px 70px, #ffffffcc, transparent 60%),
                radial-gradient(1.5px 1.5px at 130px 90px, #fff, transparent 60%),
                radial-gradient(1px 1px at 200px 10px, #fff, transparent 60%),
                radial-gradient(1.5px 1.5px at 250px 130px, #ffffffd9, transparent 60%),
                radial-gradient(1px 1px at 300px 200px, #fff, transparent 60%);
            background-size: 300px 300px;
            animation-duration: 120s;
            opacity: .7;
        }

        /* camada 2: estrelas maiores/raras */
        .stars2 {
            width: 220%;
            height: 220%;
            background-image:
                radial-gradient(2px 2px at 50px 150px, #fff, transparent 60%),
                radial-gradient(2.5px 2.5px at 220px 80px, #ffffffd9, transparent 60%),
                radial-gradient(1.8px 1.8px at 140px 240px, #fff, transparent 60%),
                radial-gradient(2px 2px at 300px 120px, #fff, transparent 60%);
            background-size: 350px 350px;
            animation-duration: 200s;
            opacity: .8;
        }

        @keyframes drift {
            from {
                transform: translate3d(0, 0, 0) scale(1);
            }

            to {
                transform: translate3d(-15%, -10%, 0) scale(1.02);
            }
        }

        /* vinheta leve e brilho */
        .vignette::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(closest-side at 50% 40%, transparent, rgba(0, 0, 0, .25)),
                linear-gradient(#00000055, #00000020 35%, #0000);
            pointer-events: none;
        }

        /* === CONTEÚDO === */
        .card {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 720px;
            padding: 48px 28px;
        }

        .logo {
            height: 75px;
            width: auto;
            display: block;
            margin: 0 auto 10px;
            filter: drop-shadow(0 2px 10px rgba(0, 0, 0, .35));
        }

        h1 {
            margin: 6px 0 10px;
            font-size: clamp(28px, 3.5vw, 44px);
            font-weight: 700;
        }

        p {
            margin: 0 0 18px;
            opacity: .9
        }

        .cta {
            display: inline-block;
            padding: 15px 25px;
            background: linear-gradient(90deg, var(--accent), #9a7bff 60%, var(--accent));
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            border-radius: 15px;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
        }

        .cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 30px rgba(124, 92, 255, .45)
        }

        .meta {
            font-size: 13px;
            opacity: .7;
            margin-top: 10px
        }

        /* Responsivo */
        @media (max-width:420px) {
            .card {
                padding: 36px 20px
            }
        }
    </style>
</head>

<body>
    <!-- fundo -->
    <div class="space vignette" aria-hidden="true">
        <div class="stars"></div>
        <div class="stars2"></div>
    </div>

    <!-- conteúdo -->
    <main class="card" role="main">
        <!-- <img class="logo" src="<?= base_url('assets/img/logo.png') ?>" alt="Mechanical Academy" /> -->
        <p style="font-size: 5rem; line-height: 1; font-weight: bold;">404</p>
        <h1>Página não encontrada</h1>
        <p>A rota que você tentou acessar não existe ou foi movida.</p>
        <a class="cta" href="<?= base_url('/') ?>">Voltar para a página inicial</a>
        <div class="meta">Código do erro: 404 • <?= esc(current_url()) ?></div>
    </main>
</body>

</html>