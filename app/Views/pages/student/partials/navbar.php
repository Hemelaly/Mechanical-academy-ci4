<nav class="navbar navbar-expand-lg navbar-dark mb-4 p-3 d-block d-md-none" style="background: #1f293a; border-radius: 15px;">
    <div class="container-fluid d-flex justify-content-between">
        <div class="image mb-4 mb-lg-0" style="width: 120px; height: auto;">
            <img class="img-fluid" src="<?= base_url('assets/img/logo_light.png') ?>" alt="">
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 text-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/student/dashboard">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/dashboard/meus_cursos">Meus Cursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/dashboard/cursos">Todos Cursos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/dashboard/perfil">Perfil</a>
                </li>
            </ul>
            <form class="d-flex d-none d-md-block" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>