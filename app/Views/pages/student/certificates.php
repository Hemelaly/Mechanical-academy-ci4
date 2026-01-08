<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Certificados<?= $this->endSection() ?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?= $this->section('certificates') ?>
<style>
    /* Estilos para o modal de visualização */
    .modal-enter {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Estilos para os certificados */
    .certificate-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e2e8f0;
    }

    .dark .certificate-card {
        border-color: #334155;
    }

    .certificate-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }

    .dark .certificate-card:hover {
        border-color: #475569;
    }

    /* Badge status */
    .status-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-badge.active {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    
    .status-badge.inactive {
        background-color: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    
    .status-badge.pending {
        background-color: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.2);
    }
</style>

<div class="h-full text-slate-800 dark:text-white transition-colors duration-300">
    <div class="min-h-screen flex flex-col">

        <!-- Conteúdo principal -->
        <main class="flex-grow container mx-auto py-6 px-4">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Seus Certificados de Conclusão</h1>
                <p class="text-slate-600 dark:text-slate-400">Visualize e faça download dos seus certificados de cursos concluídos</p>
            </div>

            <!-- KPIs com a estilização solicitada -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <!-- Total de Certificados -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">
                                Total de Certificados
                            </p>
                            <h3 id="kpi-total" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">0</h3>
                            <div class="flex items-center gap-1">
                                <i class="bi bi-arrow-up-short text-green-500 text-sm flex-shrink-0"></i>
                                <span class="text-green-500 text-sm font-medium truncate">
                                    +2 este mês
                                </span>
                            </div>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <i class="bi bi-award text-blue-600 dark:text-blue-400 text-base sm:text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Concluídos Recentemente -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">
                                Concluídos Recentemente
                            </p>
                            <h3 id="kpi-recent" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">0</h3>
                            <div class="flex items-center gap-1">
                                <i class="bi bi-arrow-up-short text-green-500 text-sm flex-shrink-0"></i>
                                <span class="text-green-500 text-sm font-medium truncate">
                                    Últimos 30 dias
                                </span>
                            </div>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <i class="bi bi-calendar-check text-green-600 dark:text-green-400 text-base sm:text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Downloads Disponíveis -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">
                                Downloads Disponíveis
                            </p>
                            <h3 id="kpi-available" class="text-2xl font-bold text-slate-800 dark:text-white mb-1">0</h3>
                            <div class="flex items-center gap-1">
                                <i class="bi bi-check-circle text-blue-500 text-sm flex-shrink-0"></i>
                                <span class="text-blue-500 text-sm font-medium truncate">
                                    Todos os certificados
                                </span>
                            </div>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <i class="bi bi-cloud-arrow-down text-purple-600 dark:text-purple-400 text-base sm:text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros melhorados -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 mb-8 border border-slate-200 dark:border-slate-700">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Todos os Certificados</h2>
                        <p class="text-slate-600 dark:text-slate-400">Clique em um certificado para visualizá-lo</p>
                    </div>

                    <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                        <!-- Busca -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-slate-400"></i>
                            </div>
                            <input id="search-certificates" type="text" placeholder="Buscar certificados..." class="pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full md:w-64">
                        </div>
                        
                        <!-- Filtro de curso -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-filter text-slate-400"></i>
                            </div>
                            <select id="filter-course" class="pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                                <option value="">Todos os cursos</option>
                            </select>
                        </div>
                        
                        <!-- Botão de ordenar -->
                        <button id="sort-certificates" class="px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-300 rounded-xl font-medium flex items-center space-x-2 shadow-sm hover:shadow-md transition-all duration-300" data-sort="newest">
                            <i class="bi bi-sort-down"></i>
                            <span>Ordenar</span>
                        </button>
                    </div>
                </div>

                <!-- Grid de certificados melhorado -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" id="certificates-grid">
                    <!-- Os certificados serão carregados aqui via JavaScript -->
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para visualização de certificado - Melhorado -->
    <div id="certificate-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto overflow-x-hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative w-full max-w-4xl">
                <!-- Overlay de fundo -->
                <div class="fixed inset-0 bg-black bg-opacity-70"></div>
                
                <!-- Modal content -->
                <div class="relative bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg p-4 md:p-6">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4 md:pb-5">
                        <div>
                            <h3 id="modal-title" class="text-lg font-medium text-slate-900 dark:text-white">
                                Visualização do Certificado
                            </h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Certificado válido e registrado</p>
                        </div>
                        <button type="button" id="close-modal" class="text-slate-500 dark:text-slate-400 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white rounded-lg text-sm w-9 h-9 ms-auto inline-flex justify-center items-center">
                            <i class="bi bi-x-lg text-lg"></i>
                            <span class="sr-only">Fechar modal</span>
                        </button>
                    </div>
                    
                    <!-- Modal body -->
                    <div class="pt-4 md:pt-6">
                        <div class="flex flex-col items-center">
                            <!-- Imagem do certificado no modal -->
                            <div class="relative w-full max-w-3xl mb-6">
                                <div class="absolute -inset-4 bg-gradient-to-r from-blue-500/10 to-purple-500/10 rounded-3xl blur-xl"></div>
                                <img id="modal-image" src="" alt="Certificado" class="relative w-full rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700">
                                <div class="absolute -bottom-4 -right-4 w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                    <i class="bi bi-award text-white text-2xl"></i>
                                </div>
                            </div>

                            <!-- Detalhes do certificado -->
                            <div class="w-full max-w-3xl mb-8">
                                <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-6 pb-3 border-b border-slate-200 dark:border-slate-700">Detalhes do Certificado</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-200 dark:border-slate-600">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center mr-3">
                                                <i class="bi bi-book text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 dark:text-slate-500">Curso</p>
                                                <p id="modal-course" class="font-medium text-slate-900 dark:text-white">-</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-200 dark:border-slate-600">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center mr-3">
                                                <i class="bi bi-calendar-check text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 dark:text-slate-500">Data de Conclusão</p>
                                                <p id="modal-date" class="font-medium text-slate-900 dark:text-white">-</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-200 dark:border-slate-600">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/40 rounded-lg flex items-center justify-center mr-3">
                                                <i class="bi bi-hash text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 dark:text-slate-500">Código do Certificado</p>
                                                <p id="modal-code" class="font-medium text-slate-900 dark:text-white font-mono">-</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-200 dark:border-slate-600">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/40 rounded-lg flex items-center justify-center mr-3">
                                                <i class="bi bi-clock text-amber-600 dark:text-amber-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 dark:text-slate-500">Carga Horária</p>
                                                <p id="modal-hours" class="font-medium text-slate-900 dark:text-white">-</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de ação -->
                            <div class="flex flex-wrap justify-center gap-3 w-full max-w-3xl">
                                <button id="download-pdf" class="inline-flex items-center text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-sm font-medium text-sm px-4 py-2.5 rounded-lg">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>
                                    <span>Baixar PDF</span>
                                </button>

                                <button id="share-certificate" class="inline-flex items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 shadow-sm font-medium text-sm px-4 py-2.5 rounded-lg">
                                    <i class="bi bi-share me-2"></i>
                                    <span>Compartilhar</span>
                                </button>

                                <button id="print-certificate" class="inline-flex items-center text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 shadow-sm font-medium text-sm px-4 py-2.5 rounded-lg">
                                    <i class="bi bi-printer me-2"></i>
                                    <span>Imprimir</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dados dos certificados (DB -> JS)
        const certificatesRaw = <?= json_encode(
                                    $certificates ?? [],
                                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                                ) ?>;
        const fallbackCover = 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1000&q=80';
        const courseImageBase = "<?= base_url('assets/instructor/img/courses') ?>";
        const downloadBase = "<?= site_url('/certificados/download') ?>";

        function formatDateBr(value) {
            const d = new Date(value);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        const certificates = (certificatesRaw || []).map(r => {
            const issuedAt = r.issued_at_certificate ?? '';
            const imageCourse = r.image_course ? `${courseImageBase}/${r.image_course}` : fallbackCover;
            const enrollmentId = Number(r.enrollment_id ?? 0);

            return {
                id: Number(r.id_certificate ?? r.id ?? 0),
                title: r.title_course ?? 'Certificado',
                course: r.title_course ?? '-',
                date: issuedAt ? formatDateBr(issuedAt) : '-',
                hours: r.hours_course ?? '-',
                code: r.number_certificate ?? r.uuid_certificate ?? '-',
                image: imageCourse,
                available: Boolean(r.pdf_path_certificate),
                pdfUrl: (r.pdf_path_certificate && enrollmentId) ? `${downloadBase}/${enrollmentId}` : '#',
                issuedAtRaw: issuedAt
            };
        });

        // Elementos do DOM
        const certificatesGrid = document.getElementById('certificates-grid');
        const modal = document.getElementById('certificate-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');
        const modalCourse = document.getElementById('modal-course');
        const modalDate = document.getElementById('modal-date');
        const modalCode = document.getElementById('modal-code');
        const modalHours = document.getElementById('modal-hours');
        const downloadPdfBtn = document.getElementById('download-pdf');
        const searchInput = document.getElementById('search-certificates');
        const filterCourse = document.getElementById('filter-course');
        const sortSelectBtn = document.getElementById('sort-certificates');
        const kpiTotal = document.getElementById('kpi-total');
        const kpiRecent = document.getElementById('kpi-recent');
        const kpiAvailable = document.getElementById('kpi-available');

        // Certificado atualmente selecionado
        let currentCertificate = null;

        function updateKpis(list) {
            if (!kpiTotal || !kpiRecent || !kpiAvailable) return;

            const total = list.length;
            const now = new Date();
            const recent = list.filter(c => {
                if (!c.issuedAtRaw) return false;
                const d = new Date(c.issuedAtRaw);
                if (Number.isNaN(d.getTime())) return false;
                const diffDays = (now - d) / (1000 * 60 * 60 * 24);
                return diffDays <= 30;
            }).length;
            const available = list.filter(c => c.available).length;

            kpiTotal.textContent = String(total);
            kpiRecent.textContent = String(recent);
            kpiAvailable.textContent = String(available);
        }

        function buildCourseOptions() {
            if (!filterCourse) return;
            const courses = Array.from(new Set(certificates.map(c => c.course).filter(Boolean))).sort((a, b) => a.localeCompare(b));

            filterCourse.innerHTML = '<option value="">Todos os cursos</option>';
            courses.forEach(title => {
                const opt = document.createElement('option');
                opt.value = title;
                opt.textContent = title;
                filterCourse.appendChild(opt);
            });
        }

        function applyFiltersAndSort() {
            let list = [...certificates];

            const searchTerm = (searchInput?.value || '').toLowerCase().trim();
            if (searchTerm) {
                list = list.filter(c => {
                    const hay = `${c.title} ${c.course} ${c.code}`.toLowerCase();
                    return hay.includes(searchTerm);
                });
            }

            const courseFilter = filterCourse?.value || '';
            if (courseFilter) {
                list = list.filter(c => c.course === courseFilter);
            }

            const sortMode = sortSelectBtn?.dataset.sort || 'newest';
            if (sortMode === 'oldest') {
                list.sort((a, b) => new Date(a.issuedAtRaw || 0) - new Date(b.issuedAtRaw || 0));
            } else if (sortMode === 'title') {
                list.sort((a, b) => (a.course || '').localeCompare(b.course || ''));
            } else {
                list.sort((a, b) => new Date(b.issuedAtRaw || 0) - new Date(a.issuedAtRaw || 0));
            }

            loadCertificates(list);
            updateKpis(list);
        }

        function cycleSortMode() {
            const current = sortSelectBtn?.dataset.sort || 'newest';
            const next = current === 'newest' ? 'oldest' : (current === 'oldest' ? 'title' : 'newest');
            if (sortSelectBtn) {
                sortSelectBtn.dataset.sort = next;
                const label = next === 'newest' ? 'Mais recentes' : (next === 'oldest' ? 'Mais antigos' : 'Curso A-Z');
                const span = sortSelectBtn.querySelector('span');
                if (span) span.textContent = label;
            }
            applyFiltersAndSort();
        }

        function statusLabel(cert) {
            return cert.available ? 'Ativo' : 'Pendente';
        }

        // Carregar certificados na grid
        function loadCertificates(list) {
            certificatesGrid.innerHTML = '';

            if (!list.length) {
                certificatesGrid.innerHTML = '<div class="col-span-full text-center text-slate-500 dark:text-slate-400">Nenhum certificado encontrado.</div>';
                return;
            }

            list.forEach(cert => {
                const card = document.createElement('div');
                card.className = 'certificate-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl cursor-pointer';
                card.setAttribute('data-id', cert.id);

                card.innerHTML = `
                    <div class="relative overflow-hidden h-48">
                        <img src="${cert.image}" alt="${cert.title}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                        <div class="absolute top-4 right-4">
                            <span class="status-badge ${cert.available ? 'active' : 'pending'}">${statusLabel(cert)}</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                            <h4 class="font-bold text-xl text-white mb-1 line-clamp-1">${cert.title}</h4>
                            <p class="text-blue-200 text-sm">${cert.hours}</p>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="bi bi-book text-slate-600 dark:text-slate-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-500 dark:text-slate-500 mb-1">Curso</p>
                                    <p class="font-medium text-slate-900 dark:text-white text-sm line-clamp-2">${cert.course}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between gap-3">
                            <button class="view-btn flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-300 flex items-center justify-center space-x-2 shadow-sm hover:shadow-md focus:ring-2 focus:ring-blue-300 focus:outline-none">
                                <i class="bi bi-eye text-sm"></i>
                                <span class="font-medium text-sm">Visualizar</span>
                            </button>
                            <button class="download-btn flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-300 rounded-lg transition-all duration-300 flex items-center justify-center space-x-2 shadow-sm hover:shadow-md focus:ring-2 focus:ring-slate-300 focus:outline-none">
                                <i class="bi bi-download text-sm"></i>
                                <span class="font-medium text-sm">PDF</span>
                            </button>
                        </div>
                    </div>
                `;

                certificatesGrid.appendChild(card);

                // Adicionar evento para visualizar
                const viewBtn = card.querySelector('.view-btn');
                viewBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openCertificateModal(cert);
                });

                // Adicionar evento para download
                const downloadBtn = card.querySelector('.download-btn');
                downloadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    downloadCertificatePdf(cert);
                });
                if (!cert.available) {
                    downloadBtn.disabled = true;
                    downloadBtn.classList.add('opacity-60', 'cursor-not-allowed');
                }

                // Adicionar evento para abrir modal ao clicar no card
                card.addEventListener('click', () => {
                    openCertificateModal(cert);
                });
            });
        }

        // Abrir modal com certificado
        function openCertificateModal(certificate) {
            currentCertificate = certificate;

            modalImage.src = certificate.image;
            modalTitle.textContent = certificate.title;
            modalCourse.textContent = certificate.course;
            modalDate.textContent = certificate.date;
            modalCode.textContent = certificate.code;
            modalHours.textContent = certificate.hours;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Fechar modal
        function closeCertificateModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Baixar PDF do certificado
        function downloadCertificatePdf(certificate) {
            if (!certificate.pdfUrl || certificate.pdfUrl === '#') {
                alert('PDF do certificado nÃ£o estÃ¡ disponÃ­vel.');
                return;
            }

            const link = document.createElement('a');
            link.href = certificate.pdfUrl;
            link.download = `certificado-${certificate.code}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Compartilhar certificado
        function shareCertificate() {
            if (navigator.share && currentCertificate) {
                navigator.share({
                        title: `Meu certificado: ${currentCertificate.title}`,
                        text: `Concluí o curso ${currentCertificate.course} e obtive este certificado!`,
                        url: window.location.href,
                    })
                    .catch(console.error);
            } else {
                alert(`Compartilhe o link para o certificado: ${currentCertificate ? currentCertificate.title : 'Certificado'}`);
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            buildCourseOptions();
            if (sortSelectBtn) {
                const span = sortSelectBtn.querySelector('span');
                if (span) span.textContent = 'Mais recentes';
            }
            applyFiltersAndSort();

            // Eventos do modal
            closeModalBtn.addEventListener('click', closeCertificateModal);
            
            // Fechar modal ao clicar no overlay
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('fixed')) {
                    closeCertificateModal();
                }
            });

            // Eventos dos botões do modal
            downloadPdfBtn.addEventListener('click', () => {
                if (currentCertificate) {
                    downloadCertificatePdf(currentCertificate);
                }
            });

            document.getElementById('share-certificate').addEventListener('click', shareCertificate);

            document.getElementById('print-certificate').addEventListener('click', () => {
                window.print();
            });

            searchInput?.addEventListener('input', applyFiltersAndSort);
            filterCourse?.addEventListener('change', applyFiltersAndSort);
            sortSelectBtn?.addEventListener('click', cycleSortMode);

            // Evento de tecla ESC para fechar modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeCertificateModal();
                }
            });
        });
    </script>
</div>

<?= $this->endSection() ?>


