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

    /* ativo (aula atual) */
    .lesson-row.current {
        background: rgba(0, 0, 0, .25);
        /* mais escuro */
        border-left: 3px solid #8b5cf6;
        /* faixa de destaque */
    }

    /* opcional: diferenciar hover do ativo */
    .lesson-row.current:hover {
        background: rgba(0, 0, 0, .32);
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
</style>

<div class="main-container">
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
                        src="https://player.vimeo.com/video/<?= esc($videoId) ?>?badge=0&autopause=0&player_id=<?= esc($lesson->id_lesson) ?>&app_id=58479&title=0&byline=0&portrait=0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen referrerpolicy="no-referrer" loading="lazy"
                        sandbox="allow-same-origin allow-scripts allow-presentation"
                        oncontextmenu="return false;"></iframe>
                <?php else: ?>
                    <p class="text-danger">Link de vídeo inválido</p>
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
            <a href="<?= site_url('student/dashboard/ver_aulas/' . $nextLesson) ?>" class="nav-btn">Próxima Aula →</a>
        <?php endif; ?>
    </div>
</div>

<?php if (ENVIRONMENT === 'production'): ?>
    <script>
        // bloqueios (opcional): mantém seus handlers originais aqui se quiser
    </script>
<?php endif; ?>

<script>
    // ------- Helpers CSRF -------
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
        // atualiza CSRF se vier no header
        const newHash = res.headers.get('X-CSRF-Hash');
        if (newHash) csrfHash = newHash;
        return res.json();
    }

    // ------- Progresso -------
    function computeProgress() {
        const total = document.querySelectorAll('.lesson-row').length;
        const done = document.querySelectorAll('.lesson-check:checked').length;
        const pct = total ? Math.round((done / total) * 100) : 0;
        document.getElementById('progressPercentage').textContent = pct + '%';
        document.getElementById('progressBar').style.width = pct + '%';
    }

    // ------- Toggle conclusão -------
    async function toggleLessonComplete(lessonId, isChecked) {
        try {
            const url = isChecked ?
                '<?= site_url('student/lessons/complete') ?>' :
                '<?= site_url('student/lessons/uncomplete') ?>';

            const data = await fetchJSON(url, {
                method: 'POST',
                body: JSON.stringify({
                    lesson_id: lessonId
                })
            });

            if (!data?.ok) {
                // revert UI se falhar
                const cb = document.querySelector(`.lesson-row[data-lesson-id="${lessonId}"] .lesson-check`);
                if (cb) cb.checked = !isChecked;
                alert(data?.message || 'Não foi possível atualizar a conclusão.');
            }
        } catch (e) {
            const cb = document.querySelector(`.lesson-row[data-lesson-id="${lessonId}"] .lesson-check`);
            if (cb) cb.checked = !isChecked;
            alert('Erro de rede ao salvar. Tente novamente.');
        } finally {
            computeProgress();
        }
    }

    // Bind checkboxes
    document.querySelectorAll('.lesson-check').forEach(cb => {
        cb.addEventListener('change', (e) => {
            const row = e.target.closest('.lesson-row');
            const id = row?.dataset?.lessonId;
            if (id) toggleLessonComplete(id, e.target.checked);
        });
    });

    // Inicializa progresso
    computeProgress();
</script>

<?= $this->endSection() ?>