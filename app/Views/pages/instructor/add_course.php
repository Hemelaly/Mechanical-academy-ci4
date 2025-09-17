<?php
$user = service('auth')->user();
?>

<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Criar Novo Curso<?= $this->endSection() ?>

<?= $this->section('add_course') ?>
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
        /* ícone upload da FontAwesome */
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
                    Criar Novo Curso
                </h1>
                <p class="lead mb-0 opacity-90">
                    Transforme seu conhecimento em um curso incrível
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
            <form id="courseForm" action="<?= base_url('instructor/dashboard/novo_curso/criar') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_instructor_course" value="<?= $user->id ?>">

                <div class="tab-content" id="courseCreationTabContent">
                    <!-- Step 1: Basic Info -->
                    <div class="tab-pane fade show active" id="basic-info" role="tabpanel"
                        aria-labelledby="basic-info-tab">
                        <div class="bg-transparent">
                            <div class="card-header pb-4 mb-4">
                                <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Informações Básicas do Curso</h5>
                                <p class="text-muted mb-0 mt-2">Preencha os campos obrigatórios para o seu curso</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="mb-4">
                                            <label for="title_course" class="form-label fw-semibold">
                                                <i class="fas fa-heading me-2 text-primary"></i>Título do Curso *
                                            </label>
                                            <input type="text" class="form-control" id="title_course" name="title_course"
                                                placeholder="Ex: Desenvolvimento Web Completo" required />
                                        </div>
                                        <div class="mb-4">
                                            <label for="courseSubtitle" class="form-label fw-semibold">
                                                <i class="fas fa-align-left me-2 text-primary"></i>Subtítulo do Curso *
                                            </label>
                                            <input type="text" class="form-control" id="courseSubtitle" name="subtitle_course"
                                                placeholder="Ex: Do zero ao avançado com HTML, CSS e JavaScript" required />
                                        </div>
                                        <div class="mb-4">
                                            <label for="courseDescription" class="form-label fw-semibold">
                                                <i class="fas fa-file-alt me-2 text-primary"></i>Descrição do Curso *
                                            </label>
                                            <textarea rows="10" id="courseDescription" name="description_course"
                                                class="content-editor form-control"
                                                placeholder="Descreva detalhadamente..."></textarea>
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
                                            <div class="upload-area" id="upload-area">
                                                <i
                                                    class="fas fa-cloud-upload-alt fs-1 text-primary mb-3"></i>
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
                                            <div
                                                class="mt-3"
                                                id="image-preview"
                                                style="display: none">
                                                <img
                                                    id="preview-img"
                                                    src=""
                                                    alt="Preview"
                                                    class="img-fluid rounded-3 shadow-sm" />
                                                <button
                                                    type="button"
                                                    class="btn btn-danger btn-sm mt-2 w-100"
                                                    id="remove-image">
                                                    <i class="fas fa-times me-1"></i>Remover Imagem
                                                </button>
                                            </div>
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
                                <div id="modules-container"></div>
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
                                        <input class="form-check-input" type="radio" name="courseType" value="free" checked />
                                        <label class="form-check-label">Gratuito</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="courseType" value="paid" />
                                        <label class="form-check-label">Pago</label>
                                    </div>
                                </div>
                                <div class="mb-3" id="price-settings" style="display: none">
                                    <label for="coursePrice" class="form-label fw-semibold">Preço do Curso</label>
                                    <input type="number" class="form-control" id="coursePrice" name="price_course" min="0" step="0.01" />
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
        const fileInput = document.getElementById("courseImage");
        const previewContainer = document.getElementById("image-preview");
        const previewImg = document.getElementById("preview-img");
        const removeBtn = document.getElementById("remove-image");
        const uploadArea = document.getElementById("upload-area");

        // Quando seleciona uma imagem
        fileInput.addEventListener("change", (event) => {
            const file = event.target.files[0];

            if (file && file.type.startsWith("image/")) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = "block";
                    uploadArea.style.display = "none";
                };

                reader.readAsDataURL(file);
            } else {
                alert("Por favor selecione um arquivo de imagem válido.");
                fileInput.value = "";
            }
        });

        // Remover imagem
        removeBtn.addEventListener("click", () => {
            fileInput.value = "";
            previewImg.src = "";
            previewContainer.style.display = "none";
            uploadArea.style.display = "block";
        });

        // Permitir arrastar e soltar
        uploadArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            uploadArea.classList.add("border-primary");
        });

        uploadArea.addEventListener("dragleave", () => {
            uploadArea.classList.remove("border-primary");
        });

        uploadArea.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadArea.classList.remove("border-primary");

            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith("image/")) {
                fileInput.files = e.dataTransfer.files;

                const reader = new FileReader();
                reader.onload = (ev) => {
                    previewImg.src = ev.target.result;
                    previewContainer.style.display = "block";
                    uploadArea.style.display = "none";
                };
                reader.readAsDataURL(file);
            } else {
                alert("Por favor arraste apenas arquivos de imagem.");
            }
        });
    });
</script>
<?= $this->endSection() ?>