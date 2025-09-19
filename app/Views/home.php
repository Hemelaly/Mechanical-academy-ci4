<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- jQuery (necessário para Owl Carousel) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <link rel="stylesheet" href="./assets/css/style.css" />
    <title>Mechanical Academy | Aprenda a fazer do jeito certo</title>

    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Inter";
            color: #e2e2e2;
        }

        body {
            background-color: #030712;
        }

        main {
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom,
                    rgba(3, 7, 18, 0.7) 0%,
                    /* overlay escuro no topo */
                    rgba(0, 97, 253, 0.151) 50%,
                    /* azul no meio */
                    rgba(0, 97, 253, 0) 100%
                    /* some suavemente */
                ) !important;
        }

        .navbar {
            background: transparent !important;
        }

        .navbar .navbar-brand {
            width: 150px;
        }

        #main-banner {
            background: url(<?= base_url('assets/img/grid.png') ?>) no-repeat center center/cover;
        }

        #main-banner .overlay {
            width: 100%;
            height: 100%;
            padding-bottom: 70px;
            background: linear-gradient(to bottom,
                    rgba(3, 7, 18, 0.7) 0%,
                    /* overlay escuro no topo*/
                    rgba(0, 97, 253, 0.151) 30%,
                    /* azul no meio */
                    rgba(3, 7, 18, 0.7) 100%
                    /* some suavemente */
                ) !important;
        }

        .main-title {
            font-size: 75px;
        }

        @media (max-width: 989px) {
            .main-title {
                font-size: 40px;
            }
        }

        .carousel-control-next,
        .carousel-control-prev {
            & * {
                color: #fff !important;
                background-color: #fff !important;
            }
        }

        @media (max-width: 768px) {
            .main-title {
                font-size: 40px;
            }

            .depoimentos {
                justify-content: center;
                align-items: center;

                & * {
                    text-align: center;
                }
            }

            footer .ft2 {
                flex-direction: column !important;
                align-items: start !important;
            }
        }

        @media (max-width: 576px) {
            .main-title {
                font-size: 35px;
            }
        }

        .description {
            max-width: 900px;
            width: 100%;
        }

        .img {
            width: 100%;
        }

        .bg-course-card {
            background-color: #081125;
        }

        .card-custom {
            transition: 0.6;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .card-custom:hover {
            transition: 0.6;
            background: radial-gradient(circle at center,
                    rgb(26, 47, 80) 0%,
                    /* azul suave no centro */
                    rgb(19, 23, 48) 40%,
                    /* roxo intermediário */
                    rgb(12, 12, 31) 100%
                    /* volta ao fundo escuro nas bordas */
                );
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
        }

        .course-rating {
            background-color: #030712;
        }

        .img-bw {
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }

        .course-card:hover .img-bw {
            filter: grayscale(0%) !important;
        }

        .course-card {
            transition: 0.5s;
        }

        .course-card:hover {
            transform: translateY(-10px);
            background-color: #081125;
        }

        .bg-custom-dark {
            background-color: #121b33;
        }

        footer {
            background: linear-gradient(to right,
                    #030712 0%,
                    #090f1e 30%,
                    #040916 50%,
                    #090f1e 70%,
                    #030712 100%);
        }

        /* Sempre mostrar nav e dots */
        .owl-nav,
        .owl-dots {
            display: block !important;
        }

        /* Estilo das setas */
        .owl-nav button.owl-prev,
        .owl-nav button.owl-next {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5) !important;
            color: #fff !important;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 20px !important;
            line-height: 40px;
        }

        .owl-nav button.owl-prev {
            left: -50px;
        }

        .owl-nav button.owl-next {
            right: -50px;
        }

        /* Estilo dos dots */
        .owl-dots {
            text-align: center;
            margin-top: 15px;
        }

        .owl-dot span {
            width: 12px;
            height: 12px;
            margin: 5px 7px;
            background: #bbb;
            display: block;
            border-radius: 50%;
            transition: background 0.3s;
        }

        .owl-dot.active span {
            background: #0d6efd;
        }
    </style>
</head>

<body>
    <main>
        <section id="main-banner">
            <div class="overlay">
                <header class="px-3 px-md-4 px-lg-5">
                    <nav class="navbar bg-body-tertiary bg-transparent">
                        <div class="container-fluid">
                            <a class="navbar-brand">
                                <img src="./assets/img/logo_light.png" alt="Logo" class="w-100" />
                            </a>
                            <div class="d-flex align-items-center">
                                <a href="/login" class="btn btn-light px-5 py-2 rounded-5 fw-semibold">Entrar</a>
                            </div>
                        </div>
                    </nav>
                </header>

                <div class="container">
                    <div class="row mt-5">
                        <div class="my-5"></div>
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="border rounded-5 text-center py-1" style="width: 300px;">Mechanical
                                Academy</span>
                            <h1 class="h1 fs-md-1 fs-md-3 text-center fw-bold mt-4 main-title">Aprenda a Fazer do <span
                                    class="text-primary">Jeito Certo</span> com a Mechanical Academy</h1>
                            <p class="text-center description">Na Mechanical Academy, você encontra a combinação
                                perfeita entre teoria e prática. <span class="d-none d-md-block">Nosso objetivo é
                                    ensinar de forma clara, objetiva e aplicada, garantindo que você aprenda não apenas
                                    o como fazer, mas também o porquê fazer.</span></p>
                            <div class="d-flex align-items-center flex-column flex-md-row gap-4 mt-4">
                                <a href="#cursos"
                                    class="btn btn-light py-3 rounded-5 text-decoration-none fw-medium text-center"
                                    style="width: 200px;">Ver todos cursos</a>
                                <a href="/login"
                                    class=" btn btn-outline-primary px-5 py-3 rounded-5 text-decoration-none fw-medium">Entrar</a>
                            </div>
                        </div>

                        <div class="img mt-5">
                            <img src="./assets/img/dashboard.png" alt="Academia" class="w-100 rounded-3" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="gamification" class="py-lg-5 py-md-3 py-3">
            <div class="container">
                <div class="row py-lg-5 py-md-3 py-3">
                    <div class="col-6 col-lg-3">
                        <div class="d-flex flex-column">
                            <h2 class="text-center fw-bold h1"><span class="counter" data-target="100">0</span>+</h2>
                            <p class="text-center text-secondary">
                                Alunos que já mudaram de profissão
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="d-flex flex-column">
                            <h2 class="text-center fw-bold h1"><span class="counter" data-target="2">0</span>M+</h2>
                            <p class="text-center text-secondary">
                                Alunos seguindo profissionalmente em todo o mundo
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="d-flex flex-column">
                            <h2 class="text-center fw-bold h1"><span class="counter" data-target="350">0</span>M+</h2>
                            <p class="text-center text-secondary">
                                Alunos que conseguiram seu primeiro emprego na área
                            </p>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="d-flex flex-column">
                            <h2 class="text-center fw-bold h1"><span class="counter" data-target="500">0</span>+</h2>
                            <p class="text-center text-secondary">
                                Alunos que já passaram profissionalmente para empresas Tech
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Courses Section -->
        <section class="courses-section py-5" id="cursos">
            <div class="container">
                <h2 class="section-title text-center h1 fw-semibold">Nossos Cursos</h2>
                <p class="section-subtitle text-center mb-3">
                    Aprenda na Prática com os Melhores Conteúdos em Tecnologia
                </p>

                <div class="owl-carousel owl-theme mt-5">

                    <!-- Card 1 -->
                    <div class="item">
                        <div class="card bg-course-card course-card rounded-3 overflow-hidden">
                            <img src="./assets/img/Excell.jpg" class="card-img-top img-bw" alt="..." />
                            <div class="card-body p-4">
                                <h5><span class="badge text-bg-secondary">Dados</span></h5>
                                <h3 class="card-title text-white fw-semibold">Curso de Excel</h3>
                                <p class="card-description">
                                    Aprenda a utilizar o Excel para organizar dados, criar relatórios e aplicar fórmulas que facilitam o dia a dia. </p>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stars">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <span class="course-rating p-1 rounded-1">5.0</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-bold h3">999<sub style="font-size: 12px !important;">MZN</sub></div>
                                    <a href="https://edson-09.github.io/curso-excel/" target="_blank" class="btn btn-primary">Saber mais</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="item">
                        <div class="card bg-course-card course-card rounded-3 overflow-hidden">
                            <img src="./assets/img/html-css-js.jpg" class="card-img-top img-bw" alt="..." />
                            <div class="card-body p-4">
                                <h5><span class="badge text-bg-secondary">Programacao</span></h5>
                                <h3 class="card-title text-white fw-semibold">Programação Web</h3>
                                <p class="card-description">
                                    Aprenda do zero a criar sites dinâmicos e responsivos. Domine HTML, CSS, JavaScript e muito mais.
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stars">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <span class="course-rating p-1 rounded-1">5.0</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-bold h3">999<sub style="font-size: 12px !important;">MZN</sub></div>
                                    <button class="btn btn-primary">Saber mais</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="item">
                        <div class="card bg-course-card course-card rounded-3 overflow-hidden">
                            <img src="./assets/img/p-bi.jpg" class="card-img-top img-bw" alt="..." />
                            <div class="card-body p-4">
                                <h5><span class="badge text-bg-secondary">Analise de dados</span></h5>
                                <h3 class="card-title text-white fw-semibold">Power BI</h3>
                                <p class="card-description">
                                    Aprenda a transformar dados em dashboards interativos, relatórios e insights de negócio com Power BI.
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stars">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <span class="course-rating p-1 rounded-1">5.0</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-bold h3">999<sub style="font-size: 12px !important;">MZN</sub></div>
                                    <button class="btn btn-primary">Saber mais</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="item">
                        <div class="card bg-course-card course-card rounded-3 overflow-hidden">
                            <img src="./assets/img/Cisco-01.jpg" class="card-img-top img-bw" alt="..." />
                            <div class="card-body p-4">
                                <h5><span class="badge text-bg-secondary">Redes</span></h5>
                                <h3 class="card-title text-white fw-semibold">Cisco CCNA</h3>
                                <p class="card-description">
                                    Torne-se especialista em redes, domine configuração de roteadores, switches e fundamentos de TCP/IP.
                                </p>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="stars">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                    <span class="course-rating p-1 rounded-1">5.0</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="fw-bold h3">999<sub style="font-size: 12px !important;">MZN</sub></div>
                                    <button class="btn btn-primary">Saber mais</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- NOSSA METODOLOGIA -->
        <section class="courses-section py-5" id="cursos">
            <div class="container">
                <div class="row">
                    <div class="d-flex flex-column align-items-center">
                        <h2 class="section-title text-center h1 fw-semibold">
                            Nossa Metodologia
                        </h2>
                        <p class="section-subtitle text-center mb-3 description text-center w-100">Na Mechanical
                            Academy,
                            acreditamos que a melhor forma de aprender é praticando. Por isso, desenvolvemos uma
                            metodologia
                            baseada em experiências reais, projetos práticos e um acompanhamento próximo do aluno.</p>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-lg-4 col-md-6 p-2 d-flex align-items-end">
                        <div class="card rounded-4 course-card card-custom bg-transparent border border-secondary">
                            <div class="card-body p-3">
                                <div class="rounded-5 border border-secondary d-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px">
                                    <h1 class="h1 fw-bold m-0">1</h1>
                                </div>
                                <h3 class="card-title text-white fw-semibold">Aprenda</h3>
                                <p class="card-description">
                                    Aulas teóricas e práticas com especialistas do mercado.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 p-2 d-flex align-items-end">
                        <div class="card rounded-4 course-card card-custom bg-transparent border border-secondary">
                            <div class="card-body p-4">
                                <div class="rounded-5 border border-secondary d-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px">
                                    <h1 class="h1 fw-bold m-0">2</h1>
                                </div>
                                <h3 class="card-title text-white fw-semibold">Pratique</h3>
                                <p class="card-description">Desafios reais para consolidar o conhecimento didatico.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 p-2">
                        <div class="card rounded-4 course-card card-custom bg-transparent border border-secondary py-2">
                            <div class="card-body p-4">
                                <div class="rounded-5 border border-secondary d-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px">
                                    <h1 class="h1 fw-bold m-0">3</h1>
                                </div>
                                <h3 class="card-title text-white fw-semibold">Projectos</h3>
                                <p class="card-description">Construa um portfólio com projetos profissionais para
                                    impulsionar a sua carreira.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section id="cta" class="py-5 px-1">
            <div class="container">
                <div class="row">
                    <div
                        class="p-5 py-md-3 d-flex flex-column align-items-center justify-content-center bg-white text-dark rounded-5">
                        <h1 class="h1 text-center mt-5 fw-bold">Transforme Sua Carreira em Tecnologia</h1>
                        <p class="description text-center text-dark">Na TechAcademy, não ensinamos apenas teoria -
                            capacitamos você a resolver problemas reais do mercado. Nossos alunos conseguem empregos com
                            salários 67% superiores à média em até 180 dias após a certificação.</p>
                        <a href="<?= base_url('/student/dashboard/cursos') ?>" class="btn btn-dark px-5 py-3 rounded-5 mb-5">Comecar Agora</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="py-5">
            <div class="container">
                <div class="row my-5">
                    <div class="col-lg-6 col-md-12 d-flex align-items-center">
                        <div class="d-flex flex-column depoimentos">
                            <h2 class="h1 mb-3 fw-bold">Depoimentos</h2>
                            <p>Na Mechanical Tecnologia, acreditamos que a melhor forma de aprender é praticando. Por
                                isso, desenvolvemos uma metodologia baseada em experiências reais, projetos práticos e
                                um acompanhamento próximo do aluno.</p>
                            <a href="<?= base_url('/student/dashboard/cursos') ?>" class="btn btn-light text-dark fw-medium py-3 rounded-5 mt-4"
                                style="width: 250px;">Comecar a aprender</a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mt-5">
                        <!-- Carousel wrapper -->
                        <div id="carouselExampleControls" class="carousel slide text-center carousel-dark"
                            data-bs-ride="carousel" data-bs-interval="5000">
                            <div class="carousel-inner">
                                <!-- Item 2 -->
                                <div class="carousel-item active">
                                    <img class="rounded-circle shadow-1-strong mb-4"
                                        src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/img%20(32).webp" alt="avatar"
                                        style="width: 150px;" />
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-lg-8">
                                            <h5 class="mb-3">John Doe</h5>
                                            <p class="text-white">
                                                <i class="fas fa-quote-left pe-2"></i>
                                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Minus et
                                                deleniti
                                                nesciunt sint eligendi reprehenderit reiciendis.
                                            </p>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled d-flex justify-content-center text-warning mb-0">
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="far fa-star fa-sm"></i></li>
                                    </ul>
                                </div>

                                <!-- Item 3 -->

                                <div class="carousel-item">
                                    <img class="rounded-circle shadow-1-strong mb-4"
                                        src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/img%20(1).webp" alt="avatar"
                                        style="width: 150px;" />
                                    <div class="row d-flex justify-content-center">
                                        <div class="col-lg-8">
                                            <h5 class="mb-3">Anna Deynah</h5>
                                            <p class="text-white">
                                                <i class="fas fa-quote-left pe-2"></i>
                                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Minus et
                                                deleniti
                                                nesciunt sint eligendi reprehenderit reiciendis, quibusdam illo, beatae
                                                quia.
                                            </p>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled d-flex justify-content-center text-warning mb-0">
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="fas fa-star fa-sm"></i></li>
                                        <li><i class="far fa-star fa-sm"></i></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Controls -->
                            <button class="carousel-control-prev text-white" type="button"
                                data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon text-white" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next text-white" type="button"
                                data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                <span class="carousel-control-next-icon text-white" aria-hidden="true"><i
                                        class="bi bi-chevron-left text-white"></i></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <!-- Carousel wrapper -->

                        <!-- Carousel wrapper -->
                    </div>
                </div>
            </div>
        </section>

        <footer class="py-5 bg-custom-dark">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-md-12 d-flex align-items-center">
                        <img src="./assets/img/logo_light.png" alt="Logo" style="width: 150px;">
                    </div>
                    <div class="col-lg-10 col-md-12 mt-4">
                        <div class="flex flex-column">
                            <div class="d-flex flex-row align-items-center justify-content-between ft2">
                                <div class="d-flex align-items-center ft2 gap-2">
                                    <a href="#" class="text-light text-decoration-none">Inicio</a>
                                    <a href="#" class="text-light text-decoration-none">Cursos</a>
                                    <a href="#" class="text-light text-decoration-none">Termos & Condicoes</a>
                                    <a href="#" class="text-light text-decoration-none">Politica de Privacidade</a>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <a href="https://facebook.com" target="_blank"
                                        class="text-decoration-none text-light"><i
                                            class="text-decoration-none fs-4 bi bi-facebook"></i></a>
                                    <a href="https://instagram.com" target="_blank"
                                        class="text-decoration-none text-light"><i
                                            class="text-decoration-none fs-4 bi bi-instagram"></i></a>
                                    <a href="https://linkedin.com" target="_blank"
                                        class="text-decoration-none text-light"><i
                                            class="text-decoration-none fs-4 bi bi-linkedin"></i></a>
                                    <a href="https://youtube.com" target="_blank"
                                        class="text-decoration-none text-light"><i
                                            class="text-decoration-none fs-4 bi bi-youtube"></i></a>
                                </div>
                            </div>

                            <hr class="hr">

                            <p class="mb-3">Mechanical Academy &copy; 2025 Todos os direitos reservados. Desenvolvido do
                                jeito certo pela
                                <a href="https://mechanical.co.mz" target="_blank"
                                    class="text-decoration-none">Mechanical Tecnologia.</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $(".owl-carousel").owlCarousel({
                loop: true, // loop infinito
                margin: 20, // espaço entre cards
                nav: true, // botões anterior/próximo
                dots: true, // bolinhas de navegação
                autoplay: true, // autoplay
                autoplayTimeout: 4000, // tempo entre slides (4s)
                autoplayHoverPause: true, // pausa no hover
                responsive: {
                    0: {
                        items: 1
                    }, // mobile: 1 card
                    768: {
                        items: 2
                    }, // tablet: 2 cards
                    1200: {
                        items: 3
                    } // desktop: 3 cards
                }
            });
        });


        // Função que anima os contadores
        function animateCounter(counter) {
            const target = +counter.getAttribute("data-target"); // número final
            const increment = target / 100; // velocidade da contagem
            let current = 0;

            const update = () => {
                current += increment;
                if (current < target) {
                    counter.innerText = Math.ceil(current);
                    requestAnimationFrame(update);
                } else {
                    counter.innerText = target;
                }
            };

            update();
        }

        // Ativar a animação quando o usuário acessar a seção
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll(".counter");
            counters.forEach((counter) => animateCounter(counter));
        });
    </script>
</body>

</html>