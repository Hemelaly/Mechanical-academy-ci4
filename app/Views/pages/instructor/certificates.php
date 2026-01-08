<?php
// dd($certificates);
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Gerenciar Certificados<?= $this->endSection() ?>

<?= $this->section('certificates') ?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- CSRF (para AJAX/FormData) -->
<meta name="csrf-name" content="<?= csrf_token() ?>">
<meta name="csrf-hash" content="<?= csrf_hash() ?>">

<style>
    .drag-active {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, .05);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-enter {
        animation: fadeIn .2s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-in {
        animation: slideIn .35s ease-out;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .status-active {
        background: rgba(16, 185, 129, .12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, .22);
    }

    .status-inactive {
        background: rgba(239, 68, 68, .12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, .22);
    }

    .status-pending {
        background: rgba(245, 158, 11, .12);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, .22);
    }

    .certificate-card {
        transition: all .25s ease;
        border: 1px solid #e2e8f0;
    }

    .dark .certificate-card {
        border-color: #334155;
    }

    .certificate-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, .1), 0 4px 6px -2px rgba(0, 0, 0, .05);
        border-color: #cbd5e1;
    }

    .dark .certificate-card:hover {
        border-color: #475569;
    }

    /* opcional: borda por status */
    .certificate-card.active {
        box-shadow: 0 0 0 1px rgba(16, 185, 129, .25) inset;
    }

    .certificate-card.inactive {
        box-shadow: 0 0 0 1px rgba(239, 68, 68, .25) inset;
    }

    .certificate-card.pending {
        box-shadow: 0 0 0 1px rgba(245, 158, 11, .25) inset;
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="h-full text-slate-800 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow container mx-auto py-4 px-4">

            <!-- Cabeçalho -->
            <div class="mb-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Gerenciar Certificados</h1>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">
                            Faça upload do PDF do certificado para os alunos (pendentes após concluírem o curso).
                        </p>
                    </div>

                    <div class="mt-4 md:mt-0">
                        <button id="open-upload-modal"
                            class="px-6 py-3.5 bg-gradient-to-br from-blue-500 to-blue-900 hover:bg-blue-700 text-white rounded-xl font-medium flex items-center gap-3 shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer">
                            <i class="bi bi-cloud-upload text-lg"></i>
                            <span class="font-semibold">Upload de Certificado</span>
                        </button>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">Total de Registros</p>
                                <h3 id="kpiTotal" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">0</h3>
                                <div class="flex items-center gap-1">
                                    <i class="bi bi-award text-blue-500 text-sm flex-shrink-0"></i>
                                    <span class="text-slate-600 dark:text-slate-400 text-sm font-medium truncate">certificados (ativos/pendentes)</span>
                                </div>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                                <i class="bi bi-award text-blue-600 dark:text-blue-400 text-base sm:text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">Cursos com Registros</p>
                                <h3 id="kpiCourses" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">0</h3>
                                <div class="flex items-center gap-1">
                                    <i class="bi bi-book text-green-500 text-sm flex-shrink-0"></i>
                                    <span class="text-slate-600 dark:text-slate-400 text-sm font-medium truncate">distintos</span>
                                </div>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                                <i class="bi bi-book text-green-600 dark:text-green-400 text-base sm:text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">Alunos Envolvidos</p>
                                <h3 id="kpiStudents" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">0</h3>
                                <div class="flex items-center gap-1">
                                    <i class="bi bi-people text-purple-500 text-sm flex-shrink-0"></i>
                                    <span class="text-slate-600 dark:text-slate-400 text-sm font-medium truncate">distintos</span>
                                </div>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                                <i class="bi bi-people text-purple-600 dark:text-purple-400 text-base sm:text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">Última Atualização</p>
                                <h3 id="kpiLastLabel" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">-</h3>
                                <div class="flex items-center gap-1">
                                    <i class="bi bi-clock text-amber-500 text-sm flex-shrink-0"></i>
                                    <span id="kpiLastTime" class="text-amber-500 text-sm font-medium truncate">-</span>
                                </div>
                            </div>
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                                <i class="bi bi-calendar-check text-amber-600 dark:text-amber-400 text-base sm:text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros e busca -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 mb-8 border border-slate-200 dark:border-slate-700 mt-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Todos os Certificados</h2>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">Filtre por curso, status e pesquise por aluno/número</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 mt-4 md:mt-0">
                        <!-- Busca -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-slate-400"></i>
                            </div>
                            <input type="text" id="search-certificates" placeholder="Buscar por curso, aluno, nº..."
                                class="pl-10 pr-4 py-3.5 w-full bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>

                        <!-- Filtro de curso (dinâmico) -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-filter text-slate-400"></i>
                            </div>
                            <select id="filter-course"
                                class="text-sm pl-10 pr-10 py-3.5 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none w-full sm:w-auto">
                                <option value="">Todos os cursos</option>
                            </select>
                        </div>

                        <!-- Filtro de status -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-bar-chart text-slate-400"></i>
                            </div>
                            <select id="filter-status"
                                class="text-sm pl-10 pr-10 py-3.5 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none w-full sm:w-auto">
                                <option value="">Todos os status</option>
                                <option value="active">Ativo</option>
                                <option value="pending">Pendente</option>
                                <option value="inactive">Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contador de resultados -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        <span id="results-count">0</span> registros encontrados
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <button id="clear-filters" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center">
                            <i class="bi bi-arrow-clockwise mr-2"></i>
                            <span>Limpar filtros</span>
                        </button>

                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Ordenar por:</span>
                            <select id="sort-certificates" class="text-sm pl-10 pr-10 py-3.5 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none w-full sm:w-auto">
                                <option value="newest">Mais recentes</option>
                                <option value="oldest">Mais antigos</option>
                                <option value="course">Curso A-Z</option>
                                <option value="student">Aluno A-Z</option>
                                <option value="status">Status</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="certificates-grid"></div>

                <!-- Estado vazio -->
                <div id="empty-state" class="hidden p-10 text-center rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900/50 border border-dashed border-slate-300 dark:border-slate-700">
                    <div class="mx-auto w-28 h-28 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mb-6">
                        <i class="bi bi-award text-slate-400 dark:text-slate-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Nenhum registro encontrado</h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6 max-w-md mx-auto">Ajuste os filtros ou faça upload para um aluno pendente.</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button id="empty-upload-btn"
                            class="px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium flex items-center justify-center space-x-3 shadow-lg hover:shadow-xl">
                            <i class="bi bi-cloud-upload"></i>
                            <span class="font-semibold">Upload de Certificado</span>
                        </button>
                        <button id="clear-search-btn"
                            class="px-6 py-3.5 bg-slate-600 hover:bg-slate-700 text-white rounded-xl font-medium flex items-center justify-center space-x-3 shadow-lg hover:shadow-xl">
                            <i class="bi bi-x-circle"></i>
                            <span class="font-semibold">Limpar busca</span>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Upload (NOVO: curso + aluno + nº + data + PDF) -->
    <div id="upload-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto overflow-x-hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-4xl">
                <div class="fixed inset-0 bg-black bg-opacity-60" id="modal-overlay"></div>

                <div class="relative bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg p-4 md:p-6 modal-enter">
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4 md:pb-5">
                        <div>
                            <h3 id="modal-title" class="text-lg font-medium text-slate-900 dark:text-white">Upload de Certificado (PDF)</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Selecione o curso, o aluno e envie o PDF</p>
                        </div>
                        <button type="button" id="close-upload-modal"
                            class="text-slate-500 dark:text-slate-400 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white rounded-lg text-sm w-9 h-9 ms-auto inline-flex justify-center items-center">
                            <i class="bi bi-x-lg text-lg"></i>
                            <span class="sr-only">Fechar modal</span>
                        </button>
                    </div>

                    <form id="upload-form" class="pt-4 md:pt-6">
                        <input type="hidden" id="certificate-id" value="">

                        <!-- Seletores -->
                        <div class="mb-6">
                            <h4 class="text-base font-medium text-slate-900 dark:text-white mb-4 pb-3 border-b border-slate-200 dark:border-slate-700">
                                Dados do Certificado
                            </h4>

                            <div class="grid gap-4 grid-cols-2">
                                <!-- Curso -->
                                <div class="col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Curso *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="bi bi-book text-slate-400"></i>
                                        </div>
                                        <select id="modal-course" class="bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm appearance-none" required>
                                            <option value="">Selecione um curso</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Aluno (baseado nos registros do curso) -->
                                <div class="col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Aluno *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="bi bi-person text-slate-400"></i>
                                        </div>
                                        <select id="modal-student" class="bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm appearance-none" required>
                                            <option value="">Selecione o aluno</option>
                                        </select>
                                    </div>
                                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                        Dica: por padrão mostramos primeiro os <b>pendentes</b>.
                                    </p>
                                </div>

                                <!-- Número -->
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Número do Certificado</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="bi bi-hash text-slate-400"></i>
                                        </div>
                                        <input type="text" id="modal-number"
                                            class="bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm"
                                            placeholder="Ex: CERT-2026-001">
                                    </div>
                                </div>

                                <!-- Data de emissão -->
                                <div class="col-span-2 sm:col-span-1">
                                    <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Data de Emissão</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="bi bi-calendar-date text-slate-400"></i>
                                        </div>
                                        <input type="date" id="modal-issued-at"
                                            class="bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm">
                                    </div>
                                </div>

                                <!-- Status (somente leitura) -->
                                <div class="col-span-2">
                                    <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-600 rounded-lg p-3">
                                        <div class="text-sm text-slate-600 dark:text-slate-300">
                                            Status atual do registro:
                                        </div>
                                        <div id="modal-status" class="status-badge status-pending">Pendente</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload PDF -->
                        <div id="drop-area"
                            class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-6 text-center mb-6 transition-all duration-300 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/10">
                            <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-4">
                                <i class="bi bi-cloud-arrow-up text-blue-600 dark:text-blue-400 text-2xl"></i>
                            </div>
                            <h4 class="text-base font-medium text-slate-900 dark:text-white mb-2">Arraste e solte o PDF</h4>
                            <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">Faça upload do certificado em PDF (máx. 10MB)</p>

                            <label for="file-input"
                                class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-sm font-medium text-sm px-4 py-2.5 rounded-lg cursor-pointer">
                                <i class="bi bi-folder me-2"></i>
                                <span>Selecionar PDF</span>
                            </label>
                            <input type="file" id="file-input" class="hidden" accept=".pdf,application/pdf">
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-3">Tamanho máximo: 10MB</p>

                            <div id="file-preview" class="hidden mt-6 animate-slide-in">
                                <div class="flex items-center justify-between bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bi bi-file-earmark-pdf text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <p id="file-name" class="font-medium text-slate-900 dark:text-white text-sm"></p>
                                            <p id="file-size" class="text-xs text-slate-600 dark:text-slate-400"></p>
                                        </div>
                                    </div>
                                    <button type="button" id="remove-file"
                                        class="text-red-500 hover:text-red-700 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg text-sm w-8 h-8 flex items-center justify-center">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center space-x-4 border-t border-slate-200 dark:border-slate-700 pt-4 md:pt-6">
                            <button type="submit" id="submit-certificate"
                                class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-sm font-medium text-sm px-4 py-2.5 rounded-lg">
                                <i class="bi bi-save me-2"></i>
                                <span id="submit-text">Salvar / Enviar PDF</span>
                            </button>
                            <button type="button" id="cancel-upload"
                                class="text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-600 hover:text-slate-900 dark:hover:text-white focus:ring-4 focus:ring-slate-300 dark:focus:ring-slate-600 shadow-sm font-medium text-sm px-4 py-2.5 rounded-lg">
                                Cancelar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Exclusão -->

<script>
    /* =========================
       DATA (PHP -> JS)
       ========================= */
    const csrfName = document.querySelector('meta[name="csrf-name"]')?.content;
    let csrfHash = document.querySelector('meta[name="csrf-hash"]')?.content;

    const certificatesRaw = <?= json_encode(
                                $certificates ?? [],
                                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                            ) ?>;

    const fallbackCover = 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80';

    // Normaliza pro formato do front
    let certificatesData = (certificatesRaw || []).map(r => {
        const hasPdf = Boolean(r.pdf_path_certificate);
        const status = hasPdf ? 'active' : 'pending';

        return {
            id: Number(r.id_certificate ?? r.id ?? 0),

            // Nota: tenta varias chaves possiveis
            enrollmentId: Number(
                r.enrollment_id ?? r.id_enrollment ?? r.enrollmentId ?? 0
            ),

            courseId: Number(r.id_course_certificate ?? r.id_course ?? 0),
            courseTitle: r.title_course ?? '-',
            studentName: r.student_name ?? '-',
            number: r.number_certificate ?? '',
            issuedAt: r.issued_at_certificate ?? '',
            availableAt: r.avaiable_at_certificate ?? '',
            revokedAt: r.revoked_at_certificate ?? '',
            createdAt: r.created_at_certificate ?? '',
            filePath: r.pdf_path_certificate ?? '',
            status,
            image: r.image_course ? `<?= base_url('assets/instructor/img/courses') ?>/${r.image_course}` : fallbackCover,
        };
    });

    /* =========================
       DOM
       ========================= */
    const certificatesGrid = document.getElementById('certificates-grid');
    const emptyState = document.getElementById('empty-state');

    const searchInput = document.getElementById('search-certificates');
    const filterCourse = document.getElementById('filter-course');
    const filterStatus = document.getElementById('filter-status');
    const sortSelect = document.getElementById('sort-certificates');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const resultsCount = document.getElementById('results-count');

    // KPI
    const kpiTotal = document.getElementById('kpiTotal');
    const kpiCourses = document.getElementById('kpiCourses');
    const kpiStudents = document.getElementById('kpiStudents');
    const kpiLastLabel = document.getElementById('kpiLastLabel');
    const kpiLastTime = document.getElementById('kpiLastTime');

    // Modal upload
    const uploadModal = document.getElementById('upload-modal');
    const openUploadModalBtn = document.getElementById('open-upload-modal');
    const emptyUploadBtn = document.getElementById('empty-upload-btn');
    const closeUploadModalBtn = document.getElementById('close-upload-modal');
    const cancelUploadBtn = document.getElementById('cancel-upload');

    const modalCourse = document.getElementById('modal-course');
    const modalStudent = document.getElementById('modal-student');
    const modalNumber = document.getElementById('modal-number');
    const modalIssuedAt = document.getElementById('modal-issued-at');
    const modalStatus = document.getElementById('modal-status');
    const hiddenCertId = document.getElementById('certificate-id');

    // Upload (arquivo)
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('file-input');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeFileBtn = document.getElementById('remove-file');
    const uploadForm = document.getElementById('upload-form');
    // Estado
    let currentFile = null;

    // Endpoints (ajuste se necessário)
    const uploadEndpoint = "<?= site_url('/instructor/dashboard/certificados') ?>";
    const deleteEndpoint = "<?= site_url('/instructor/dashboard/certificados/delete') ?>";

    /* =========================
       INIT
       ========================= */
    document.addEventListener('DOMContentLoaded', function() {
        buildCourseOptions();
        renderKPIs();
        loadCertificatesCards();
        setupEventListeners();
        setupDragAndDrop();
        setTodayAsDefaultDate();
        updateResultsCount(certificatesData.length);
    });

    /* =========================
       UI HELPERS
       ========================= */
    function statusBadgeClass(status) {
        if (status === 'active') return 'status-active';
        if (status === 'inactive') return 'status-inactive';
        return 'status-pending';
    }

    function statusLabel(status) {
        if (status === 'active') return 'Ativo';
        if (status === 'inactive') return 'Inativo';
        return 'Pendente';
    }

    function parseDateAny(s) {
        if (!s) return null;
        const d = new Date(s);
        return isNaN(d.getTime()) ? null : d;
    }

    function formatDateTime(d) {
        if (!d) return '-';
        try {
            return d.toLocaleString('pt-PT', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch {
            return d.toISOString();
        }
    }

    function updateResultsCount(count) {
        resultsCount.textContent = count;
    }

    function setTodayAsDefaultDate() {
        const today = new Date().toISOString().split('T')[0];
        if (modalIssuedAt) modalIssuedAt.value = today;
    }

    function showNotification(message, type) {
        const existing = document.querySelectorAll('.custom-notification');
        existing.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `custom-notification fixed top-4 right-4 z-50 px-6 py-4 rounded-2xl shadow-xl flex items-center space-x-3 animate-slide-in ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} text-xl"></i>
            <span class="font-medium">${message}</span>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-10px)';
            notification.style.transition = 'all .25s ease';
            setTimeout(() => notification.remove(), 250);
        }, 3800);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    /* =========================
       BUILD DROPDOWNS + KPI
       ========================= */
    function buildCourseOptions() {
        // cursos únicos
        const courses = new Map();
        certificatesData.forEach(c => {
            if (c.courseId) courses.set(String(c.courseId), c.courseTitle);
        });

        // filtro
        filterCourse.innerHTML = `<option value="">Todos os cursos</option>`;
        [...courses.entries()]
        .sort((a, b) => (a[1] || '').localeCompare(b[1] || ''))
            .forEach(([id, title]) => {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = title;
                filterCourse.appendChild(opt);
            });

        // modal
        modalCourse.innerHTML = `<option value="">Selecione um curso</option>`;
        [...courses.entries()]
        .sort((a, b) => (a[1] || '').localeCompare(b[1] || ''))
            .forEach(([id, title]) => {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = title;
                modalCourse.appendChild(opt);
            });

        // ao mudar curso no modal, recarrega alunos
        modalCourse.addEventListener('change', () => {
            fillStudentsForCourse(Number(modalCourse.value || 0));
            // ao trocar curso, limpa seleção/arquivo e hidden id
            hiddenCertId.value = '';
            modalNumber.value = '';
            setTodayAsDefaultDate();
            setModalStatus('pending');
            removeFile();
        });
    }

    function renderKPIs() {
        const total = certificatesData.length;
        const coursesSet = new Set(certificatesData.map(c => c.courseId).filter(Boolean));
        const studentsSet = new Set(certificatesData.map(c => c.studentId).filter(Boolean));

        // última atualização: pega o maior entre issuedAt e createdAt
        let last = null;
        certificatesData.forEach(c => {
            const d1 = parseDateAny(c.issuedAt);
            const d2 = parseDateAny(c.createdAt);
            const best = d1 && d2 ? (d1 > d2 ? d1 : d2) : (d1 || d2);
            if (best && (!last || best > last)) last = best;
        });

        kpiTotal.textContent = total;
        kpiCourses.textContent = coursesSet.size;
        kpiStudents.textContent = studentsSet.size;

        if (last) {
            // "Hoje"/"Ontem" simples
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const lastDay = new Date(last.getFullYear(), last.getMonth(), last.getDate());
            const diffDays = Math.round((today - lastDay) / (1000 * 60 * 60 * 24));

            kpiLastLabel.textContent = diffDays === 0 ? 'Hoje' : (diffDays === 1 ? 'Ontem' : formatDateTime(last).split(',')[0]);
            kpiLastTime.textContent = formatDateTime(last).split(',').slice(1).join(',').trim() || '-';
        } else {
            kpiLastLabel.textContent = '-';
            kpiLastTime.textContent = '-';
        }
    }

    /* =========================
       CARDS
       ========================= */
    function loadCertificatesCards(filteredData = null) {
        const dataToDisplay = filteredData || certificatesData;
        certificatesGrid.innerHTML = '';

        if (!dataToDisplay.length) {
            certificatesGrid.classList.add('hidden');
            emptyState.classList.remove('hidden');
            return;
        }

        certificatesGrid.classList.remove('hidden');
        emptyState.classList.add('hidden');

        dataToDisplay.forEach((cert, index) => {
            const card = createCertificateCard(cert, index);
            certificatesGrid.appendChild(card);
        });
    }

    function createCertificateCard(cert, index) {
        const statusClass = statusBadgeClass(cert.status);
        const statusText = statusLabel(cert.status);
        const cardColorClass = cert.status; // active|pending|inactive

        const card = document.createElement('div');
        card.className = `certificate-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden border border-slate-200 dark:border-slate-700 hover:shadow-2xl cursor-pointer ${cardColorClass} animate-slide-in`;
        card.style.animationDelay = `${index * 0.05}s`;
        card.setAttribute('data-id', cert.id);

        const subline = `
            <div class="flex items-center text-slate-600 dark:text-slate-400">
                <i class="bi bi-book me-2 text-sm"></i>
                <p class="text-sm line-clamp-2">
                    ${escapeHtml(cert.courseTitle)}
                    <span class="mx-2">-</span>
                    <span class="font-medium">${escapeHtml(cert.studentName)}</span>
                </p>
            </div>
        `;

        let meta = '';
        if (cert.status === 'pending') {
            meta = `<div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        Disponível para o aluno em: <b>${escapeHtml(String(cert.availableAt ?? '-'))}</b>
                    </div>`;
        } else if (cert.status === 'active') {
            meta = `<div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        Emitido em: <b>${escapeHtml(String(cert.issuedAt ?? '-'))}</b>
                    </div>`;
        } else {
            meta = `<div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        Revogado em: <b>${escapeHtml(String(cert.revokedAt ?? '-'))}</b>
                    </div>`;
        }

        card.innerHTML = `
            <div class="relative overflow-hidden h-48 rounded-t-lg">
                <img src="${cert.image}" alt="${escapeHtml(cert.courseTitle)}" class="w-full h-full object-cover">
                <div class="absolute top-4 right-4">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-2">
                    <h4 class="font-bold text-lg text-slate-900 dark:text-white mb-2 line-clamp-1">
                        ${escapeHtml(cert.courseTitle)} ${cert.number ? `<span class="text-slate-500 dark:text-slate-300 font-semibold">(${escapeHtml(cert.number)})</span>` : ''}
                    </h4>
                    ${subline}
                    ${meta}
                </div>

                <div class="mt-4 flex justify-between gap-3">
                    <button class="edit-btn flex-1 px-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center justify-center space-x-2 focus:ring-2 focus:ring-blue-300 focus:outline-none">
                        <i class="bi bi-upload text-xs"></i>
                        <span>${cert.status === 'active' ? 'Atualizar PDF' : 'Enviar PDF'}</span>
                    </button>

                    <button class="delete-btn flex-1 px-3 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-300 rounded-lg text-sm font-medium flex items-center justify-center space-x-2 focus:ring-2 focus:ring-slate-300 focus:outline-none">
                        <i class="bi bi-trash text-xs"></i>
                        <span>Excluir</span>
                    </button>
                </div>
            </div>
        `;

        // eventos
        card.querySelector('.edit-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            openUploadModalForCertificate(cert.id);
        });

        card.querySelector('.delete-btn').addEventListener('click', (e) => {
            e.stopPropagation();
            confirmDeleteWithSwal(cert.id);
        });

        // clique no card: se tiver pdf, abre (opcional)
        card.addEventListener('click', () => {
            if (cert.enrollmentId && cert.status === 'active') {
                window.open(`<?= site_url('/certificados/download') ?>/${cert.enrollmentId}`, '_blank');
            } else {
                openUploadModalForCertificate(cert.id);
            }
        });

        return card;
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /* =========================
       FILTER / SORT
       ========================= */
    function filterCertificates() {
        const searchTerm = (searchInput.value || '').toLowerCase().trim();
        const courseFilter = filterCourse.value;
        const statusFilter = filterStatus.value;

        let filtered = [...certificatesData];

        if (searchTerm) {
            filtered = filtered.filter(c => {
                const hay = `${c.courseTitle} ${c.studentName} ${c.number}`.toLowerCase();
                return hay.includes(searchTerm);
            });
        }

        if (courseFilter) {
            filtered = filtered.filter(c => String(c.courseId) === String(courseFilter));
        }

        if (statusFilter) {
            filtered = filtered.filter(c => c.status === statusFilter);
        }

        filtered = sortCertificatesList(filtered, sortSelect.value);

        loadCertificatesCards(filtered);
        updateResultsCount(filtered.length);
    }

    function sortCertificates() {
        filterCertificates();
    }

    function sortCertificatesList(list, sortBy) {
        const sorted = [...list];

        const statusOrder = {
            'active': 1,
            'pending': 2,
            'inactive': 3
        };

        switch (sortBy) {
            case 'newest':
                return sorted.sort((a, b) => {
                    const da = parseDateAny(a.issuedAt) || parseDateAny(a.createdAt) || new Date(0);
                    const db = parseDateAny(b.issuedAt) || parseDateAny(b.createdAt) || new Date(0);
                    return db - da;
                });
            case 'oldest':
                return sorted.sort((a, b) => {
                    const da = parseDateAny(a.issuedAt) || parseDateAny(a.createdAt) || new Date(0);
                    const db = parseDateAny(b.issuedAt) || parseDateAny(b.createdAt) || new Date(0);
                    return da - db;
                });
            case 'course':
                return sorted.sort((a, b) => (a.courseTitle || '').localeCompare(b.courseTitle || ''));
            case 'student':
                return sorted.sort((a, b) => (a.studentName || '').localeCompare(b.studentName || ''));
            case 'status':
                return sorted.sort((a, b) => (statusOrder[a.status] || 99) - (statusOrder[b.status] || 99));
            default:
                return sorted;
        }
    }

    function clearFilters() {
        searchInput.value = '';
        filterCourse.value = '';
        filterStatus.value = '';
        sortSelect.value = 'newest';
        loadCertificatesCards();
        updateResultsCount(certificatesData.length);
        showNotification('Filtros limpos com sucesso!', 'success');
    }

    function clearSearch() {
        searchInput.value = '';
        filterCertificates();
    }

    /* =========================
       MODAL UPLOAD
       ========================= */
    function openUploadModal() {
        uploadModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeUploadModal() {
        uploadModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        resetUploadForm();
    }

    function resetUploadForm() {
        hiddenCertId.value = '';
        modalCourse.value = '';
        modalStudent.innerHTML = `<option value="">Selecione o aluno</option>`;
        modalNumber.value = '';
        setTodayAsDefaultDate();
        setModalStatus('pending');
        removeFile();
    }

    function setModalStatus(status) {
        modalStatus.className = `status-badge ${statusBadgeClass(status)}`;
        modalStatus.textContent = statusLabel(status);
    }

    // Preenche alunos do curso (pendentes primeiro)
    function fillStudentsForCourse(courseId) {
        modalStudent.innerHTML = `<option value="">Selecione o aluno</option>`;
        if (!courseId) return;

        const list = certificatesData
            .filter(c => c.courseId === courseId)
            .sort((a, b) => {
                const w = (s) => (s === 'pending' ? 0 : (s === 'active' ? 1 : 2));
                const dw = w(a.status) - w(b.status);
                if (dw !== 0) return dw;
                return (a.studentName || '').localeCompare(b.studentName || '');
            });

        list.forEach(c => {
            const opt = document.createElement('option');
            opt.value = String(c.id); // guardamos o ID do certificado aqui (melhor)
            opt.textContent = `${c.studentName} - ${statusLabel(c.status)}${c.number ? ` - ${c.number}` : ''}`;
            modalStudent.appendChild(opt);
        });
    }

    // Abre modal já em modo "editar/enviar" para um certificado específico
    function openUploadModalForCertificate(certId) {
        const cert = certificatesData.find(c => c.id === Number(certId));
        if (!cert) return;

        // abre modal
        openUploadModal();

        // seta selects
        modalCourse.value = String(cert.courseId || '');
        fillStudentsForCourse(cert.courseId);

        // seleciona o aluno/certificado
        modalStudent.value = String(cert.id);

        // preenche campos
        hiddenCertId.value = String(cert.id);
        modalNumber.value = cert.number || '';
        if (cert.issuedAt) {
            // tenta colocar em yyyy-mm-dd se vier datetime
            const d = parseDateAny(cert.issuedAt);
            if (d) modalIssuedAt.value = d.toISOString().split('T')[0];
        } else {
            setTodayAsDefaultDate();
        }
        setModalStatus(cert.status);
        removeFile();
    }

    // Quando escolhe aluno no modal (o value é o id_certificate)
    modalStudent.addEventListener('change', () => {
        const certId = parseInt(modalStudent.value, 10);

        if (!Number.isFinite(certId)) {
            hiddenCertId.value = '';
            modalNumber.value = '';
            setTodayAsDefaultDate();
            setModalStatus('pending');
            removeFile();
            return;
        }

        const cert = certificatesData.find(c => c.id === certId);
        if (!cert) {
            hiddenCertId.value = '';
            showNotification('Registro do aluno não encontrado.', 'error');
            return;
        }

        hiddenCertId.value = String(cert.id);
        modalNumber.value = cert.number || '';

        const d = cert.issuedAt ? new Date(cert.issuedAt) : null;
        modalIssuedAt.value = d && !isNaN(d.getTime()) ? d.toISOString().split('T')[0] : new Date().toISOString().split('T')[0];

        setModalStatus(cert.status);
        removeFile();
    });

    /* =========================
       FILE UPLOAD (PDF)
       ========================= */
    function setupDragAndDrop() {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('drag-active'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('drag-active'), false);
        });

        dropArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files && files.length) handleFile(files[0]);
        }, false);
    }

    fileInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files.length) handleFile(e.target.files[0]);
    });

    removeFileBtn.addEventListener('click', removeFile);

    function handleFile(file) {
        const validTypes = ['application/pdf'];
        if (!validTypes.includes(file.type)) {
            showNotification('Selecione apenas PDF.', 'error');
            return;
        }
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            showNotification('Arquivo muito grande. Máx: 10MB.', 'error');
            return;
        }
        currentFile = file;
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        filePreview.classList.remove('hidden');
        showNotification('PDF selecionado!', 'success');
    }

    function removeFile() {
        currentFile = null;
        filePreview.classList.add('hidden');
        fileInput.value = '';
    }

    /* =========================
       SUBMIT (FormData)
       ========================= */
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const courseId = Number(modalCourse.value || 0);
        const certId = parseInt(modalStudent.value, 10); // Nota: pega direto do select
        const issuedAt = modalIssuedAt.value || '';
        const number = modalNumber.value || '';

        if (!courseId) return showNotification('Selecione um curso.', 'error');
        if (!Number.isFinite(certId)) return showNotification('Selecione um aluno (registro).', 'error');
        if (!currentFile) return showNotification('Selecione o PDF do certificado.', 'error');

        const cert = certificatesData.find(c => c.id === certId);
        if (!cert || !cert.enrollmentId) {
            return showNotification('Enrollment do aluno não encontrado neste registro.', 'error');
        }

        const fd = new FormData();
        fd.append('enrollment_id', String(cert.enrollmentId)); // Nota: controller
        fd.append('certificate_pdf', currentFile); // Nota: controller

        // (opcional) se você também quer salvar número/data no backend, o controller precisa aceitar:
        fd.append('number_certificate', number);
        fd.append('issued_at_certificate', issuedAt);

        if (csrfName && csrfHash) fd.append(csrfName, csrfHash);

        try {
            const res = await fetch(uploadEndpoint, {
                method: 'POST',
                body: fd,
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const newHash = res.headers.get('X-CSRF-Hash');
            if (newHash) csrfHash = newHash;

            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data?.ok) {
                return showNotification(data?.message || 'Falha ao enviar o PDF.', 'error');
            }

            // atualiza localmente
            cert.status = 'active';
            cert.filePath = data.file_path || cert.filePath;
            cert.issuedAt = issuedAt || cert.issuedAt;

            renderKPIs();
            filterCertificates();
            closeUploadModal();
            showNotification('Certificado enviado com sucesso!', 'success');

        } catch (err) {
            showNotification('Erro de rede ao enviar. Tente novamente.', 'error');
        }
    });

        /* =========================
       DELETE (SWAL)
       ========================= */
    async function confirmDeleteWithSwal(certId) {
        const cert = certificatesData.find(c => c.id === Number(certId));
        if (!cert) return;

        const result = await Swal.fire({
            title: 'Excluir certificado?',
            text: `Excluir o certificado do aluno "${cert.studentName}" no curso "${cert.courseTitle}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Excluir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626'
        });

        if (!result.isConfirmed) return;

        try {
            const payload = {
                id_certificate: Number(cert.id)
            };

            const res = await fetch(deleteEndpoint, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfName && csrfHash ? {
                        [csrfName]: csrfHash
                    } : {})
                },
                body: JSON.stringify(payload)
            });

            const newHash = res.headers.get('X-CSRF-Hash');
            if (newHash) csrfHash = newHash;

            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data?.ok) {
                showNotification(data?.message || 'Falha ao excluir.', 'error');
                return;
            }

            certificatesData = certificatesData.filter(c => c.id !== Number(cert.id));
            renderKPIs();
            buildCourseOptions();
            filterCertificates();
            showNotification('Excluido com sucesso!', 'success');

        } catch {
            showNotification('Erro de rede ao excluir.', 'error');
        }
    }

    /* =========================
       EVENT LISTENERS
       ========================= */
    function setupEventListeners() {
        // abrir modal
        openUploadModalBtn.addEventListener('click', () => {
            resetUploadForm();
            openUploadModal();
        });
        emptyUploadBtn?.addEventListener('click', () => {
            resetUploadForm();
            openUploadModal();
        });

        // fechar modal
        closeUploadModalBtn.addEventListener('click', closeUploadModal);
        cancelUploadBtn.addEventListener('click', closeUploadModal);

        // filtros
        searchInput.addEventListener('input', filterCertificates);
        filterCourse.addEventListener('change', filterCertificates);
        filterStatus.addEventListener('change', filterCertificates);
        sortSelect.addEventListener('change', sortCertificates);
        clearFiltersBtn.addEventListener('click', clearFilters);
        clearSearchBtn?.addEventListener('click', clearSearch);

        // ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!uploadModal.classList.contains('hidden')) closeUploadModal();
            }
        });

        // clique no overlay do upload para fechar
        document.getElementById('modal-overlay')?.addEventListener('click', closeUploadModal);
    }
</script>

<?= $this->endSection() ?>


























