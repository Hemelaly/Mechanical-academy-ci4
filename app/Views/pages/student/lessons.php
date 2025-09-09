<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Assistir<?= $this->endSection() ?>

<?= $this->section('lessons') ?>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        background-color: #1e1b4b;
        color: #ffffff;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        margin: 0;
        padding: 0;
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
        color: #a78bfa;
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
        background: linear-gradient(135deg, #3730a3 0%, #1e1b4b 100%);
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
        <a href="#" onclick="goBack()">← Voltar aos Cursos</a>
        <span class="separator">/</span>
        <span>JavaScript Avançado</span>
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
        <!-- Video Section -->
        <div class="video-section">
            <div class="video-player" id="videoPlayer">
                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&h=450&fit=crop"
                    alt="Video Thumbnail" class="video-thumbnail" id="videoThumbnail">
                <div class="play-button" id="playButton">▶</div>
            </div>
            <div class="video-controls">
                <button class="play-button" id="controlPlayButton" style="background: none; border: none; color: white; font-size: 16px; cursor: pointer;">▶</button>
                <div class="time-display" id="timeDisplay">0:00 / 12:24</div>
            </div>

            <!-- Lesson Info -->
            <div class="lesson-info">
                <h2 class="lesson-title" id="lessonTitle">Higher-Order Components</h2>
                <div class="lesson-meta">
                    <div class="lesson-meta-item">
                        <span>⏱</span>
                        <span id="lessonDuration">35 minutos</span>
                    </div>
                    <div class="lesson-meta-item">
                        <span>👁</span>
                        <span>1,234 visualizações</span>
                    </div>
                    <div class="lesson-meta-item">
                        <span>📅</span>
                        <span>Publicado em 15/01/2024</span>
                    </div>
                </div>
                <p class="lesson-description" id="lessonDescription">
                    Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes.
                </p>
                <div class="lesson-actions">
                    <button class="btn-primary-custom" onclick="markAsCompleted()">✓ Marcar como Concluída</button>
                    <button class="btn-secondary-custom" onclick="addNote()">📝 Adicionar Nota</button>
                    <button class="btn-secondary-custom" onclick="addToFavorites()">⭐ Favoritar</button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h3 class="sidebar-title">Conteúdo do Curso</h3>
                <button class="collapse-all-btn" onclick="collapseAll()">🔄 Recolher Tudo</button>
            </div>

            <!-- Module 1 -->
            <div class="module-item">
                <div class="module-header" onclick="toggleModule('module1')">
                    <div class="module-info">
                        <div class="module-icon"></div>
                        <span class="module-title">Módulo 1: Introdução</span>
                    </div>
                    <div class="module-progress">
                        <span>3/3</span>
                        <span>✓</span>
                    </div>
                </div>
                <div class="lessons-container" id="module1">
                    <div class="lesson-item completed" onclick="selectLesson('Configuração do Ambiente', 15, 'Aprenda a configurar seu ambiente de desenvolvimento para React.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status completed">✓</div>
                            <span>Configuração do Ambiente</span>
                        </div>
                        <div class="lesson-duration">15 min</div>
                    </div>
                    <div class="lesson-item completed" onclick="selectLesson('Conceitos Básicos', 22, 'Revisão dos conceitos fundamentais do React.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status completed">✓</div>
                            <span>Conceitos Básicos</span>
                        </div>
                        <div class="lesson-duration">22 min</div>
                    </div>
                    <div class="lesson-item completed" onclick="selectLesson('Exercício Prático 1', 30, 'Primeiro exercício prático do curso.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status completed">✓</div>
                            <span>Exercício Prático 1</span>
                        </div>
                        <div class="lesson-duration">30 min</div>
                    </div>
                </div>
            </div>

            <!-- Module 2 -->
            <div class="module-item">
                <div class="module-header active" onclick="toggleModule('module2')">
                    <div class="module-info">
                        <div class="module-icon"></div>
                        <span class="module-title">Módulo 2: Componentes Avançados</span>
                    </div>
                    <div class="module-progress">
                        <span>2/4</span>
                        <span>⏳</span>
                    </div>
                </div>
                <div class="lessons-container show" id="module2">
                    <div class="lesson-item current" onclick="selectLesson('Higher-Order Components', 35, 'Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status current">▶</div>
                            <span>Higher-Order Components</span>
                        </div>
                        <div class="lesson-duration">26 min</div>
                    </div>
                    <div class="lesson-item completed" onclick="selectLesson('Render Props', 25, 'Aprenda sobre o padrão Render Props no React.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status completed">✓</div>
                            <span>Render Props</span>
                        </div>
                        <div class="lesson-duration">25 min</div>
                    </div>
                    <div class="lesson-item pending" onclick="selectLesson('Projeto Prático', 45, 'Desenvolva um projeto prático aplicando os conceitos aprendidos.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status pending">○</div>
                            <span>Projeto Prático</span>
                        </div>
                        <div class="lesson-duration">45 min</div>
                    </div>
                    <div class="lesson-item pending" onclick="selectLesson('Exercícios Avançados', 40, 'Exercícios mais complexos para fixar o conhecimento.')">
                        <div class="lesson-info-item">
                            <div class="lesson-status pending">○</div>
                            <span>Exercícios Avançados</span>
                        </div>
                        <div class="lesson-duration">40 min</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="navigation-buttons">
        <button class="nav-btn secondary" onclick="previousLesson()">← Aula Anterior</button>
        <button class="nav-btn" onclick="nextLesson()">Próxima Aula →</button>
    </div>
</div>

<script>
    // State management
    let isPlaying = false;
    let currentTime = 0;
    let totalTime = 744; // 12:24 in seconds
    let currentLesson = 'Higher-Order Components';

    // Lesson data
    const lessons = {
        'Configuração do Ambiente': {
            duration: 15,
            description: 'Aprenda a configurar seu ambiente de desenvolvimento para React.',
            completed: true
        },
        'Conceitos Básicos': {
            duration: 22,
            description: 'Revisão dos conceitos fundamentais do React.',
            completed: true
        },
        'Exercício Prático 1': {
            duration: 30,
            description: 'Primeiro exercício prático do curso.',
            completed: true
        },
        'Higher-Order Components': {
            duration: 35,
            description: 'Nesta aula, você aprenderá como criar e utilizar hooks personalizados no React. Vamos explorar casos práticos e boas práticas para reutilização de lógica entre componentes.',
            completed: false
        },
        'Render Props': {
            duration: 25,
            description: 'Aprenda sobre o padrão Render Props no React.',
            completed: true
        },
        'Projeto Prático': {
            duration: 45,
            description: 'Desenvolva um projeto prático aplicando os conceitos aprendidos.',
            completed: false
        },
        'Exercícios Avançados': {
            duration: 40,
            description: 'Exercícios mais complexos para fixar o conhecimento.',
            completed: false
        }
    };

    // Video controls
    function togglePlay() {
        isPlaying = !isPlaying;
        const playButton = document.getElementById('playButton');
        const controlPlayButton = document.getElementById('controlPlayButton');

        if (isPlaying) {
            playButton.innerHTML = '⏸';
            controlPlayButton.innerHTML = '⏸';
            // Simulate video playing
            startTimeUpdate();
        } else {
            playButton.innerHTML = '▶';
            controlPlayButton.innerHTML = '▶';
            stopTimeUpdate();
        }
    }

    let timeInterval;

    function startTimeUpdate() {
        timeInterval = setInterval(() => {
            if (currentTime < totalTime) {
                currentTime++;
                updateTimeDisplay();
            } else {
                togglePlay();
            }
        }, 1000);
    }

    function stopTimeUpdate() {
        clearInterval(timeInterval);
    }

    function updateTimeDisplay() {
        const minutes = Math.floor(currentTime / 60);
        const seconds = currentTime % 60;
        const totalMinutes = Math.floor(totalTime / 60);
        const totalSeconds = totalTime % 60;

        document.getElementById('timeDisplay').textContent =
            `${minutes}:${seconds.toString().padStart(2, '0')} / ${totalMinutes}:${totalSeconds.toString().padStart(2, '0')}`;
    }

    // Event listeners for video controls
    document.getElementById('playButton').addEventListener('click', togglePlay);
    document.getElementById('controlPlayButton').addEventListener('click', togglePlay);

    // Module controls
    function toggleModule(moduleId) {
        const module = document.getElementById(moduleId);
        const header = module.previousElementSibling;

        module.classList.toggle('show');
        header.classList.toggle('active');
    }

    function collapseAll() {
        const modules = document.querySelectorAll('.lessons-container');
        const headers = document.querySelectorAll('.module-header');

        modules.forEach(module => module.classList.remove('show'));
        headers.forEach(header => header.classList.remove('active'));
    }

    // Lesson selection
    function selectLesson(title, duration, description) {
        currentLesson = title;

        // Update UI
        document.getElementById('lessonTitle').textContent = title;
        document.getElementById('lessonDuration').textContent = `${duration} minutos`;
        document.getElementById('lessonDescription').textContent = description;

        // Update current lesson in sidebar
        document.querySelectorAll('.lesson-item').forEach(item => {
            item.classList.remove('current');
            if (item.textContent.includes(title)) {
                item.classList.add('current');
            }
        });

        // Reset video state
        isPlaying = false;
        currentTime = 0;
        totalTime = duration * 60;
        document.getElementById('playButton').innerHTML = '▶';
        document.getElementById('controlPlayButton').innerHTML = '▶';
        updateTimeDisplay();

        // Show notification
        showNotification(`Agora assistindo: ${title}`);
    }

    // Action functions
    function markAsCompleted() {
        lessons[currentLesson].completed = true;
        showNotification('Aula marcada como concluída!');

        // Update progress
        updateProgress();

        // Update lesson status in sidebar
        document.querySelectorAll('.lesson-item').forEach(item => {
            if (item.textContent.includes(currentLesson)) {
                item.classList.remove('current', 'pending');
                item.classList.add('completed');
                const status = item.querySelector('.lesson-status');
                status.classList.remove('current', 'pending');
                status.classList.add('completed');
                status.innerHTML = '✓';
            }
        });
    }

    function addNote() {
        const note = prompt('Digite sua nota:');
        if (note) {
            showNotification('Nota adicionada com sucesso!');
        }
    }

    function addToFavorites() {
        showNotification('Aula adicionada aos favoritos!');
    }

    function updateProgress() {
        const totalLessons = Object.keys(lessons).length;
        const completedLessons = Object.values(lessons).filter(lesson => lesson.completed).length;
        const percentage = Math.round((completedLessons / totalLessons) * 100);

        document.getElementById('progressPercentage').textContent = `${percentage}%`;
        document.getElementById('progressBar').style.width = `${percentage}%`;
    }

    // Navigation functions
    function previousLesson() {
        showNotification('Voltando para aula anterior...');
    }

    function nextLesson() {
        showNotification('Avançando para próxima aula...');
    }

    function goBack() {
        showNotification('Voltando aos cursos...');
    }

    // Notification system
    function showNotification(message) {
        // Create notification element
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

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Initialize
    updateTimeDisplay();
    updateProgress();

    // Show welcome notification
    setTimeout(() => {
        showNotification('Bem-vindo ao curso JavaScript Avançado!');
    }, 1000);
</script>
<?= $this->endSection() ?>