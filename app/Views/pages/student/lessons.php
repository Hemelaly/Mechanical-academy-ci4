<?php
// dd($enrollment)
$autoplayFlag = (int) ($_GET['autoplay'] ?? 0); // CI4
$auto  = $autoplayFlag ? 1 : 0;
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Assistir<?= $this->endSection() ?>

<?= $this->section('lessons') ?>

<!-- CSS base (Plyr opcional) -->
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<!-- CSRF para AJAX -->
<meta name="csrf-name" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">

<style>
    :root {
        --bg: #0f1021;
        --panel: #181a2e;
        --panel-2: #1d2040;
        --accent: #8b5cf6;
        --accent-2: #a855f7;
        --text: #e5e7eb;
        --muted: #a5b4fc;
        --line: #2a2e5b;
        --ok: #22c55e;
        --warn: #f59e0b;
    }

    * {
        box-sizing: border-box
    }

    body {
        background: var(--bg)
    }

    .main-container {
        padding: 24px;
        max-width: 100%;
        margin: 0 auto;
        color: var(--text)
    }

    a {
        color: var(--muted);
        text-decoration: none
    }

    a:hover {
        opacity: .9
    }

    /* Breadcrumb */
    .breadcrumb-nav {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 16px;
        font-size: .95rem
    }

    .breadcrumb-nav .separator {
        opacity: .6
    }

    /* Progress */
    .progress-section {
        background: linear-gradient(135deg, #16174a 0%, #1d1b4b 100%);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 18px
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px
    }

    .progress-title {
        font-size: .9rem;
        color: #cbd5e1;
        opacity: .9
    }

    .progress-percentage {
        font-weight: 800
    }

    .progress-bar-container {
        width: 100%;
        height: 10px;
        background: rgba(255, 255, 255, .06);
        border-radius: 999px;
        overflow: hidden
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent), var(--accent-2));
        transition: width .3s ease
    }

    /* Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1.6fr .8fr;
        gap: 22px;
        align-items: start;
        grid-auto-rows: auto;
    }

    @media (max-width:1024px) {
        .content-grid {
            grid-template-columns: 1fr
        }
    }

    /* Video card */
    .video-section {
        background: var(--panel);
        border: 1px solid var(--line);
        border-radius: 14px;
        overflow: hidden
    }

    @media (min-width:992px) {
        .video-section.sticky-top {
            position: sticky;
            top: 28px;
            z-index: 2
        }
    }

    @media (max-width:991px) {
        .video-section.sticky-top {
            position: static
        }
    }

    .video-player {
        position: relative;
        padding-top: 56.25%;
        overflow: hidden
    }

    .video-player iframe {
        position: absolute;
        inset: 0;
        border: 0;
        width: 100%;
        height: 100%
    }

    .lesson-info {
        padding: 30px
    }

    .lesson-title {
        font-size: 1.4rem;
        font-weight: 800;
        margin: 0 0 10px
    }

    .lesson-meta {
        display: flex;
        gap: 18px;
        color: var(--muted);
        font-size: .9rem;
        margin-bottom: 10px
    }

    .lesson-description {
        opacity: .9;
        line-height: 1.6
    }

    /* Sidebar */
    .sidebar2 {
        background: transparent
    }

    .sidebar-title {
        font-weight: 800;
        margin-bottom: 10px
    }

    .module {
        border: 1px solid var(--line);
        border-radius: 12px;
        background: var(--panel);
        overflow: hidden
    }

    .module+.module {
        margin-top: 10px
    }

    .module-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 14px 16px;
        background: var(--panel-2);
        border: none;
        cursor: pointer
    }

    .module-header .left {
        display: flex;
        gap: 10px;
        align-items: center
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--accent)
    }

    .module-title {
        font-weight: 700;
        color: #dbeafe
    }

    .module-count {
        font-size: .8rem;
        opacity: .7
    }

    .module-body {
        padding: 0
    }

    .lesson-row {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-top: 1px solid var(--line);
        background: transparent;
        transition: background .2s
    }

    .lesson-row:hover {
        background: rgba(255, 255, 255, .03)
    }

    .lesson-row.current {
        background: rgba(0, 0, 0, .25);
        border-left: 3px solid #8b5cf6;
    }

    .lesson-row.locked {
        opacity: .55;
        pointer-events: auto;
    }

    .lesson-row.locked .checkbox {
        opacity: .4;
        pointer-events: none;
    }

    .nav-btn.disabled-next {
        opacity: .55;
        cursor: not-allowed;
    }

    .lesson-row.current:hover {
        background: rgba(0, 0, 0, .32)
    }

    .lesson-link {
        color: var(--text);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px
    }

    .badge-current {
        font-size: .7rem;
        padding: 2px 6px;
        border-radius: 999px;
        background: rgba(139, 92, 246, .15);
        border: 1px solid var(--accent)
    }

    .duration {
        font-size: .85rem;
        opacity: .75
    }

    .check-wrap {
        display: flex;
        align-items: center;
        gap: 8px
    }

    .checkbox {
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid var(--line);
        border-radius: 6px;
        display: inline-grid;
        place-content: center;
        background: transparent;
        cursor: pointer;
        transition: .15s;
    }

    .checkbox:checked {
        border-color: var(--ok);
        background: rgba(34, 197, 94, .2)
    }

    .checkbox:checked::after {
        content: "✓";
        font-weight: 900;
        font-size: .9rem;
        transform: translateY(-1px)
    }

    /* Nav buttons */
    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 24px
    }

    .nav-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--accent);
        border: none;
        color: #fff;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s
    }

    .nav-btn:hover {
        filter: brightness(1.05)
    }

    .nav-btn.secondary {
        background: #4c1d95
    }

    /* Overlay mostrado quando o vídeo termina */
    .end-overlay {
        position: absolute;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
        background: rgba(0, 0, 0, .45);
        z-index: 5;
    }

    .end-overlay.show {
        display: flex;
    }

    .end-card {
        background: var(--panel);
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        max-width: 360px;
        width: 92%;
        color: var(--text);
        box-shadow: 0 10px 25px rgba(0, 0, 0, .35);
    }

    .end-card h4 {
        margin: 0 0 10px;
    }

    .end-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 14px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--accent);
        border: none;
        color: #fff;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .btn.secondary {
        background: #4c1d95;
    }

    .small-note {
        font-size: .85rem;
        opacity: .8;
        margin-top: 8px;
    }
</style>

<div class="main-container" data-enrollment-id="<?= (int)($enrollment->id_enrollment) ?>">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <a href="/student/dashboard/meus_cursos">← Voltar aos Cursos</a>
        <span class="separator">/</span>
        <span><?= esc($course->title_course) ?></span>
    </div>

    <!-- Progress -->
    <?php $completedLessonIds = $completedLessonIds ?? []; ?>
    <div class="progress-section mb-5">
        <div class="progress-header">
            <div class="progress-title">Progresso do Curso</div>
            <div class="progress-percentage" id="progressPercentage">0%</div>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" id="progressBar" style="width:0%"></div>
        </div>
    </div>

    <!-- Main -->
    <div class="content-grid">
        <!-- Vídeo -->
        <div class="video-section sticky-top">
            <div class="video-player" oncontextmenu="return false;" ondragstart="return false;" onmousedown="return false;" onselectstart="return false;">
                <?php
                function getVimeoId($url)
                {
                    preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $url, $m);
                    return $m[1] ?? null;
                }
                $videoId = getVimeoId($lesson->video_url_lesson);
                ?>
                <?php if ($videoId): ?>
                    <iframe id="vimeoPlayer"
                        src="https://player.vimeo.com/video/<?= esc($videoId) ?>?badge=0&autopause=0&player_id=<?= esc($lesson->id_lesson) ?>&app_id=58479&title=0&byline=0&portrait=0&autoplay=<?= $auto ?>"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen referrerpolicy="no-referrer" loading="lazy"
                        sandbox="allow-same-origin allow-scripts allow-presentation"
                        oncontextmenu="return false">
                    </iframe>
                <?php else: ?>
                    <p class="text-danger">Link de vídeo inválido</p>
                <?php endif; ?>

                <?php if ($nextLesson): ?>
                    <!-- Overlay DENTRO do .video-player -->
                    <div id="endOverlay" class="end-overlay" oncontextmenu="return false;">
                        <div class="end-card">
                            <h4>Aula concluída 🎉</h4>
                            <p>Avance para a próxima aula quando quiser.</p>
                            <div class="end-actions">
                                <a id="goNextBtn"
                                    href="<?= site_url('student/dashboard/ver_aulas/' . $nextLesson) ?>?autoplay=1"
                                    class="btn">Próxima Aula →</a>
                                <button id="stayBtn" type="button" class="btn secondary">Ficar aqui</button>
                            </div>
                            <div class="small-note" id="autoNote">Indo automaticamente em <span id="countdown">5</span>s…</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="lesson-info">
                <h2 class="lesson-title"><?= esc($lesson->title_lesson) ?></h2>
                <div class="lesson-meta">
                    <div>⏱ <?= esc($lesson->duration_lesson) ?> minutos</div>
                    <div>📅 <?= date('d/m/Y', strtotime($lesson->created_at)) ?></div>
                </div>
                <p class="lesson-description"><?= esc($lesson->content_lesson) ?></p>
            </div>
        </div>

        <!-- Sidebar / Conteúdo -->
        <div class="sidebar2">
            <h3 class="sidebar-title">Conteúdo do Curso</h3>

            <?php foreach ($modules as $index => $m): ?>
                <div class="module" x-module="<?= $index ?>">
                    <button class="module-header" type="button" data-bs-toggle="collapse" data-bs-target="#mod<?= $index ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="mod<?= $index ?>">
                        <div class="left">
                            <span class="dot"></span>
                            <span class="module-title"><?= esc($m->title_module) ?></span>
                        </div>
                        <span class="module-count text-white"><?= count($m->lessons) ?> aulas</span>
                    </button>

                    <div id="mod<?= $index ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>">
                        <div class="module-body">
                            <?php foreach ($m->lessons as $l): ?>
                                <?php $isCurrent = ($l->id_lesson == $lesson->id_lesson); ?>
                                <?php $isDone    = in_array($l->id_lesson, $completedLessonIds ?? [], true); ?>
                                <div class="lesson-row <?= $isCurrent ? 'current' : '' ?>" data-lesson-id="<?= (int)$l->id_lesson ?>">
                                    <div class="check-wrap">
                                        <input type="checkbox"
                                            class="checkbox lesson-check"
                                            <?= $isDone ? 'checked' : '' ?>
                                            aria-label="Marcar aula como concluída">
                                    </div>

                                    <a class="lesson-link" href="<?= site_url('student/dashboard/ver_aulas/' . $l->id_lesson) ?>">
                                        <span><?= esc($l->title_lesson) ?></span>
                                        <?php if ($isCurrent): ?><span class="badge-current">A assistir</span><?php endif; ?>
                                    </a>

                                    <span class="duration"><?= esc($l->duration_lesson) ?> min</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Navegação -->
    <div class="navigation-buttons">
        <?php if ($prevLesson): ?>
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $prevLesson) ?>" class="nav-btn secondary">← Aula Anterior</a>
        <?php else: ?><span></span><?php endif; ?>

        <?php if ($nextLesson): ?>
            <!-- Botão inferior também com autoplay -->
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $nextLesson) ?>?autoplay=1" class="nav-btn">Próxima Aula →</a>
        <?php endif; ?>
    </div>
</div>

<?php if (ENVIRONMENT === 'production'): ?>
    <script>
        // bloqueios (opcional): mantém seus handlers originais aqui se quiser
    </script>
<?php endif; ?>

<!-- SDK do Vimeo -->
<script src="https://player.vimeo.com/api/player.js"></script>

<script>
    /* =========================
   Helpers CSRF / Progress
   ========================= */
    const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
    let csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;

    async function fetchJSON(url, opts = {}) {
        const cfg = {
            method: 'GET',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...opts
        };
        if (!cfg.headers) cfg.headers = {};
        if (csrfName && csrfHash && cfg.method !== 'GET') {
            cfg.headers['Content-Type'] = 'application/json';
            cfg.headers[csrfName] = csrfHash;
        }
        const res = await fetch(url, cfg);
        const newHash = res.headers.get('X-CSRF-Hash');
        if (newHash) csrfHash = newHash;
        return res.json();
    }

    const enrollmentId = document.querySelector('.main-container')?.dataset?.enrollmentId;
    if (!enrollmentId) console.warn('Enrollment ID não encontrado no data-enrollment-id da .main-container');

    function computeProgress() {
        const total = document.querySelectorAll('.lesson-row').length;
        const done = document.querySelectorAll('.lesson-check:checked').length;
        const pct = total ? Math.round((done / total) * 100) : 0;
        const ppEl = document.getElementById('progressPercentage');
        const barEl = document.getElementById('progressBar');
        if (ppEl) ppEl.textContent = pct + '%';
        if (barEl) barEl.style.width = pct + '%';
    }

    async function toggleLessonComplete(lessonId, isChecked, checkboxEl) {
        if (!enrollmentId) {
            alert('Matrícula não identificada. Recarregue a página.');
            if (checkboxEl) checkboxEl.checked = !isChecked;
            return;
        }
        if (checkboxEl) checkboxEl.disabled = true;

        try {
            const url = isChecked ?
                '<?= site_url('student/lessons/complete') ?>' :
                '<?= site_url('student/lessons/uncomplete') ?>';

            const row = document.querySelector(`.lesson-row[data-lesson-id="${lessonId}"]`);
            row?.classList.toggle('done', isChecked);
            computeProgress();

            const data = await fetchJSON(url, {
                method: 'POST',
                body: JSON.stringify({
                    lesson_id: Number(lessonId),
                    enrollment_id: Number(enrollmentId)
                })
            });

            if (!data?.ok) {
                if (checkboxEl) checkboxEl.checked = !isChecked;
                row?.classList.toggle('done', !isChecked);
                alert(data?.message || 'Não foi possível atualizar a conclusão.');
                return;
            }
            if (typeof data.progress === 'number') {
                const ppEl = document.getElementById('progressPercentage');
                const barEl = document.getElementById('progressBar');
                if (ppEl) ppEl.textContent = data.progress + '%';
                if (barEl) barEl.style.width = data.progress + '%';
            } else {
                computeProgress();
            }
        } catch (e) {
            if (checkboxEl) checkboxEl.checked = !isChecked;
            const row = document.querySelector(`.lesson-row[data-lesson-id="${lessonId}"]`);
            row?.classList.toggle('done', !isChecked);
            alert('Erro de rede ao salvar. Tente novamente.');
        } finally {
            if (checkboxEl) checkboxEl.disabled = false;
        }
    }

    document.querySelectorAll('.lesson-check').forEach(cb => {
        const initRow = cb.closest('.lesson-row');
        if (initRow) initRow.classList.toggle('done', cb.checked);
        cb.addEventListener('change', (e) => {
            const row = e.target.closest('.lesson-row');
            const id = row?.dataset?.lessonId;
            if (id) toggleLessonComplete(id, e.target.checked, e.target);
        });
    });
    computeProgress();

    /* =========================
       Vimeo Player + Navegação
       ========================= */
    const vimeoIframe = document.getElementById('vimeoPlayer');
    const player = vimeoIframe ? new Vimeo.Player(vimeoIframe) : null;

    const currentLessonId = <?= (int)$lesson->id_lesson ?>;
    const hasNext = <?= $nextLesson ? 'true' : 'false' ?>;
    const nextUrl = "<?= $nextLesson ? site_url('student/dashboard/ver_aulas/' . $nextLesson) : '' ?>";

    const endOverlay = document.getElementById('endOverlay');
    const goNextBtn = document.getElementById('goNextBtn');
    const stayBtn = document.getElementById('stayBtn');
    const countdownEl = document.getElementById('countdown');
    const autoNote = document.getElementById('autoNote');

    function withAutoplay(url) {
        return url + (url.includes('?') ? '&' : '?') + 'autoplay=1';
    }

    let autoTimer = null;
    let seconds = 5;

    function showEndOverlay() {
        if (!endOverlay) return;
        endOverlay.classList.add('show');

        if (goNextBtn && nextUrl) {
            goNextBtn.href = withAutoplay(nextUrl);
        }
        if (autoNote && countdownEl) {
            seconds = 5;
            countdownEl.textContent = seconds;
            autoTimer = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(autoTimer);
                    if (nextUrl) window.location.href = withAutoplay(nextUrl);
                }
            }, 1000);
        }
    }

    function hideEndOverlay() {
        if (!endOverlay) return;
        endOverlay.classList.remove('show');
        if (autoTimer) {
            clearInterval(autoTimer);
            autoTimer = null;
        }
    }

    if (stayBtn) {
        stayBtn.addEventListener('click', hideEndOverlay);
    }

    // Marca concluído ao terminar
    async function markCompletedOnEnd() {
        const checkbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
        if (checkbox && !checkbox.checked) {
            checkbox.checked = true;
            await toggleLessonComplete(currentLessonId, true, checkbox);
        }
    }

    // Autoplay “failsafe” ao carregar esta página, se veio com ?autoplay=1
    const urlParams = new URLSearchParams(window.location.search);
    const shouldAutoplay = urlParams.get('autoplay') === '1';
    if (player && shouldAutoplay) {
        player.ready().then(() => {
            player.play().catch(() => {
                /* navegador pode bloquear */ });
        });
    }

    if (player) {
        player.on('ended', async function() {
            try {
                await markCompletedOnEnd();
            } catch (e) {
                console.warn('Falha ao marcar concluída no término:', e);
            }
            if (hasNext && nextUrl) showEndOverlay();
        });

        // (Opcional) marcar com 90% assistido
        player.on('timeupdate', async function(data) {
            try {
                const duration = (await player.getDuration()) || 0;
                const watched = data.seconds || 0;
                if (duration > 0 && watched / duration >= 0.95) {
                    const checkbox = document.querySelector(`.lesson-row[data-lesson-id="${currentLessonId}"] .lesson-check`);
                    if (checkbox && !checkbox.checked) {
                        checkbox.checked = true;
                        await toggleLessonComplete(currentLessonId, true, checkbox);
                    }
                }
            } catch {}
        });
    }
</script>

<?= $this->endSection() ?>