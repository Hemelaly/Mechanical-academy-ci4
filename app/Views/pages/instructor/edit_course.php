<?php
$user = service('auth')->user();
?>

<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Editar Curso<?= $this->endSection() ?>

<?= $this->section('edit_course') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-gray-50 dark:bg-dark-bg py-8">
    <div class="container mx-auto px-4">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">
                        <i class="bi bi-pencil-square text-blue-500 mr-3"></i>
                        Editar Curso
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Atualize seu curso facilmente e gerencie todo o conteúdo
                    </p>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="bg-white dark:bg-dark-panel rounded-2xl shadow-lg border border-gray-200 dark:border-dark-line p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-semibold text-sm">1</div>
                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center font-semibold text-sm">2</div>
                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center font-semibold text-sm">3</div>
                        <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center font-semibold text-sm">4</div>
                    </div>
                    <span class="text-gray-500 dark:text-gray-400 text-sm font-medium" id="progress-text">
                        Passo 1 de 4
                    </span>
                </div>
                <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div id="progress-bar" class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-300" style="width: 25%"></div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button class="tab-btn active px-4 py-2.5 bg-blue-500 text-white font-medium rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl" data-tab="basic-info">
                <i class="bi bi-info-circle mr-2"></i>
                Informações Básicas
            </button>
            <button class="tab-btn px-4 py-2.5 bg-white dark:bg-dark-panel text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-dark-line font-medium rounded-xl transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-blue-300 dark:hover:border-blue-600" data-tab="content-structure">
                <i class="bi bi-diagram-3 mr-2"></i>
                Estrutura do Conteúdo
            </button>
            <button class="tab-btn px-4 py-2.5 bg-white dark:bg-dark-panel text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-dark-line font-medium rounded-xl transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-blue-300 dark:hover:border-blue-600" data-tab="advanced-settings">
                <i class="bi bi-gear mr-2"></i>
                Configurações Avançadas
            </button>
            <button class="tab-btn px-4 py-2.5 bg-white dark:bg-dark-panel text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-dark-line font-medium rounded-xl transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-blue-300 dark:hover:border-blue-600" data-tab="review-publish">
                <i class="bi bi-rocket mr-2"></i>
                Revisão e Publicação
            </button>
        </div>

        <!-- Form -->
        <form id="courseForm" action="<?= base_url('instructor/dashboard/editar_curso/' . $course->id_course) ?>" method="post" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="id_instructor_course" value="<?= $user->id ?>">

            <!-- Step 1: Basic Info -->
            <div id="basic-info" class="tab-content active bg-white dark:bg-dark-panel rounded-3xl shadow-xl border border-gray-200 dark:border-dark-line overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-dark-line bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-info-circle text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Informações Básicas do Curso</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Atualize os campos do seu curso</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column (Text fields) -->
                        <div class="lg:col-span-2 space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-type text-blue-500 mr-2"></i>
                                    Título do Curso *
                                </label>
                                <input type="text" id="title_course" name="title_course"
                                    placeholder="Ex: Desenvolvimento Web Completo"
                                    required value="<?= esc($course->title_course) ?>"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-text-left text-blue-500 mr-2"></i>
                                    Subtítulo do Curso *
                                </label>
                                <input type="text" id="courseSubtitle" name="subtitle_course"
                                    placeholder="Ex: Do zero ao avançado com HTML, CSS e JavaScript"
                                    required value="<?= esc($course->subtitle_course) ?>"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-card-text text-blue-500 mr-2"></i>
                                    Descrição do Curso *
                                </label>
                                <textarea rows="8" id="courseDescription" name="description_course"
                                    placeholder="Descreva detalhadamente seu curso..."
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm"><?= esc($course->description_course) ?></textarea>
                            </div>
                        </div>

                        <!-- Right Column (Image upload) -->
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-image text-blue-500 mr-2"></i>
                                    Imagem de Capa *
                                </label>

                                <!-- Upload Area -->
                                <div id="upload-area" class="border-2 border-dashed border-blue-400 rounded-2xl p-6 text-center bg-blue-50 dark:bg-blue-900/20 transition-all duration-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 hover:border-blue-500 cursor-pointer <?= $course->image_course ? 'hidden' : '' ?>">
                                    <i class="bi bi-cloud-arrow-up text-blue-500 text-3xl mb-3"></i>
                                    <h6 class="font-medium text-gray-700 dark:text-gray-300 text-sm mb-1">
                                        Arraste uma imagem ou clique para selecionar
                                    </h6>
                                    <p class="text-gray-500 dark:text-gray-400 text-xs mb-3">
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Content Structure -->
            <div id="content-structure" class="tab-content hidden bg-white dark:bg-dark-panel rounded-3xl shadow-xl border border-gray-200 dark:border-dark-line overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-dark-line bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-diagram-3 text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Estrutura do Conteúdo</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Organize seu curso em módulos e aulas</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div id="modules-container" class="space-y-4">
                        <?php foreach ($modules as $mIndex => $module): ?>
                            <div class="module-card border border-gray-300 dark:border-gray-700 rounded-2xl p-4 bg-gray-50 dark:bg-gray-900" data-index="<?= $mIndex ?>">
                                <div class="flex items-center justify-between mb-3">
                                    <input type="text" name="modules[<?= $mIndex ?>][title]"
                                        placeholder="Nome do Módulo"
                                        value="<?= esc($module->title_module) ?>"
                                        class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="bi bi-x-circle text-red-500 text-lg ml-2 cursor-pointer hover:text-red-600 transition-colors remove-module" title="Remover módulo"></i>
                                </div>

                                <textarea name="modules[<?= $mIndex ?>][description]"
                                    placeholder="Descrição do Módulo"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= esc($module->description_module) ?></textarea>

                                <!-- Lessons Container -->
                                <div class="lessons-container space-y-3 mb-3">
                                    <?php foreach ($module->lessons as $lIndex => $lesson): ?>
                                        <div class="lesson-item border border-gray-200 dark:border-gray-700 rounded-xl p-3 bg-white dark:bg-gray-800" data-index="<?= $lIndex ?>">
                                            <div class="flex items-center justify-between mb-2">
                                                <input type="text"
                                                    name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][title]"
                                                    placeholder="Título da Aula"
                                                    value="<?= esc($lesson->title_lesson) ?>"
                                                    class="flex-1 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <i class="bi bi-x-circle text-red-500 text-base ml-2 cursor-pointer hover:text-red-600 transition-colors remove-lesson" title="Remover aula"></i>
                                            </div>

                                            <select name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][type]"
                                                class="w-full px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <option value="video" <?= $lesson->type_lesson == 'video' ? 'selected' : '' ?>>Vídeo</option>
                                                <option value="text" <?= $lesson->type_lesson == 'text' ? 'selected' : '' ?>>Texto</option>
                                                <option value="quiz" <?= $lesson->type_lesson == 'quiz' ? 'selected' : '' ?>>Quiz</option>
                                                <option value="exercise" <?= $lesson->type_lesson == 'exercise' ? 'selected' : '' ?>>Exercício</option>
                                            </select>

                                            <input type="number" name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][duration]"
                                                placeholder="Duração (min)"
                                                value="<?= esc($lesson->duration_lesson) ?>"
                                                class="w-full px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">

                                            <input type="url" name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][video_url]"
                                                placeholder="Link do vídeo (para aulas de vídeo)"
                                                value="<?= esc($lesson->video_url_lesson) ?>"
                                                class="w-full px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
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
            <div id="advanced-settings" class="tab-content hidden bg-white dark:bg-dark-panel rounded-3xl shadow-xl border border-gray-200 dark:border-dark-line overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-dark-line bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-gear text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Configurações Avançadas</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Defina as configurações do curso</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Tipo de Curso
                            </label>
                            <div class="flex flex-wrap gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="courseType" value="free" <?= $course->price_course == 0 ? 'checked' : '' ?>
                                        class="text-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">Gratuito</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="courseType" value="paid" <?= $course->price_course > 0 ? 'checked' : '' ?>
                                        class="text-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">Pago</span>
                                </label>
                            </div>
                        </div>

                        <div id="price-settings" class="space-y-2 <?= $course->price_course > 0 ? '' : 'hidden' ?>">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Preço do Curso
                            </label>
                            <input type="number" id="coursePrice" name="price_course" min="0" step="0.01"
                                value="<?= esc($course->price_course) ?>"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Review and Publish -->
            <div id="review-publish" class="tab-content hidden bg-white dark:bg-dark-panel rounded-3xl shadow-xl border border-gray-200 dark:border-dark-line overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-dark-line bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="bi bi-rocket text-white text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white">Revisão e Publicação</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Finalize e publique seu curso</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="space-y-3">
                        <button type="submit" id="publish-course"
                            class="w-full px-6 py-3.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                            <i class="bi bi-rocket mr-2"></i>
                            Publicar Curso
                        </button>

                        <button type="submit" name="draft" value="1"
                            class="w-full px-6 py-3.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-xl transition-colors">
                            <i class="bi bi-save mr-2"></i>
                            Salvar como Rascunho
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center mt-6">
            <button id="prev-step" class="px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm" disabled>
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
        const modulesContainer = document.getElementById("modules-container");
        const nextBtn = document.getElementById("next-step");
        const prevBtn = document.getElementById("prev-step");
        const tabBtns = document.querySelectorAll(".tab-btn");
        const tabContents = document.querySelectorAll(".tab-content");
        const priceSettings = document.getElementById("price-settings");
        const priceInput = document.getElementById("coursePrice");
        const courseTypeRadios = document.querySelectorAll('input[name="courseType"]');

        // ======================
        // Step Navigation
        // ======================
        function updateStepIndicators(step) {
            const indicators = document.querySelectorAll(".flex.space-x-3 > div");
            const progressBar = document.getElementById("progress-bar");
            const progressText = document.getElementById("progress-text");

            indicators.forEach((indicator, index) => {
                if (index + 1 < step) {
                    indicator.classList.remove("bg-gray-200", "dark:bg-gray-700", "text-gray-600", "dark:text-gray-400");
                    indicator.classList.add("bg-green-500", "text-white");
                    indicator.innerHTML = '<i class="bi bi-check text-sm"></i>';
                } else if (index + 1 === step) {
                    indicator.classList.remove("bg-gray-200", "dark:bg-gray-700", "text-gray-600", "dark:text-gray-400");
                    indicator.classList.add("bg-blue-500", "text-white");
                    indicator.innerHTML = index + 1;
                } else {
                    indicator.classList.remove("bg-blue-500", "bg-green-500", "text-white");
                    indicator.classList.add("bg-gray-200", "dark:bg-gray-700", "text-gray-600", "dark:text-gray-400");
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
                btn.classList.add("bg-white", "dark:bg-dark-panel", "text-gray-700", "dark:text-gray-300", "border");
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
                activeBtn.classList.remove("bg-white", "dark:bg-dark-panel", "text-gray-700", "dark:text-gray-300", "border");
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

        // ======================
        // Modules and Lessons Management
        // ======================
        function addModule(moduleData = null) {
            const mIndex = moduleIndex++;
            const moduleId = moduleData?.id_module || "";
            const moduleTitle = moduleData?.title || "";
            const moduleDescription = moduleData?.description || "";
            let lessonsHtml = "";

            if (moduleData?.lessons) {
                moduleData.lessons.forEach((lesson, lIndex) => {
                    lessonsHtml += createLessonHTML(mIndex, lIndex, lesson);
                });
            }

            const moduleHtml = `
            <div class="module-card border border-gray-300 dark:border-gray-700 rounded-2xl p-4 bg-gray-50 dark:bg-gray-900" data-index="${mIndex}">
                <input type="hidden" name="modules[${mIndex}][id_module]" value="${moduleId}">
                <div class="flex items-center justify-between mb-3">
                    <input type="text" name="modules[${mIndex}][title]" 
                           placeholder="Nome do Módulo" 
                           value="${moduleTitle}"
                           class="flex-1 px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="bi bi-x-circle text-red-500 text-lg ml-2 cursor-pointer hover:text-red-600 transition-colors remove-module" title="Remover módulo"></i>
                </div>
                <textarea name="modules[${mIndex}][description]" 
                          placeholder="Descrição do Módulo"
                          class="w-full px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm mb-3 focus:outline-none focus:ring-2 focus:ring-blue-500">${moduleDescription}</textarea>
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

            return `
            <div class="lesson-item border border-gray-200 dark:border-gray-700 rounded-xl p-3 bg-white dark:bg-gray-800" data-index="${lIndex}">
                <input type="hidden" name="modules[${mIndex}][lessons][${lIndex}][id_lesson]" value="${lessonId}">
                <div class="flex items-center justify-between mb-2">
                    <input type="text" 
                           name="modules[${mIndex}][lessons][${lIndex}][title]" 
                           placeholder="Título da Aula" 
                           value="${title}"
                           class="flex-1 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <i class="bi bi-x-circle text-red-500 text-base ml-2 cursor-pointer hover:text-red-600 transition-colors remove-lesson" title="Remover aula"></i>
                </div>
                <select name="modules[${mIndex}][lessons][${lIndex}][type]" 
                        class="w-full px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="video" ${type==='video'?'selected':''}>Vídeo</option>
                    <option value="text" ${type==='text'?'selected':''}>Texto</option>
                    <option value="quiz" ${type==='quiz'?'selected':''}>Quiz</option>
                    <option value="exercise" ${type==='exercise'?'selected':''}>Exercício</option>
                </select>
                <input type="number" 
                       name="modules[${mIndex}][lessons][${lIndex}][duration]" 
                       placeholder="Duração (min)" 
                       value="${duration}"
                       class="w-full px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <input type="url" 
                       name="modules[${mIndex}][lessons][${lIndex}][video_url]" 
                       placeholder="Link do vídeo (para aulas de vídeo)" 
                       value="${video_url}"
                       class="w-full px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
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

            // You can add more validation here as needed
        });
    });
</script>

<?= $this->endSection() ?>