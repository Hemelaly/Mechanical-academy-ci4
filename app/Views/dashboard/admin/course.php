<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cursos - Mechanical Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Outfit Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    <link rel="stylesheet" href="./assets/css/course.css">
</head>

<body>

    <?php include 'functions.php' ?>
    <?php echo getSidebar('Meus Curos') ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item">
                    <a href="#" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item active text-light" aria-current="page">Ver Curso</li>
            </ol>
        </nav>

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-light mb-0">Ver Curso</h3>
        </div>

        <div class="row">
            <!-- Video Section -->
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card p-3 rounded-4">
                    <div class="card-body">
                        <!-- YouTube Video Embed -->
                        <div class="ratio ratio-16x9 rounded-4">
                            <iframe id="player" class="rounded-4" width="100%" height="100%"
                                src="https://www.youtube.com/embed/eWb14aj5pAQ"
                                title="Design UI de um Site de Emprego #figma" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="card-body">
                        <h4 class="text-light mb-3"><span id="module">Módulo 1</span>: <span id="title">Tipos de
                                Dados</span></h4>
                        <div class="d-flex align-items-center mb-3 text-muted">
                            <div>
                                <i class="bi bi-clock me-2"></i>
                                <span class="me-3"><span id="time">45</span> minutos</span>
                            </div>
                            <div>
                                <i class="bi bi-eye me-2"></i>
                                <span class="me-3"><span id="views">1,234</span> visualizações</span>
                            </div>
                        </div>
                        <p class="text-light" id="description">Nesta aula, você aprenderá como criar e utilizar hooks
                            personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de
                            lógica entre componentes.</p>
                    </div>
                </div>
            </div>

            <!-- Course Content Sidebar -->
            <div class="col-lg-4 col-md-12">
                <div class="card p-4 rounded-4">
                    <div class="card-header border-bottom mb-3 d-flex justify-content-between align-items-center">
                        <h5 class="my-2 text-light">Conteúdo do Curso</h5>
                    </div>
                    <div class="card-body p-0">
                        <!-- Accordion for Course Modules -->
                        <div class="accordion accordion-flush" id="courseAccordion">
                            <!-- Module 1 -->
                            <div class="accordion-item border rounded-4 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#module1" aria-expanded="true" aria-controls="module1">
                                        <i class="bi bi-chevron-down me-2"></i>
                                        <span class="fw-bold">Módulo 1: Introdução</span>
                                        <span class="module-progress ms-auto">1/3</span>
                                    </button>
                                </h2>
                                <div id="module1" class="accordion-collapse collapse show"
                                    data-bs-parent="#courseAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="lesson-item px-3" data-video="https://youtu.be/fga6PqjiHqY"
                                            data-video-id="1" data-title="Configuracao do Ambiente"
                                            data-module="Modulo 1"
                                            data-description="Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes."
                                            data-views="1,559" data-time="35">
                                            <div class="lesson-icon completed">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Configuração do Ambiente</div>
                                                <div class="lesson-duration">5 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item px-3" data-video="https://youtu.be/P4sSx_H7xm4"
                                            data-video-id="2" data-title="Concitos Basicos" data-module="Modulo 1"
                                            data-description="Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes.Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes."
                                            data-views="3,559" data-time="19">
                                            <div class="lesson-icon completed">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Conceitos Básicos</div>
                                                <div class="lesson-duration">22 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item px-3" data-video="https://youtu.be/PfyDfxEsQpo"
                                            data-video-id="3" data-title="Conceitos Basicos - Parte 2"
                                            data-module="Modulo 1"
                                            data-description="Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes.Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes.<br>Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes."
                                            data-views="5,559" data-time="51">
                                            <div class="lesson-icon completed">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Conceitos Básicos</div>
                                                <div class="lesson-duration">8 min</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Module 2 -->
                            <div class="accordion-item border rounded-4 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#module2" aria-expanded="false" aria-controls="module2">
                                        <i class="bi bi-chevron-down me-2"></i>
                                        <span class="fw-bold">Módulo 2: Componentes Avançados</span>
                                        <span class="module-progress ms-auto">2/3</span>
                                    </button>
                                </h2>
                                <div id="module2" class="accordion-collapse collapse" data-bs-parent="#courseAccordion">
                                    <div class="accordion-body">
                                        <div class="lesson-item">
                                            <div class="lesson-icon completed">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Configuração do Ambiente</div>
                                                <div class="lesson-duration">15 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item">
                                            <div class="lesson-icon">
                                                <i class="bi bi-play-fill text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Conceitos Básicos</div>
                                                <div class="lesson-duration">22 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item">
                                            <div class="lesson-icon">
                                                <i class="bi bi-play-fill text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Conceitos Básicos</div>
                                                <div class="lesson-duration">8 min</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Module 3 -->
                            <div class="accordion-item border rounded-4 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#module3" aria-expanded="false" aria-controls="module3">
                                        <i class="bi bi-chevron-down me-2"></i>
                                        <span class="fw-bold">Módulo 3: Hooks Avançados</span>
                                        <span class="module-progress ms-auto">1/4</span>
                                    </button>
                                </h2>
                                <div id="module3" class="accordion-collapse collapse" data-bs-parent="#courseAccordion">
                                    <div class="accordion-body">
                                        <div class="lesson-item">
                                            <div class="lesson-icon">
                                                <i class="bi bi-play-fill text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Introdução aos Hooks</div>
                                                <div class="lesson-duration">12 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item">
                                            <div class="lesson-icon">
                                                <i class="bi bi-play-fill text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">useState e useEffect</div>
                                                <div class="lesson-duration">18 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item">
                                            <div class="lesson-icon">
                                                <i class="bi bi-play-fill text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Hooks Personalizados</div>
                                                <div class="lesson-duration">25 min</div>
                                            </div>
                                        </div>
                                        <div class="lesson-item">
                                            <div class="lesson-icon">
                                                <i class="bi bi-play-fill text-white"></i>
                                            </div>
                                            <div class="lesson-content">
                                                <div class="lesson-title">Exercícios Práticos</div>
                                                <div class="lesson-duration">30 min</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>