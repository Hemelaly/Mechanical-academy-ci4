<?php
$user = service('auth')->user();
$courseDescriptionValue = str_replace('</textarea>', '&lt;/textarea&gt;', $course->description_course ?? '');
$courseLearningValue = str_replace('</textarea>', '&lt;/textarea&gt;', $course->learning_course ?? '');
?>

<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Editar Curso<?= $this->endSection() ?>

<?= $this->section('edit_course') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    .lesson-placeholder {
        border: 2px dashed #94a3b8;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        color: #64748b;
        font-size: 12px;
        background: rgba(148, 163, 184, 0.08);
    }

    .lessons-container.drag-active .lesson-item {
        opacity: 0.5;
        filter: blur(0.2px);
        transition: opacity 0.35s ease, transform 0.35s ease;
    }

    .lessons-container.drag-active .lesson-placeholder {
        opacity: 1;
        filter: none;
    }

    .lesson-placeholder {
        transition: background 0.35s ease, border-color 0.35s ease;
    }

    .lesson-item.dragging {
        opacity: 0 !important;
    }

    .lessons-container.drag-active {
        transition: box-shadow 0.35s ease, border-color 0.35s ease;
    }

    .lessons-container.drag-over {
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.45);
        border-radius: 12px;
    }

    .note-editor.note-frame {
        border: 1px solid #cbd5f5;
        border-radius: 1rem;
        background-color: #fff;
    }

    .note-editor .note-toolbar,
    .note-editor .note-statusbar {
        background-color: #f8fafc;
    }

    .note-editor .note-editable {
        background-color: #fff;
        min-height: 220px;
        color: #0f172a;
    }

    .dark .note-editor.note-frame {
        border-color: #475569;
        background-color: #0f172a;
    }

    .dark .note-editor .note-toolbar,
    .dark .note-editor .note-statusbar,
    .dark .note-editor .note-editable {
        background-color: #0f172a;
        color: #e2e8f0;
    }

    .note-editor.note-dark-mode .note-editable,
    .note-editor.note-dark-mode .note-toolbar,
    .note-editor.note-dark-mode .note-statusbar {
        background-color: #0f172a;
        color: #e2e8f0 !important;
    }

    .course-preview-card {
        position: sticky;
        top: 24px;
    }

    .course-preview-cover {
        position: relative;
        min-height: 220px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        overflow: hidden;
    }

    .course-preview-cover img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
    }

    .course-preview-cover-fallback {
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, .9);
        font-size: 42px;
    }

    .course-preview-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(15, 23, 42, 0.78);
        color: #fff;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        backdrop-filter: blur(8px);
    }

    .course-preview-price {
        position: absolute;
        right: 16px;
        bottom: 16px;
        background: #fff;
        color: #0f172a;
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 14px;
        font-weight: 800;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.18);
    }

    .dark .course-preview-price {
        background: #0f172a;
        color: #fff;
        border: 1px solid #334155;
    }

    .course-preview-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        object-fit: contain;
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 8px;
    }

    .dark .course-preview-icon {
        background: #0f172a;
        border-color: #334155;
    }

    .course-preview-section-title {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #64748b;
    }

    .dark .course-preview-section-title {
        color: #94a3b8;
    }

    .course-preview-richtext ul,
    .course-preview-richtext ol {
        padding-left: 18px;
        margin: 8px 0;
    }

    .course-preview-richtext p {
        margin: 0 0 8px;
    }

    .course-preview-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 600;
    }

    .dark .course-preview-pill {
        background: rgba(59, 130, 246, .15);
        color: #93c5fd;
    }

    .course-preview-module {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 14px;
        background: #fff;
    }

    .dark .course-preview-module {
        background: #0f172a;
        border-color: #334155;
    }

    .course-preview-project {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 12px;
        background: #fff;
    }

    .dark .course-preview-project {
        background: #0f172a;
        border-color: #334155;
    }

    .course-preview-project img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 10px;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">

<div class="min-w-0 bg-slate-50 dark:bg-slate-900 py-8">
    <div class="container mx-auto px-4">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">
                        <i class="bi bi-pencil-square text-blue-600 mr-3"></i>
                        Editar Curso
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Atualize seu curso facilmente e gerencie todo o conteúdo
                    </p>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-5 sm:p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold text-sm">1</div>
                        <div class="w-8 h-8 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full flex items-center justify-center font-semibold text-sm">2</div>
                        <div class="w-8 h-8 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full flex items-center justify-center font-semibold text-sm">3</div>
                        <div class="w-8 h-8 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full flex items-center justify-center font-semibold text-sm">4</div>
                    </div>
                    <span class="text-slate-500 dark:text-slate-400 text-sm font-medium" id="progress-text">
                        Passo 1 de 4
                    </span>
                </div>
                <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                    <div id="progress-bar" class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300" style="width: 25%"></div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-2">
            <button class="tab-btn active px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg" data-tab="basic-info">
                <i class="bi bi-info-circle mr-2"></i>
                Informações Básicas
            </button>
            <button class="tab-btn px-4 py-2.5 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-medium rounded-xl transition-all duration-200 hover:bg-white dark:hover:bg-slate-800 hover:border-blue-300 dark:hover:border-blue-600" data-tab="content-structure">
                <i class="bi bi-diagram-3 mr-2"></i>
                Estrutura do Conteúdo
            </button>
            <button class="tab-btn px-4 py-2.5 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-medium rounded-xl transition-all duration-200 hover:bg-white dark:hover:bg-slate-800 hover:border-blue-300 dark:hover:border-blue-600" data-tab="advanced-settings">
                <i class="bi bi-gear mr-2"></i>
                Configurações Avançadas
            </button>
            <button class="tab-btn px-4 py-2.5 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 font-medium rounded-xl transition-all duration-200 hover:bg-white dark:hover:bg-slate-800 hover:border-blue-300 dark:hover:border-blue-600" data-tab="review-publish">
                <i class="bi bi-rocket mr-2"></i>
                Revisão e Publicação
            </button>
        </div>

        <!-- Form -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
            <div class="xl:col-span-2">
                <form id="courseForm" action="<?= base_url('instructor/dashboard/editar_curso/' . $course->id_course) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="id_instructor_course" value="<?= $user->id ?>">
                    <input type="hidden" id="modules-json" name="modules">
                    <input type="hidden" id="modules-json-alt" name="modules_json">
                    <input type="hidden" id="projects-json" name="projects_json">
                    <input type="hidden" name="projects_present" value="1">

                    <!-- Step 1: Basic Info -->
                    <div id="basic-info" class="tab-content active bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <!-- Header -->
                        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-info-circle text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Informações Básicas do Curso</h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Atualize os campos do seu curso</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Left Column (Text fields) -->
                                <div class="lg:col-span-2 space-y-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-type text-blue-500 mr-2"></i>
                                            Título do Curso *
                                        </label>
                                        <input type="text" id="title_course" name="title_course"
                                            placeholder="Ex: Desenvolvimento Web Completo"
                                            required value="<?= esc($course->title_course) ?>"
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-text-left text-blue-500 mr-2"></i>
                                            Subtítulo do Curso *
                                        </label>
                                        <input type="text" id="courseSubtitle" name="subtitle_course"
                                            placeholder="Ex: Do zero ao avançado com HTML, CSS e JavaScript"
                                            required value="<?= esc($course->subtitle_course) ?>"
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-card-text text-blue-500 mr-2"></i>
                                            Descrição do Curso *
                                        </label>
                                        <textarea rows="8" id="courseDescription" name="description_course"
                                            placeholder="Descreva detalhadamente seu curso..."
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"><?= $courseDescriptionValue ?></textarea>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-list-check text-blue-500 mr-2"></i>
                                            O que você aprenderá *
                                        </label>
                                        <textarea rows="6" id="courseLearning" name="learning_course"
                                            placeholder="Liste os principais tópicos ou habilidades que os alunos dominarão..."
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"><?= $courseLearningValue ?></textarea>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            Use o editor para criar listas, negritos e links personalizados
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-play-btn text-blue-500 mr-2"></i>
                                            Vídeo de visão geral
                                        </label>
                                        <input type="url" id="courseVideo" name="url_video_course"
                                            placeholder="https://vimeo.com/xxxxx ou https://www.youtube.com/watch?v=..."
                                            value="<?= esc($course->url_video_course) ?>"
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            URL exibida na página pública dentro do bloco de vídeo.
                                        </p>
                                    </div>
                                </div>

                                <!-- Right Column (Image upload) -->
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-image text-blue-500 mr-2"></i>
                                            Imagem de Capa *
                                        </label>

                                        <!-- Upload Area -->
                                        <div id="upload-area" class="border-2 border-dashed border-blue-400 rounded-2xl p-6 text-center bg-blue-50 dark:bg-blue-900/20 transition-all duration-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:border-blue-500 cursor-pointer <?= $course->image_course ? 'hidden' : '' ?>">
                                            <i class="bi bi-cloud-arrow-up text-blue-500 text-3xl mb-3"></i>
                                            <h6 class="font-medium text-slate-700 dark:text-slate-300 text-sm mb-1">
                                                Arraste uma imagem ou clique para selecionar
                                            </h6>
                                            <p class="text-slate-500 dark:text-slate-400 text-xs mb-3">
                                                Recomendado: 1280x720px, máx. 2MB
                                            </p>
                                            <input type="file" id="courseImage" name="image_course" accept="image/*" class="hidden">
                                            <button type="button" onclick="document.getElementById('courseImage').click()"
                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm">
                                                <i class="bi bi-folder2-open mr-2"></i>
                                                Selecionar Arquivo
                                            </button>
                                        </div>

                                        <!-- Image Preview -->
                                        <div id="image-preview" class="<?= !empty($course->image_course) ? '' : 'hidden' ?> mt-4">
                                            <div class="relative">
                                                <img id="preview-img"
                                                    src="<?= !empty($course->image_course) ? base_url('assets/instructor/img/courses/' . $course->image_course) : '' ?>"
                                                    alt="Preview"
                                                    class="w-full h-32 sm:h-48 object-cover rounded-2xl shadow-lg" />
                                                <button type="button"
                                                    id="remove-image"
                                                    class="absolute top-2 right-2 w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded-full flex items-center justify-center transition-colors">
                                                    <i class="bi bi-x text-sm"></i>
                                                </button>
                                            </div>
                                            <p class="text-xs text-green-600 dark:text-green-400 mt-2 text-center">
                                                <i class="bi bi-check-circle"></i> Imagem selecionada com sucesso
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label for="courseIcon" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            <i class="bi bi-grid-1x2-fill text-blue-500 mr-2"></i>
                                            Ícone do Curso
                                        </label>
                                        <input type="file" id="courseIcon" name="icon_course"
                                            accept=".png,.jpg,.jpeg,.gif,.webp,.avif"
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            Ícone exibido nos cards da home. Recomendado: 64x64px (PNG/WebP).
                                        </p>
                                        <div id="icon-preview" class="mt-2 inline-flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2 bg-slate-50 dark:bg-slate-900 <?= !empty($course->icon_course) ? '' : 'hidden' ?>">
                                            <img id="icon-preview-img"
                                                src="<?= !empty($course->icon_course) ? base_url('assets/img/' . $course->icon_course) : '' ?>"
                                                alt="Ícone do curso"
                                                class="w-10 h-10 object-contain rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600">
                                            <span id="icon-preview-label" class="text-xs text-slate-600 dark:text-slate-300">
                                                <?= !empty($course->icon_course) ? 'Ícone atual do curso' : 'Pré-visualização do ícone' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Content Structure -->
                    <div id="content-structure" class="tab-content hidden bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <!-- Header -->
                        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-diagram-3 text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Estrutura do Conteúdo</h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Organize por módulos (ex.: “Mês 1”, “Mês 2”). Em cada aula pode anexar PDF/ZIP e marcar preview.</p>
                                </div>
                            </div>
                            <div class="mt-3 text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-900 rounded-xl p-3">
                                Dica: use títulos como <strong>Mês 1 — Fundamentos</strong> para organizar o calendário.
                                Em quizzes, use o importador HTML com <code>data-q</code>, <code>data-points</code> e <code>li[data-correct]</code>.
                                Ficheiros da aula ficam em <code>writable/uploads/lesson_files/</code> e aparecem no player do aluno.
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div id="modules-container" class="space-y-4">
                                <?php foreach ($modules as $mIndex => $module): ?>
                                    <div class="module-card border border-slate-300 dark:border-slate-700 rounded-2xl p-4 bg-slate-50 dark:bg-slate-900" data-index="<?= $mIndex ?>">
                                        <div class="flex items-center justify-between mb-3">
                                            <input type="text" name="modules[<?= $mIndex ?>][title]"
                                                placeholder="Nome do Módulo"
                                                value="<?= esc($module->title_module) ?>"
                                                class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <i class="bi bi-x-circle text-red-500 text-lg ml-2 cursor-pointer hover:text-red-600 transition-colors remove-module" title="Remover módulo"></i>
                                        </div>

                                        <textarea name="modules[<?= $mIndex ?>][description]"
                                            placeholder="Descrição do Módulo"
                                            class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= esc($module->description_module) ?></textarea>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                                    Nota mínima do quiz (%)
                                                </label>
                                                <input type="number"
                                                    name="modules[<?= $mIndex ?>][min_score]"
                                                    min="0"
                                                    max="100"
                                                    value="<?= esc($module->min_score_module ?? 80) ?>"
                                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Ex: 80">
                                            </div>
                                        </div>

                                        <!-- Lessons Container -->
                                        <div class="lessons-container space-y-3 mb-3">
                                            <?php foreach ($module->lessons as $lIndex => $lesson): ?>
                                                <div class="lesson-item border border-slate-200 dark:border-slate-700 rounded-xl p-3 bg-white dark:bg-slate-800" data-index="<?= $lIndex ?>" draggable="true">
                                                    <?php
                                                    $quizQuestions = [];
                                                    if (($lesson->type_lesson ?? '') === 'quiz' && !empty($lesson->content_lesson)) {
                                                        $decodedQuiz = json_decode($lesson->content_lesson, true);
                                                        if (is_array($decodedQuiz) && !empty($decodedQuiz['questions'])) {
                                                            $quizQuestions = $decodedQuiz['questions'];
                                                        }
                                                    }

                                                    foreach ($quizQuestions as $qIndex => $q) {
                                                        if (!is_array($q)) {
                                                            $quizQuestions[$qIndex] = [
                                                                'question' => (string) $q,
                                                                'options' => ['', '', '', ''],
                                                                'correct' => 0,
                                                            ];
                                                            continue;
                                                        }

                                                        if (!isset($q['options'])) {
                                                            $quizQuestions[$qIndex]['options'] = ['', '', '', ''];
                                                            $quizQuestions[$qIndex]['correct'] = 0;
                                                        }
                                                    }
                                                    ?>
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="drag-handle text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-grab select-none px-1" title="Arraste para ordenar" draggable="true">
                                                            <i class="bi bi-grip-vertical"></i>
                                                        </span>
                                                        <input type="text"
                                                            name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][title]"
                                                            placeholder="Título da Aula"
                                                            value="<?= esc($lesson->title_lesson) ?>"
                                                            class="flex-1 px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        <i class="bi bi-x-circle text-red-500 text-base ml-2 cursor-pointer hover:text-red-600 transition-colors remove-lesson" title="Remover aula"></i>
                                                    </div>

                                                    <select name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][type]"
                                                        class="lesson-type w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        <option value="video" <?= $lesson->type_lesson == 'video' ? 'selected' : '' ?>>Vídeo</option>
                                                        <option value="text" <?= $lesson->type_lesson == 'text' ? 'selected' : '' ?>>Texto</option>
                                                        <option value="quiz" <?= $lesson->type_lesson == 'quiz' ? 'selected' : '' ?>>Quiz</option>
                                                        <option value="exercise" <?= $lesson->type_lesson == 'exercise' ? 'selected' : '' ?>>Exercício</option>
                                                    </select>

                                                    <input type="number" name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][duration]"
                                                        placeholder="Duração (min)"
                                                        value="<?= esc($lesson->duration_lesson) ?>"
                                                        class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">

                                                    <div class="video-fields">
                                                        <input type="url" name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][video_url]"
                                                            placeholder="Link do vídeo (para aulas de vídeo)"
                                                            value="<?= esc($lesson->video_url_lesson) ?>"
                                                            class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    </div>

                                                    <div class="mt-2 rounded-xl border border-emerald-300 bg-emerald-100 px-3 py-2 shadow-sm dark:border-emerald-500/70 dark:bg-emerald-950/85">
                                                        <input type="hidden"
                                                            name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][is_preview]"
                                                            value="0">
                                                        <label class="flex items-start gap-3 cursor-pointer">
                                                            <input type="checkbox"
                                                                name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][is_preview]"
                                                                value="1"
                                                                class="lesson-preview-toggle mt-0.5 h-4 w-4 rounded border-emerald-400 bg-white text-emerald-600 focus:ring-emerald-500 dark:border-emerald-400 dark:bg-emerald-950"
                                                                <?= !empty($lesson->is_preview_lesson) ? 'checked' : '' ?>>
                                                            <span>
                                                                <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-950 dark:text-emerald-100">
                                                                    <i class="bi bi-unlock-fill text-emerald-700 dark:text-emerald-300"></i>
                                                                    Aula com pre-visualizacao gratuita
                                                                </span>
                                                                <span class="block text-[11px] text-emerald-800 dark:text-emerald-200/90">
                                                                    Exibe cadeado aberto na pagina do curso e permite assistir esta aula antes da compra.
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                                                        <div>
                                                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                                                Arquivo da aula (opcional)
                                                            </label>
                                                            <input type="file"
                                                                name="lesson_files[<?= $mIndex ?>][<?= $lIndex ?>]"
                                                                accept=".zip,.rar,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                                                class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                            <input type="hidden"
                                                                name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][file_existing]"
                                                                value="<?= esc($lesson->attachment_path_lesson ?? '') ?>">
                                                            <input type="hidden"
                                                                name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][file_existing_name]"
                                                                value="<?= esc($lesson->attachment_name_lesson ?? '') ?>">
                                                            <?php if (!empty($lesson->attachment_path_lesson)): ?>
                                                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                                                    Atual: <?= esc($lesson->attachment_name_lesson ?? $lesson->attachment_path_lesson) ?>
                                                                </p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <div class="quiz-fields <?= $lesson->type_lesson === 'quiz' ? '' : 'hidden' ?> mt-3 bg-slate-100/60 dark:bg-slate-900/60 border border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-3">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                                Perguntas do quiz
                                                            </span>
                                                            <button type="button"
                                                                class="btn-add-quiz-question inline-flex items-center gap-1 px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-[11px] font-medium rounded-lg">
                                                                <i class="bi bi-plus"></i>
                                                                Adicionar pergunta
                                                            </button>
                                                        </div>
                                                        <div class="quiz-questions space-y-2">
                                                            <?php foreach ($quizQuestions as $qIndex => $question): ?>
                                                                <div class="quiz-question grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                                                                    <div class="space-y-2">
                                                                    <input type="text"
                                                                        name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][question]"
                                                                        class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                        placeholder="Pergunta"
                                                                        value="<?= esc($question['question'] ?? '') ?>">
                                                                    <input type="number" min="0.5" step="0.5"
                                                                        name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][points]"
                                                                        class="w-28 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-xs"
                                                                        placeholder="Pontos"
                                                                        value="<?= esc($question['points'] ?? 1) ?>">
                                                                    </div>
                                                                    <div class="flex flex-col gap-2">
                                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                                            <?php for ($opt = 0; $opt < 4; $opt++): ?>
                                                                                <input type="text"
                                                                                    name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][options][<?= $opt ?>]"
                                                                                    class="px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                                    placeholder="Alternativa <?= $opt + 1 ?>"
                                                                                    value="<?= esc($question['options'][$opt] ?? '') ?>">
                                                                            <?php endfor; ?>
                                                                        </div>
                                                                        <div class="flex gap-2 items-center">
                                                                            <select name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][correct]"
                                                                                class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                                                <?php for ($opt = 0; $opt < 4; $opt++): ?>
                                                                                    <option value="<?= $opt ?>" <?= (int) ($question['correct'] ?? 0) === $opt ? 'selected' : '' ?>>
                                                                                        Correta: alternativa <?= $opt + 1 ?>
                                                                                    </option>
                                                                                <?php endfor; ?>
                                                                            </select>
                                                                            <button type="button"
                                                                                class="remove-quiz-question text-red-500 hover:text-red-600 text-base"
                                                                                title="Remover pergunta">
                                                                                <i class="bi bi-x-circle"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <details class="mt-3 bg-white/70 dark:bg-slate-800/70 rounded-lg p-3">
                                                            <summary class="text-xs font-semibold cursor-pointer text-slate-700 dark:text-slate-200">Importar quiz por HTML</summary>
                                                            <p class="text-[11px] text-slate-500 mt-2 mb-2">
                                                                Cole HTML com <code>data-q</code>, opções em <code>li</code> e marque a correta com <code>data-correct</code>. Pontuação: <code>data-points="2"</code>.
                                                            </p>
                                                            <textarea class="quiz-html-import w-full text-[11px] px-2 py-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900" rows="5" placeholder='<div data-q data-points="2"><p>Pergunta?</p><ul><li data-correct>Certa</li><li>Errada</li><li>Errada</li><li>Errada</li></ul></div>'></textarea>
                                                            <button type="button" class="btn-import-quiz-html mt-2 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] rounded-lg">Importar HTML</button>
                                                        </details>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <button type="button" class="add-lesson px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm" data-module="<?= $mIndex ?>">
                                            <i class="bi bi-plus-circle mr-1"></i> Adicionar Aula
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="button" id="add-module" class="mt-4 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="bi bi-plus-circle mr-2"></i>
                                Adicionar Módulo
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Advanced Settings -->
                    <div id="advanced-settings" class="tab-content hidden bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <!-- Header -->
                        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-gear text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Configurações Avançadas</h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Defina as configurações do curso</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Tipo de Curso
                                    </label>
                                    <div class="flex flex-wrap gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="courseType" value="free" <?= $course->price_course == 0 ? 'checked' : '' ?>
                                                class="text-blue-500 focus:ring-blue-500">
                                            <span class="ml-2 text-slate-700 dark:text-slate-300 text-sm">Gratuito</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="courseType" value="paid" <?= $course->price_course > 0 ? 'checked' : '' ?>
                                                class="text-blue-500 focus:ring-blue-500">
                                            <span class="ml-2 text-slate-700 dark:text-slate-300 text-sm">Pago</span>
                                        </label>
                                    </div>
                                </div>

                                <div id="price-settings" class="space-y-4 <?= $course->price_course > 0 ? '' : 'hidden' ?>">
                                    <div class="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/70 dark:bg-blue-950/30 p-4 space-y-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                                <i class="bi bi-tags text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-800 dark:text-white">Preço e promoção</h4>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Defina o preço normal, o desconto e até quando a oferta é válida. A contagem regressiva aparece na home, na página do curso e no checkout.</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                    Preço normal (MZN)
                                                </label>
                                                <input type="number" id="coursePrice" name="price_course" min="0" step="0.01"
                                                    value="<?= esc($course->price_course) ?>"
                                                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                    Preço promocional (opcional)
                                                </label>
                                                <input type="number" id="coursePromoPrice" name="promo_price_course" min="0" step="0.01"
                                                    value="<?= esc($course->promo_price_course ?? '') ?>"
                                                    placeholder="Ex.: menor que o preço normal"
                                                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                    Promoção válida até
                                                </label>
                                                <input type="datetime-local" name="promo_ends_at_course" id="coursePromoEndsAt"
                                                    value="<?= !empty($course->promo_ends_at_course) ? esc(date('Y-m-d\TH:i', strtotime((string) $course->promo_ends_at_course))) : '' ?>"
                                                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                                <p class="text-xs text-slate-500">Sem data = promoção sem contagem regressiva.</p>
                                            </div>
                                            <div class="space-y-2">
                                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                    Pré-visualização
                                                </label>
                                                <?php
                                                $editPromoLeft = 0;
                                                if (! empty($course->promo_price_course) && ! empty($course->promo_ends_at_course)) {
                                                    $editPromoLeft = max(0, strtotime((string) $course->promo_ends_at_course) - time());
                                                }
                                                ?>
                                                <div id="promoPreviewBox" class="px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-dashed border-blue-300 dark:border-blue-800 text-sm text-slate-600 dark:text-slate-300 min-h-[52px] flex items-center">
                                                    <?php if ($editPromoLeft > 0): ?>
                                                        <span><i class="bi bi-hourglass-split text-blue-600 me-1"></i> Oferta activa · termina em <strong class="js-instructor-promo-preview" data-left="<?= (int) $editPromoLeft ?>">--:--:--</strong></span>
                                                    <?php else: ?>
                                                        <span class="text-slate-400">Preencha preço promo + data para ver o timer.</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                                Aulas grátis antes do pagamento
                                            </label>
                                            <input type="number" name="free_lessons_count_course" min="0" step="1"
                                                value="<?= esc((int) ($course->free_lessons_count_course ?? 0)) ?>"
                                                class="w-full px-4 py-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                            <p class="text-xs text-slate-500">Ex.: 3 = o aluno pode ver as 3 primeiras aulas; depois o sistema pede pagamento.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3 pt-2 border-t border-slate-200 dark:border-slate-700">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Carga horária</label>
                                    <div class="flex flex-wrap gap-4">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="hours_mode_course" value="auto" class="text-blue-500"
                                                <?= (($course->hours_mode_course ?? 'auto') !== 'manual') ? 'checked' : '' ?>>
                                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Automática (soma das aulas)</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="hours_mode_course" value="manual" class="text-blue-500"
                                                <?= (($course->hours_mode_course ?? '') === 'manual') ? 'checked' : '' ?>>
                                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Manual</span>
                                        </label>
                                    </div>
                                    <?php
                                    $hoursModeUi = (string) ($course->hours_mode_course ?? 'auto');
                                    $hoursManualRaw = $course->hours_manual_course ?? null;
                                    $hoursCourseRaw = $course->hours_course ?? null;
                                    $isManualHoursUi = $hoursModeUi === 'manual' || (int) $hoursManualRaw === 1;
                                    if ($hoursCourseRaw !== null && $hoursCourseRaw !== '') {
                                        $hoursInputValue = $hoursCourseRaw;
                                    } elseif ($isManualHoursUi && $hoursManualRaw !== null && (float) $hoursManualRaw > 1) {
                                        $hoursInputValue = $hoursManualRaw;
                                    } elseif ($hoursModeUi === 'manual' && $hoursManualRaw !== null && $hoursManualRaw !== '' && (int) $hoursManualRaw !== 1) {
                                        $hoursInputValue = $hoursManualRaw;
                                    } else {
                                        $hoursInputValue = '';
                                    }
                                    ?>
                                    <input type="number" name="hours_manual_course" min="0" step="0.5"
                                        value="<?= esc($hoursInputValue) ?>"
                                        placeholder="Ex.: 40"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-sm">
                                    <p class="text-xs text-slate-500">Usado no certificado e na página do curso quando o modo for Manual.</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        WhatsApp comercial (opcional)
                                    </label>
                                    <input type="text" name="whatsapp_contact_course"
                                        value="<?= esc($course->whatsapp_contact_course ?? $course->whatsapp_course ?? '258842726761') ?>"
                                        placeholder="258842726761"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white text-sm">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                        Cor primária do curso
                                    </label>
                                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-center">
                                        <input type="text"
                                            id="courseColorText"
                                            name="color_course"
                                            value="<?= esc($course->color_course ?? '#3b82f6') ?>"
                                            class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                                            placeholder="#3b82f6">
                                        <input type="color"
                                            id="courseColorPicker"
                                            value="<?= esc($course->color_course ?? '#3b82f6') ?>"
                                            class="h-12 w-16 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900">
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Cole um código HEX ou selecione no color picker.
                                    </p>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                            Projetos relacionados
                                        </label>
                                        <button type="button"
                                            id="add-project"
                                            class="inline-flex items-center gap-2 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-xl transition-all duration-200">
                                            <i class="bi bi-plus-circle"></i>
                                            Adicionar projeto
                                        </button>
                                    </div>
                                    <div id="projects-container" class="space-y-4">
                                        <?php foreach (($projects ?? []) as $pIndex => $project): ?>
                                            <div class="project-card border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-slate-50 dark:bg-slate-900" data-index="<?= $pIndex ?>">
                                                <div class="flex items-center justify-between gap-2 mb-2">
                                                    <input type="text"
                                                        name="projects[<?= $pIndex ?>][title]"
                                                        value="<?= esc($project->title_project ?? '') ?>"
                                                        class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <button type="button"
                                                        class="remove-project text-red-500 hover:text-red-600 text-lg"
                                                        title="Remover projeto">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                <textarea name="projects[<?= $pIndex ?>][description]"
                                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    placeholder="Descrição do projeto"><?= esc($project->description_project ?? '') ?></textarea>
                                                <div>
                                                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                                        Imagem do projeto (opcional)
                                                    </label>
                                                    <input type="file"
                                                        name="project_images[<?= $pIndex ?>]"
                                                        accept="image/*"
                                                        class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                    <input type="hidden"
                                                        name="projects[<?= $pIndex ?>][img_existing]"
                                                        value="<?= esc($project->img_project ?? '') ?>">
                                                    <?php if (!empty($project->img_project)): ?>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                            Atual: <?= esc($project->img_project) ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Adicione projetos práticos relacionados ao curso.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Review and Publish -->
                    <div id="review-publish" class="tab-content hidden bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <!-- Header -->
                        <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <i class="bi bi-rocket text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Revisão e Publicação</h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Finalize e publique seu curso</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="space-y-6">
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-people text-blue-600"></i>
                                        Estudantes inscritos
                                    </h4>
                                    <div class="text-2xl font-bold text-slate-800 dark:text-white">
                                        <?= number_format((int) ($enrolledCount ?? 0), 0, '', '.') ?>
                                    </div>
                                </div>

                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-bar-chart text-blue-600"></i>
                                        Resumo do curso
                                    </h4>
                                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Horas do curso</div>
                                            <div id="stats-course-hours" class="text-lg font-semibold text-slate-800 dark:text-white">0h</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Minutos das aulas</div>
                                            <div id="stats-lesson-minutes" class="text-lg font-semibold text-slate-800 dark:text-white">0</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Total de aulas</div>
                                            <div id="stats-lessons" class="text-lg font-semibold text-slate-800 dark:text-white">0</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Total de módulos</div>
                                            <div id="stats-modules" class="text-lg font-semibold text-slate-800 dark:text-white">0</div>
                                        </div>
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-3">
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">Arquivos do curso</div>
                                            <div id="stats-files" class="text-lg font-semibold text-slate-800 dark:text-white">0</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <button type="submit" id="publish-course" name="publish" value="1"
                                        class="w-full px-6 py-3.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                                        <i class="bi bi-rocket mr-2"></i>
                                        Publicar Curso
                                    </button>

                                    <button type="submit" name="draft" value="1"
                                        class="w-full px-6 py-3.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-xl transition-colors">
                                        <i class="bi bi-save mr-2"></i>
                                        Salvar como Rascunho
                                    </button>
                                    <button type="button" id="open-student-preview"
                                        class="w-full px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl transition-colors dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                        <i class="bi bi-display mr-2"></i>
                                        Abrir tela do aluno
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center mt-6">
            <button id="prev-step" class="px-5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm" disabled>
                <i class="bi bi-arrow-left mr-2"></i>
                Anterior
            </button>

            <button id="next-step" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl text-sm">
                Próximo
                <i class="bi bi-arrow-right ml-2"></i>
            </button>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    $(function() {
        if (typeof $.fn.summernote !== "function") {
            return;
        }

        const baseSummernoteConfig = {
            placeholder: "Descreva detalhadamente o conteúdo, objetivos e benefícios do seu curso...",
            height: 220,
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["font", ["superscript", "subscript"]],
                ["para", ["paragraph"]],
                ["insert", ["link"]],
                ["view", ["fullscreen", "codeview"]],
            ],
            callbacks: {
                onChange: function() {
                    window.dispatchEvent(new Event("course-editor-input"));
                },
                onKeyup: function() {
                    window.dispatchEvent(new Event("course-editor-input"));
                },
                onPaste: function(e) {
                    e.preventDefault();
                    const clipboard = ((e.originalEvent || e).clipboardData || window.clipboardData);
                    let text = clipboard ? (clipboard.getData("text/plain") || "") : "";
                    text = String(text).replace(/\r\n/g, "\n").trim();
                    document.execCommand("insertText", false, text);
                    window.dispatchEvent(new Event("course-editor-input"));
                }
            }
        };

        const descriptionEditor = $("#courseDescription");
        const learningEditor = $("#courseLearning");

        const applyEditorTheme = () => {
            const isDark = document.documentElement.classList.contains("dark");
            $(".note-editor.note-frame").toggleClass("note-dark-mode", isDark);
        };

        if (descriptionEditor.length) {
            descriptionEditor.summernote(baseSummernoteConfig);
        }

        if (learningEditor.length) {
            learningEditor.summernote(
                $.extend(true, {}, baseSummernoteConfig, {
                    placeholder: "Liste os principais tópicos que os alunos dominarão com este curso...",
                })
            );
        }

        applyEditorTheme();
        const themeObserver = new MutationObserver(applyEditorTheme);
        themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ["class"]
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // ======================
        // Global Variables
        // ======================
        let currentStep = 1;
        let moduleIndex = document.querySelectorAll('.module-card').length;

        // ======================
        // Elements
        // ======================
        const fileInput = document.getElementById("courseImage");
        const previewContainer = document.getElementById("image-preview");
        const previewImg = document.getElementById("preview-img");
        const removeBtn = document.getElementById("remove-image");
        const uploadArea = document.getElementById("upload-area");
        const iconInput = document.getElementById("courseIcon");
        const iconPreview = document.getElementById("icon-preview");
        const iconPreviewImg = document.getElementById("icon-preview-img");
        const iconPreviewLabel = document.getElementById("icon-preview-label");
        const previewCoverImgStatic = document.getElementById("preview-cover-img");
        const previewCoverFallbackStatic = document.getElementById("preview-cover-fallback");

        previewImg?.addEventListener("error", () => {
            previewImg.removeAttribute("src");
            previewContainer?.classList.add("hidden");
            uploadArea?.classList.remove("hidden");
        });

        previewCoverImgStatic?.addEventListener("error", () => {
            previewCoverImgStatic.removeAttribute("src");
            previewCoverImgStatic.classList.add("hidden");
            previewCoverFallbackStatic?.classList.remove("hidden");
        });

        iconPreviewImg?.addEventListener("error", () => {
            iconPreviewImg.removeAttribute("src");
            iconPreview?.classList.add("hidden");
        });

        if (previewImg?.getAttribute("src")?.trim() && previewImg.complete && previewImg.naturalWidth === 0) {
            previewImg.removeAttribute("src");
            previewContainer?.classList.add("hidden");
            uploadArea?.classList.remove("hidden");
        }

        if (previewCoverImgStatic?.getAttribute("src")?.trim() && previewCoverImgStatic.complete && previewCoverImgStatic.naturalWidth === 0) {
            previewCoverImgStatic.removeAttribute("src");
            previewCoverImgStatic.classList.add("hidden");
            previewCoverFallbackStatic?.classList.remove("hidden");
        }

        if (iconPreviewImg?.getAttribute("src")?.trim() && iconPreviewImg.complete && iconPreviewImg.naturalWidth === 0) {
            iconPreviewImg.removeAttribute("src");
            iconPreview?.classList.add("hidden");
        }
        const modulesContainer = document.getElementById("modules-container");
        const nextBtn = document.getElementById("next-step");
        const prevBtn = document.getElementById("prev-step");
        const tabBtns = document.querySelectorAll(".tab-btn");
        const tabContents = document.querySelectorAll(".tab-content");
        const priceSettings = document.getElementById("price-settings");
        const priceInput = document.getElementById("coursePrice");
        const courseTypeRadios = document.querySelectorAll('input[name="courseType"]');
        const form = document.getElementById("courseForm");
        const draftSaveUrl = "<?= base_url('instructor/dashboard/novo_curso/rascunho/' . $course->id_course) ?>";
        const openStudentPreviewButton = document.getElementById("open-student-preview");
        const studentPreviewUrl = "<?= base_url('instructor/dashboard/cursos/preview/' . $course->id_course) ?>";

        // Projetos
        const projectsContainer = document.getElementById("projects-container");
        const addProjectButton = document.getElementById("add-project");
        let projectIndex = projectsContainer ? projectsContainer.querySelectorAll(".project-card").length : 0;

        // Cor primÃ¡ria
        const colorTextInput = document.getElementById("courseColorText");
        const colorPickerInput = document.getElementById("courseColorPicker");

        let saveToastElement = null;
        let saveToastTimer = null;
        let lastSaveToastKey = "";
        let lastSaveToastAt = 0;

        function showSaveToast(message, type = "success") {
            const now = Date.now();
            const toastKey = `${type}:${message}`;
            const cooldown = type === "error" ? 4000 : 1200;

            if (toastKey === lastSaveToastKey && now - lastSaveToastAt < cooldown) {
                return;
            }

            lastSaveToastKey = toastKey;
            lastSaveToastAt = now;

            if (!saveToastElement) {
                saveToastElement = document.createElement("div");
                saveToastElement.className = "fixed bottom-4 right-4 z-[9999] px-4 py-2 rounded-lg shadow-lg text-sm font-medium transition-all duration-200 opacity-0 translate-y-2 pointer-events-none";
                document.body.appendChild(saveToastElement);
            }

            const toneClasses = type === "error" ?
                "bg-red-600 text-white" :
                "bg-emerald-600 text-white";

            saveToastElement.className = `fixed bottom-4 right-4 z-[9999] px-4 py-2 rounded-lg shadow-lg text-sm font-medium transition-all duration-200 pointer-events-none ${toneClasses}`;
            saveToastElement.textContent = message;
            saveToastElement.classList.remove("opacity-0", "translate-y-2");
            saveToastElement.classList.add("opacity-100", "translate-y-0");

            clearTimeout(saveToastTimer);
            saveToastTimer = setTimeout(() => {
                if (!saveToastElement) return;
                saveToastElement.classList.remove("opacity-100", "translate-y-0");
                saveToastElement.classList.add("opacity-0", "translate-y-2");
            }, 1800);
        }

        // ======================
        // Step Navigation
        // ======================
        function updateStepIndicators(step) {
            const indicators = document.querySelectorAll(".flex.space-x-3 > div");
            const progressBar = document.getElementById("progress-bar");
            const progressText = document.getElementById("progress-text");

            indicators.forEach((indicator, index) => {
                if (index + 1 < step) {
                    indicator.classList.remove("bg-slate-200", "dark:bg-slate-700", "text-slate-600", "dark:text-slate-400");
                    indicator.classList.add("bg-green-500", "text-white");
                    indicator.innerHTML = '<i class="bi bi-check text-sm"></i>';
                } else if (index + 1 === step) {
                    indicator.classList.remove("bg-slate-200", "dark:bg-slate-700", "text-slate-600", "dark:text-slate-400");
                    indicator.classList.add("bg-blue-500", "text-white");
                    indicator.innerHTML = index + 1;
                } else {
                    indicator.classList.remove("bg-blue-500", "bg-green-500", "text-white");
                    indicator.classList.add("bg-slate-200", "dark:bg-slate-700", "text-slate-600", "dark:text-slate-400");
                    indicator.innerHTML = index + 1;
                }
            });

            progressBar.style.width = (step * 25) + "%";
            progressText.textContent = `Passo ${step} de 4`;
        }

        function updateNavigationButtons() {
            prevBtn.disabled = currentStep === 1;
            nextBtn.style.display = currentStep === 4 ? "none" : "inline-block";
        }

        function showTab(tabId) {
            // Hide all tabs
            tabContents.forEach(tab => tab.classList.add("hidden"));
            tabContents.forEach(tab => tab.classList.remove("active"));

            // Remove active class from all tab buttons
            tabBtns.forEach(btn => {
                btn.classList.remove("active", "bg-blue-500", "text-white", "shadow-lg");
                btn.classList.add("bg-white", "dark:bg-slate-800", "text-slate-700", "dark:text-slate-300", "border");
            });

            // Show selected tab
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.classList.remove("hidden");
                selectedTab.classList.add("active");
            }

            // Update active tab button
            const activeBtn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
            if (activeBtn) {
                activeBtn.classList.add("active", "bg-blue-500", "text-white", "shadow-lg");
                activeBtn.classList.remove("bg-white", "dark:bg-slate-800", "text-slate-700", "dark:text-slate-300", "border");
            }
        }

        nextBtn.addEventListener("click", () => {
            if (currentStep < 4) {
                currentStep++;
                const tabIds = ["basic-info", "content-structure", "advanced-settings", "review-publish"];
                showTab(tabIds[currentStep - 1]);
                updateStepIndicators(currentStep);
                updateNavigationButtons();
            }
        });

        prevBtn.addEventListener("click", () => {
            if (currentStep > 1) {
                currentStep--;
                const tabIds = ["basic-info", "content-structure", "advanced-settings", "review-publish"];
                showTab(tabIds[currentStep - 1]);
                updateStepIndicators(currentStep);
                updateNavigationButtons();
            }
        });

        tabBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const tabId = btn.dataset.tab;
                const tabIds = ["basic-info", "content-structure", "advanced-settings", "review-publish"];
                currentStep = tabIds.indexOf(tabId) + 1;
                showTab(tabId);
                updateStepIndicators(currentStep);
                updateNavigationButtons();
            });
        });

        // Initialize first tab
        showTab("basic-info");
        updateNavigationButtons();
        updateStepIndicators(currentStep);

        // ======================
        // Image Upload
        // ======================
        function handleImageUpload(file) {
            if (!file.type.startsWith("image/")) {
                alert("Por favor selecione apenas arquivos de imagem.");
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert("O arquivo é muito grande. Tamanho máximo: 2MB");
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                if (previewImg) {
                    previewImg.src = e.target.result;
                }
                if (previewContainer) {
                    previewContainer.classList.remove("hidden");
                }
                if (uploadArea) uploadArea.classList.add("hidden");
            };
            reader.readAsDataURL(file);
        }

        if (uploadArea) {
            uploadArea.addEventListener("dragover", (e) => {
                e.preventDefault();
                uploadArea.classList.add("border-blue-500", "bg-blue-100", "dark:bg-blue-900/40");
            });

            uploadArea.addEventListener("dragleave", (e) => {
                e.preventDefault();
                uploadArea.classList.remove("border-blue-500", "bg-blue-100", "dark:bg-blue-900/40");
            });

            uploadArea.addEventListener("drop", (e) => {
                e.preventDefault();
                uploadArea.classList.remove("border-blue-500", "bg-blue-100", "dark:bg-blue-900/40");
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleImageUpload(e.dataTransfer.files[0]);
                }
            });
        }

        if (fileInput) {
            fileInput.addEventListener("change", (e) => {
                if (e.target.files.length) handleImageUpload(e.target.files[0]);
            });
        }

        function removeImage() {
            const previewCoverImg = document.getElementById("preview-cover-img");
            const previewCoverFallback = document.getElementById("preview-cover-fallback");

            fileInput.value = "";

            if (previewImg) {
                previewImg.removeAttribute("src");
            }

            if (previewContainer) {
                previewContainer.classList.add("hidden");
            }

            if (uploadArea) {
                uploadArea.classList.remove("hidden");
            }

            if (previewCoverImg) {
                previewCoverImg.removeAttribute("src");
                previewCoverImg.classList.add("hidden");
            }

            if (previewCoverFallback) {
                previewCoverFallback.classList.remove("hidden");
            }

            updateCoursePreview();
            scheduleAutoSave();
        }

        // Attach remove image event
        document.addEventListener("click", function(e) {
            const trigger = e.target.closest("#remove-image");
            if (trigger) {
                removeImage();
            }
        });

        if (iconInput) {
            iconInput.addEventListener("change", (event) => {
                const file = event.target.files?.[0];
                if (!file) {
                    return;
                }

                if (!file.type.startsWith("image/")) {
                    alert("Por favor selecione um arquivo de imagem válido para o ícone.");
                    iconInput.value = "";
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    if (iconPreviewImg) {
                        iconPreviewImg.src = e.target.result;
                    }
                    if (iconPreviewLabel) {
                        iconPreviewLabel.textContent = "Pré-visualização do novo ícone";
                    }
                    if (iconPreview) {
                        iconPreview.classList.remove("hidden");
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // ======================
        // Modules and Lessons Management
        // ======================
        function serializeModules() {
            const modules = [];
            document.querySelectorAll(".module-card").forEach((modCard, i) => {
                const moduleTitle =
                    modCard.querySelector('input[name$="[title]"]')?.value || `Módulo ${i + 1}`;
                const moduleDescription =
                    modCard.querySelector('textarea[name$="[description]"]')?.value || "";
                const moduleMinScore =
                    modCard.querySelector('input[name$="[min_score]"]')?.value || 80;
                const lessons = [];

                modCard.querySelectorAll(".lesson-item").forEach((lessonEl, j) => {
                    const title =
                        lessonEl.querySelector('input[name$="[title]"]')?.value || `Aula ${j + 1}`;
                    const type =
                        lessonEl.querySelector('select[name$="[type]"]')?.value || "text";
                    const duration =
                        lessonEl.querySelector('input[name$="[duration]"]')?.value || 0;
                    const video_url =
                        lessonEl.querySelector('input[name$="[video_url]"]')?.value || null;
                    const is_preview =
                        lessonEl.querySelector('.lesson-preview-toggle')?.checked ? 1 : 0;
                    const fileExisting =
                        lessonEl.querySelector('input[name$="[file_existing]"]')?.value || "";
                    const fileExistingName =
                        lessonEl.querySelector('input[name$="[file_existing_name]"]')?.value || "";
                    const fileInput = lessonEl.querySelector('input[type="file"][name^="lesson_files"]');
                    let fileInputIndex = null;
                    if (fileInput?.name) {
                        const match = fileInput.name.match(/lesson_files\[(\d+)\]\[(\d+)\]/);
                        fileInputIndex = match ? parseInt(match[2], 10) : null;
                    }

                    const quiz_questions = [];
                    if (type === "quiz") {
                        lessonEl.querySelectorAll(".quiz-question").forEach((qEl) => {
                            const question = qEl.querySelector('input[name$="[question]"]')?.value?.trim() || "";
                            const options = [];
                            qEl.querySelectorAll('input[name*="[options]"]').forEach((optEl) => {
                                options.push(optEl.value?.trim() || "");
                            });
                            const correctRaw = qEl.querySelector('select[name$="[correct]"]')?.value;
                            const correct = Number.isFinite(parseInt(correctRaw, 10)) ? parseInt(correctRaw, 10) : 0;

                            if (question || options.some(Boolean)) {
                                quiz_questions.push({
                                    question,
                                    options,
                                    correct
                                });
                            }
                        });
                    }

                    lessons.push({
                        title,
                        type,
                        duration,
                        video_url,
                        is_preview,
                        quiz_questions,
                        file_input_index: fileInputIndex,
                        file_existing: fileExisting,
                        file_existing_name: fileExistingName
                    });
                });

                const minScoreValue = parseInt(moduleMinScore, 10);
                modules.push({
                    title: moduleTitle,
                    description: moduleDescription,
                    min_score: Number.isFinite(minScoreValue) ? minScoreValue : 80,
                    lessons,
                });
            });

            const modulesJson = JSON.stringify(modules);
            const modulesHidden = document.getElementById("modules-json");
            if (modulesHidden) modulesHidden.value = modulesJson;
            const modulesHiddenAlt = document.getElementById("modules-json-alt");
            if (modulesHiddenAlt) modulesHiddenAlt.value = modulesJson;
        }

        function serializeProjects() {
            const projectsHidden = document.getElementById("projects-json");
            if (!projectsHidden) return;

            const projects = [];
            document.querySelectorAll(".project-card").forEach((projectCard) => {
                const title = projectCard.querySelector('input[name$="[title]"]')?.value || "";
                const description = projectCard.querySelector('textarea[name$="[description]"]')?.value || "";
                const imgExisting = projectCard.querySelector('input[name$="[img_existing]"]')?.value || "";

                projects.push({
                    title,
                    description,
                    img_existing: imgExisting,
                });
            });

            projectsHidden.value = JSON.stringify(projects);
        }

        function stripRedundantDynamicFieldNames() {
            const touched = [];
            const fields = document.querySelectorAll(".module-card [name], .project-card [name]");
            fields.forEach((field) => {
                const name = field.getAttribute("name");
                if (!name) return;
                if (field.type === "file") return;
                field.dataset.compactOriginalName = name;
                field.removeAttribute("name");
                touched.push(field);
            });
            return touched;
        }

        function restoreDynamicFieldNames(fields) {
            fields.forEach((field) => {
                const originalName = field.dataset.compactOriginalName;
                if (!originalName) return;
                field.setAttribute("name", originalName);
                delete field.dataset.compactOriginalName;
            });
        }

        function syncRichTextEditors() {
            if (typeof window.jQuery !== "function" || typeof window.jQuery.fn?.summernote !== "function") {
                return;
            }

            const descriptionEditor = window.jQuery("#courseDescription");
            if (descriptionEditor.length && descriptionEditor.next(".note-editor").length) {
                const descriptionField = document.getElementById("courseDescription");
                if (descriptionField) {
                    descriptionField.value = descriptionEditor.summernote("code");
                }
            }

            const learningEditor = window.jQuery("#courseLearning");
            if (learningEditor.length && learningEditor.next(".note-editor").length) {
                const learningField = document.getElementById("courseLearning");
                if (learningField) {
                    learningField.value = learningEditor.summernote("code");
                }
            }
        }

        async function saveDraftSilently() {
            if (!form) return false;

            syncRichTextEditors();
            serializeModules();
            serializeProjects();

            const compactedFields = stripRedundantDynamicFieldNames();
            const formData = new FormData(form);
            restoreDynamicFieldNames(compactedFields);

            try {
                const response = await fetch(draftSaveUrl, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                });

                let payload = {};
                try {
                    payload = await response.json();
                } catch (error) {
                    payload = {};
                }

                if (!response.ok || !payload.ok) {
                    console.warn(payload.message || "Falha ao salvar rascunho automaticamente.");
                    showSaveToast(payload.message || "Falha ao salvar rascunho automaticamente.", "error");
                    return false;
                }

                showSaveToast("Configuracao salva.");
                return true;
            } catch (error) {
                console.warn("Falha ao salvar rascunho automaticamente.", error);
                showSaveToast("Falha ao salvar rascunho automaticamente.", "error");
                return false;
            }
        }

        function buildStudentPreviewUrl() {
            const params = new URLSearchParams({
                return_url: window.location.href
            });

            return `${studentPreviewUrl}?${params.toString()}`;
        }

        async function openStudentPreview() {
            const saved = await saveDraftSilently();
            if (!saved) return;

            const previewUrl = buildStudentPreviewUrl();
            window.open(previewUrl, "_blank", "noopener");
        }

        function syncQuizFields(lessonEl) {
            const typeSelect = lessonEl.querySelector(".lesson-type");
            const quizFields = lessonEl.querySelector(".quiz-fields");
            if (!typeSelect || !quizFields) return;

            if (typeSelect.value === "quiz") {
                quizFields.classList.remove("hidden");
            } else {
                quizFields.classList.add("hidden");
            }
        }

        function syncVideoFields(lessonEl) {
            const typeSelect = lessonEl.querySelector(".lesson-type");
            const videoFields = lessonEl.querySelector(".video-fields");
            if (!typeSelect || !videoFields) return;

            if (typeSelect.value === "video") {
                videoFields.classList.remove("hidden");
            } else {
                videoFields.classList.add("hidden");
            }
        }

        function addQuizQuestion(lessonEl) {
            const questionsContainer = lessonEl.querySelector(".quiz-questions");
            if (!questionsContainer) return;

            const index = questionsContainer.querySelectorAll(".quiz-question").length;
            const moduleMatch = lessonEl.querySelector('input[name$="[title]"]')?.name?.match(/modules\[(\d+)\]/);
            const lessonMatch = lessonEl.querySelector('input[name$="[title]"]')?.name?.match(/lessons\[(\d+)\]/);
            const moduleId = moduleMatch ? moduleMatch[1] : 0;
            const lessonIndex = lessonMatch ? lessonMatch[1] : index;

            const questionHtml = `
                <div class="quiz-question grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                    <div class="space-y-2">
                    <input type="text"
                           name="modules[${moduleId}][lessons][${lessonIndex}][quiz][${index}][question]"
                           class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                           placeholder="Pergunta">
                    <input type="number" min="0.5" step="0.5"
                           name="modules[${moduleId}][lessons][${lessonIndex}][quiz][${index}][points]"
                           class="w-28 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-xs"
                           placeholder="Pontos" value="1">
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            ${[0,1,2,3].map(opt => `
                                <input type="text"
                                       name="modules[${moduleId}][lessons][${lessonIndex}][quiz][${index}][options][${opt}]"
                                       class="px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                       placeholder="Alternativa ${opt + 1}">
                            `).join("")}
                        </div>
                        <div class="flex gap-2 items-center">
                            <select name="modules[${moduleId}][lessons][${lessonIndex}][quiz][${index}][correct]"
                                    class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                ${[0,1,2,3].map(opt => `
                                    <option value="${opt}">Correta: alternativa ${opt + 1}</option>
                                `).join("")}
                            </select>
                            <button type="button"
                                    class="remove-quiz-question text-red-500 hover:text-red-600 text-base"
                                    title="Remover pergunta">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            questionsContainer.insertAdjacentHTML("beforeend", questionHtml);
        }

        function importQuizHtml(lessonEl) {
            const textarea = lessonEl.querySelector(".quiz-html-import");
            const html = textarea?.value?.trim() || "";
            if (!html) {
                alert("Cole o HTML do quiz primeiro.");
                return;
            }
            const wrap = document.createElement("div");
            wrap.innerHTML = html;
            const blocks = wrap.querySelectorAll("[data-q], .quiz-question-html, question");
            const sources = blocks.length ? blocks : wrap.querySelectorAll("div");
            let imported = 0;
            sources.forEach((block) => {
                const questionText = (block.querySelector("p, .question, h4, h5")?.textContent || block.getAttribute("data-question") || "").trim();
                const lis = Array.from(block.querySelectorAll("li"));
                if (!questionText || lis.length < 2) return;
                addQuizQuestion(lessonEl);
                const questions = lessonEl.querySelectorAll(".quiz-question");
                const last = questions[questions.length - 1];
                if (!last) return;
                last.querySelector('input[name*="[question]"]').value = questionText;
                const points = parseFloat(block.getAttribute("data-points") || block.getAttribute("points") || "1") || 1;
                const pointsInput = last.querySelector('input[name*="[points]"]');
                if (pointsInput) pointsInput.value = points;
                let correct = 0;
                lis.slice(0, 4).forEach((li, idx) => {
                    const optInput = last.querySelector(`input[name*="[options][${idx}]"]`);
                    if (optInput) optInput.value = (li.textContent || "").trim();
                    if (li.hasAttribute("data-correct") || li.classList.contains("correct") || li.getAttribute("data-answer") === "1") {
                        correct = idx;
                    }
                });
                const select = last.querySelector('select[name*="[correct]"]');
                if (select) select.value = String(correct);
                imported++;
            });
            if (!imported) {
                alert("Nenhuma pergunta válida encontrada. Use data-q + li[data-correct].");
                return;
            }
            if (textarea) textarea.value = "";
            alert(imported + " pergunta(s) importada(s).");
        }

        function addModule(moduleData = null) {
            const mIndex = moduleIndex++;
            const moduleId = moduleData?.id_module || "";
            const moduleTitle = moduleData?.title || "";
            const moduleDescription = moduleData?.description || "";
            const moduleMinScore = moduleData?.min_score ?? 80;
            let lessonsHtml = "";

            if (moduleData?.lessons) {
                moduleData.lessons.forEach((lesson, lIndex) => {
                    lessonsHtml += createLessonHTML(mIndex, lIndex, lesson);
                });
            }

            const moduleHtml = `
            <div class="module-card border border-slate-300 dark:border-slate-700 rounded-2xl p-4 bg-slate-50 dark:bg-slate-900" data-index="${mIndex}">
                <input type="hidden" name="modules[${mIndex}][id_module]" value="${moduleId}">
                <div class="flex items-center justify-between mb-3">
                    <input type="text" name="modules[${mIndex}][title]" 
                           placeholder="Nome do Módulo" 
                           value="${moduleTitle}"
                           class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="bi bi-x-circle text-red-500 text-lg ml-2 cursor-pointer hover:text-red-600 transition-colors remove-module" title="Remover módulo"></i>
                </div>
                <textarea name="modules[${mIndex}][description]" 
                          placeholder="Descrição do Módulo"
                          class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">${moduleDescription}</textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Nota mínima do quiz (%)
                        </label>
                        <input type="number"
                               name="modules[${mIndex}][min_score]"
                               min="0"
                               max="100"
                               value="${moduleMinScore}"
                               class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                               placeholder="Ex: 80">
                    </div>
                </div>
                <div class="lessons-container space-y-3 mb-3">${lessonsHtml}</div>
                <button type="button" class="add-lesson px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors text-sm" data-module="${mIndex}">
                    <i class="bi bi-plus-circle mr-1"></i> Adicionar Aula
                </button>
            </div>
        `;

            modulesContainer.insertAdjacentHTML("beforeend", moduleHtml);
        }

        function createLessonHTML(mIndex, lIndex, lessonData = {}) {
            const lessonId = lessonData.id_lesson || "";
            const title = lessonData.title || "";
            const type = lessonData.type || "text";
            const duration = lessonData.duration || 0;
            const video_url = lessonData.video_url || "";
            const isPreview = Number(lessonData.is_preview ?? lessonData.is_preview_lesson ?? 0) === 1;
            const fileExisting = lessonData.file_existing || lessonData.attachment_path || "";
            const fileExistingName = lessonData.file_existing_name || lessonData.attachment_name || "";
            const fileLabel = fileExisting ?
                `<p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Atual: ${fileExistingName || fileExisting}</p>` :
                "";

            return `
            <div class="lesson-item border border-slate-200 dark:border-slate-700 rounded-xl p-3 bg-white dark:bg-slate-800" data-index="${lIndex}" draggable="true">
                <input type="hidden" name="modules[${mIndex}][lessons][${lIndex}][id_lesson]" value="${lessonId}">
                <div class="flex items-center justify-between mb-2">
                    <span class="drag-handle text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-grab select-none px-1" title="Arraste para ordenar" draggable="true">
                        <i class="bi bi-grip-vertical"></i>
                    </span>
                    <input type="text" 
                           name="modules[${mIndex}][lessons][${lIndex}][title]" 
                           placeholder="Título da Aula" 
                           value="${title}"
                           class="flex-1 px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <i class="bi bi-x-circle text-red-500 text-base ml-2 cursor-pointer hover:text-red-600 transition-colors remove-lesson" title="Remover aula"></i>
                </div>
                <select name="modules[${mIndex}][lessons][${lIndex}][type]" 
                        class="lesson-type w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="video" ${type==='video'?'selected':''}>Vídeo</option>
                    <option value="text" ${type==='text'?'selected':''}>Texto</option>
                    <option value="quiz" ${type==='quiz'?'selected':''}>Quiz</option>
                    <option value="exercise" ${type==='exercise'?'selected':''}>Exercício</option>
                </select>
                <input type="number" 
                       name="modules[${mIndex}][lessons][${lIndex}][duration]" 
                       placeholder="Duração (min)" 
                       value="${duration}"
                       class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <div class="video-fields">
                    <input type="url" 
                           name="modules[${mIndex}][lessons][${lIndex}][video_url]" 
                           placeholder="Link do vídeo (para aulas de vídeo)" 
                           value="${video_url}"
                           class="w-full px-3 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="mt-2 rounded-xl border border-emerald-300 bg-emerald-100 px-3 py-2 shadow-sm dark:border-emerald-500/70 dark:bg-emerald-950/85">
                    <input type="hidden"
                           name="modules[${mIndex}][lessons][${lIndex}][is_preview]"
                           value="0">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="modules[${mIndex}][lessons][${lIndex}][is_preview]"
                               value="1"
                               class="lesson-preview-toggle mt-0.5 h-4 w-4 rounded border-emerald-400 bg-white text-emerald-600 focus:ring-emerald-500 dark:border-emerald-400 dark:bg-emerald-950"
                               ${isPreview ? "checked" : ""}>
                        <span>
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-950 dark:text-emerald-100">
                                <i class="bi bi-unlock-fill text-emerald-700 dark:text-emerald-300"></i>
                                Aula com pre-visualizacao gratuita
                            </span>
                            <span class="block text-[11px] text-emerald-800 dark:text-emerald-200/90">
                                Exibe cadeado aberto na pagina do curso e permite assistir esta aula antes da compra.
                            </span>
                        </span>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Arquivo da aula (opcional)
                        </label>
                        <input type="file"
                               name="lesson_files[${mIndex}][${lIndex}]"
                               accept=".zip,.rar,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                               class="w-full px-3 py-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <input type="hidden"
                               name="modules[${mIndex}][lessons][${lIndex}][file_existing]"
                               value="${fileExisting}">
                        <input type="hidden"
                               name="modules[${mIndex}][lessons][${lIndex}][file_existing_name]"
                               value="${fileExistingName}">
                        ${fileLabel}
                    </div>
                </div>
                <div class="quiz-fields hidden mt-3 bg-slate-100/60 dark:bg-slate-900/60 border border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                            Perguntas do quiz
                        </span>
                        <button type="button"
                                class="btn-add-quiz-question inline-flex items-center gap-1 px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-[11px] font-medium rounded-lg">
                            <i class="bi bi-plus"></i>
                            Adicionar pergunta
                        </button>
                    </div>
                    <div class="quiz-questions space-y-2"></div>
                </div>
            </div>
        `;
        }

        // Add Module Button
        document.getElementById("add-module")?.addEventListener("click", () => {
            addModule();
            scheduleAutoSave();
        });

        // Event Delegation for Modules Container
        modulesContainer?.addEventListener("click", (e) => {
            // Remove module
            if (e.target.classList.contains("remove-module")) {
                e.target.closest(".module-card").remove();
                scheduleAutoSave();
            }
            // Remove lesson
            if (e.target.classList.contains("remove-lesson")) {
                e.target.closest(".lesson-item").remove();
                scheduleAutoSave();
            }
            // Add quiz question
            if (e.target.closest(".btn-add-quiz-question")) {
                const lessonEl = e.target.closest(".lesson-item");
                addQuizQuestion(lessonEl);
                scheduleAutoSave();
            }
            if (e.target.closest(".btn-import-quiz-html")) {
                const lessonEl = e.target.closest(".lesson-item");
                importQuizHtml(lessonEl);
                scheduleAutoSave();
            }
            // Remove quiz question
            if (e.target.closest(".remove-quiz-question")) {
                e.target.closest(".quiz-question")?.remove();
                scheduleAutoSave();
            }
            // Add lesson
            const addLessonBtn = e.target.closest('.add-lesson');
            if (addLessonBtn) {
                const moduleId = addLessonBtn.dataset.module;
                const lessonsContainer = addLessonBtn.previousElementSibling;
                const lessonCount = lessonsContainer.querySelectorAll(".lesson-item").length;

                lessonsContainer.insertAdjacentHTML(
                    "beforeend",
                    createLessonHTML(moduleId, lessonCount)
                );
                scheduleAutoSave();
            }

        });

        modulesContainer?.addEventListener("change", (e) => {
            if (e.target.classList.contains("lesson-type")) {
                const lessonEl = e.target.closest(".lesson-item");
                syncQuizFields(lessonEl);
                syncVideoFields(lessonEl);
                scheduleAutoSave();
            }
        });

        function updateCourseStats() {
            if (!modulesContainer) return;
            const lessonItems = modulesContainer.querySelectorAll(".lesson-item");
            const moduleItems = modulesContainer.querySelectorAll(".module-card");
            let totalMinutes = 0;
            let filesCount = 0;

            lessonItems.forEach((lessonEl) => {
                const durationInput = lessonEl.querySelector('input[name$="[duration]"]');
                const durationVal = parseFloat(durationInput?.value || "0");
                if (Number.isFinite(durationVal) && durationVal > 0) {
                    totalMinutes += durationVal;
                }

                const fileInput = lessonEl.querySelector('input[type="file"][name^="lesson_files"]');
                const existingFile = lessonEl.querySelector('input[name$="[file_existing]"]')?.value || "";
                if ((fileInput && fileInput.files && fileInput.files.length > 0) || existingFile) {
                    filesCount += 1;
                }
            });

            const hours = totalMinutes / 60;
            const statsCourseHours = document.getElementById("stats-course-hours");
            const statsLessonMinutes = document.getElementById("stats-lesson-minutes");
            const statsLessons = document.getElementById("stats-lessons");
            const statsModules = document.getElementById("stats-modules");
            const statsFiles = document.getElementById("stats-files");

            if (statsCourseHours) statsCourseHours.textContent = `${hours.toFixed(1)}h`;
            if (statsLessonMinutes) statsLessonMinutes.textContent = `${Math.round(totalMinutes)}`;
            if (statsLessons) statsLessons.textContent = `${lessonItems.length}`;
            if (statsModules) statsModules.textContent = `${moduleItems.length}`;
            if (statsFiles) statsFiles.textContent = `${filesCount}`;
        }

        modulesContainer?.addEventListener("input", updateCourseStats);
        modulesContainer?.addEventListener("change", updateCourseStats);

        let draggedLesson = null;
        let dragPlaceholder = null;

        modulesContainer?.addEventListener("dragstart", (e) => {
            const handle = e.target.closest(".drag-handle");
            if (!handle) {
                e.preventDefault();
                return;
            }
            const lessonEl = handle.closest(".lesson-item");
            if (!lessonEl) return;
            draggedLesson = lessonEl;
            draggedLesson.classList.add("dragging");

            modulesContainer.querySelectorAll(".lessons-container")
                .forEach((c) => c.classList.add("drag-active"));

            const lessonWidth = lessonEl.offsetWidth;
            const lessonHeight = lessonEl.offsetHeight;
            dragPlaceholder = document.createElement("div");
            dragPlaceholder.className = "lesson-placeholder";
            dragPlaceholder.innerHTML = "<span>Soltar aqui</span>";
            dragPlaceholder.style.height = `${lessonHeight}px`;
            lessonEl.parentElement.insertBefore(dragPlaceholder, lessonEl.nextSibling);

            if (e.dataTransfer) {
                e.dataTransfer.effectAllowed = "move";
                e.dataTransfer.setData("text/plain", "");
                const ghost = lessonEl.cloneNode(true);
                ghost.style.width = `${lessonWidth}px`;
                ghost.style.opacity = "0.9";
                ghost.style.transform = "rotate(1deg)";
                ghost.style.position = "absolute";
                ghost.style.top = "-1000px";
                document.body.appendChild(ghost);
                e.dataTransfer.setDragImage(ghost, 20, 20);
                setTimeout(() => ghost.remove(), 0);
            }
            setTimeout(() => {
                if (draggedLesson) {
                    draggedLesson.style.display = "none";
                }
            }, 0);
        });

        modulesContainer?.addEventListener("dragend", () => {
            if (draggedLesson) {
                draggedLesson.classList.remove("dragging");
                draggedLesson.style.display = "";
            }
            modulesContainer.querySelectorAll(".lessons-container")
                .forEach((c) => c.classList.remove("drag-active"));
            if (dragPlaceholder && draggedLesson) {
                dragPlaceholder.parentElement?.insertBefore(draggedLesson, dragPlaceholder);
                dragPlaceholder.remove();
            }
            draggedLesson = null;
            dragPlaceholder = null;
            updateCourseStats();
            scheduleAutoSave();
        });

        modulesContainer?.addEventListener("dragover", (e) => {
            if (!draggedLesson || !dragPlaceholder) return;
            const targetContainer = e.target.closest(".lessons-container");
            if (!targetContainer) return;
            e.preventDefault();

            const targetLesson = e.target.closest(".lesson-item");
            if (!targetLesson || targetLesson === draggedLesson) {
                if (dragPlaceholder.parentElement !== targetContainer || dragPlaceholder.nextSibling !== null) {
                    targetContainer.appendChild(dragPlaceholder);
                }
                return;
            }
            if (targetLesson === dragPlaceholder) return;
            const rect = targetLesson.getBoundingClientRect();
            const after = (e.clientY - rect.top) > rect.height / 2;
            if (after) {
                if (dragPlaceholder.previousSibling !== targetLesson) {
                    targetContainer.insertBefore(dragPlaceholder, targetLesson.nextSibling);
                }
            } else {
                if (dragPlaceholder.nextSibling !== targetLesson) {
                    targetContainer.insertBefore(dragPlaceholder, targetLesson);
                }
            }
        });

        modulesContainer?.addEventListener("dragenter", (e) => {
            if (!draggedLesson) return;
            const targetContainer = e.target.closest(".lessons-container");
            if (targetContainer) {
                targetContainer.classList.add("drag-over");
            }
        });

        modulesContainer?.addEventListener("dragleave", (e) => {
            const targetContainer = e.target.closest(".lessons-container");
            if (!targetContainer) return;
            if (!targetContainer.contains(e.relatedTarget)) {
                targetContainer.classList.remove("drag-over");
            }
        });

        modulesContainer?.addEventListener("drop", (e) => {
            const targetContainer = e.target.closest(".lessons-container");
            if (targetContainer) {
                targetContainer.classList.remove("drag-over");
            }
        });

        updateCourseStats();

        document.querySelectorAll(".lesson-item").forEach((lessonEl) => {
            syncQuizFields(lessonEl);
            syncVideoFields(lessonEl);
        });

        // ======================
        // Cor primÃ¡ria
        // ======================
        const hexPattern = /^#([0-9a-f]{6})$/i;
        if (colorTextInput && colorPickerInput) {
            if (!hexPattern.test(colorTextInput.value)) {
                colorTextInput.value = "#3b82f6";
            }
            colorPickerInput.value = colorTextInput.value;

            colorTextInput.addEventListener("input", () => {
                const value = colorTextInput.value.trim();
                if (hexPattern.test(value)) {
                    colorPickerInput.value = value;
                }
            });

            colorPickerInput.addEventListener("input", () => {
                colorTextInput.value = colorPickerInput.value;
            });
        }

        // ======================
        // Projetos
        // ======================
        function buildProjectCard(index, project = {}) {
            const title = project.title || "";
            const description = project.description || "";
            const imgExisting = project.img_existing || "";
            const imgLabel = imgExisting ? `
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Atual: ${imgExisting}
                </p>` : "";

            return `
                <div class="project-card border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-slate-50 dark:bg-slate-900" data-index="${index}">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <input type="text"
                               name="projects[${index}][title]"
                               value="${title}"
                               class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Título do projeto">
                        <button type="button"
                                class="remove-project text-red-500 hover:text-red-600 text-lg"
                                title="Remover projeto">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <textarea name="projects[${index}][description]"
                              class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Descrição do projeto">${description}</textarea>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Imagem do projeto (opcional)
                        </label>
                        <input type="file"
                               name="project_images[${index}]"
                               accept="image/*"
                               class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <input type="hidden"
                               name="projects[${index}][img_existing]"
                               value="${imgExisting}">
                        ${imgLabel}
                    </div>
                </div>
            `;
        }

        if (addProjectButton && projectsContainer) {
            addProjectButton.addEventListener("click", () => {
                projectsContainer.insertAdjacentHTML("beforeend", buildProjectCard(projectIndex));
                projectIndex++;
                scheduleAutoSave();
            });

            projectsContainer.addEventListener("click", (e) => {
                if (e.target.closest(".remove-project")) {
                    e.target.closest(".project-card")?.remove();
                    scheduleAutoSave();
                }
            });
        }

        // ======================
        // Course Type and Price Toggle
        // ======================
        courseTypeRadios.forEach(radio => {
            radio.addEventListener("change", () => {
                if (radio.value === "paid") {
                    priceSettings.classList.remove("hidden");
                    if (priceInput) priceInput.focus();
                } else {
                    priceSettings.classList.add("hidden");
                }
            });
        });

        // ======================
        // Auto-save
        // ======================
        let autoSaveTimer = null;
        let autoSaveInFlight = false;
        let autoSaveQueued = false;

        function scheduleAutoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(async () => {
                if (autoSaveInFlight) {
                    autoSaveQueued = true;
                    return;
                }

                autoSaveInFlight = true;
                await saveDraftSilently();
                autoSaveInFlight = false;

                if (autoSaveQueued) {
                    autoSaveQueued = false;
                    scheduleAutoSave();
                }
            }, 900);
        }

        document.addEventListener("input", scheduleAutoSave);
        document.addEventListener("change", scheduleAutoSave);
        window.addEventListener("course-editor-input", scheduleAutoSave);

        if (openStudentPreviewButton) {
            openStudentPreviewButton.addEventListener("click", async () => {
                await openStudentPreview();
            });
        }

        // ======================
        // Form Validation (Basic)
        // ======================
        document.getElementById('courseForm')?.addEventListener('submit', function(e) {
            syncRichTextEditors();
            const title = document.getElementById('title_course').value.trim();
            const subtitle = document.getElementById('courseSubtitle').value.trim();
            const description = document.getElementById('courseDescription').value.trim();

            if (!title || !subtitle || !description) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios (*).');
                showTab('basic-info');
                updateStepIndicators(1);
                return;
            }

            serializeModules();
            serializeProjects();
            stripRedundantDynamicFieldNames();
            // You can add more validation here as needed
        });

        document.addEventListener("input", updateCoursePreview);
        document.addEventListener("change", updateCoursePreview);
        window.addEventListener("course-editor-input", updateCoursePreview);

        updateCoursePreview();
    });

    function escapeHtml(value) {
        const div = document.createElement("div");
        div.textContent = value ?? "";
        return div.innerHTML;
    }

    function formatMoneyPreview(value) {
        const amount = Number(value || 0);
        return amount > 0 ?
            `${amount.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 })} MZN` :
            "Gratuito";
    }

    function getEditorHtmlOrTextareaValue(selector) {
        if (typeof window.jQuery === "function" && typeof window.jQuery.fn?.summernote === "function") {
            const editor = window.jQuery(selector);
            if (editor.length && editor.next(".note-editor").length) {
                return editor.summernote("code");
            }
        }

        const el = document.querySelector(selector);
        return el ? el.value : "";
    }

    function getCourseTypePreview() {
        const paidRadio = document.querySelector('input[name="courseType"][value="paid"]');
        return paidRadio?.checked ? "Curso pago" : "Curso gratuito";
    }

    function getCoursePricePreview() {
        const paidRadio = document.querySelector('input[name="courseType"][value="paid"]');
        const priceInput = document.getElementById("price_course") || document.getElementById("coursePrice");
        if (paidRadio?.checked) {
            return formatMoneyPreview(priceInput?.value || 0);
        }
        return "Gratuito";
    }

    function getModulePreviewData() {
        const moduleCards = document.querySelectorAll(".module-card");
        let totalLessons = 0;
        let totalMinutes = 0;

        const modules = Array.from(moduleCards).map((moduleCard, moduleIndex) => {
            const title = moduleCard.querySelector('input[name$="[title]"]')?.value?.trim() || `Módulo ${moduleIndex + 1}`;
            const description = moduleCard.querySelector('textarea[name$="[description]"]')?.value?.trim() || "";

            const lessons = Array.from(moduleCard.querySelectorAll(".lesson-item")).map((lessonEl, lessonIndex) => {
                const lessonTitle = lessonEl.querySelector('input[name$="[title]"]')?.value?.trim() || `Aula ${lessonIndex + 1}`;
                const lessonType = lessonEl.querySelector('select[name$="[type]"]')?.value || "text";
                const lessonDuration = Number(lessonEl.querySelector('input[name$="[duration]"]')?.value || 0);
                const lessonPreview = lessonEl.querySelector('.lesson-preview-toggle')?.checked || false;

                totalLessons += 1;
                totalMinutes += lessonDuration > 0 ? lessonDuration : 0;

                return {
                    title: lessonTitle,
                    type: lessonType,
                    duration: lessonDuration,
                    isPreview: lessonPreview
                };
            });

            return {
                title,
                description,
                lessons
            };
        });

        return {
            modules,
            totalLessons,
            totalMinutes
        };
    }

    function getProjectsPreviewData() {
        const projectCards = document.querySelectorAll(".project-card");
        return Array.from(projectCards).map((projectCard, index) => {
            const title = projectCard.querySelector('input[name$="[title]"]')?.value?.trim() || `Projeto ${index + 1}`;
            const description = projectCard.querySelector('textarea[name$="[description]"]')?.value?.trim() || "";
            const fileInput = projectCard.querySelector('input[type="file"][name^="project_images"]');
            const existingText = projectCard.querySelector('input[name$="[img_existing]"]')?.value || "";

            let imageUrl = "";
            if (fileInput?.files?.[0]) {
                imageUrl = URL.createObjectURL(fileInput.files[0]);
            } else if (existingText) {
                imageUrl = existingText.includes("/") ? existingText : `<?= base_url('assets/img/') ?>/${existingText}`;
            }

            return {
                title,
                description,
                imageUrl
            };
        }).filter(project => project.title || project.description || project.imageUrl);
    }

    function renderPreviewModules() {
        const container = document.getElementById("preview-modules");
        const totalModulesEl = document.getElementById("preview-total-modules");
        const totalLessonsEl = document.getElementById("preview-total-lessons");
        const totalDurationEl = document.getElementById("preview-total-duration");

        if (!container) return;

        const {
            modules,
            totalLessons,
            totalMinutes
        } = getModulePreviewData();

        if (totalModulesEl) {
            totalModulesEl.textContent = `${modules.length} módulo${modules.length === 1 ? "" : "s"}`;
        }
        if (totalLessonsEl) {
            totalLessonsEl.textContent = `${totalLessons} aula${totalLessons === 1 ? "" : "s"}`;
        }
        if (totalDurationEl) {
            totalDurationEl.textContent = `${Math.round(totalMinutes)} min`;
        }

        if (!modules.length) {
            container.innerHTML = `<div class="text-sm text-slate-500 dark:text-slate-400">Nenhum módulo adicionado ainda.</div>`;
            return;
        }

        container.innerHTML = modules.map((module, idx) => `
        <div class="course-preview-module">
            <div class="flex items-center justify-between gap-3 mb-2">
                <h5 class="font-semibold text-slate-800 dark:text-white text-sm">
                    ${idx + 1}. ${escapeHtml(module.title)}
                </h5>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    ${module.lessons.length} aula${module.lessons.length === 1 ? "" : "s"}
                </span>
            </div>
            ${module.description ? `<p class="text-xs text-slate-600 dark:text-slate-400 mb-3">${escapeHtml(module.description)}</p>` : ""}
            <div class="space-y-2">
                ${
                    module.lessons.length
                    ? module.lessons.map((lesson, lessonIndex) => `
                        <div class="flex items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <i class="bi ${
                                    lesson.type === "video" ? "bi-play-btn" :
                                    lesson.type === "quiz" ? "bi-patch-question" :
                                    lesson.type === "exercise" ? "bi-journal-check" :
                                    "bi-file-text"
                                } text-blue-600"></i>
                                <span class="truncate text-slate-700 dark:text-slate-300">
                                    ${lessonIndex + 1}. ${escapeHtml(lesson.title)}
                                </span>
                            </div>
                            <div class="flex items-center gap-2 whitespace-nowrap">
                                <span class="${lesson.isPreview ? "text-emerald-600 dark:text-emerald-400" : "text-slate-500 dark:text-slate-400"}">
                                    <i class="bi ${lesson.isPreview ? "bi-unlock-fill" : "bi-lock-fill"}"></i>
                                </span>
                                <span class="text-slate-500 dark:text-slate-400">
                                    ${lesson.duration > 0 ? `${lesson.duration} min` : "--"}
                                </span>
                            </div>
                        </div>
                    `).join("")
                    : `<div class="text-xs text-slate-500 dark:text-slate-400">Sem aulas neste módulo.</div>`
                }
            </div>
        </div>
    `).join("");
    }

    function renderPreviewProjects() {
        const container = document.getElementById("preview-projects");
        if (!container) return;

        const projects = getProjectsPreviewData();

        if (!projects.length) {
            container.innerHTML = `<div class="text-sm text-slate-500 dark:text-slate-400">Nenhum projeto adicionado ainda.</div>`;
            return;
        }

        container.innerHTML = projects.map(project => `
        <div class="course-preview-project">
            ${project.imageUrl ? `<img src="${project.imageUrl}" alt="${escapeHtml(project.title)}">` : ""}
            <div class="font-semibold text-sm text-slate-800 dark:text-white">
                ${escapeHtml(project.title || "Projeto")}
            </div>
            ${project.description ? `<p class="text-xs text-slate-600 dark:text-slate-400 mt-1">${escapeHtml(project.description)}</p>` : ""}
        </div>
    `).join("");
    }

    function updateCoursePreview() {
        const titleEl = document.getElementById("preview-title");
        const subtitleEl = document.getElementById("preview-subtitle");
        const descriptionEl = document.getElementById("preview-description");
        const learningEl = document.getElementById("preview-learning");
        const statusEl = document.getElementById("preview-status");
        const priceEl = document.getElementById("preview-price");
        const coverEl = document.getElementById("preview-cover");
        const coverImgEl = document.getElementById("preview-cover-img");
        const coverFallbackEl = document.getElementById("preview-cover-fallback");

        const titleInput = document.getElementById("title_course");
        const subtitleInput = document.getElementById("courseSubtitle");
        const colorInput = document.getElementById("courseColorText") || document.querySelector('input[name="color_course"]');
        const imageInput = document.getElementById("courseImage");
        const iconInputLocal = document.getElementById("courseIcon");
        const previewIconImg = document.getElementById("preview-icon-img");

        if (titleEl) titleEl.textContent = titleInput?.value?.trim() || "Título do curso";
        if (subtitleEl) subtitleEl.textContent = subtitleInput?.value?.trim() || "Subtítulo do curso aparecerá aqui.";
        if (descriptionEl) descriptionEl.innerHTML = getEditorHtmlOrTextareaValue("#courseDescription") || "<p>Adicione uma descrição para ver a pré-visualização.</p>";
        if (learningEl) learningEl.innerHTML = getEditorHtmlOrTextareaValue("#courseLearning") || "<p>Os tópicos de aprendizagem aparecerão aqui.</p>";
        if (statusEl) statusEl.textContent = getCourseTypePreview();
        if (priceEl) priceEl.textContent = getCoursePricePreview();

        const selectedColor = colorInput?.value?.trim() || "#3b82f6";
        if (coverEl) {
            coverEl.style.background = `linear-gradient(135deg, ${selectedColor}, #1d4ed8)`;
        }

        const existingCoverSrc = coverImgEl?.getAttribute("src")?.trim();

        if (imageInput?.files?.[0] && coverImgEl) {
            const reader = new FileReader();
            reader.onload = (e) => {
                coverImgEl.src = e.target.result;
                coverImgEl.classList.remove("hidden");
                coverFallbackEl?.classList.add("hidden");
            };
            reader.readAsDataURL(imageInput.files[0]);
        } else if (existingCoverSrc) {
            coverImgEl.classList.remove("hidden");
            coverFallbackEl?.classList.add("hidden");
        } else {
            coverImgEl?.classList.add("hidden");
            coverFallbackEl?.classList.remove("hidden");
        }

        if (iconInputLocal?.files?.[0] && previewIconImg) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewIconImg.src = e.target.result;
                previewIconImg.classList.remove("hidden");
            };
            reader.readAsDataURL(iconInputLocal.files[0]);
        }

        renderPreviewModules();
        renderPreviewProjects();
    }
</script>
<script>
(function () {
  const el = document.querySelector('.js-instructor-promo-preview');
  if (!el) return;
  let left = parseInt(el.getAttribute('data-left') || '0', 10);
  const pad = (n) => String(n).padStart(2, '0');
  const fmt = (secs) => {
    const d = Math.floor(secs / 86400);
    const h = Math.floor((secs % 86400) / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = secs % 60;
    if (d > 0) return d + 'd ' + pad(h) + ':' + pad(m) + ':' + pad(s);
    return pad(h) + ':' + pad(m) + ':' + pad(s);
  };
  const tick = () => {
    el.textContent = fmt(Math.max(0, left));
    if (left <= 0) return;
    left -= 1;
    setTimeout(tick, 1000);
  };
  tick();
})();
</script>

<?= $this->endSection() ?>
