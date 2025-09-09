<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Painel do Estudante<?= $this->endSection() ?>

<?= $this->section('home_student') ?>
<!-- Banner -->
<div class="gradient-banner">
    <div>
        <h4>Olá, <?php echo $user->username ?>!</h4>
        <p class="text-light mb-0">
            Bem-vindo de volta ao seu painel de estudante. Acompanhe seu
            desempenho e engajamento dos alunos.
        </p>
    </div>
    <div class="d-flex gap-2 mt-4">
        <a
            href=""
            style="text-decoration: none"
            class="btn-modern btn-modern-primary">
            Ver Meus Cursos
        </a>
        <a
            href=""
            style="text-decoration: none"
            class="btn-modern bg-light text-dark">
            Ver Todos Cursos
        </a>
    </div>
</div>

<!-- Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div
            class="card text-white d-flex flex-row align-items-center justify-content-between">
            <div class="info">
                <h6>Total de Cursos</h6>
                <h3 class="fw-bold">24</h3>
                <small class="text-success">+3 novos este mês</small>
            </div>
            <div class="ico badge bg-success-100 p-3">
                <i class="bi bi-book fs-6 text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div
            class="card text-white d-flex flex-row align-items-center justify-content-between">
            <div class="info">
                <h6>Alunos Inscritos</h6>
                <h3 class="fw-bold">1,245</h3>
                <small class="text-success">+150 este mês</small>
            </div>
            <div class="ico badge bg-success-100 p-3">
                <i class="bi bi-people fs-6 text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div
            class="card text-white d-flex flex-row align-items-center justify-content-between">
            <div class="info">
                <h6>Receita Mensal</h6>
                <h3 class="fw-bold">R$ 5,430</h3>
                <small class="text-success">+R$ 800 este mês</small>
            </div>
            <div class="ico badge bg-success-100 p-3">
                <i class="bi bi-currency-dollar fs-6 text-success"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div
            class="card text-white d-flex flex-row align-items-center justify-content-between">
            <div class="info">
                <h6>Avaliação Média</h6>
                <h3 class="fw-bold">4.8/5</h3>
                <small class="text-success">⭐ 4.8</small>
            </div>
            <div class="ico badge bg-success-100 p-3">
                <i class="bi bi-star fs-6 text-success"></i>
            </div>
        </div>
    </div>
</div>

<!-- Cursos em Destaque -->
<div class="card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Cursos em Destaque</h5>
        <a href="#" class="text-primary fw-semibold">Ver todos os cursos</a>
    </div>

    <div class="row g-3">
        <!-- Curso 1 -->
        <div class="col-md-4">
            <div
                class="card h-100 p-3"
                style="background: #1e293b; border: 1px solid #334155">
                <div
                    class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-1 text-white">
                            JavaScript Avançado
                        </h6>
                        <small class="text-muted">234 alunos</small>
                    </div>
                    <span class="badge bg-info">Ativo</span>
                </div>
                <div class="progress mb-2" style="height: 6px">
                    <div
                        class="progress-bar bg-primary"
                        style="width: 85%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">85% completo</small>
                    <small class="text-warning">⭐ 4.9</small>
                </div>
            </div>
        </div>

        <!-- Curso 2 -->
        <div class="col-md-4">
            <div
                class="card h-100 p-3"
                style="background: #1e293b; border: 1px solid #334155">
                <div
                    class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-1 text-white">
                            React para Iniciantes
                        </h6>
                        <small class="text-muted">189 alunos</small>
                    </div>
                    <span class="badge bg-success">Popular</span>
                </div>
                <div class="progress mb-2" style="height: 6px">
                    <div
                        class="progress-bar bg-success"
                        style="width: 92%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">92% completo</small>
                    <small class="text-warning">⭐ 4.7</small>
                </div>
            </div>
        </div>

        <!-- Curso 3 -->
        <div class="col-md-4">
            <div
                class="card h-100 p-3"
                style="background: #1e293b; border: 1px solid #334155">
                <div
                    class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Node.js Completo</h6>
                        <small class="text-muted">152 alunos</small>
                    </div>
                    <span class="badge bg-warning text-dark">Em alta</span>
                </div>
                <div class="progress mb-2" style="height: 6px">
                    <div
                        class="progress-bar bg-warning"
                        style="width: 75%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">75% completo</small>
                    <small class="text-warning">⭐ 4.8</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Atividade e Metas -->
<div class="row g-3">
    <!-- Atividade Recente -->
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="fw-bold mb-3">Atividade Recente</h5>
            <ul class="list-unstyled">
                <li class="d-flex align-items-center mb-3">
                    <div
                        class="badge bg-primary p-3 rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-plus fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold text-white">
                            25 novos alunos se inscreveram
                        </p>
                        <small class="text-muted">Há 2 horas</small>
                    </div>
                </li>
                <li class="d-flex align-items-center mb-3">
                    <div
                        class="badge bg-success p-3 rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold text-white">
                            15 alunos completaram o curso
                        </p>
                        <small class="text-muted">Hoje</small>
                    </div>
                </li>
                <li class="d-flex align-items-center">
                    <div
                        class="badge bg-warning p-3 rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-star-fill fs-5"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold text-white">
                            8 novas avaliações recebidas
                        </p>
                        <small class="text-muted">Há 1 dia</small>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Próximas Metas -->
    <div class="col-md-6">
        <div class="card p-3">
            <h5 class="fw-bold mb-3">Próximas Metas</h5>

            <div class="mb-3">
                <div class="d-flex justify-content-between text-white">
                    <p class="mb-1 fw-semibold">+100 alunos</p>
                    <small class="text-muted">65%</small>
                </div>
                <div class="progress">
                    <div
                        class="progress-bar bg-primary"
                        style="width: 65%"></div>
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between text-white">
                    <p class="mb-1 fw-semibold">Avaliação 4.9+</p>
                    <small class="text-muted">80%</small>
                </div>
                <div class="progress">
                    <div
                        class="progress-bar bg-success"
                        style="width: 80%"></div>
                </div>
            </div>

            <div>
                <div class="d-flex justify-content-between text-white">
                    <p class="mb-1 fw-semibold">Novo curso</p>
                    <small class="text-muted">30%</small>
                </div>
                <div class="progress">
                    <div
                        class="progress-bar bg-warning"
                        style="width: 30%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>