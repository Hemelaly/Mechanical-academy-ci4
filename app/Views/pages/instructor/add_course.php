<?php
$user = service('auth')->user();
$draftDescription = str_replace('</textarea>', '&lt;/textarea&gt;', $draft->description_course ?? '');
$draftLearning = str_replace('</textarea>', '&lt;/textarea&gt;', $draft->learning_course ?? '');
?>

<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Criar Novo Curso<?= $this->endSection() ?>

<?= $this->section('add_course') ?>

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

<div class="min-w-0 bg-slate-50 dark:bg-slate-900 py-6">
    <div class="container mx-auto px-4">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
                        <i class="bi bi-mortarboard text-blue-600 mr-3"></i>
                        Criar Novo Curso
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Transforme seu conhecimento em um curso incrível
                    </p>
                </div>
            </div>
        </div>

        <!-- Progress Indicator / Stepper -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <ol id="stepper" class="flex items-center w-full text-sm font-medium text-center text-body sm:text-base">
                    <li class="flex md:w-full items-center text-fg-brand step-item" data-step="1">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-fg-disabled">
                            <svg class="w-5 h-5 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>1. Básico</span>
                        </span>
                    </li>
                    <li class="flex md:w-full items-center step-item" data-step="2">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-fg-disabled">
                            <span class="me-2">2</span>
                            Estrutura
                        </span>
                    </li>
                    <li class="flex md:w-full items-center step-item" data-step="3">
                        <span class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-fg-disabled">
                            <span class="me-2">3</span>
                            Configurações
                        </span>
                    </li>
                    <li class="flex items-center step-item" data-step="4">
                        <span class="me-2">4</span>
                        Publicação
                    </li>
                </ol>

                <div class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Passo <span class="text-blue-600 dark:text-blue-400" id="current-step-text">1</span> de 4
                </div>
            </div>

            <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500" id="progress-bar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">
            <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">

                <!-- Navigation Tabs -->
                <div class="border-b border-slate-200 dark:border-slate-700">
                    <div class="flex overflow-x-auto">
                        <button class="tab-button flex items-center gap-2 px-4 sm:px-6 py-4 border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-medium text-sm sm:text-base whitespace-nowrap flex-shrink-0" data-tab="basic-info">
                            <i class="bi bi-info-circle"></i>
                            Informações Básicas
                        </button>
                        <button class="tab-button flex items-center gap-2 px-4 sm:px-6 py-4 border-b-2 border-transparent text-slate-500 dark:text-slate-400 font-medium text-sm sm:text-base whitespace-nowrap flex-shrink-0" data-tab="content-structure">
                            <i class="bi bi-diagram-3"></i>
                            Estrutura do Conteúdo
                        </button>
                        <button class="tab-button flex items-center gap-2 px-4 sm:px-6 py-4 border-b-2 border-transparent text-slate-500 dark:text-slate-400 font-medium text-sm sm:text-base whitespace-nowrap flex-shrink-0" data-tab="advanced-settings">
                            <i class="bi bi-gear"></i>
                            Configurações Avançadas
                        </button>
                        <button class="tab-button flex items-center gap-2 px-4 sm:px-6 py-4 border-b-2 border-transparent text-slate-500 dark:text-slate-400 font-medium text-sm sm:text-base whitespace-nowrap flex-shrink-0" data-tab="review-publish">
                            <i class="bi bi-rocket"></i>
                            Revisão e Publicação
                        </button>
                    </div>
                </div>

                <!-- Form -->
                <form id="courseForm" action="<?= base_url('instructor/dashboard/novo_curso/criar') ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_instructor_course" value="<?= $user->id ?>">
                    <input type="hidden" id="draft-id" name="draft_id" value="<?= esc($draft->id_course ?? '') ?>">
                    <input type="hidden" id="modules-json" name="modules">
                    <input type="hidden" id="modules-json-alt" name="modules_json">
                    <input type="hidden" id="tags-json" name="tags">
                    <input type="hidden" id="projects-json" name="projects_json">
                    <input type="hidden" name="projects_present" value="1">

                    <div class="p-4 sm:p-6 lg:p-8">
                        <!-- Step 1: Basic Info -->
                        <div class="tab-content active" id="basic-info">
                            <div class="space-y-6">
                                <!-- Header -->
                                <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                                    <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-pencil-square text-blue-600"></i>
                                        Informações Básicas do Curso
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
                                        Preencha os campos obrigatórios para o seu curso
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <!-- Left Column - Text Fields -->
                                    <div class="lg:col-span-2 space-y-6">
                                        <!-- Course Title -->
                                        <div>
                                            <label for="title_course" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-type text-blue-600"></i>
                                                Título do Curso *
                                            </label>
                                            <input type="text"
                                                id="title_course"
                                                name="title_course"
                                                value="<?= esc($draft->title_course ?? '') ?>"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                placeholder="Ex: Desenvolvimento Web Completo"
                                                required />
                                        </div>

                                        <!-- Course Subtitle -->
                                        <div>
                                            <label for="courseSubtitle" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-text-paragraph text-blue-600"></i>
                                                Subtítulo do Curso *
                                            </label>
                                            <input type="text"
                                                id="courseSubtitle"
                                                name="subtitle_course"
                                                value="<?= esc($draft->subtitle_course ?? '') ?>"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                placeholder="Ex: Do zero ao avançado com HTML, CSS e JavaScript"
                                                required />
                                        </div>

                                        <!-- Course Description -->
                                        <div>
                                            <label for="courseDescription" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-file-text text-blue-600"></i>
                                                Descrição do Curso *
                                            </label>
                                            <textarea id="courseDescription"
                                                name="description_course"
                                                rows="8"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                                placeholder="Descreva detalhadamente o conteúdo, objetivos e benefícios do seu curso..."><?= $draftDescription ?></textarea>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                                Recomendado: mínimo 200 caracteres
                                            </p>
                                        </div>

                                        <div>
                                            <label for="courseLearning" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-list-check text-blue-600"></i>
                                                O que você aprenderá *
                                            </label>
                                            <textarea id="courseLearning"
                                                name="learning_course"
                                                rows="6"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                                placeholder="Liste os principais tópicos ou habilidades que os alunos vão dominar..."><?= $draftLearning ?></textarea>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                                Use o editor para criar listas, negritos e links personalizados
                                            </p>
                                        </div>

                                        <div>
                                            <label for="courseVideo" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-play-btn text-blue-600"></i>
                                                Vídeo de visão geral
                                            </label>
                                            <input type="url"
                                                id="courseVideo"
                                                name="url_video_course"
                                                value="<?= esc($draft->url_video_course ?? '') ?>"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"
                                                placeholder="https://vimeo.com/xxxxx ou https://www.youtube.com/watch?v=...">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                                URL exibida na página pública dentro do bloco de vídeo.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Column - Image Upload -->
                                    <div class="space-y-6">
                                        <!-- Image Upload -->
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-image text-blue-600"></i>
                                                Imagem de Capa *
                                            </label>

                                            <!-- Upload Area -->
                                            <div id="upload-area"
                                                class="border-2 border-dashed border-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 cursor-pointer <?= !empty($draft->image_course) ? 'hidden' : '' ?>">
                                                <i class="bi bi-cloud-arrow-up text-blue-500 text-2xl sm:text-3xl mb-3"></i>
                                                <h6 class="font-semibold text-slate-800 dark:text-white mb-2 text-sm">
                                                    Arraste uma imagem ou clique para selecionar
                                                </h6>
                                                <p class="text-slate-500 dark:text-slate-400 text-xs mb-4">
                                                    Recomendado: 1280x720px, máx. 2MB
                                                </p>
                                                <input type="file"
                                                    id="courseImage"
                                                    name="image_course"
                                                    accept="image/*"
                                                    class="hidden" />
                                                <button type="button"
                                                    onclick="document.getElementById('courseImage').click()"
                                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                                                    <i class="bi bi-folder2-open"></i>
                                                    Selecionar Arquivo
                                                </button>
                                            </div>

                                            <!-- Image Preview -->
                                            <div id="image-preview" class="<?= !empty($draft->image_course) ? '' : 'hidden' ?> mt-4">
                                                <div class="relative">
                                                    <img id="preview-img"
                                                        src="<?= !empty($draft->image_course) ? base_url('assets/instructor/img/courses/' . $draft->image_course) : '' ?>"
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

                                        <div>
                                            <label for="courseIcon" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                                                <i class="bi bi-grid-1x2-fill text-blue-600"></i>
                                                Ícone do Curso
                                            </label>
                                            <input type="file"
                                                id="courseIcon"
                                                name="icon_course"
                                                accept=".png,.jpg,.jpeg,.gif,.webp,.avif"
                                                class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700" />
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                                Ícone usado nos cards da home. Recomendado: 64x64px (PNG/WebP).
                                            </p>
                                            <div id="icon-preview" class="mt-3 inline-flex items-center gap-3 rounded-xl border border-slate-200 dark:border-slate-600 px-3 py-2 bg-slate-50 dark:bg-slate-700/40 <?= !empty($draft->icon_course) ? '' : 'hidden' ?>">
                                                <img id="icon-preview-img"
                                                    src="<?= !empty($draft->icon_course) ? base_url('assets/img/' . $draft->icon_course) : '' ?>"
                                                    alt="Ícone do curso"
                                                    class="w-10 h-10 object-contain rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600">
                                                <span id="icon-preview-label" class="text-xs text-slate-600 dark:text-slate-300">
                                                    <?= !empty($draft->icon_course) ? 'Ícone atual do curso' : 'Pré-visualização do ícone' ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Quick Tips -->
                                        <div class="bg-slate-50 dark:bg-slate-700 rounded-2xl p-4">
                                            <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-3 flex items-center gap-2">
                                                <i class="bi bi-lightbulb text-amber-500"></i>
                                                Dicas Rápidas
                                            </h4>
                                            <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-2">
                                                <li class="flex items-start gap-2">
                                                    <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                                                    Título claro e objetivo
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                                                    Subtítulo atrativo
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                                                    Imagem de alta qualidade
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                                                    Descrição detalhada
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Content Structure -->
                        <div class="tab-content hidden" id="content-structure">
                            <div class="space-y-6">
                                <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                                    <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-diagram-3 text-blue-600"></i>
                                        Estrutura do Conteúdo
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
                                        Organize seu curso em módulos e aulas
                                    </p>
                                </div>

                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white">
                                        Módulos e Aulas
                                    </h4>
                                    <button type="button"
                                        id="add-module"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-all duration-300">
                                        <i class="bi bi-plus-circle"></i>
                                        Adicionar Módulo
                                    </button>
                                </div>

                                <div id="modules-container" class="space-y-4">
                                    <?php if (!empty($draftModules)): ?>
                                        <?php foreach ($draftModules as $mIndex => $module): ?>
                                            <div class="module-card mb-4 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-white dark:bg-slate-900">
                                                <div class="flex justify-between items-center mb-3 gap-2">
                                                    <input type="text"
                                                        name="modules[<?= $mIndex ?>][title]"
                                                        class="flex-1 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                                                        placeholder="Nome do módulo"
                                                        value="<?= esc($module->title_module) ?>">
                                                    <button type="button"
                                                        class="remove-module text-red-500 hover:text-red-600 text-lg"
                                                        title="Remover módulo">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                                <textarea name="modules[<?= $mIndex ?>][description]"
                                                    class="w-full px-3 py-2 mb-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                                                    placeholder="Descrição do módulo (opcional)"><?= esc($module->description_module ?? '') ?></textarea>

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
                                                            class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                            placeholder="Ex: 80">
                                                    </div>
                                                </div>

                                                <div class="lessons-container space-y-2 mb-3">
                                                    <?php foreach ($module->lessons as $lIndex => $lesson): ?>
                                                        <div class="lesson-item border border-slate-200 dark:border-slate-700 rounded-xl p-3 bg-slate-50 dark:bg-slate-800" draggable="true">
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
                                                            <div class="flex justify-between items-center mb-2 gap-2">
                                                                <span class="drag-handle text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-grab select-none px-1" title="Arraste para ordenar" draggable="true">
                                                                    <i class="bi bi-grip-vertical"></i>
                                                                </span>
                                                                <input type="text"
                                                                    name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][title]"
                                                                    class="flex-1 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                                    placeholder="Título da aula"
                                                                    value="<?= esc($lesson->title_lesson) ?>">
                                                                <button type="button"
                                                                    class="remove-lesson text-red-500 hover:text-red-600 text-base"
                                                                    title="Remover aula">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                            </div>
                                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                                                                <select name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][type]"
                                                                    class="lesson-type px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100">
                                                                    <option value="video" <?= $lesson->type_lesson == 'video' ? 'selected' : '' ?>>Vídeo</option>
                                                                    <option value="text" <?= $lesson->type_lesson == 'text' ? 'selected' : '' ?>>Texto</option>
                                                                    <option value="quiz" <?= $lesson->type_lesson == 'quiz' ? 'selected' : '' ?>>Quiz</option>
                                                                    <option value="exercise" <?= $lesson->type_lesson == 'exercise' ? 'selected' : '' ?>>Exercício</option>
                                                                </select>
                                                                <input type="number"
                                                                    name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][duration]"
                                                                    class="px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                                    placeholder="Duração (min)"
                                                                    value="<?= esc($lesson->duration_lesson) ?>">
                                                                <div class="video-fields">
                                                                    <input type="url"
                                                                        name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][video_url]"
                                                                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                                        placeholder="Link do vídeo (opcional)"
                                                                        value="<?= esc($lesson->video_url_lesson) ?>">
                                                                </div>
                                                            </div>
                                                            <div class="mb-2 rounded-xl border border-emerald-300 bg-emerald-100 px-3 py-2 shadow-sm dark:border-emerald-500/70 dark:bg-emerald-950/85">
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
                                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                                                                <div>
                                                                    <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                                                        Arquivo da aula (opcional)
                                                                    </label>
                                                                    <input type="file"
                                                                        name="lesson_files[<?= $mIndex ?>][<?= $lIndex ?>]"
                                                                        accept=".zip,.rar,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                                                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100" />
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
                                                            <div class="quiz-fields <?= $lesson->type_lesson === 'quiz' ? '' : 'hidden' ?> bg-white/60 dark:bg-slate-900/60 border border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-3">
                                                                <div class="flex items-center justify-between mb-2">
                                                                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                                                        Perguntas do quiz
                                                                    </span>
                                                                    <button type="button"
                                                                        class="btn-add-quiz-question inline-flex items-center gap-1 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-medium rounded-lg">
                                                                        <i class="bi bi-plus"></i>
                                                                        Adicionar pergunta
                                                                    </button>
                                                                </div>
                                                                <div class="quiz-questions space-y-2">
                                                                    <?php foreach ($quizQuestions as $qIndex => $question): ?>
                                                                        <div class="quiz-question grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                                                                            <input type="text"
                                                                                name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][question]"
                                                                                class="px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                                                placeholder="Pergunta"
                                                                                value="<?= esc($question['question'] ?? '') ?>">
                                                                            <div class="flex flex-col gap-2">
                                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                                                    <?php for ($opt = 0; $opt < 4; $opt++): ?>
                                                                                        <input type="text"
                                                                                            name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][options][<?= $opt ?>]"
                                                                                            class="px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                                                            placeholder="Alternativa <?= $opt + 1 ?>"
                                                                                            value="<?= esc($question['options'][$opt] ?? '') ?>">
                                                                                    <?php endfor; ?>
                                                                                </div>
                                                                                <div class="flex gap-2 items-center">
                                                                                    <select name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][correct]"
                                                                                        class="flex-1 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100">
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
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <button type="button"
                                                    class="btn-add-lesson inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl"
                                                    data-module="<?= $mIndex ?>">
                                                    <i class="bi bi-plus-circle"></i>
                                                    Adicionar Aula
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    Você poderá reorganizar e editar os módulos e aulas depois.
                                </p>
                            </div>
                        </div>

                        <!-- Step 3: Advanced Settings -->
                        <div class="tab-content hidden" id="advanced-settings">
                            <div class="space-y-6">
                                <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                                    <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-gear text-blue-600"></i>
                                        Configurações Avançadas
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
                                        Configure as opções avançadas do seu curso
                                    </p>
                                </div>

                                <!-- Tipo de curso -->
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-4">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-currency-dollar text-blue-600"></i>
                                        Tipo de curso
                                    </h4>
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="courseType" value="free" class="text-blue-600" <?= !empty($draft->price_course) && $draft->price_course > 0 ? '' : 'checked' ?>>
                                            <span class="text-sm text-slate-700 dark:text-slate-200">Gratuito</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="courseType" value="paid" class="text-blue-600" <?= !empty($draft->price_course) && $draft->price_course > 0 ? 'checked' : '' ?>>
                                            <span class="text-sm text-slate-700 dark:text-slate-200">Pago</span>
                                        </label>
                                    </div>

                                    <div id="price-settings" class="mt-3 space-y-3 <?= !empty($draft->price_course) && $draft->price_course > 0 ? '' : 'hidden' ?>">
                                        <div class="rounded-2xl border border-blue-200 dark:border-blue-900/50 bg-blue-50/70 dark:bg-blue-950/20 p-4 space-y-3">
                                            <div>
                                                <h5 class="text-sm font-bold text-slate-800 dark:text-white">Preço e promoção</h5>
                                                <p class="text-xs text-slate-500 mt-0.5">O timer de promoção aparece na home e nas páginas do curso quando houver data de fim.</p>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label for="price_course" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                                        Preço normal (MZN)
                                                    </label>
                                                    <input type="number" step="0.01" min="0" id="price_course" name="price_course"
                                                        value="<?= esc($draft->price_course ?? '') ?>"
                                                        class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl text-sm"
                                                        placeholder="Ex: 4990">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">Preço promocional</label>
                                                    <input type="number" step="0.01" min="0" name="promo_price_course"
                                                        value="<?= esc($draft->promo_price_course ?? '') ?>"
                                                        class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl text-sm"
                                                        placeholder="Opcional">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">Promoção válida até</label>
                                                    <input type="datetime-local" name="promo_ends_at_course"
                                                        value="<?= !empty($draft->promo_ends_at_course) ? esc(date('Y-m-d\TH:i', strtotime((string) $draft->promo_ends_at_course))) : '' ?>"
                                                        class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">Aulas grátis antes do pagamento</label>
                                                    <input type="number" min="0" step="1" name="free_lessons_count_course"
                                                        value="<?= esc((int) ($draft->free_lessons_count_course ?? 0)) ?>"
                                                        class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl text-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 space-y-2">
                                        <label class="block text-sm font-semibold text-slate-800 dark:text-white">Carga horária</label>
                                        <div class="flex flex-wrap gap-4">
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input type="radio" name="hours_mode_course" value="auto" <?= (($draft->hours_mode_course ?? 'auto') !== 'manual') ? 'checked' : '' ?>>
                                                Automática
                                            </label>
                                            <label class="inline-flex items-center gap-2 text-sm">
                                                <input type="radio" name="hours_mode_course" value="manual" <?= (($draft->hours_mode_course ?? '') === 'manual') ? 'checked' : '' ?>>
                                                Manual
                                            </label>
                                        </div>
                                        <?php
                                        $draftHoursMode = (string) ($draft->hours_mode_course ?? 'auto');
                                        $draftHoursManual = $draft->hours_manual_course ?? null;
                                        $draftHoursCourse = $draft->hours_course ?? null;
                                        $draftIsManual = $draftHoursMode === 'manual' || (int) $draftHoursManual === 1;
                                        if ($draftHoursCourse !== null && $draftHoursCourse !== '') {
                                            $draftHoursInput = $draftHoursCourse;
                                        } elseif ($draftIsManual && $draftHoursManual !== null && (float) $draftHoursManual > 1) {
                                            $draftHoursInput = $draftHoursManual;
                                        } elseif ($draftHoursMode === 'manual' && $draftHoursManual !== null && $draftHoursManual !== '' && (int) $draftHoursManual !== 1) {
                                            $draftHoursInput = $draftHoursManual;
                                        } else {
                                            $draftHoursInput = '';
                                        }
                                        ?>
                                        <input type="number" min="0" step="0.5" name="hours_manual_course"
                                            value="<?= esc($draftHoursInput) ?>"
                                            placeholder="Horas manuais"
                                            class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm">
                                        <input type="text" name="whatsapp_contact_course"
                                            value="<?= esc($draft->whatsapp_contact_course ?? $draft->whatsapp_course ?? '258842627671') ?>"
                                            placeholder="WhatsApp comercial"
                                            class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl text-sm">
                                    </div>
                                </div>

                                <!-- Cor primária -->
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-palette text-blue-600"></i>
                                        Cor primária do curso
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-center">
                                        <input type="text"
                                            id="courseColorText"
                                            name="color_course"
                                            value="<?= esc($draft->color_course ?? '#3b82f6') ?>"
                                            class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                            placeholder="#3b82f6">
                                        <input type="color"
                                            id="courseColorPicker"
                                            value="<?= esc($draft->color_course ?? '#3b82f6') ?>"
                                            class="h-12 w-16 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700">
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Cole um código HEX ou selecione no color picker.
                                    </p>
                                </div>

                                <!-- Projetos do curso -->
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                            <i class="bi bi-kanban text-blue-600"></i>
                                            Projetos relacionados
                                        </h4>
                                        <button type="button"
                                            id="add-project"
                                            class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl transition-all duration-300">
                                            <i class="bi bi-plus-circle"></i>
                                            Adicionar projeto
                                        </button>
                                    </div>
                                    <div id="projects-container" class="space-y-4">
                                        <?php if (!empty($draftProjects ?? [])): ?>
                                            <?php foreach (($draftProjects ?? []) as $pIndex => $project): ?>
                                                <div class="project-card border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-white dark:bg-slate-800" data-index="<?= $pIndex ?>">
                                                    <div class="flex items-center justify-between gap-2 mb-2">
                                                        <input type="text"
                                                            name="projects[<?= $pIndex ?>][title]"
                                                            value="<?= esc($project->title_project ?? '') ?>"
                                                            class="flex-1 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                                                            placeholder="Título do projeto">
                                                        <button type="button"
                                                            class="remove-project text-red-500 hover:text-red-600 text-lg"
                                                            title="Remover projeto">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </div>
                                                    <textarea name="projects[<?= $pIndex ?>][description]"
                                                        class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                                                        placeholder="Descrição do projeto"><?= esc($project->description_project ?? '') ?></textarea>
                                                    <div class="mt-3">
                                                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                                            Imagem do projeto (opcional)
                                                        </label>
                                                        <input type="file"
                                                            name="project_images[<?= $pIndex ?>]"
                                                            accept="image/*"
                                                            class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100">
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
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Adicione projetos práticos relacionados ao curso.
                                    </p>
                                </div>

                                <!-- Status do curso -->
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-toggle-on text-blue-600"></i>
                                        Status do curso
                                    </h4>
                                    <select name="status_course"
                                        id="status_course"
                                        class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                        <option value="Rascunho" <?= ($draft->status_course ?? '') === 'Rascunho' ? 'selected' : '' ?>>Rascunho</option>
                                        <option value="Ativo" <?= ($draft->status_course ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                                        <option value="Arquivado" <?= ($draft->status_course ?? '') === 'Arquivado' ? 'selected' : '' ?>>Arquivado</option>
                                    </select>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Você pode manter o curso como rascunho e publicar depois.
                                    </p>
                                </div>

                                <!-- Tags -->
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-4 sm:p-6 space-y-3">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-tags text-blue-600"></i>
                                        Tags do curso
                                    </h4>
                                    <input type="text"
                                        id="courseTags"
                                        class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                        placeholder="Digite uma tag e pressione Enter (ex: JavaScript, Front-end)">
                                    <div id="tags-display" class="flex flex-wrap gap-2 mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review and Publish -->
                        <div class="tab-content hidden" id="review-publish">
                            <div class="space-y-6">
                                <div class="border-b border-slate-200 dark:border-slate-700 pb-4">
                                    <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                        <i class="bi bi-rocket text-blue-600"></i>
                                        Revisão e Publicação
                                    </h3>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">
                                        Revise e publique seu curso
                                    </p>
                                </div>

                                <!-- Resumo do curso -->
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

                                <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6 text-center">
                                    <i class="bi bi-rocket text-slate-400 text-4xl mb-4"></i>
                                    <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2">Revisão e Publicação</h4>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm">
                                        Revise todas as informações e publique seu curso
                                    </p>
                                    <button type="submit" name="publish" value="1" class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-300">
                                        <i class="bi bi-rocket"></i>
                                        Publicar Curso
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex flex-col sm:flex-row justify-between gap-4 p-4 sm:p-6 lg:p-8 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
                        <button type="button"
                            class="order-2 sm:order-1 w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            id="prev-step"
                            disabled>
                            <i class="bi bi-arrow-left"></i>
                            Anterior
                        </button>

                        <div class="order-1 sm:order-2 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button"
                                id="save-draft"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-500 hover:bg-slate-600 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5">
                                <i class="bi bi-file-earmark"></i>
                                Salvar Rascunho
                            </button>
                            <button type="button"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5"
                                id="next-step">
                                Próximo
                                <i class="bi bi-arrow-right"></i>
                            </button>
                            <button type="button"
                                id="open-student-preview"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                                <i class="bi bi-display"></i>
                                Abrir tela do aluno
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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
                onPaste: function() {
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
        // Variáveis globais
        // ======================
        let currentStep = 1;
        const totalSteps = 4;
        let tags = [];

        const form = document.getElementById("courseForm");
        const draftIdInput = document.getElementById("draft-id");
        const saveDraftButton = document.getElementById("save-draft");
        const openStudentPreviewButton = document.getElementById("open-student-preview");
        const draftCreateUrl = "<?= base_url('instructor/dashboard/novo_curso/rascunho') ?>";
        const draftSaveBaseUrl = "<?= base_url('instructor/dashboard/novo_curso/rascunho') ?>";
        const studentPreviewBaseUrl = "<?= base_url('instructor/dashboard/cursos/preview') ?>";

        // Navegação / stepper / tabs
        const prevButton = document.getElementById("prev-step");
        const nextButton = document.getElementById("next-step");
        const currentStepText = document.getElementById("current-step-text");
        const progressBar = document.getElementById("progress-bar");
        const tabButtons = document.querySelectorAll(".tab-button");
        const tabContents = document.querySelectorAll(".tab-content");
        const stepperItems = document.querySelectorAll("#stepper .step-item");

        // Upload de imagem
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

        // Módulos
        const modulesContainer = document.getElementById("modules-container");
        let moduleIndex = modulesContainer ? modulesContainer.querySelectorAll(".module-card").length : 0;

        // Projetos
        const projectsContainer = document.getElementById("projects-container");
        const addProjectButton = document.getElementById("add-project");
        let projectIndex = projectsContainer ? projectsContainer.querySelectorAll(".project-card").length : 0;

        // Preço
        const priceSettings = document.getElementById("price-settings");
        const courseTypeRadios = document.querySelectorAll('input[name="courseType"]');

        // Cor primÃ¡ria
        const colorTextInput = document.getElementById("courseColorText");
        const colorPickerInput = document.getElementById("courseColorPicker");

        // Hidden inputs
        const modulesHidden = document.getElementById("modules-json");
        const tagsHidden = document.getElementById("tags-json");
        const projectsHidden = document.getElementById("projects-json");

        // ======================
        // Funções auxiliares
        // ======================
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

        const tabsByStep = ["basic-info", "content-structure", "advanced-settings", "review-publish"];

        function getTabForStep(step) {
            return tabsByStep[step - 1];
        }

        function getStepForTab(tabId) {
            return tabsByStep.indexOf(tabId) + 1;
        }

        function updateStepperUI() {
            // texto "Passo X de 4"
            if (currentStepText) {
                currentStepText.textContent = currentStep;
            }

            // barra de progresso
            const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
            if (progressBar) {
                progressBar.style.width = `${progressPercentage}%`;
            }

            // stepper <ol>
            stepperItems.forEach((item) => {
                const stepNumber = parseInt(item.dataset.step);
                item.classList.remove("text-fg-brand");
                item.classList.remove("opacity-50");

                if (stepNumber < currentStep) {
                    // completado
                    item.classList.add("text-fg-brand");
                } else if (stepNumber === currentStep) {
                    // atual
                    item.classList.add("text-fg-brand");
                } else {
                    // futuro
                    item.classList.add("opacity-50");
                }
            });

            // tabs (botões de navegação)
            tabButtons.forEach((button) => {
                const isActive = button.dataset.tab === getTabForStep(currentStep);
                if (isActive) {
                    button.classList.add("border-blue-600", "text-blue-600", "dark:text-blue-400");
                    button.classList.remove("border-transparent", "text-slate-500", "dark:text-slate-400");
                } else {
                    button.classList.remove("border-blue-600", "text-blue-600", "dark:text-blue-400");
                    button.classList.add("border-transparent", "text-slate-500", "dark:text-slate-400");
                }
            });

            // conteúdo das tabs
            tabContents.forEach((content) => {
                if (content.id === getTabForStep(currentStep)) {
                    content.classList.remove("hidden");
                    content.classList.add("active");
                } else {
                    content.classList.add("hidden");
                    content.classList.remove("active");
                }
            });

            // botões
            if (prevButton) prevButton.disabled = currentStep === 1;
            if (nextButton) {
                nextButton.innerHTML =
                    currentStep === totalSteps ?
                    '<span class="flex items-center gap-2">Publicar <i class="bi bi-rocket"></i></span>' :
                    '<span class="flex items-center gap-2">Próximo <i class="bi bi-arrow-right"></i></span>';
            }
        }

        function hasExistingImage() {
            const imageSrc = previewImg?.getAttribute("src")?.trim();
            return previewContainer &&
                !previewContainer.classList.contains("hidden") &&
                previewImg &&
                imageSrc;
        }

        function validateCurrentStep() {
            switch (currentStep) {
                case 1: {
                    const title = document.getElementById("title_course")?.value.trim();
                    const subtitle = document.getElementById("courseSubtitle")?.value.trim();
                    const description = document.getElementById("courseDescription")?.value.trim();
                    const image = fileInput?.files[0];

                    if (!title) {
                        alert("Por favor, preencha o título do curso.");
                        return false;
                    }
                    if (!subtitle) {
                        alert("Por favor, preencha o subtítulo do curso.");
                        return false;
                    }
                    if (!description) {
                        alert("Por favor, preencha a descrição do curso.");
                        return false;
                    }
                    if (!image && !hasExistingImage()) {
                        alert("Por favor, selecione uma imagem de capa para o curso.");
                        return false;
                    }
                    return true;
                }
                case 2:
                    // pode adicionar validações de módulos aqui se quiser (ex: ao menos 1 módulo)
                    return true;
                case 3:
                    // validações de preço/status, se necessário
                    return true;
                case 4:
                    return true;
                default:
                    return true;
            }
        }

        function serializeModulesAndTags() {
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
            if (modulesHidden) modulesHidden.value = modulesJson;
            const modulesHiddenAlt = document.getElementById("modules-json-alt");
            if (modulesHiddenAlt) modulesHiddenAlt.value = modulesJson;
            if (tagsHidden) tagsHidden.value = JSON.stringify(tags);

            if (projectsHidden) {
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

        async function saveDraft({
            validate = false,
            silent = false
        } = {}) {
            if (!form) return false;
            if (validate && !validateCurrentStep()) return false;

            syncRichTextEditors();
            serializeModulesAndTags();

            const currentDraftId = draftIdInput?.value?.trim();
            const endpoint = currentDraftId ? `${draftSaveBaseUrl}/${currentDraftId}` : draftCreateUrl;
            const compactedFields = stripRedundantDynamicFieldNames();
            const formData = new FormData(form);
            restoreDynamicFieldNames(compactedFields);

            try {
                const response = await fetch(endpoint, {
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
                    showSaveToast(payload.message || "Falha ao salvar rascunho.", "error");
                    return false;
                }

                if (!currentDraftId && payload.id_course && draftIdInput) {
                    draftIdInput.value = payload.id_course;
                }

                showSaveToast("Configuracao salva.");

                return true;
            } catch (error) {
                showSaveToast("Falha ao salvar rascunho. Tente novamente.", "error");
                return false;
            }
        }

        function buildStudentPreviewUrl(courseId) {
            const returnUrl = new URL(window.location.href);
            returnUrl.searchParams.set("load_draft", "1");

            const params = new URLSearchParams({
                return_url: returnUrl.toString()
            });

            return `${studentPreviewBaseUrl}/${courseId}?${params.toString()}`;
        }

        async function openStudentPreview() {
            const saved = await saveDraft({
                silent: true
            });
            if (!saved) return;

            const currentDraftId = draftIdInput?.value?.trim();
            if (!currentDraftId) {
                showSaveToast("Salve o rascunho antes de abrir a pre-visualizacao.", "error");
                return;
            }

            const previewUrl = buildStudentPreviewUrl(currentDraftId);
            window.open(previewUrl, "_blank", "noopener");
        }

        // ======================
        // Navegação (próximo / anterior / clique em tabs / clique no stepper)
        // ======================
        if (nextButton) {
            nextButton.addEventListener("click", async () => {
                if (!validateCurrentStep()) return;

                if (currentStep < totalSteps) {
                    const saved = await saveDraft({
                        silent: true
                    });
                    if (!saved) return;
                    currentStep++;
                    updateStepperUI();
                } else {
                    // último passo -> serializa módulos/tags e envia o form
                    if (typeof form.requestSubmit === "function") {
                        form.requestSubmit();
                    } else {
                        serializeModulesAndTags();
                        stripRedundantDynamicFieldNames();
                        form.submit();
                    }
                }
            });
        }

        if (form) {
            form.addEventListener("submit", () => {
                syncRichTextEditors();
                serializeModulesAndTags();
                stripRedundantDynamicFieldNames();
            });
        }

        if (prevButton) {
            prevButton.addEventListener("click", () => {
                if (currentStep > 1) {
                    currentStep--;
                    updateStepperUI();
                }
            });
        }

        tabButtons.forEach((button) => {
            button.addEventListener("click", () => {
                const targetStep = getStepForTab(button.dataset.tab);
                // só deixa voltar para passos já visitados
                if (targetStep <= currentStep) {
                    currentStep = targetStep;
                    updateStepperUI();
                }
            });
        });

        stepperItems.forEach((item) => {
            item.addEventListener("click", () => {
                const targetStep = parseInt(item.dataset.step);
                if (targetStep <= currentStep) {
                    currentStep = targetStep;
                    updateStepperUI();
                }
            });
        });

        // ======================
        // Upload de imagem
        // ======================
        if (fileInput) {
            fileInput.addEventListener("change", (event) => {
                const file = event.target.files[0];

                if (file && file.type.startsWith("image/")) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert("O arquivo é muito grande. Por favor selecione uma imagem de até 2MB.");
                        fileInput.value = "";
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        previewContainer.classList.remove("hidden");
                        uploadArea.classList.add("hidden");
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("Por favor selecione um arquivo de imagem válido (JPG, PNG, etc.).");
                    fileInput.value = "";
                }
            });
        }

        if (removeBtn) {
            const previewCoverImg = document.getElementById("preview-cover-img");
            const previewCoverFallback = document.getElementById("preview-cover-fallback");
            if (previewCoverImg) {
                previewCoverImg.removeAttribute("src");
                previewCoverImg.classList.add("hidden");
            }
            if (previewCoverFallback) {
                previewCoverFallback.classList.remove("hidden");
            }
            updateCoursePreview();
            removeBtn.addEventListener("click", () => {
                fileInput.value = "";
                previewImg.removeAttribute("src");
                previewContainer.classList.add("hidden");
                uploadArea.classList.remove("hidden");
                scheduleAutoSave();
            });
        }

        if (uploadArea) {
            uploadArea.addEventListener("dragover", (e) => {
                e.preventDefault();
                uploadArea.classList.add("border-blue-500", "bg-blue-100", "dark:bg-blue-900/40");
            });

            uploadArea.addEventListener("dragleave", () => {
                uploadArea.classList.remove("border-blue-500", "bg-blue-100", "dark:bg-blue-900/40");
            });

            uploadArea.addEventListener("drop", (e) => {
                e.preventDefault();
                uploadArea.classList.remove("border-blue-500", "bg-blue-100", "dark:bg-blue-900/40");

                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith("image/")) {
                    if (file.size > 2 * 1024 * 1024) {
                        alert("O arquivo é muito grande. Por favor selecione uma imagem de até 2MB.");
                        return;
                    }

                    const dt = new DataTransfer();
                    dt.items.add(file);
                    fileInput.files = dt.files;

                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        previewImg.src = ev.target.result;
                        previewContainer.classList.remove("hidden");
                        uploadArea.classList.add("hidden");
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert("Por favor arraste apenas arquivos de imagem válidos (JPG, PNG, etc.).");
                }
            });
        }

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
        // Módulos e Aulas (dinâmico)
        // ======================
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
            const moduleId = lessonEl.closest(".module-card")?.querySelector('input[name$="[title]"]')?.name?.match(/modules\[(\d+)\]/)?.[1] || 0;
            const lessonCount = lessonEl.querySelector('input[name$="[title]"]')?.name?.match(/lessons\[(\d+)\]/)?.[1] || index;

            const questionHtml = `
                <div class="quiz-question grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                    <input type="text"
                           name="modules[${moduleId}][lessons][${lessonCount}][quiz][${index}][question]"
                           class="px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                           placeholder="Pergunta">
                    <div class="flex flex-col gap-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            ${[0,1,2,3].map(opt => `
                                <input type="text"
                                       name="modules[${moduleId}][lessons][${lessonCount}][quiz][${index}][options][${opt}]"
                                       class="px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                       placeholder="Alternativa ${opt + 1}">
                            `).join("")}
                        </div>
                        <div class="flex gap-2 items-center">
                            <select name="modules[${moduleId}][lessons][${lessonCount}][quiz][${index}][correct]"
                                    class="flex-1 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100">
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

        if (modulesContainer) {
            const addModuleBtn = document.getElementById("add-module");

            addModuleBtn?.addEventListener("click", () => {
                const moduleHtml = `
                <div class="module-card mb-4 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-white dark:bg-slate-900">
                    <div class="flex justify-between items-center mb-3 gap-2">
                        <input type="text"
                               name="modules[${moduleIndex}][title]"
                               class="flex-1 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                               placeholder="Nome do módulo">
                        <button type="button"
                                class="remove-module text-red-500 hover:text-red-600 text-lg"
                                title="Remover módulo">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <textarea name="modules[${moduleIndex}][description]"
                              class="w-full px-3 py-2 mb-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                              placeholder="Descrição do módulo (opcional)"></textarea>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                Nota mínima do quiz (%)
                            </label>
                            <input type="number"
                                   name="modules[${moduleIndex}][min_score]"
                                   min="0"
                                   max="100"
                                   value="80"
                                   class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                   placeholder="Ex: 80">
                        </div>
                    </div>

                    <div class="lessons-container space-y-2 mb-3"></div>

                    <button type="button"
                            class="btn-add-lesson inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-xl"
                            data-module="${moduleIndex}">
                        <i class="bi bi-plus-circle"></i>
                        Adicionar Aula
                    </button>
                </div>
            `;
                modulesContainer.insertAdjacentHTML("beforeend", moduleHtml);
                moduleIndex++;
                scheduleAutoSave();
            });

            modulesContainer.addEventListener("click", (e) => {
                const target = e.target;

                // remover módulo
                if (target.closest(".remove-module")) {
                    target.closest(".module-card").remove();
                    scheduleAutoSave();
                    return;
                }

                // adicionar aula
                if (target.closest(".btn-add-lesson")) {
                    const btn = target.closest(".btn-add-lesson");
                    const moduleId = btn.dataset.module;
                    const lessonsContainer = btn.previousElementSibling;
                    const lessonCount = lessonsContainer.querySelectorAll(".lesson-item").length;

                    const lessonHtml = `
                    <div class="lesson-item border border-slate-200 dark:border-slate-700 rounded-xl p-3 bg-slate-50 dark:bg-slate-800" draggable="true">
                        <div class="flex justify-between items-center mb-2 gap-2">
                            <span class="drag-handle text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-grab select-none px-1" title="Arraste para ordenar" draggable="true">
                                <i class="bi bi-grip-vertical"></i>
                            </span>
                            <input type="text"
                                   name="modules[${moduleId}][lessons][${lessonCount}][title]"
                                   class="flex-1 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                   placeholder="Título da aula">
                            <button type="button"
                                    class="remove-lesson text-red-500 hover:text-red-600 text-base"
                                    title="Remover aula">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                            <select name="modules[${moduleId}][lessons][${lessonCount}][type]"
                                    class="lesson-type px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100">
                                <option value="video">Vídeo</option>
                                <option value="text">Texto</option>
                                <option value="quiz">Quiz</option>
                                <option value="exercise">Exercício</option>
                            </select>
                            <input type="number"
                                   name="modules[${moduleId}][lessons][${lessonCount}][duration]"
                                   class="px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                   placeholder="Duração (min)">
                            <div class="video-fields">
                                <input type="url"
                                       name="modules[${moduleId}][lessons][${lessonCount}][video_url]"
                                       class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                       placeholder="Link do vídeo (opcional)">
                            </div>
                        </div>
                        <div class="mb-2 rounded-xl border border-emerald-300 bg-emerald-100 px-3 py-2 shadow-sm dark:border-emerald-500/70 dark:bg-emerald-950/85">
                            <input type="hidden"
                                   name="modules[${moduleId}][lessons][${lessonCount}][is_preview]"
                                   value="0">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox"
                                       name="modules[${moduleId}][lessons][${lessonCount}][is_preview]"
                                       value="1"
                                       class="lesson-preview-toggle mt-0.5 h-4 w-4 rounded border-emerald-400 bg-white text-emerald-600 focus:ring-emerald-500 dark:border-emerald-400 dark:bg-emerald-950">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                    Arquivo da aula (opcional)
                                </label>
                                <input type="file"
                                       name="lesson_files[${moduleId}][${lessonCount}]"
                                       accept=".zip,.rar,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx"
                                       class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100" />
                                <input type="hidden"
                                       name="modules[${moduleId}][lessons][${lessonCount}][file_existing]"
                                       value="">
                                <input type="hidden"
                                       name="modules[${moduleId}][lessons][${lessonCount}][file_existing_name]"
                                       value="">
                            </div>
                        </div>
                        <div class="quiz-fields hidden bg-white/60 dark:bg-slate-900/60 border border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    Perguntas do quiz
                                </span>
                                <button type="button"
                                        class="btn-add-quiz-question inline-flex items-center gap-1 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-medium rounded-lg">
                                    <i class="bi bi-plus"></i>
                                    Adicionar pergunta
                                </button>
                            </div>
                            <div class="quiz-questions space-y-2"></div>
                        </div>
                    </div>
                `;

                    lessonsContainer.insertAdjacentHTML("beforeend", lessonHtml);
                    scheduleAutoSave();
                    return;
                }

                // adicionar pergunta de quiz
                if (target.closest(".btn-add-quiz-question")) {
                    const lessonEl = target.closest(".lesson-item");
                    addQuizQuestion(lessonEl);
                    scheduleAutoSave();
                    return;
                }

                // remover pergunta de quiz
                if (target.closest(".remove-quiz-question")) {
                    target.closest(".quiz-question")?.remove();
                    scheduleAutoSave();
                    return;
                }

                // remover aula
                if (target.closest(".remove-lesson")) {
                    target.closest(".lesson-item").remove();
                    scheduleAutoSave();
                }
            });

            modulesContainer.addEventListener("change", (e) => {
                if (e.target.classList.contains("lesson-type")) {
                    const lessonEl = e.target.closest(".lesson-item");
                    syncQuizFields(lessonEl);
                    syncVideoFields(lessonEl);
                    scheduleAutoSave();
                }
            });

            function updateCourseStats() {
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

            modulesContainer.addEventListener("input", updateCourseStats);
            modulesContainer.addEventListener("change", updateCourseStats);

            let draggedLesson = null;
            let dragPlaceholder = null;

            modulesContainer.addEventListener("dragstart", (e) => {
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

            modulesContainer.addEventListener("dragend", () => {
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

            modulesContainer.addEventListener("dragover", (e) => {
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

            modulesContainer.addEventListener("dragenter", (e) => {
                if (!draggedLesson) return;
                const targetContainer = e.target.closest(".lessons-container");
                if (targetContainer) {
                    targetContainer.classList.add("drag-over");
                }
            });

            modulesContainer.addEventListener("dragleave", (e) => {
                const targetContainer = e.target.closest(".lessons-container");
                if (!targetContainer) return;
                if (!targetContainer.contains(e.relatedTarget)) {
                    targetContainer.classList.remove("drag-over");
                }
            });

            modulesContainer.addEventListener("drop", (e) => {
                const targetContainer = e.target.closest(".lessons-container");
                if (targetContainer) {
                    targetContainer.classList.remove("drag-over");
                }
            });

            updateCourseStats();
        }

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
                <div class="project-card border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-white dark:bg-slate-800" data-index="${index}">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <input type="text"
                               name="projects[${index}][title]"
                               value="${title}"
                               class="flex-1 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                               placeholder="Título do projeto">
                        <button type="button"
                                class="remove-project text-red-500 hover:text-red-600 text-lg"
                                title="Remover projeto">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <textarea name="projects[${index}][description]"
                              class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100"
                              placeholder="Descrição do projeto">${description}</textarea>
                    <div class="mt-3">
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-200 mb-1">
                            Imagem do projeto (opcional)
                        </label>
                        <input type="file"
                               name="project_images[${index}]"
                               accept="image/*"
                               class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100">
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
        // Tags
        // ======================
        const tagsInput = document.getElementById("courseTags");
        const tagsDisplay = document.getElementById("tags-display");

        function renderTags() {
            if (!tagsDisplay) return;
            tagsDisplay.innerHTML = tags
                .map(
                    (tag) => `
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-600 text-white text-xs rounded-full">
                    <span>${tag}</span>
                    <button type="button"
                            class="remove-tag text-white/80 hover:text-white text-xs"
                            data-tag="${tag}">
                        ×
                    </button>
                </span>
            `
                )
                .join("");
        }

        if (tagsInput) {
            tagsInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    const tag = tagsInput.value.trim();
                    if (tag && !tags.includes(tag)) {
                        tags.push(tag);
                        renderTags();
                        tagsInput.value = "";
                        scheduleAutoSave();
                    }
                }
            });
        }

        if (tagsDisplay) {
            tagsDisplay.addEventListener("click", (e) => {
                const btn = e.target.closest(".remove-tag");
                if (!btn) return;
                const tagToRemove = btn.dataset.tag;
                tags = tags.filter((t) => t !== tagToRemove);
                renderTags();
                scheduleAutoSave();
            });
        }

        // ======================
        // Tipo de curso / preço
        // ======================
        courseTypeRadios.forEach((radio) => {
            radio.addEventListener("change", () => {
                if (radio.value === "paid") {
                    priceSettings.classList.remove("hidden");
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

        const scheduleAutoSave = () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(async () => {
                if (autoSaveInFlight) {
                    autoSaveQueued = true;
                    return;
                }

                autoSaveInFlight = true;
                await saveDraft({
                    silent: true
                });
                autoSaveInFlight = false;

                if (autoSaveQueued) {
                    autoSaveQueued = false;
                    scheduleAutoSave();
                }
            }, 900);
        };

        document.addEventListener("input", scheduleAutoSave);
        document.addEventListener("change", scheduleAutoSave);
        window.addEventListener("course-editor-input", scheduleAutoSave);

        if (saveDraftButton) {
            saveDraftButton.addEventListener("click", async () => {
                await saveDraft({
                    silent: false
                });
            });
        }

        if (openStudentPreviewButton) {
            openStudentPreviewButton.addEventListener("click", async () => {
                await openStudentPreview();
            });
        }

        // inicializar UI
        updateStepperUI();

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

<?= $this->endSection() ?>
