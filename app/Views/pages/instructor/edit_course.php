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
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8">
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
        <form id="courseForm" action="<?= base_url('instructor/dashboard/editar_curso/' . $course->id_course) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="id_instructor_course" value="<?= $user->id ?>">
            <input type="hidden" id="modules-json" name="modules">
            <input type="hidden" id="modules-json-alt" name="modules_json">
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
                                <?php if ($course->image_course): ?>
                                    <div id="image-preview" class="mt-3">
                                        <img id="preview-img"
                                            src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
                                            alt="Preview"
                                            class="w-full h-48 object-cover rounded-2xl shadow-lg">
                                        <button type="button" id="remove-image"
                                            class="w-full mt-3 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors text-sm">
                                            <i class="bi bi-trash mr-2"></i>
                                            Remover Imagem
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div id="image-preview" class="hidden"></div>
                                <?php endif; ?>
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
                            <p class="text-slate-600 dark:text-slate-400 text-sm">Organize seu curso em módulos e aulas</p>
                        </div>
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
                                            value="<?= esc($module->min_score_module ?? 75) ?>"
                                            class="w-full px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Ex: 75">
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
                                                            <input type="text"
                                                                name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][quiz][<?= $qIndex ?>][question]"
                                                                class="px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                                placeholder="Pergunta"
                                                                value="<?= esc($question['question'] ?? '') ?>">
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

                        <div id="price-settings" class="space-y-2 <?= $course->price_course > 0 ? '' : 'hidden' ?>">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Preço do Curso
                            </label>
                            <input type="number" id="coursePrice" name="price_course" min="0" step="0.01"
                                value="<?= esc($course->price_course) ?>"
                                class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
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
                        <button type="submit" id="publish-course"
                            class="w-full px-6 py-3.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                            <i class="bi bi-rocket mr-2"></i>
                            Publicar Curso
                        </button>

                        <button type="submit" name="draft" value="1"
                            class="w-full px-6 py-3.5 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-xl transition-colors">
                            <i class="bi bi-save mr-2"></i>
                            Salvar como Rascunho
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

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
    $(function () {
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
        themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ["class"] });
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
        const modulesContainer = document.getElementById("modules-container");
        const nextBtn = document.getElementById("next-step");
        const prevBtn = document.getElementById("prev-step");
        const tabBtns = document.querySelectorAll(".tab-btn");
        const tabContents = document.querySelectorAll(".tab-content");
        const priceSettings = document.getElementById("price-settings");
        const priceInput = document.getElementById("coursePrice");
        const courseTypeRadios = document.querySelectorAll('input[name="courseType"]');

        // Projetos
        const projectsContainer = document.getElementById("projects-container");
        const addProjectButton = document.getElementById("add-project");
        let projectIndex = projectsContainer ? projectsContainer.querySelectorAll(".project-card").length : 0;

        // Cor primÃ¡ria
        const colorTextInput = document.getElementById("courseColorText");
        const colorPickerInput = document.getElementById("courseColorPicker");

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
                    if (!previewContainer.querySelector('img')) {
                        previewContainer.innerHTML = `
                        <img id="preview-img" src="${e.target.result}" alt="Preview" class="w-full h-48 object-cover rounded-2xl shadow-lg">
                        <button type="button" id="remove-image" class="w-full mt-3 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors text-sm">
                            <i class="bi bi-trash mr-2"></i>
                            Remover Imagem
                        </button>
                    `;
                        // Re-attach remove button event
                        document.getElementById('remove-image').addEventListener('click', removeImage);
                    } else {
                        previewImg.src = e.target.result;
                    }
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
            fileInput.value = "";
            if (previewContainer) previewContainer.classList.add("hidden");
            if (uploadArea) uploadArea.classList.remove("hidden");
        }

        // Attach remove image event
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'remove-image') {
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
            const modulesHidden = document.getElementById("modules-json");
            if (modulesHidden) modulesHidden.value = modulesJson;
            const modulesHiddenAlt = document.getElementById("modules-json-alt");
            if (modulesHiddenAlt) modulesHiddenAlt.value = modulesJson;
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
                    <input type="text"
                           name="modules[${moduleId}][lessons][${lessonIndex}][quiz][${index}][question]"
                           class="px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                           placeholder="Pergunta">
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

        function addModule(moduleData = null) {
            const mIndex = moduleIndex++;
            const moduleId = moduleData?.id_module || "";
            const moduleTitle = moduleData?.title || "";
            const moduleDescription = moduleData?.description || "";
            const moduleMinScore = moduleData?.min_score ?? 75;
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
                               placeholder="Ex: 75">
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
            const fileExisting = lessonData.file_existing || lessonData.attachment_path || "";
            const fileExistingName = lessonData.file_existing_name || lessonData.attachment_name || "";
            const fileLabel = fileExisting
                ? `<p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Atual: ${fileExistingName || fileExisting}</p>`
                : "";

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
        document.getElementById("add-module")?.addEventListener("click", () => addModule());

        // Event Delegation for Modules Container
        modulesContainer?.addEventListener("click", (e) => {
            // Remove module
            if (e.target.classList.contains("remove-module")) {
                e.target.closest(".module-card").remove();
            }
            // Remove lesson
            if (e.target.classList.contains("remove-lesson")) {
                e.target.closest(".lesson-item").remove();
            }
            // Add quiz question
            if (e.target.closest(".btn-add-quiz-question")) {
                const lessonEl = e.target.closest(".lesson-item");
                addQuizQuestion(lessonEl);
            }
            // Remove quiz question
            if (e.target.closest(".remove-quiz-question")) {
                e.target.closest(".quiz-question")?.remove();
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
            }

        });

        modulesContainer?.addEventListener("change", (e) => {
            if (e.target.classList.contains("lesson-type")) {
                const lessonEl = e.target.closest(".lesson-item");
                syncQuizFields(lessonEl);
                syncVideoFields(lessonEl);
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
            });

            projectsContainer.addEventListener("click", (e) => {
                if (e.target.closest(".remove-project")) {
                    e.target.closest(".project-card")?.remove();
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
        // Form Validation (Basic)
        // ======================
        document.getElementById('courseForm')?.addEventListener('submit', function(e) {
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
            // You can add more validation here as needed
        });
    });
</script>

<?= $this->endSection() ?>





