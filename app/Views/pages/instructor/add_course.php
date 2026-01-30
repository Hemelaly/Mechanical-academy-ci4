<?php
$user = service('auth')->user();
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
</style>

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-6">
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
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">

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
                                    placeholder="Descreva detalhadamente o conteúdo, objetivos e benefícios do seu curso..."><?= esc($draft->description_course ?? '') ?></textarea>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                            Recomendado: mínimo 200 caracteres
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
                                                        value="<?= esc($module->min_score_module ?? 75) ?>"
                                                        class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                                        placeholder="Ex: 75">
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

                                <div id="price-settings" class="mt-3 <?= !empty($draft->price_course) && $draft->price_course > 0 ? '' : 'hidden' ?>">
                                    <label for="price_course" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                        Preço do curso
                                    </label>
                                    <div class="flex gap-2 items-center">
                                        <span class="px-3 py-2 rounded-l-xl bg-slate-200 dark:bg-slate-700 text-sm text-slate-800 dark:text-slate-100">
                                            $
                                        </span>
                                        <input type="number"
                                            step="0.01"
                                            min="0"
                                            id="price_course"
                                            name="price_course"
                                            value="<?= esc($draft->price_course ?? '') ?>"
                                            class="w-full px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-r-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                            placeholder="Ex: 49.90">
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        O preço será aplicado apenas se o curso for marcado como pago.
                                    </p>
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
                                <button type="submit" class="mt-4 inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-all duration-300">
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

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
        const draftCreateUrl = "<?= base_url('instructor/dashboard/novo_curso/rascunho') ?>";
        const draftSaveBaseUrl = "<?= base_url('instructor/dashboard/novo_curso/rascunho') ?>";

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

        // ======================
        // Funções auxiliares
        // ======================
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
            return previewContainer &&
                !previewContainer.classList.contains("hidden") &&
                previewImg &&
                previewImg.src;
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
                    modCard.querySelector('input[name$="[min_score]"]')?.value || 75;
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
                                quiz_questions.push({ question, options, correct });
                            }
                        });
                    }

                    lessons.push({
                        title,
                        type,
                        duration,
                        video_url,
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
                    min_score: Number.isFinite(minScoreValue) ? minScoreValue : 75,
                    lessons,
                });
            });

            const modulesJson = JSON.stringify(modules);
            if (modulesHidden) modulesHidden.value = modulesJson;
            const modulesHiddenAlt = document.getElementById("modules-json-alt");
            if (modulesHiddenAlt) modulesHiddenAlt.value = modulesJson;
            if (tagsHidden) tagsHidden.value = JSON.stringify(tags);
        }

        async function saveDraft({ validate = false, silent = false } = {}) {
            if (!form) return false;
            if (validate && !validateCurrentStep()) return false;

            serializeModulesAndTags();

            const currentDraftId = draftIdInput?.value?.trim();
            const endpoint = currentDraftId ? `${draftSaveBaseUrl}/${currentDraftId}` : draftCreateUrl;
            const formData = new FormData(form);

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
                    if (!silent) {
                        alert(payload.message || "Falha ao salvar rascunho.");
                    }
                    return false;
                }

                if (!currentDraftId && payload.id_course && draftIdInput) {
                    draftIdInput.value = payload.id_course;
                }

                if (!silent) {
                    alert("Rascunho salvo com sucesso.");
                }

                return true;
            } catch (error) {
                if (!silent) {
                    alert("Falha ao salvar rascunho. Tente novamente.");
                }
                return false;
            }
        }

        // ======================
        // Navegação (próximo / anterior / clique em tabs / clique no stepper)
        // ======================
        if (nextButton) {
            nextButton.addEventListener("click", async () => {
                if (!validateCurrentStep()) return;

                if (currentStep < totalSteps) {
                    const saved = await saveDraft({ silent: true });
                    if (!saved) return;
                    currentStep++;
                    updateStepperUI();
                } else {
                    // último passo -> serializa módulos/tags e envia o form
                    serializeModulesAndTags();
                    form.submit();
                }
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
            removeBtn.addEventListener("click", () => {
                fileInput.value = "";
                previewImg.src = "";
                previewContainer.classList.add("hidden");
                uploadArea.classList.remove("hidden");
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
                                   value="75"
                                   class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-600 text-xs text-slate-800 dark:text-slate-100"
                                   placeholder="Ex: 75">
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
            });

            modulesContainer.addEventListener("click", (e) => {
                const target = e.target;

                // remover módulo
                if (target.closest(".remove-module")) {
                    target.closest(".module-card").remove();
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
                    return;
                }

                // adicionar pergunta de quiz
                if (target.closest(".btn-add-quiz-question")) {
                    const lessonEl = target.closest(".lesson-item");
                    addQuizQuestion(lessonEl);
                    return;
                }

                // remover pergunta de quiz
                if (target.closest(".remove-quiz-question")) {
                    target.closest(".quiz-question")?.remove();
                    return;
                }

                // remover aula
                if (target.closest(".remove-lesson")) {
                    target.closest(".lesson-item").remove();
                }
            });

            modulesContainer.addEventListener("change", (e) => {
                if (e.target.classList.contains("lesson-type")) {
                    const lessonEl = e.target.closest(".lesson-item");
                    syncQuizFields(lessonEl);
                    syncVideoFields(lessonEl);
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
            });

            projectsContainer.addEventListener("click", (e) => {
                if (e.target.closest(".remove-project")) {
                    e.target.closest(".project-card")?.remove();
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
        let autoSaveTimer;
        document.addEventListener("input", () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                saveDraft({ silent: true });
            }, 2000);
        });

        if (saveDraftButton) {
            saveDraftButton.addEventListener("click", async () => {
                await saveDraft({ silent: false });
            });
        }

        // inicializar UI
        updateStepperUI();
    });
</script>

<?= $this->endSection() ?>
