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

    .video-wrapper {
        position: relative;
        background: #000;
        height: 45vh;
    }

    .video-controls {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        opacity: 0;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        transition: opacity 0.3s;
        align-items: center;
        padding: 10px;
        display: flex;
        gap: 10px;
    }

    .video-wrapper:hover .video-controls {
        opacity: 1;
    }

    #progressBar {
        flex: 1;
        height: 5px;
        -webkit-appearance: none;
        appearance: none;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 5px;
        outline: none;
        position: relative;
    }

    #progressBar::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #8b5cf6;
        cursor: pointer;
        position: relative;
    }

    #progressBar::-moz-range-thumb {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #8b5cf6;
        cursor: pointer;
        border: none;
    }

    .control-btn {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
    }

    .time-display {
        font-size: 14px;
        color: white;
        min-width: 100px;
        text-align: center;
    }

    .volume-control {
        display: flex;
        align-items: center;
        gap: 5px;
        position: relative;
    }

    #volumeControl {
        width: 80px;
        height: 5px;
        -webkit-appearance: none;
        appearance: none;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 5px;
        outline: none;
    }

    #volumeControl::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #8b5cf6;
        cursor: pointer;
    }

    #volumeControl::-moz-range-thumb {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #8b5cf6;
        cursor: pointer;
        border: none;
    }

    /* Tooltips para mostrar valores */
    .tooltip {
        position: absolute;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
        z-index: 100;
        bottom: 25px;
        transform: translateX(-50%);
    }

    .progress-tooltip {
        left: 0;
    }

    .volume-tooltip {
        left: 50%;
        bottom: 25px;
    }

    /* Apenas no mobile */
    @media (max-width: 768px) {
        .volume-slider-wrapper {
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            height: 120px;
            /* altura do slider vertical */
        }

        .volume-slider-wrapper.active {
            display: block;
        }

        /* Slider vertical */
        #volumeControl {
            -webkit-appearance: none;
            width: 8px;
            /* largura da barra vertical */
            height: 100%;
            background: #4c1d95;
            /* cor de fundo da barra */
            border-radius: 5px;
            outline: none;
            writing-mode: bt-lr;
            /* vertical */
            transform: rotate(270deg);
            /* gira o range para vertical */
        }

        /* Thumb */
        #volumeControl::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            border: 2px solid #8b5cf6;
            cursor: pointer;
        }

        #volumeControl::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            border: 2px solid #8b5cf6;
            cursor: pointer;
        }

        /* Barra preenchida dinamicamente */
        #volumeControl::-webkit-slider-runnable-track {
            background: linear-gradient(to top, #8b5cf6 0%, #8b5cf6 var(--volume), #4c1d95 var(--volume), #4c1d95 100%);
            border-radius: 5px;
        }

        #volumeControl::-moz-range-track {
            background: linear-gradient(to top, #8b5cf6 0%, #8b5cf6 var(--volume), #4c1d95 var(--volume), #4c1d95 100%);
            border-radius: 5px;
        }
    }

    .show-tooltip {
        opacity: 1;
    }

    /* Preview no progresso */
    .progress-preview {
        position: absolute;
        top: -30px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
        z-index: 100;
        transform: translateX(-50%);
    }

    .progress-container {
        position: relative;
        flex: 1;
        display: flex;
        align-items: center;
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
            <div class="progress-bar-fill" style="width: 75%;"></div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Vídeo -->
        <div class="video-section container my-4 pt-3">
            <!-- Player -->
            <div class="video-wrapper rounded-3 shadow-lg overflow-hidden position-relative">
                <div id="player"></div>

                <!-- Controles personalizados -->
                <div class="video-controls d-flex align-items-center justify-content-between p-3">
                    <button class="control-btn" id="playPauseBtn">
                        <i class="bi bi-play-fill" id="playIcon"></i>
                    </button>

                    <span class="time-display">
                        <span id="currentTime">00:00</span> / <span id="totalTime">00:00</span>
                    </span>

                    <div class="progress-container">
                        <input type="range" id="progressBar" value="0" min="0" max="100" step="0.1" class="flex-fill mx-2">
                        <div class="progress-tooltip tooltip" id="progressTooltip">00:00</div>
                    </div>

                    <div class="volume-control">
                        <button class="control-btn" id="volumeBtn">
                            <i class="bi bi-volume-up-fill" id="volumeIcon"></i>
                        </button>
                        <div class="volume-slider-wrapper">
                            <input type="range" id="volumeControl" min="0" max="100" value="100" orient="vertical">
                            <div class="volume-tooltip tooltip" id="volumeTooltip">100%</div>
                        </div>
                    </div>

                    <button class="control-btn" id="fullscreenBtn">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                </div>
            </div>

            <!-- Informações da aula -->
            <div class="lesson-info mt-4">
                <h2 class="lesson-title fw-bold"><?= esc($lesson->title_lesson) ?></h2>
                <div class="lesson-meta d-flex gap-3 text-muted mb-3">
                    <div>⏱ <?= esc($lesson->duration_lesson) ?> minutos</div>
                    <div>📅 <?= date('d/m/Y', strtotime($lesson->created_at)) ?></div>
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

<?php
function getYouTubeId($url)
{
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $url, $matches);
    return $matches[1] ?? null;
}
$videoId = getYouTubeId($lesson->video_url_lesson);
?>

<script>
    // Carregar a API do YouTube IFrame
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    let player;
    const videoId = "<?= $videoId ?>";
    const playPauseBtn = document.getElementById("playPauseBtn");
    const playIcon = document.getElementById("playIcon");
    const progressBar = document.getElementById("progressBar");
    const progressTooltip = document.getElementById("progressTooltip");
    const currentTimeEl = document.getElementById("currentTime");
    const totalTimeEl = document.getElementById("totalTime");
    const volumeControl = document.getElementById("volumeControl");
    const volumeTooltip = document.getElementById("volumeTooltip");
    const volumeBtn = document.getElementById("muteBtn"); // ícone que abre slider
    const volumeWrapper = document.querySelector('.volume-slider-wrapper');
    const volumeIcon = document.getElementById("volumeIcon");
    const fullscreenBtn = document.getElementById("fullscreenBtn");

    let isSeeking = false;
    let updateInterval;
    let videoDuration = 0;

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            height: '100%',
            width: '100%',
            videoId: videoId,
            playerVars: { rel: 0, controls: 0, modestbranding: 1 },
            events: { 'onReady': onPlayerReady, 'onStateChange': onPlayerStateChange }
        });
    }

    function onPlayerReady() {
        videoDuration = player.getDuration();
        totalTimeEl.textContent = formatTime(videoDuration);
        updateInterval = setInterval(updateProgress, 1000);
        setupEventListeners();
    }

    function setupEventListeners() {
        // Play/Pause
        playPauseBtn.addEventListener("click", () => {
            if (!player) return;
            const state = player.getPlayerState();
            if (state !== YT.PlayerState.PLAYING) player.playVideo();
            else player.pauseVideo();
        });

        // Barra de progresso
        progressBar.addEventListener("mousedown", () => { isSeeking = true; progressTooltip.classList.add('show-tooltip'); });
        progressBar.addEventListener("input", () => {
            const percent = progressBar.value;
            const seekTo = (percent / 100) * videoDuration;
            progressTooltip.textContent = formatTime(seekTo);
            progressTooltip.style.left = `${percent}%`;
            currentTimeEl.textContent = formatTime(seekTo);
        });
        progressBar.addEventListener("change", () => {
            const percent = progressBar.value;
            const seekTo = (percent / 100) * videoDuration;
            player.seekTo(seekTo, true);
            isSeeking = false;
            setTimeout(() => { progressTooltip.classList.remove('show-tooltip'); }, 1000);
        });
        progressBar.addEventListener("mouseenter", () => progressTooltip.classList.add('show-tooltip'));
        progressBar.addEventListener("mouseleave", () => { if (!isSeeking) progressTooltip.classList.remove('show-tooltip'); });

        // Volume: abrir/esconder slider (somente mobile)
        volumeBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            volumeWrapper.classList.toggle('active');
        });

        document.addEventListener("click", (e) => {
            if (!volumeWrapper.contains(e.target) && e.target !== volumeBtn) {
                volumeWrapper.classList.remove('active');
            }
        });

        // Volume input
        volumeControl.addEventListener("input", () => {
            if (!player) return;
            const vol = volumeControl.value;
            player.setVolume(vol);
            volumeTooltip.textContent = `${vol}%`;
            updateVolumeIcon(vol);
            // Atualizar barra preenchida
            volumeControl.style.setProperty('--volume', `${vol}%`);
        });

        // Tooltip volume
        volumeControl.addEventListener("mouseenter", () => volumeTooltip.classList.add('show-tooltip'));
        volumeControl.addEventListener("mouseleave", () => volumeTooltip.classList.remove('show-tooltip'));

        // Fullscreen
        fullscreenBtn.addEventListener("click", () => {
            const wrapper = document.querySelector(".video-wrapper");
            if (!document.fullscreenElement) wrapper.requestFullscreen?.() || wrapper.webkitRequestFullscreen?.() || wrapper.msRequestFullscreen?.();
            else document.exitFullscreen?.() || document.webkitExitFullscreen?.() || document.msExitFullscreen?.();
        });
    }

    function updateProgress() {
        if (!player || !player.getCurrentTime) return;
        if (!isSeeking) {
            const currentTime = player.getCurrentTime();
            const percent = (currentTime / videoDuration) * 100;
            progressBar.value = percent;
            currentTimeEl.textContent = formatTime(currentTime);
            progressTooltip.style.left = `${percent}%`;
            progressTooltip.textContent = formatTime(currentTime);
        }
    }

    function onPlayerStateChange(event) {
        playIcon.className = (event.data === YT.PlayerState.PLAYING) ? "bi bi-pause-fill" : "bi bi-play-fill";
    }

    function updateVolumeIcon(volume) {
        if (volume == 0) volumeIcon.className = "bi bi-volume-mute-fill";
        else if (volume < 50) volumeIcon.className = "bi bi-volume-down-fill";
        else volumeIcon.className = "bi bi-volume-up-fill";
    }

    function formatTime(seconds) {
        if (isNaN(seconds)) return "00:00";
        const min = Math.floor(seconds / 60).toString().padStart(2, "0");
        const sec = Math.floor(seconds % 60).toString().padStart(2, "0");
        return `${min}:${sec}`;
    }
</script>


<?= $this->endSection() ?>