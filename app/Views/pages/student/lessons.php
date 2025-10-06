<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Assistir<?= $this->endSection() ?>

<?= $this->section('lessons') ?>

<!-- Plyr CSS -->
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<style>
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

    .lesson-description {
        color: #cbd5e1;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Só ativa sticky no desktop */
    @media (min-width: 992px) {
        .video-section.sticky-top {
            position: sticky;
            top: 40px;
            /* 👈 aqui você controla o espaço do topo */
            z-index: 1020;
            /* garante que fica acima do conteúdo */
        }
    }

    /* No mobile e tablet (até 991px) ele se comporta normal */
    @media (max-width: 991px) {
        .video-section.sticky-top {
            position: static;
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
        <div class="progress-percentage" id="progressPercentage">0%</div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progressBar" style="width: 0%;"></div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Vídeo -->
        <div class="video-section sticky-top">
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
                    <!-- Plyr Embed -->
                    <div class="plyr__video-embed" id="player">
                        <iframe
                            src="https://www.youtube.com/embed/<?= esc($videoId) ?>?origin=<?= base_url() ?>&iv_load_policy=3&modestbranding=1&rel=0&showinfo=0"
                            allowfullscreen
                            allow="autoplay; encrypted-media">
                        </iframe>
                    </div>
                    
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
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $prevLesson) ?>" class="nav-btn secondary">
                ← Aula Anterior
            </a>
        <?php endif; ?>
        <?php if ($nextLesson): ?>
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $nextLesson) ?>" class="nav-btn">Próxima Aula →</a> <?php endif; ?>
    </div>
</div>
</div>

<!-- Plyr JS -->
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<script>
    const player = new Plyr('#player', {
        controls: [
            'play-large', 'play', 'progress', 'current-time', 'duration',
            'mute', 'volume', 'settings', 'pip', 'fullscreen'
        ],
        settings: ['quality', 'speed', 'loop'],
        ratio: '16:9',
        autoplay: false,
    });
</script>

<?= $this->endSection() ?>