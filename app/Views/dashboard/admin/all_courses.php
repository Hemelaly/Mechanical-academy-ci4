<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mechanical Dashboard</title>
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
    <link rel="stylesheet" href="./assets/css/all_courses.css">
</head>

<body>

    <?php include 'functions.php' ?>
    <?php echo getSidebar('Todos Cursos') ?>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0 m-0">
                <li class="breadcrumb-item">
                    <a href="index.html" class="text-decoration-none text-muted">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="breadcrumb-item active text-light" aria-current="page">Todos Cursos</li>
            </ol>
        </nav>
        <!-- Courses Section -->
        <div class="courses-section">
            <div class="row px-3">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-light px-5 py-3" id="todos" data-bs-toggle="tab"
                            data-bs-target="#todos-pane" type="button" role="tab" aria-controls="todos-tab-pane"
                            aria-selected="true"><i class="bi bi-play-circle"></i>
                            Todos Cursos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-light px-5 py-3" id="activos-tab" data-bs-toggle="tab"
                            data-bs-target="#activos-tab-pane" type="button" role="tab" aria-controls="activos-tab-pane"
                            aria-selected="false"><i class="bi bi-check-circle"></i> Activos</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-light px-5 py-3" id="pendentes-tab" data-bs-toggle="tab"
                            data-bs-target="#pendentes-tab-pane" type="button" role="tab"
                            aria-controls="pendentes-tab-pane" aria-selected="false"><i class="bi bi-clock"></i>
                            Pendentes</button>
                    </li>
                </ul>
                <div class="tab-content py-2 rounded-4 mt-4 px-0 bg-custom-dark" id="myTabContent">
                    <div class="tab-pane rounded-2 fade show active" id="todos-pane" role="tabpanel"
                        aria-labelledby="todos" tabindex="0">
                        <div class="row">
                            <div class="d-flex flex-wrap">
                                <div class="col-lg-4 mb-4 p-3 m-p-1">
                                    <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                        <div class="course-image">
                                            <img class="w-100" src="./assets/img/Cisco-01.jpg" />
                                        </div>

                                        <div class="course-info p-4">
                                            <h5
                                                class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                                <span>Cisco CCNA</span> <span
                                                    class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                        class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                    Activo</span>
                                            </h5>

                                            <div class="d-flex align-items-center gap-1 mb-3">
                                                <div class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <span class="rating-score">5.0</span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                        Honwana</span></p>
                                                <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                                <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                            </div>

                                            <div class="d-flex align-items-center gap-5">
                                                <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Editar
                                                </a>
                                                <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4 p-3 m-p-1">
                                    <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                        <div class="course-image">
                                            <img class="w-100" src="./assets/img/php-mysql.jpg" />
                                        </div>

                                        <div class="course-info p-4">
                                            <h5
                                                class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                                <span>Cisco CCNA</span> <span
                                                    class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                        class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                    Activo</span>
                                            </h5>

                                            <div class="d-flex align-items-center gap-1 mb-3">
                                                <div class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <span class="rating-score">5.0</span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                        Honwana</span></p>
                                                <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                                <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                            </div>

                                            <div class="d-flex align-items-center gap-5">
                                                <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Editar
                                                </a>
                                                <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4 p-3 m-p-1">
                                    <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                        <div class="course-image">
                                            <img class="w-100" src="./assets/img/ms-p.jpg" />
                                        </div>

                                        <div class="course-info p-4">
                                            <h5
                                                class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                                <span>Cisco CCNA</span> <span
                                                    class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                        class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                    Activo</span>
                                            </h5>

                                            <div class="d-flex align-items-center gap-1 mb-3">
                                                <div class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <span class="rating-score">5.0</span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                        Honwana</span></p>
                                                <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                                <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                            </div>

                                            <div class="d-flex align-items-center gap-5">
                                                <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Editar
                                                </a>
                                                <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4 p-3 m-p-1">
                                    <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                        <div class="course-image">
                                            <img class="w-100" src="./assets/img/Excell.jpg" />
                                        </div>

                                        <div class="course-info p-4">
                                            <h5
                                                class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                                <span>Cisco CCNA</span> <span
                                                    class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                        class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                    Activo</span>
                                            </h5>

                                            <div class="d-flex align-items-center gap-1 mb-3">
                                                <div class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <span class="rating-score">5.0</span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                        Honwana</span></p>
                                                <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                                <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                            </div>

                                            <div class="d-flex align-items-center gap-5">
                                                <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Editar
                                                </a>
                                                <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4 p-3 m-p-1">
                                    <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                        <div class="course-image">
                                            <img class="w-100" src="./assets/img/html-css-js.jpg" />
                                        </div>

                                        <div class="course-info p-4">
                                            <h5
                                                class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                                <span>Cisco CCNA</span> <span
                                                    class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                        class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                    Activo</span>
                                            </h5>

                                            <div class="d-flex align-items-center gap-1 mb-3">
                                                <div class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <span class="rating-score">5.0</span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                        Honwana</span></p>
                                                <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                                <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                            </div>

                                            <div class="d-flex align-items-center gap-5">
                                                <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Editar
                                                </a>
                                                <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-4 p-3 m-p-1">
                                    <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                        <div class="course-image">
                                            <img class="w-100" src="./assets/img/Sense.jpg" />
                                        </div>

                                        <div class="course-info p-4">
                                            <h5
                                                class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                                <span>Cisco CCNA</span> <span
                                                    class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                        class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                    Activo</span>
                                            </h5>

                                            <div class="d-flex align-items-center gap-1 mb-3">
                                                <div class="stars">
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star-fill"></i>
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <span class="rating-score">5.0</span>
                                            </div>

                                            <div class="mb-4">
                                                <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                        Honwana</span></p>
                                                <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                                <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                            </div>

                                            <div class="d-flex align-items-center gap-5">
                                                <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Editar
                                                </a>
                                                <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Excluir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane rounded-2 fade" id="activos-tab-pane" role="tabpanel"
                        aria-labelledby="activos-tab" tabindex="1">
                        <div class="row">
                            <div class="col-lg-4 mb-4 p-3 m-p-1">
                                <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                    <div class="course-image">
                                        <img class="w-100" src="./assets/img/Cisco-01.jpg" />
                                    </div>

                                    <div class="course-info p-4">
                                        <h5
                                            class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                            <span>Cisco CCNA</span> <span
                                                class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                    class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                Activo</span>
                                        </h5>

                                        <div class="d-flex align-items-center gap-1 mb-3">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="rating-score">5.0</span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                    Honwana</span></p>
                                            <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                            <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Editar
                                            </a>
                                            <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                <i class="bi bi-trash me-2"></i>
                                                Excluir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4 p-3 m-p-1">
                                <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                    <div class="course-image">
                                        <img class="w-100" src="./assets/img/php-mysql.jpg" />
                                    </div>

                                    <div class="course-info p-4">
                                        <h5
                                            class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                            <span>Cisco CCNA</span> <span
                                                class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                    class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                Activo</span>
                                        </h5>

                                        <div class="d-flex align-items-center gap-1 mb-3">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="rating-score">5.0</span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                    Honwana</span></p>
                                            <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                            <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Editar
                                            </a>
                                            <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                <i class="bi bi-trash me-2"></i>
                                                Excluir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4 p-3 m-p-1">
                                <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                    <div class="course-image">
                                        <img class="w-100" src="./assets/img/ms-p.jpg" />
                                    </div>

                                    <div class="course-info p-4">
                                        <h5
                                            class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                            <span>Cisco CCNA</span> <span
                                                class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                    class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                Activo</span>
                                        </h5>

                                        <div class="d-flex align-items-center gap-1 mb-3">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="rating-score">5.0</span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                    Honwana</span></p>
                                            <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                            <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Editar
                                            </a>
                                            <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                <i class="bi bi-trash me-2"></i>
                                                Excluir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4 p-3 m-p-1">
                                <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                    <div class="course-image">
                                        <img class="w-100" src="./assets/img/Excell.jpg" />
                                    </div>

                                    <div class="course-info p-4">
                                        <h5
                                            class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                            <span>Cisco CCNA</span> <span
                                                class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                    class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                Activo</span>
                                        </h5>

                                        <div class="d-flex align-items-center gap-1 mb-3">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="rating-score">5.0</span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                    Honwana</span></p>
                                            <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                            <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Editar
                                            </a>
                                            <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                <i class="bi bi-trash me-2"></i>
                                                Excluir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane rounded-2 fade" id="pendentes-tab-pane" role="tabpanel"
                        aria-labelledby="pendentes-tab" tabindex="1">
                        <div class="row">
                            <div class="col-lg-4 mb-4 p-3 m-p-1">
                                <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                    <div class="course-image">
                                        <img class="w-100" src="./assets/img/Cisco-01.jpg" />
                                    </div>

                                    <div class="course-info p-4">
                                        <h5
                                            class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                            <span>Cisco CCNA</span> <span
                                                class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                    class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                Activo</span>
                                        </h5>

                                        <div class="d-flex align-items-center gap-1 mb-3">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="rating-score">5.0</span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                    Honwana</span></p>
                                            <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                            <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Editar
                                            </a>
                                            <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                <i class="bi bi-trash me-2"></i>
                                                Excluir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 mb-4 p-3 m-p-1">
                                <div class="course-card w-100 rounded-4 overflow-hidden bg-card">
                                    <div class="course-image">
                                        <img class="w-100" src="./assets/img/php-mysql.jpg" />
                                    </div>

                                    <div class="course-info p-4">
                                        <h5
                                            class="lh-base fw-bold fs-4 d-flex align-items-center justify-content-between">
                                            <span>Cisco CCNA</span> <span
                                                class="bage bg-success-subtle text-success fs-6 rounded-5 d-flex align-items-center pe-2"><i
                                                    class="bi bi-dot fs-1" style="line-height: 0px;"></i>
                                                Activo</span>
                                        </h5>

                                        <div class="d-flex align-items-center gap-1 mb-3">
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star"></i>
                                            </div>
                                            <span class="rating-score">5.0</span>
                                        </div>

                                        <div class="mb-4">
                                            <p class="h5"><b>Instructor: </b> <span class="text-muted">Milton
                                                    Honwana</span></p>
                                            <p class="h5"><b>Inscritos: </b> <span class="text-muted">12</span></p>
                                            <p class="h5"><b>Conslusao: </b> <span class="text-muted">1%</span></p>
                                        </div>

                                        <div class="d-flex align-items-center gap-5">
                                            <a href="./course.html" class=" py-3 w-100 btn btn-primary course-btn">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Editar
                                            </a>
                                            <a href="./course.html" class=" py-3 w-100 btn btn-danger course-btn">
                                                <i class="bi bi-trash me-2"></i>
                                                Excluir
                                            </a>
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
    <!-- Custom JS -->
    <script src="./assets/js/sidebar.js"></script>
</body>

</html>