<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Assistir<?= $this->endSection() ?>

<?= $this->section('lessons') ?>
<style>
    .sidebar2 {
        padding: 20px 0;
    }

    .main-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        color: #8159fbff;
        font-size: 14px;
    }

    .breadcrumb-nav a {
        color: #a78bfa;
        text-decoration: none;
    }

    .breadcrumb-nav .separator {
        margin: 0 10px;
    }

    .progress-section {
        background: linear-gradient(135deg, #1a1473ff 0%, #1e1b4b 100%);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .progress-title {
        color: #cbd5e1;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .progress-percentage {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background-color: #312e81;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #8b5cf6, #a855f7);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 20px;
    }

    .video-section {
        background-color: #312e81;
        border-radius: 12px;
        overflow: hidden;
        align-self: start;
    }

    .video-player {
        position: relative;
        width: 100%;
        aspect-ratio: 16/9;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .video-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .play-button {
        position: absolute;
        width: 60px;
        height: 60px;
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .play-button:hover {
        background-color: rgba(0, 0, 0, 0.8);
        transform: scale(1.1);
    }

    .video-controls {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background-color: #1e1b4b;
        gap: 12px;
    }

    .time-display {
        font-size: 14px;
        color: #cbd5e1;
    }

    .lesson-info {
        padding: 24px;
    }

    .lesson-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .lesson-meta {
        display: flex;
        gap: 20px;
        margin-bottom: 16px;
        font-size: 14px;
        color: #a78bfa;
    }

    .lesson-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .lesson-item {
        text-decoration: none;
        color: #fff;
    }

    .lesson-description {
        color: #cbd5e1;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .lesson-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-primary-custom {
        background-color: #8b5cf6;
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        background-color: #7c3aed;
        transform: translateY(-1px);
    }

    .btn-secondary-custom {
        background: none;
        border: 1px solid #4c1d95;
        color: #a78bfa;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary-custom:hover {
        border-color: #8b5cf6;
        color: #8b5cf6;
    }

    .collapse-all-btn {
        background: none;
        border: none;
        color: #a78bfa;
        font-size: 14px;
        cursor: pointer;
    }

    .module-item {
        border-bottom: 1px solid #4c1d95;
    }

    .module-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .module-header:hover {
        background-color: #4c1d95;
    }

    .module-header.active {
        background-color: #4c1d95;
    }

    .module-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .module-icon {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8b5cf6;
    }

    .module-title {
        font-weight: 600;
        font-size: 14px;
    }

    .module-progress {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #a78bfa;
    }

    .lessons-container {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .lessons-container.show {
        max-height: 1000px;
    }

    .lesson-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px 12px 44px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        border-bottom: 1px solid #4c1d95;
    }

    .lesson-item:hover {
        background-color: #4c1d95;
    }

    .lesson-item.current {
        background-color: #8b5cf6;
    }

    .lesson-item.completed {
        background-color: rgba(34, 197, 94, 0.1);
    }

    .lesson-info-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lesson-status {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    .lesson-status.completed {
        background-color: #22c55e;
        color: white;
    }

    .lesson-status.current {
        background-color: #8b5cf6;
        color: white;
    }

    .lesson-status.pending {
        background-color: #4c1d95;
        color: #a78bfa;
    }

    .lesson-duration {
        color: #a78bfa;
        font-size: 12px;
    }

    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }

    .nav-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #8b5cf6;
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .nav-btn:hover {
        background-color: #7c3aed;
    }

    .nav-btn.secondary {
        background-color: #4c1d95;
    }

    .nav-btn.secondary:hover {
        background-color: #6b21a8;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="main-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <a href="/student/dashboard/meus_cursos">← Voltar aos Cursos</a>
        <span class="separator">/</span>
        <span><?= $course->title_course ?></span>
    </div>

    <!-- Progress Section -->
    <div class="progress-section">
        <div class="progress-title">Progresso do Curso</div>
        <div class="progress-percentage" id="progressPercentage">75%</div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progressBar" style="width: 75%;"></div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Vídeo -->
        <div class="video-section">
            <div class="video-player">
                <?php
                function getYouTubeId($url)
                {
                    preg_match(
                        '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/',
                        $url,
                        $matches
                    );
                    return $matches[1] ?? null;
                }
                $videoId = getYouTubeId($lesson->video_url_lesson);
                ?>
                <?php if ($videoId): ?>
                    <iframe width="100%" height="100%"
                        src="https://www.youtube.com/embed/<?= esc($videoId) ?>?rel=0&autoplay=0"
                        title="<?= esc($lesson->title_lesson) ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                <?php else: ?>
                    <p class="text-danger">Link de vídeo inválido</p>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="lesson-info">
                <h2 class="lesson-title"><?= esc($lesson->title_lesson) ?></h2>
                <div class="lesson-meta">
                    <div class="lesson-meta-item">⏱ <?= esc($lesson->duration_lesson) ?> minutos</div>
                    <div class="lesson-meta-item">📅 <?= date('d/m/Y', strtotime($lesson->created_at)) ?></div>
                </div>
                <p class="lesson-description"><?= esc($lesson->content_lesson) ?></p>
            </div>
        </div>


        <!-- Sidebar -->
        <div class="sidebar2" style="background: transparent;">
            <div class="sidebar-header">
                <h3 class="sidebar-title">Conteúdo do Curso</h3>
            </div>

            <div class="accordion" id="courseAccordion">
                <?php foreach ($modules as $index => $m): ?>
                    <div class="accordion-item mb-2" style="background-color: #1e1b4b; border-radius: 8px; border: none;">
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>"
                                aria-expanded="false" aria-controls="collapse<?= $index ?>"
                                style="background-color: #1e1b4b; color: #a78bfa; font-weight: 600; border-radius: 8px;">
                                <div class="module-info d-flex align-items-center gap-2">
                                    <div class="module-icon" style="width: 8px; height: 8px; border-radius: 50%; background-color: #8b5cf6;"></div>
                                    <span class="module-title"><?= esc($m->title_module) ?></span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?php if ($index === 0) echo 'show'; ?>" aria-labelledby="heading<?= $index ?>">
                            <div class="accordion-body p-0">
                                <?php foreach ($m->lessons as $l): ?>
                                    <a href="<?= site_url('student/dashboard/ver_aulas/' . $l->id_lesson) ?>"
                                        class="lesson-item <?= $l->id_lesson == $lesson->id_lesson ? 'current' : '' ?>"
                                        style="display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #4c1d95; color: #cbd5e1; text-decoration: none; transition: background 0.3s;">
                                        <div class="lesson-info-item d-flex align-items-center gap-2">
                                            <div class="lesson-status <?= $l->id_lesson == $lesson->id_lesson ? 'current' : 'pending' ?>"
                                                style="width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; background-color: <?= $l->id_lesson == $lesson->id_lesson ? '#8b5cf6' : '#4c1d95' ?>;">
                                                <?= $l->id_lesson == $lesson->id_lesson ? '▶' : '○' ?>
                                            </div>
                                            <span><?= esc($l->title_lesson) ?></span>
                                        </div>
                                        <div class="lesson-duration"><?= esc($l->duration_lesson) ?> min</div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Navigation Buttons -->
    <div class="navigation-buttons">
        <?php if ($prevLesson): ?>
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $prevLesson) ?>" class="nav-btn secondary">← Aula Anterior</a>
        <?php endif; ?>
        <?php if ($nextLesson): ?>
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $nextLesson) ?>" class="nav-btn">Próxima Aula →</a>
        <?php endif; ?>
    </div>
</div>

<!-- <script>
    // Estado
    let currentLesson = 'Higher-Order Components';

    // Dados das aulas com ID do YouTube
    const lessons = {
        'Configuração do Ambiente': {
            duration: 15,
            description: 'Aprenda a configurar seu ambiente de desenvolvimento para React.',
            completed: true,
            youtubeId: 'abc123xyz'
        },
        'Conceitos Básicos': {
            duration: 22,
            description: 'Revisão dos conceitos fundamentais do React.',
            completed: true,
            youtubeId: 'def456uvw'
        },
        'Exercício Prático 1': {
            duration: 30,
            description: 'Primeiro exercício prático do curso.',
            completed: true,
            youtubeId: 'ghi789rst'
        },
        'Higher-Order Components': {
            duration: 35,
            description: 'Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React...',
            completed: false,
            youtubeId: 'jkl012mno'
        },
        'Render Props': {
            duration: 25,
            description: 'Aprenda sobre o padrão Render Props no React.',
            completed: true,
            youtubeId: 'pqr345stu'
        },
        'Projeto Prático': {
            duration: 45,
            description: 'Desenvolva um projeto prático aplicando os conceitos aprendidos.',
            completed: false,
            youtubeId: 'vwx678yz1'
        },
        'Exercícios Avançados': {
            duration: 40,
            description: 'Exercícios mais complexos para fixar o conhecimento.',
            completed: false,
            youtubeId: '234abc567'
        }
    };

    // Seleciona uma aula
    function selectLesson(title) {
        currentLesson = title;

        const lesson = lessons[title];

        // Atualiza informações da aula
        document.getElementById('lessonTitle').textContent = title;
        document.getElementById('lessonDuration').textContent = `${lesson.duration} minutos`;
        document.getElementById('lessonDescription').textContent = lesson.description;

        // Atualiza vídeo do YouTube
        const videoPlayer = document.getElementById('youtubePlayer');
        videoPlayer.src = `https://www.youtube.com/embed/${lesson.youtubeId}?rel=0&autoplay=1`;

        // Atualiza barra lateral
        document.querySelectorAll('.lesson-item').forEach(item => {
            item.classList.remove('current');
            if (item.textContent.includes(title)) item.classList.add('current');
        });

        showNotification(`Agora assistindo: ${title}`);
    }

    // Marcar aula como concluída
    function markAsCompleted() {
        if (!lessons[currentLesson].completed) {
            lessons[currentLesson].completed = true;
            showNotification('Aula marcada como concluída!');

            // Atualiza sidebar
            document.querySelectorAll('.lesson-item').forEach(item => {
                if (item.textContent.includes(currentLesson)) {
                    const status = item.querySelector('.lesson-status');
                    item.classList.remove('current', 'pending');
                    item.classList.add('completed');
                    status.classList.remove('current', 'pending');
                    status.classList.add('completed');
                    status.innerHTML = '✓';
                }
            });

            updateProgress();
        }
    }

    // Adicionar nota
    function addNote() {
        const note = prompt('Digite sua nota:');
        if (note) showNotification('Nota adicionada com sucesso!');
    }

    // Favoritar
    function addToFavorites() {
        showNotification('Aula adicionada aos favoritos!');
    }

    // Atualiza progresso do curso
    function updateProgress() {
        const totalLessons = Object.keys(lessons).length;
        const completedLessons = Object.values(lessons).filter(l => l.completed).length;
        const percentage = Math.round((completedLessons / totalLessons) * 100);

        document.getElementById('progressPercentage').textContent = `${percentage}%`;
        document.getElementById('progressBar').style.width = `${percentage}%`;
    }

    // Navegação entre aulas
    function previousLesson() {
        const lessonTitles = Object.keys(lessons);
        const index = lessonTitles.indexOf(currentLesson);
        if (index > 0) selectLesson(lessonTitles[index - 1]);
    }

    function nextLesson() {
        const lessonTitles = Object.keys(lessons);
        const index = lessonTitles.indexOf(currentLesson);
        if (index < lessonTitles.length - 1) selectLesson(lessonTitles[index + 1]);
    }

    // Colapsar módulos
    function toggleModule(moduleId) {
        const module = document.getElementById(moduleId);
        const header = module.previousElementSibling;

        module.classList.toggle('show');
        header.classList.toggle('active');
    }

    function collapseAll() {
        document.querySelectorAll('.lessons-container').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.module-header').forEach(h => h.classList.remove('active'));
    }

    // Notificações
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #8b5cf6;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 1000;
            font-weight: 500;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.style.transform = 'translateX(0)', 100);
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }

    // Inicialização
    updateProgress();
    selectLesson(currentLesson);

    // Botão voltar
    function goBack() {
        showNotification('Voltando aos cursos...');
    }
</script> -->

<?= $this->endSection() ?>