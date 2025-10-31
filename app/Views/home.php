<?php

$isLoggedIn   = auth()->loggedIn();

// dd(base_url('./assets/img/logo.png'))


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mechanical Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif !important;
        }

        #banner {
            width: 100%;
            background: url(<?= base_url('./assets/img/banner.jpeg') ?>) no-repeat center center/cover;
        }

        .overlay {
            width: 100%;
            height: 100% !important;
            background-color: rgba(0, 0, 0, 0.63);
        }

        .heading-1 {
            font-size: 70px;
        }

        @media (min-width: 992px) {
            .btn-newsletter {
                width: 230px;
            }
        }

        @media (max-width: 768px) {
            .heading-1 {
                font-size: 50px;
            }
        }

        @media (max-width: 576px) {
            .heading-1 {
                font-size: 30px;
            }
        }

        .card-bg {
            background-color: #222222;
        }

        .card-border {
            border: 1px solid rgb(63, 62, 62);
        }

        #youtube-bg {
            background: url(<?= base_url('./assets/img/youtube.png') ?>) no-repeat center center/cover;
        }

        .m-600 {
            width: 100%;
            max-width: 900px !important;
        }

        .m-500 {
            width: 100%;
            max-width: 800px !important;
        }

        .patterns {
            background: url(<?= base_url('./assets/img/pattern.png') ?>);
        }

        :root {
            --pre-bg: #0b0f19;
            --pre-fg: #fff;
            --pre-accent: #7c5cff;
            --pre-z: 9999;
            --fade-ms: 360ms;
        }

        #preloader {
            position: fixed;
            inset: 0;
            z-index: var(--pre-z);
            display: grid;
            place-items: center;
            color: var(--pre-fg);
            background:
                radial-gradient(1000px 700px at 10% 10%, #11162a 0%, transparent 60%),
                radial-gradient(1000px 700px at 90% 90%, #0e1330 0%, transparent 60%),
                var(--pre-bg);
            transition: opacity var(--fade-ms) ease, visibility var(--fade-ms) ease;
        }

        #preloader.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .preloader__inner {
            display: grid;
            gap: 14px;
            place-items: center;
            text-align: center;
        }

        .preloader__logo {
            width: 84px;
            height: auto;
            filter: drop-shadow(0 2px 10px rgba(0, 0, 0, .35));
        }

        .preloader__spinner {
            width: 56px;
            height: 56px;
        }

        .preloader__track {
            fill: none;
            stroke: rgba(255, 255, 255, .18);
            stroke-width: 6;
        }

        .preloader__arc {
            fill: none;
            stroke: var(--pre-accent);
            stroke-linecap: round;
            stroke-width: 6;
            stroke-dasharray: 110 126;
            transform-origin: 50% 50%;
            animation: spin 1.05s linear infinite, dash 1.5s ease-in-out infinite;
        }

        .preloader__bar {
            width: min(320px, 70vw);
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .15);
            overflow: hidden;
        }

        .preloader__bar__fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--pre-accent), #9a7bff 60%, var(--pre-accent));
            border-radius: 999px;
            transform: translateZ(0);
        }

        .preloader__text {
            font: 600 .95rem/1.2 system-ui, -apple-system, Segoe UI, Roboto, Helvetica Neue, Arial;
            opacity: .9;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes dash {
            0% {
                stroke-dasharray: 1 235;
                stroke-dashoffset: 0;
            }

            50% {
                stroke-dasharray: 120 115;
                stroke-dashoffset: -25;
            }

            100% {
                stroke-dasharray: 1 235;
                stroke-dashoffset: -235;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .preloader__arc {
                animation: none;
            }
        }
    </style>



</head>

<body>
    <!-- PRELOADER -->
    <div id="preloader" role="status" aria-live="polite" aria-label="Carregando conteúdo">
        <div class="preloader__inner">
            <!-- opcional: seu logotipo -->
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Minha Marca" class="preloader__logo h-auto w-50" />


            <div class="preloader__bar">
                <div class="preloader__bar__fill" id="preloaderFill" style="width:0%"></div>
            </div>
            <p class="preloader__text"><span id="preloaderPct">0</span>%</p>
        </div>
    </div>





    <nav class="navbar navbar-expand-lg sticky-top bg-black navbar-dark py-3">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?= base_url('./assets/img/logo.png') ?>" alt="Logo" style="width: 150px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item me-3">
                            <a class="nav-link active" href="<?= base_url($user->role . '/dashboard/meus_cursos') ?>">Meus Cursos</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= base_url($user->role . '/dashboard/perfil') ?>" class="d-flex align-items-center text-decoration-none">
                                <img src="<?= base_url('assets/img/user-default.png') ?>" alt="User" class="rounded-circle me-2" width="35" height="35">
                                <span class="text-white fw-semibold text-nowrap"><?= $user->username ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-3">
                            <a class="nav-link active" href="#cursos">Cursos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('login') ?>">Entrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <section id="banner">
        <div class="overlay w-100 h-100 py-5 py-sm-0">
            <div class="container w-100 h-100 py-5">
                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                    <p class="text-primary fw-bold fs-4 mt-sm-5">Mechanical Academy</p>
                    <h1 class="text-light heading-1 fw-bold text-center my-3">Aprenda a Fazer do <br> Jeito <span
                            class="text-primary">Certo</span></h1>
                    <p class="text-center text-light">Cursos práticos baseados em projetos que são fáceis de
                        entender e
                        <br> direto ao ponto, SEM ENROLAÇÃO
                    </p>

                    <div class="d-flex align-items-center gap-3 mt-3 mb-5">
                        <a href="#cursos" class="py-3 px-4 btn btn-primary">Ver todos cursos</a>
                    </div>

                    <div class="d-flex flex-column flex-md-row flex-lg-row align-items-center justify-content-center w-100 mt-0 mt-lg-5 text-light">
                        <div class="d-flex flex-column align-items-center justify-content-center mx-5">
                            <i class="fa-solid fa-graduation-cap heading-1"></i>
                            <h1 class="text-center fw-bold mt-3">5+</h1>
                            <p class="text-center">Cursos</p>
                        </div>
                        <div class="d-flex flex-column align-items-center justify-content-center mx-5">
                            <i class="fa-solid fa-clock heading-1"></i>
                            <h1 class="text-center fw-bold mt-3">250+</h1>
                            <p class="text-center">Horas de conteúdo</p>
                        </div>
                        <div class="d-flex flex-column align-items-center justify-content-center mx-5">
                            <i class="fa-solid fa-users heading-1"></i>
                            <h1 class="text-center fw-bold mt-3">500+</h1>
                            <p class="text-center">Estudantes por curso</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-primary">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center mb-3 mb-md-0 mb-lg-0">
                    <h1 class="fw-bold text-uppercase text-light fs-3 mb-0 text-center text-md-start text-lg-start">Notifique-me de novos cursos</h1>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <form class="d-flex flex-column flex-md-column flex-lg-row gap-1" role="search">
                        <input class="form-control py-2" type="email" placeholder="Email" aria-label="email" />
                        <button class="btn btn-dark text-uppercase btn-newsletter py-2 fw-semibold"
                            type="submit">Notifique-me</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-black py-5 patterns" id="cursos">
        <div class="container py-5">
            <h2 class="text-primary text-center fw-bold fs-1 mb-5">Cursos mais <span class="text-light">recentes</span>
            </h2>

            <div class="row mt-4">
                <?php foreach ($courses as $key => $course): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                        <div class="card card-bg text-light W-100 h-100 p-1 card-border">
                            <div class="card-body d-flex justify-content-between ">
                                <div>
                                    <h5 class="fs-6"><i class="fa fa-clock"></i> 25 Horas</h5>
                                    <div class="mt-4 p-3" w-100>
                                        <h6 class="card-subtitle mb-2 text-primary fw-light text-uppercase fs-6">Todos os
                                            níveis</h6>
                                        <p class="card-text fs-5 text-white fw-semibold mb-3"><?= esc($course->title_course) ?></p>
                                        <a href="<?= base_url('./courses/' . $course->id_course) ?>"
                                            class="card-link text-decoration-none text-primary fw-light fs-6 py-4 stretched-link">Ver
                                            Curso</a>
                                    </div>
                                </div>
                                <div><img src="<?= base_url('./assets/img/' . $course->icon_course) ?>" style="width: 50px;" alt=""></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <section class="py-5" id="youtube-bg">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 mb-4 mb-md-0 mb-lg-0">
                    <img src="<?= base_url('./assets/img/frame-youtube.png') ?>" alt="" class="w-100">
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column align-items-center justify-content-center">
                    <h2 class="text-light fw-bold text-center">Mechanical Tecnologia no YouTube</h2>
                    <p class="text-center text-light">Nosso canal no YouTube tem mais de <span class="fw-bold">2 milhões
                            de assinantes</span> com <span class="fw-bold">1000+</span> tutoriais gratuitos e cursos
                        intensivos.</p>
                    <a href="https://www.youtube.com/@MechanicalTecnologia" class="py-3 px-5 btn btn-dark fw-bold"
                        target="_blank">Ver Canal</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-black patterns">
        <div class="container py-5 d-flex flex-column align-items-center justify-content-center">
            <h2 class="text-light text-center fw-bold">Perguntas Frequentes sobre a Mechanical Academy</h2>
            <p class="text-light text-center m-500 mb-4">Se você tem alguma questão, confira abaixo as respostas que
                preparamos especialmente para facilitar sua experiência de aprendizado.</p>
            <div class="m-600 mt-3">
                <div class="accordion accordion-flush d-flex flex-column gap-2 w-100" id="accordionFlushExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">
                                O que é Mechanical Academy?
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">É uma plataforma de ensino online desenvolvido pela <a href="https://mechanical.co.mz" class="text-decoration-none" target="_blank">Mechanical Tecnologia</a>, onde
                                oferecemos cursos exclusivos lecionados pelos nossos especialistas. O aluno tem acesso
                                a conteúdos atualizados, atividades práticas e suporte direto com a nossa equipe.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                aria-controls="flush-collapseTwo">
                                Como posso me inscrever em um curso?
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Basta acessar a página do curso desejado, clicar em
                                “Inscreva-se”, preencher os dados solicitados e efetuar o pagamento. Após a confirmação,
                                o acesso ao conteúdo será liberado automaticamente.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseThree" aria-expanded="false"
                                aria-controls="flush-collapseThree">
                                Quais dispositivos posso usar para acessar os cursos?
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">A plataforma é 100% responsiva. Você pode acessar pelo
                                computador, tablet ou celular, utilizando qualquer navegador moderno (Google Chrome,
                                Edge, Safari, Firefox).</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse4" aria-expanded="false" aria-controls="flush-collapse4">
                                Os cursos têm certificado?
                            </button>
                        </h2>
                        <div id="flush-collapse4" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Sim. Ao concluir o curso e realizar todas as atividades
                                obrigatórias, você poderá emitir seu certificado digital de conclusão diretamente pela
                                plataforma.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse5" aria-expanded="false" aria-controls="flush-collapse5">
                                Como funciona o suporte ao aluno?
                            </button>
                        </h2>
                        <div id="flush-collapse5" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Dentro da plataforma, você terá acesso a uma área de mensagens e
                                fórum, onde pode tirar dúvidas diretamente com os instrutores e interagir com outros
                                alunos.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse6" aria-expanded="false" aria-controls="flush-collapse6">
                                Posso parcelar o pagamento?
                            </button>
                        </h2>
                        <div id="flush-collapse6" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">Sim, aceitamos diversas formas de pagamento, incluindo
                                parcelamento no cartão de crédito. Os detalhes são apresentados no momento da inscrição.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-3 bg-dark ">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <img src="<?= base_url('./assets/img/logo.png') ?>" style="width: 150px;" alt="">
                </div>
                <div class="col-lg-8 col-md-7 col-sm-12 d-flex align-items-center justify-content-center my-3 my-md-0 my-lg-0">
                    <p class="text-light text-start text-md-center text-lg-center mb-0">&copy; 2025 Mechanical Academy. Todos Direitos Reservados</p>
                </div>
                <div class="col-lg-2 col-md-2 col-12 d-flex align-items-center gap-1">
                    <a href="https://facebook.com" class="text-decoration-none text-light"><i
                            class="fab fa-facebook-f fs-5"></i></a>
                    <a href="https://instagram.com" class="text-decoration-none text-light"><i
                            class="fab fa-instagram fs-5"></i></a>
                    <a href="https://youtube.com" class="text-decoration-none text-light"><i
                            class="fab fa-youtube fs-5"></i></a>
                    <a href="https://linkedin.com" class="text-decoration-none text-light"><i
                            class="fab fa-linkedin-in fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

    <script>
        (function() {
            const pre = document.getElementById('preloader');
            if (!pre) return;

            const fill = document.getElementById('preloaderFill');
            const pctEl = document.getElementById('preloaderPct');

            // ---- CONFIG ----
            const MAX_WAIT_MS = 12000; // força saída em 12s
            const MIN_SHOW_MS = 400; // evita "piscar" rápido demais
            const WEIGHTS = {
                dom: 20, // DOM pronto
                fonts: 10, // fontes carregadas
                images: 60, // imagens visíveis carregadas
                load: 10 // evento window.load
            };

            // bloqueia rolagem (opcional; se não quiser, comente)
            const lockScroll = () => {
                document.documentElement.style.overflow = 'hidden';
            };
            const unlockScroll = () => {
                document.documentElement.style.overflow = '';
            };
            lockScroll();

            let startTs = performance.now();
            let current = 0; // valor animado
            let target = 0; // alvo a perseguir
            let rafId = 0;
            let finished = false;
            let loadFired = false;

            // ---- MEDIDORES DE PROGRESSO ----
            // 1) DOM pronto
            function bumpDomReady() {
                target += WEIGHTS.dom;
                clampTarget();
            }
            if (document.readyState === 'interactive' || document.readyState === 'complete') {
                bumpDomReady();
            } else {
                document.addEventListener('DOMContentLoaded', bumpDomReady, {
                    once: true
                });
            }

            // 2) Fonts (se suportado)
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(() => {
                    target += WEIGHTS.fonts;
                    clampTarget();
                }).catch(() => {
                    // em caso de erro de fontes, ainda avançamos um pouco
                    target += Math.floor(WEIGHTS.fonts * 0.6);
                    clampTarget();
                });
            } else {
                target += Math.floor(WEIGHTS.fonts * 0.6);
                clampTarget();
            }

            // 3) Imagens (só as do DOM atual; background-images são ignoradas por simplicidade)
            const imgs = Array.from(document.images || []);
            const totalImgs = imgs.length;
            let loadedImgs = 0;

            function onImgDone() {
                loadedImgs++;
                const frac = totalImgs ? (loadedImgs / totalImgs) : 1;
                // as imagens controlam até WEIGHTS.images do total
                const imgProgress = WEIGHTS.images * frac;
                // base atual (dom + fonts) já pode ter somado; então fixamos a parte de imagens
                const base = Math.min(target, WEIGHTS.dom + WEIGHTS.fonts);
                target = base + imgProgress;
                clampTarget();
            }

            if (totalImgs === 0) {
                target += WEIGHTS.images; // não há imagens -> consideramos completo esse trecho
                clampTarget();
            } else {
                imgs.forEach(img => {
                    if (img.complete) {
                        // já carregada (ou falhou); conta mesmo assim
                        onImgDone();
                    } else {
                        img.addEventListener('load', onImgDone, {
                            once: true
                        });
                        img.addEventListener('error', onImgDone, {
                            once: true
                        });
                    }
                });
                // ainda assim, segurança: se muitas imagens travarem, damos um empurrão após 5s
                setTimeout(() => {
                    const minImgProgress = Math.max(target, WEIGHTS.dom + WEIGHTS.fonts + WEIGHTS.images * 0.5);
                    target = minImgProgress;
                    clampTarget();
                }, 5000);
            }

            // 4) window.load (fecha os 100%)
            window.addEventListener('load', () => {
                loadFired = true;
                target = 100;
                clampTarget();
            }, {
                once: true
            });

            // bailouts globais
            setTimeout(() => {
                target = Math.max(target, 95);
                clampTarget();
            }, 8000);
            setTimeout(forceFinish, MAX_WAIT_MS);

            // ---- ANIMAÇÃO SUAVE DO % ----
            function clampTarget() {
                if (target > 100) target = 100;
                if (target < 0) target = 0;
            }

            function tick() {
                // easing: aproxima current de target
                current += (target - current) * 0.12; // mais baixo = mais suave
                if ((target === 100 && current > 99.6) || (performance.now() - startTs > MAX_WAIT_MS)) {
                    current = 100;
                    render();
                    done();
                    return;
                }
                render();
                rafId = requestAnimationFrame(tick);
            }

            function render() {
                const pct = Math.round(current);
                if (fill) fill.style.width = pct + '%';
                if (pctEl) pctEl.textContent = pct;
            }

            function done() {
                if (finished) return;
                finished = true;

                const now = performance.now();
                const elapsed = now - startTs;
                const delay = Math.max(0, MIN_SHOW_MS - elapsed);

                setTimeout(() => {
                    pre.classList.add('is-hidden');
                    unlockScroll();

                    // cancela RAF para evitar “loop”
                    if (rafId) cancelAnimationFrame(rafId);

                    // remove do DOM após o fade
                    setTimeout(() => {
                        pre.remove();
                    }, 420);
                }, delay);
            }

            function forceFinish() {
                target = 100;
                clampTarget();
                // se nem DOM nem load aconteceram, ainda assim encerramos
                if (!finished) {
                    // dá 200ms para a barra chegar nos 100
                    setTimeout(done, 200);
                }
            }

            // inicia animação
            rafId = requestAnimationFrame(tick);
        })();
    </script>

</body>

</html>