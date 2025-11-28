<?php
$user = service('auth')->user();
?>

<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Criar Novo Curso<?= $this->endSection() ?>

<?= $this->section('add_course') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

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

        <!-- Progress Indicator -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div class="flex items-center gap-2 sm:gap-4 overflow-x-auto pb-2">
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold step-indicator" data-step="1">1</div>
                        <span class="text-sm font-medium text-slate-800 dark:text-white step-text">Informações Básicas</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-8 h-8 bg-slate-300 dark:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-full flex items-center justify-center text-sm font-bold step-indicator" data-step="2">2</div>
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400 step-text">Estrutura</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-8 h-8 bg-slate-300 dark:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-full flex items-center justify-center text-sm font-bold step-indicator" data-step="3">3</div>
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400 step-text">Configurações</span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-8 h-8 bg-slate-300 dark:bg-slate-600 text-slate-600 dark:text-slate-300 rounded-full flex items-center justify-center text-sm font-bold step-indicator" data-step="4">4</div>
                        <span class="text-sm font-medium text-slate-500 dark:text-slate-400 step-text">Publicação</span>
                    </div>
                </div>
                <div class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Passo <span class="text-blue-600 dark:text-blue-400" id="current-step-text">1</span> de 4
                </div>
            </div>
            <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-2 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500" id="progress-bar" style="width: 25%"></div>
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
                                            placeholder="Descreva detalhadamente o conteúdo, objetivos e benefícios do seu curso..."></textarea>
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
                                            class="border-2 border-dashed border-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 sm:p-6 text-center transition-all duration-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 cursor-pointer">
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
                                        <div id="image-preview" class="hidden mt-4">
                                            <div class="relative">
                                                <img id="preview-img"
                                                    src=""
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

                            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6 text-center">
                                <i class="bi bi-diagram-3 text-slate-400 text-4xl mb-4"></i>
                                <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2">Estrutura do Conteúdo</h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">
                                    Aqui você poderá adicionar módulos e aulas ao seu curso
                                </p>
                                <button type="button" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-300">
                                    <i class="bi bi-plus-circle"></i>
                                    Adicionar Módulo
                                </button>
                            </div>
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

                            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6 text-center">
                                <i class="bi bi-gear text-slate-400 text-4xl mb-4"></i>
                                <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2">Configurações Avançadas</h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">
                                    Configure preço, visibilidade e outras opções do curso
                                </p>
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
        let currentStep = 1;
        const totalSteps = 4;

        // Elementos da navegação
        const prevButton = document.getElementById('prev-step');
        const nextButton = document.getElementById('next-step');
        const stepIndicators = document.querySelectorAll('.step-indicator');
        const stepTexts = document.querySelectorAll('.step-text');
        const currentStepText = document.getElementById('current-step-text');
        const progressBar = document.getElementById('progress-bar');
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        // Elementos do upload de imagem
        const fileInput = document.getElementById("courseImage");
        const previewContainer = document.getElementById("image-preview");
        const previewImg = document.getElementById("preview-img");
        const removeBtn = document.getElementById("remove-image");
        const uploadArea = document.getElementById("upload-area");

        // Função para atualizar a navegação
        function updateNavigation() {
            // Atualizar texto do passo atual
            currentStepText.textContent = currentStep;

            // Atualizar barra de progresso
            const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressBar.style.width = `${progressPercentage}%`;

            // Atualizar indicadores de passo
            stepIndicators.forEach((indicator, index) => {
                const stepNumber = parseInt(indicator.dataset.step);
                if (stepNumber === currentStep) {
                    indicator.classList.remove('bg-slate-300', 'dark:bg-slate-600', 'text-slate-600', 'dark:text-slate-300');
                    indicator.classList.add('bg-blue-600', 'text-white');
                    stepTexts[index].classList.remove('text-slate-500', 'dark:text-slate-400');
                    stepTexts[index].classList.add('text-slate-800', 'dark:text-white');
                } else if (stepNumber < currentStep) {
                    indicator.classList.remove('bg-slate-300', 'dark:bg-slate-600', 'text-slate-600', 'dark:text-slate-300');
                    indicator.classList.add('bg-green-500', 'text-white');
                    stepTexts[index].classList.remove('text-slate-500', 'dark:text-slate-400');
                    stepTexts[index].classList.add('text-slate-800', 'dark:text-white');
                } else {
                    indicator.classList.remove('bg-blue-600', 'bg-green-500', 'text-white');
                    indicator.classList.add('bg-slate-300', 'dark:bg-slate-600', 'text-slate-600', 'dark:text-slate-300');
                    stepTexts[index].classList.remove('text-slate-800', 'dark:text-white');
                    stepTexts[index].classList.add('text-slate-500', 'dark:text-slate-400');
                }
            });

            // Atualizar botões
            prevButton.disabled = currentStep === 1;
            nextButton.textContent = currentStep === totalSteps ? 'Publicar' : 'Próximo';

            // Atualizar abas
            tabButtons.forEach(button => {
                if (button.dataset.tab === getTabForStep(currentStep)) {
                    button.classList.add('border-blue-600', 'text-blue-600', 'dark:text-blue-400');
                    button.classList.remove('border-transparent', 'text-slate-500', 'dark:text-slate-400');
                } else {
                    button.classList.remove('border-blue-600', 'text-blue-600', 'dark:text-blue-400');
                    button.classList.add('border-transparent', 'text-slate-500', 'dark:text-slate-400');
                }
            });

            // Atualizar conteúdo das abas
            tabContents.forEach(content => {
                if (content.id === getTabForStep(currentStep)) {
                    content.classList.remove('hidden');
                    content.classList.add('active');
                } else {
                    content.classList.add('hidden');
                    content.classList.remove('active');
                }
            });
        }

        // Função auxiliar para obter a aba correspondente ao passo
        function getTabForStep(step) {
            const tabs = ['basic-info', 'content-structure', 'advanced-settings', 'review-publish'];
            return tabs[step - 1];
        }

        // Função de validação do passo atual
        function validateCurrentStep() {
            switch (currentStep) {
                case 1:
                    const title = document.getElementById('title_course').value.trim();
                    const subtitle = document.getElementById('courseSubtitle').value.trim();
                    const description = document.getElementById('courseDescription').value.trim();
                    const image = fileInput.files[0];

                    if (!title) {
                        alert('Por favor, preencha o título do curso.');
                        return false;
                    }
                    if (!subtitle) {
                        alert('Por favor, preencha o subtítulo do curso.');
                        return false;
                    }
                    if (!description) {
                        alert('Por favor, preencha a descrição do curso.');
                        return false;
                    }
                    if (!image) {
                        alert('Por favor, selecione uma imagem de capa para o curso.');
                        return false;
                    }
                    return true;

                case 2:
                case 3:
                    // Validações para outros passos podem ser adicionadas aqui
                    return true;

                case 4:
                    return true;

                default:
                    return true;
            }
        }

        // Event Listeners para navegação
        nextButton.addEventListener('click', () => {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateNavigation();
                } else {
                    // Último passo - submeter o formulário
                    document.getElementById('courseForm').submit();
                }
            }
        });

        prevButton.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateNavigation();
            }
        });

        // Navegação por clique nas abas
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetStep = getStepForTab(button.dataset.tab);
                if (targetStep <= currentStep) {
                    currentStep = targetStep;
                    updateNavigation();
                }
            });
        });

        function getStepForTab(tab) {
            const tabs = ['basic-info', 'content-structure', 'advanced-settings', 'review-publish'];
            return tabs.indexOf(tab) + 1;
        }

        // Upload de imagem
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

        removeBtn.addEventListener("click", () => {
            fileInput.value = "";
            previewImg.src = "";
            previewContainer.classList.add("hidden");
            uploadArea.classList.remove("hidden");
        });

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

                fileInput.files = e.dataTransfer.files;
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

        // Inicializar navegação
        updateNavigation();
    });
</script>

<?= $this->endSection() ?>