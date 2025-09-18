<?php
$user = service('auth')->user();
?>

<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Editar Curso<?= $this->endSection() ?>

<?= $this->section('edit_course') ?>
<style>
    /* Caixa central com bordas suaves */
    #courseImageDropzone {
        border: 2px dashed #0d6efd;
        border-radius: 12px;
        background: #f8f9fa;
        padding: 30px;
        transition: 0.3s;
    }

    #courseImageDropzone:hover {
        background: #eef5ff;
    }

    /* Mensagem padrão */
    #courseImageDropzone .dz-message {
        font-size: 1rem;
        color: #6c757d;
    }

    #courseImageDropzone .dz-message::before {
        content: "\f382";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 2.5rem;
        display: block;
        margin-bottom: 10px;
        color: #0d6efd;
    }

    /* Preview das imagens */
    #courseImageDropzone .dz-preview .dz-image img {
        border-radius: 10px;
        max-height: 140px;
        object-fit: cover;
    }

    /* Botão remover */
    #courseImageDropzone .dz-remove {
        color: #dc3545 !important;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    <div class="mb-5">
        <!-- Header Section -->
        <div class="header-section rounded-toper">
            <div class="header-content">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="fas fa-graduation-cap me-3"></i>
                    Editar Curso
                </h1>
                <p class="lead mb-0 opacity-90">
                    Atualize seu curso facilmente
                </p>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="px-4 py-3 bg-modern-dark">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="step-indicator active me-3">1</div>
                    <div class="step-indicator me-3">2</div>
                    <div class="step-indicator me-3">3</div>
                    <div class="step-indicator">4</div>
                </div>
                <div class="text-muted small">
                    <span id="progress-text">Passo 1 de 4</span>
                </div>
            </div>
            <div class="progress mt-2" style="height: 4px">
                <div class="progress-bar" role="progressbar" style="width: 25%"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4 bg-modern-dark rounded-lower">
            <!-- Navigation Tabs -->
            <ul class="nav nav-pills nav-fill mb-4" id="courseCreationTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="basic-info-tab" data-bs-toggle="pill"
                        data-bs-target="#basic-info" type="button" role="tab" aria-controls="basic-info"
                        aria-selected="true">
                        <i class="fas fa-info-circle me-2"></i>Informações Básicas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="content-structure-tab" data-bs-toggle="pill"
                        data-bs-target="#content-structure" type="button" role="tab"
                        aria-controls="content-structure" aria-selected="false">
                        <i class="fas fa-sitemap me-2"></i>Estrutura do Conteúdo
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="advanced-settings-tab" data-bs-toggle="pill"
                        data-bs-target="#advanced-settings" type="button" role="tab"
                        aria-controls="advanced-settings" aria-selected="false">
                        <i class="fas fa-cogs me-2"></i>Configurações Avançadas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-publish-tab" data-bs-toggle="pill"
                        data-bs-target="#review-publish" type="button" role="tab"
                        aria-controls="review-publish" aria-selected="false">
                        <i class="fas fa-rocket me-2"></i>Revisão e Publicação
                    </button>
                </li>
            </ul>

            <!-- Form -->
            <form id="courseForm" action="<?= base_url('instructor/dashboard/editar_curso/' . $course->id_course) ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_instructor_course" value="<?= $user->id ?>">

                <div class="tab-content" id="courseCreationTabContent">
                    <!-- Step 1: Basic Info -->
                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel"
                        aria-labelledby="basic-info-tab">
                        <div class="bg-transparent">
                            <div class="card-header pb-4 mb-4">
                                <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Informações Básicas do Curso</h5>
                                <p class="text-muted mb-0 mt-2">Atualize os campos do seu curso</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="mb-4">
                                            <label for="title_course" class="form-label fw-semibold">
                                                <i class="fas fa-heading me-2 text-primary"></i>Título do Curso *
                                            </label>
                                            <input type="text" class="form-control" id="title_course" name="title_course"
                                                placeholder="Ex: Desenvolvimento Web Completo" required
                                                value="<?= esc($course->title_course) ?>" />
                                        </div>
                                        <div class="mb-4">
                                            <label for="courseSubtitle" class="form-label fw-semibold">
                                                <i class="fas fa-align-left me-2 text-primary"></i>Subtítulo do Curso *
                                            </label>
                                            <input type="text" class="form-control" id="courseSubtitle" name="subtitle_course"
                                                placeholder="Ex: Do zero ao avançado com HTML, CSS e JavaScript" required
                                                value="<?= esc($course->subtitle_course) ?>" />
                                        </div>
                                        <div class="mb-4">
                                            <label for="courseDescription" class="form-label fw-semibold">
                                                <i class="fas fa-file-alt me-2 text-primary"></i>Descrição do Curso *
                                            </label>
                                            <textarea rows="10" id="courseDescription" name="description_course"
                                                class="content-editor form-control"
                                                placeholder="Descreva detalhadamente..."><?= esc($course->description_course) ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-4">
                                            <label
                                                for="courseImage"
                                                class="form-label fw-semibold">
                                                <i class="fas fa-image me-2 text-primary"></i>
                                                Imagem de Capa *
                                            </label>
                                            <div class="upload-area" id="upload-area" <?= $course->image_course ? 'style="display:none;"' : '' ?>>
                                                <i class="fas fa-cloud-upload-alt fs-1 text-primary mb-3"></i>
                                                <h6 class="mb-2">
                                                    Arraste uma imagem ou clique para selecionar
                                                </h6>
                                                <p class="text-muted small mb-3">
                                                    Recomendado: 1280x720px, máx. 2MB
                                                </p>
                                                <input
                                                    type="file"
                                                    class="form-control"
                                                    id="courseImage"
                                                    name="image_course"
                                                    accept="image/*"
                                                    style="display: none" />
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-primary"
                                                    onclick="document.getElementById('courseImage').click()">
                                                    <i class="fas fa-folder-open me-2"></i>Selecionar
                                                    Arquivo
                                                </button>
                                            </div>
                                            <?php if ($course->image_course): ?>
                                                <div class="mt-3" id="image-preview">
                                                    <img
                                                        id="preview-img"
                                                        src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
                                                        alt="Preview"
                                                        class="img-fluid rounded-3 shadow-sm" />
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger btn-sm mt-2 w-100"
                                                        id="remove-image">
                                                        <i class="fas fa-times me-1"></i>Remover Imagem
                                                    </button>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-3" id="image-preview" style="display:none;"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Content Structure -->
                    <div class="tab-pane fade" id="content-structure" role="tabpanel" aria-labelledby="content-structure-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-sitemap me-2 text-primary"></i>Estrutura do Conteúdo</h5>
                                <p class="text-muted mb-0 mt-2">Organize seu curso em módulos e aulas</p>
                            </div>
                            <div class="card-body">
                                <div id="modules-container">
                                    <?php foreach ($modules as $mIndex => $module): ?>
                                        <div class="module-card mb-3 border p-3" data-index="<?= $mIndex ?>">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <input type="text" name="modules[<?= $mIndex ?>][title]"
                                                    class="form-control me-2"
                                                    placeholder="Nome do Módulo"
                                                    value="<?= esc($module->title_module) ?>">
                                                <i class="bi bi-x-circle text-danger fs-5 remove-module" role="button" title="Remover módulo"></i>
                                            </div>
                                            <textarea name="modules[<?= $mIndex ?>][description]"
                                                class="form-control mb-2"
                                                placeholder="Descrição do Módulo"><?= esc($module->description_module) ?></textarea>

                                            <div class="lessons-container">
                                                <?php foreach ($module->lessons as $lIndex => $lesson): ?>
                                                    <div class="lesson-item mb-2 border p-2" data-index="<?= $lIndex ?>">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <input type="text"
                                                                name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][title]"
                                                                class="form-control me-2"
                                                                placeholder="Título da Aula"
                                                                value="<?= esc($lesson->title_lesson) ?>">
                                                            <i class="bi bi-x-circle text-danger fs-6 remove-lesson" role="button" title="Remover aula"></i>
                                                        </div>
                                                        <select name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][type]" class="form-select text-secondary mb-1">
                                                            <option value="video" <?= $lesson->type_lesson == 'video' ? 'selected' : '' ?>>Vídeo</option>
                                                            <option value="text" <?= $lesson->type_lesson == 'text' ? 'selected' : '' ?>>Texto</option>
                                                            <option value="quiz" <?= $lesson->type_lesson == 'quiz' ? 'selected' : '' ?>>Quiz</option>
                                                            <option value="exercise" <?= $lesson->type_lesson == 'exercise' ? 'selected' : '' ?>>Exercício</option>
                                                        </select>
                                                        <input type="number" name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][duration]"
                                                            class="form-control mb-1"
                                                            placeholder="Duração (min)"
                                                            value="<?= esc($lesson->duration_lesson) ?>">
                                                        <input type="url" name="modules[<?= $mIndex ?>][lessons][<?= $lIndex ?>][video_url]"
                                                            class="form-control"
                                                            placeholder="Link do vídeo"
                                                            value="<?= esc($lesson->video_url_lesson) ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <button type="button" class="btn btn-sm btn-primary add-lesson" data-module="<?= $mIndex ?>">
                                                + Adicionar Aula
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="btn btn-outline-primary mt-3" id="add-module" type="button">
                                    <i class="fas fa-plus me-2"></i>Adicionar Módulo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Advanced Settings -->
                    <div class="tab-pane fade" id="advanced-settings" role="tabpanel" aria-labelledby="advanced-settings-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cogs me-2 text-primary"></i>Configurações Avançadas</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tipo de Curso</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="courseType" value="free" <?= $course->price_course == 0 ? 'checked' : '' ?> />
                                        <label class="form-check-label">Gratuito</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="courseType" value="paid" <?= $course->price_course > 0 ? 'checked' : '' ?> />
                                        <label class="form-check-label">Pago</label>
                                    </div>
                                </div>
                                <div class="mb-3" id="price-settings" style="display: <?= $course->price_course > 0 ? 'block' : 'none' ?>">
                                    <label for="coursePrice" class="form-label fw-semibold">Preço do Curso</label>
                                    <input type="number" class="form-control" id="coursePrice" name="price_course" min="0" step="0.01" value="<?= esc($course->price_course) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Review and Publish -->
                    <div class="tab-pane fade" id="review-publish" role="tabpanel" aria-labelledby="review-publish-tab">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-rocket me-2 text-primary"></i>Revisão e Publicação</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg" id="publish-course">
                                        <i class="fas fa-rocket me-2"></i>Publicar Curso
                                    </button>

                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="fas fa-save me-2"></i>Salvar como Rascunho
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-light" type="button" id="prev-step" disabled>
                    <i class="fas fa-arrow-left me-2"></i>Anterior
                </button>
                <button class="btn btn-primary" type="button" id="next-step">
                    Próximo<i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // ======================
    // Global Variables
    // ======================
    let currentStep = 1;
    let tags = [];

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
    const tabs = document.querySelectorAll("#courseCreationTab button");
    const priceSettings = document.getElementById("price-settings");
    const tagsInput = document.getElementById("courseTags");
    const tagsDisplay = document.getElementById("tags-display");
    let moduleIndex = modulesContainer?.children.length || 0;

    // ======================
    // Tooltips
    // ======================
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl));

    // ======================
    // Step Navigation
    // ======================
    function updateStepIndicators(step) {
        const indicators = document.querySelectorAll(".step-indicator");
        const progressBar = document.querySelector(".progress-bar");
        const progressText = document.getElementById("progress-text");

        indicators.forEach((indicator, index) => {
            indicator.classList.remove("active", "completed");
            if (index + 1 < step) {
                indicator.classList.add("completed");
                indicator.innerHTML = '<i class="fas fa-check"></i>';
            } else if (index + 1 === step) {
                indicator.classList.add("active");
                indicator.innerHTML = index + 1;
            } else {
                indicator.innerHTML = index + 1;
            }
        });

        progressBar.style.width = step * 25 + "%";
        progressText.textContent = `Passo ${step} de 4`;
    }

    function updateNavigationButtons() {
        prevBtn.disabled = currentStep === 1;
        nextBtn.style.display = currentStep === 4 ? "none" : "inline-block";
    }

    nextBtn.addEventListener("click", () => {
        if (currentStep < 4) {
            currentStep++;
            new bootstrap.Tab(tabs[currentStep - 1]).show();
            updateStepIndicators(currentStep);
            updateNavigationButtons();
        }
    });

    prevBtn.addEventListener("click", () => {
        if (currentStep > 1) {
            currentStep--;
            new bootstrap.Tab(tabs[currentStep - 1]).show();
            updateStepIndicators(currentStep);
            updateNavigationButtons();
        }
    });

    tabs.forEach((tab, index) => {
        tab.addEventListener("click", () => {
            currentStep = index + 1;
            updateStepIndicators(currentStep);
            updateNavigationButtons();
        });
    });

    updateNavigationButtons();
    updateStepIndicators(currentStep);

    // ======================
    // Rich Text Editor Placeholder
    // ======================
    const editor = document.getElementById("courseDescription");
    if (editor) {
        function updatePlaceholder() {
            editor.classList.toggle("empty", editor.textContent.trim() === "");
        }
        editor.addEventListener("input", updatePlaceholder);
        editor.addEventListener("focus", () => {
            if (editor.textContent.trim() === "") editor.innerHTML = "";
        });
        updatePlaceholder();
    }

    // ======================
    // Tags
    // ======================
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

    function renderTags() {
        if (!tagsDisplay) return;
        tagsDisplay.innerHTML = tags.map(tag => `
            <span class="badge bg-primary me-1">
                ${tag} 
                <button type="button" class="btn-close btn-close-white btn-sm" onclick="removeTag('${tag}')"></button>
            </span>
        `).join("");
    }

    window.removeTag = function (tagToRemove) {
        tags = tags.filter(tag => tag !== tagToRemove);
        renderTags();
    };

    // ======================
    // Image Upload
    // ======================
    function handleImageUpload(file) {
        if (!file.type.startsWith("image/")) {
            alert("Por favor selecione apenas arquivos de imagem.");
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            if (previewImg) previewImg.src = e.target.result;
            if (previewContainer) previewContainer.style.display = "block";
            if (uploadArea) uploadArea.style.display = "none";
        };
        reader.readAsDataURL(file);
    }

    if (uploadArea) {
        uploadArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            uploadArea.classList.add("dragover");
        });
        uploadArea.addEventListener("dragleave", (e) => {
            e.preventDefault();
            uploadArea.classList.remove("dragover");
        });
        uploadArea.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadArea.classList.remove("dragover");
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

    if (removeBtn) {
        removeBtn.addEventListener("click", () => {
            fileInput.value = "";
            if (previewContainer) previewContainer.style.display = "none";
            if (uploadArea) uploadArea.style.display = "block";
        });
    }

    // ======================
    // Modules and Lessons
    // ======================
    document.getElementById("add-module")?.addEventListener("click", () => addModule());

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
            <div class="module-card mb-3 border p-3">
                <input type="hidden" name="modules[${mIndex}][id_module]" value="${moduleId}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <input type="text" name="modules[${mIndex}][title]" class="form-control me-2" placeholder="Nome do Módulo" value="${moduleTitle}">
                    <i class="bi bi-x-circle text-danger fs-5 remove-module" role="button" title="Remover módulo"></i>
                </div>
                <textarea name="modules[${mIndex}][description]" class="form-control mb-2" placeholder="Descrição do Módulo">${moduleDescription}</textarea>
                <div class="lessons-container">${lessonsHtml}</div>
                <button type="button" class="btn btn-sm btn-primary add-lesson" data-module="${mIndex}">+ Adicionar Aula</button>
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
            <div class="lesson-item mb-2 border p-2">
                <input type="hidden" name="modules[${mIndex}][lessons][${lIndex}][id_lesson]" value="${lessonId}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <input type="text" name="modules[${mIndex}][lessons][${lIndex}][title]" class="form-control me-2" placeholder="Título da Aula" value="${title}">
                    <i class="bi bi-x-circle text-danger fs-6 remove-lesson" role="button" title="Remover aula"></i>
                </div>
                <select name="modules[${mIndex}][lessons][${lIndex}][type]" class="form-select text-secondary mb-1">
                    <option value="video" ${type==='video'?'selected':''}>Vídeo</option>
                    <option value="text" ${type==='text'?'selected':''}>Texto</option>
                    <option value="quiz" ${type==='quiz'?'selected':''}>Quiz</option>
                    <option value="exercise" ${type==='exercise'?'selected':''}>Exercício</option>
                </select>
                <input type="number" name="modules[${mIndex}][lessons][${lIndex}][duration]" class="form-control mb-1" placeholder="Duração (min)" value="${duration}">
                <input type="url" name="modules[${mIndex}][lessons][${lIndex}][video_url]" class="form-control" placeholder="Link do vídeo" value="${video_url}">
            </div>
        `;
    }

    modulesContainer?.addEventListener("click", (e) => {
        // Remove module
        if (e.target.classList.contains("remove-module")) e.target.closest(".module-card").remove();
        // Remove lesson
        if (e.target.classList.contains("remove-lesson")) e.target.closest(".lesson-item").remove();
        // Add lesson
        if (e.target.classList.contains("add-lesson")) {
            const moduleId = e.target.dataset.module;
            const lessonsContainer = e.target.previousElementSibling;
            const lessonCount = lessonsContainer.querySelectorAll(".lesson-item").length;
            lessonsContainer.insertAdjacentHTML("beforeend", createLessonHTML(moduleId, lessonCount));
        }
    });

    // ======================
    // Course Type and Price Toggle
    // ======================
    document.querySelectorAll('input[name="courseType"]').forEach(radio => {
        radio.addEventListener("change", () => {
            if (radio.value === "paid") priceSettings.style.display = "block";
            else priceSettings.style.display = "none";
        });
    });

    // ======================
    // Preview Update
    // ======================
    function updatePreview() {
        document.getElementById("preview-title")?.textContent = document.getElementById("title_course")?.value || "Título do Curso";
        document.getElementById("preview-subtitle")?.textContent = document.getElementById("courseSubtitle")?.value || "Subtítulo do curso";
        document.getElementById("preview-description")?.innerHTML = document.getElementById("courseDescription")?.value || "Descrição do curso aparecerá aqui...";
    }
    document.addEventListener("input", updatePreview);
    document.addEventListener("change", updatePreview);
    updatePreview();

    // ======================
    // Collect Data for Backend
    // ======================
    window.collectCourseData = function() {
        const form = document.getElementById("courseForm");
        const formData = new FormData(form);
        formData.append("tags", JSON.stringify(tags));

        const modules = [];
        document.querySelectorAll(".module-card").forEach((modCard) => {
            const moduleId = modCard.querySelector(`input[name$="[id_module]"]`)?.value || null;
            const moduleTitle = modCard.querySelector(`input[name$="[title]"]`)?.value || "";
            const moduleDescription = modCard.querySelector(`textarea[name$="[description]"]`)?.value || "";
            const lessons = [];
            modCard.querySelectorAll(".lesson-item").forEach((lessonEl) => {
                lessons.push({
                    id_lesson: lessonEl.querySelector(`input[name$="[id_lesson]"]`)?.value || null,
                    title: lessonEl.querySelector(`input[name$="[title]"]`)?.value || "",
                    type: lessonEl.querySelector(`select[name$="[type]"]`)?.value || "text",
                    duration: lessonEl.querySelector(`input[name$="[duration]"]`)?.value || 0,
                    video_url: lessonEl.querySelector(`input[name$="[video_url]"]`)?.value || null,
                });
            });

            modules.push({ id_module: moduleId, title: moduleTitle, description: moduleDescription, lessons });
        });

        formData.append("modules", JSON.stringify(modules));
        return formData;
    };
});

</script>
<?= $this->endSection() ?>