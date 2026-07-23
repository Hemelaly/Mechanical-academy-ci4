<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Certificados<?= $this->endSection() ?>

<?= $this->section('certificates') ?>
<style>
    .certificate-card {
        transition: transform 0.2s ease, border-color 0.2s ease;
        border: 1px solid #e2e8f0;
        background: #fff;
    }
    html.dark .certificate-card {
        border-color: rgba(255,255,255,0.1);
        background: #11151c;
    }
    .certificate-card:hover {
        transform: translateY(-2px);
        border-color: #93c5fd;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 0.375rem;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .status-badge.active {
        background-color: rgba(16, 185, 129, 0.15);
        color: #059669;
    }
    html.dark .status-badge.active { color: #34d399; }
    .status-badge.pending {
        background-color: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }
    html.dark .status-badge.pending { color: #fbbf24; }

    /* Pré-visualização do certificado (sempre claro — documento) */
    .cert-preview-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 1.414 / 1;
        overflow: hidden;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    html.dark .cert-preview-frame {
        border-bottom-color: rgba(255,255,255,0.08);
        background: #0b1220;
    }
    .cert-preview-frame img,
    .cert-preview-frame iframe,
    .cert-preview-frame object {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
        pointer-events: none;
    }
    .cert-preview-frame img {
        object-fit: cover;
        object-position: top center;
    }
    .cert-preview-frame iframe,
    .cert-preview-frame object {
        width: 140%;
        height: 140%;
        left: -20%;
        top: -8%;
        transform: scale(1);
    }

    #certificate-modal .modal-panel {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #e2e8f0;
    }
    html.dark #certificate-modal .modal-panel {
        background: #11151c;
        color: #f3f6fb;
        border-color: rgba(255,255,255,0.1);
    }
    #certificate-modal .meta-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #0f172a;
    }
    html.dark #certificate-modal .meta-card {
        background: #0c1017;
        border-color: rgba(255,255,255,0.1);
        color: #f3f6fb;
    }
    #certificate-modal .meta-card .meta-label {
        color: #64748b;
    }
    html.dark #certificate-modal .meta-card .meta-label {
        color: rgba(243,246,251,0.5);
    }
    #certificate-modal .meta-card .meta-value {
        color: #0f172a;
        font-weight: 600;
    }
    html.dark #certificate-modal .meta-card .meta-value {
        color: #f3f6fb;
    }

    #modal-pdf-wrap {
        background: #e2e8f0;
        min-height: min(78vh, 820px);
    }
    html.dark #modal-pdf-wrap {
        background: #0b1220;
    }
    #modal-pdf {
        width: 100%;
        height: min(78vh, 820px);
        min-height: 520px;
        background: #fff;
    }
</style>

<div class="h-full text-slate-800 dark:text-slate-100 transition-colors duration-300">
    <div class="min-w-0 flex flex-col">

        <main class="flex-grow w-full max-w-7xl mx-auto py-6 px-4 sm:px-6">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white mb-1">Certificados</h1>
                <p class="text-sm text-slate-500 dark:text-white/45">Visualize e descarregue os seus certificados</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                <div class="rounded-md border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-[#11151c]">
                    <p class="text-xs text-slate-500 dark:text-white/45">Total</p>
                    <h3 id="kpi-total" class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">0</h3>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-[#11151c]">
                    <p class="text-xs text-slate-500 dark:text-white/45">Últimos 30 dias</p>
                    <h3 id="kpi-recent" class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">0</h3>
                </div>
                <div class="rounded-md border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-[#11151c]">
                    <p class="text-xs text-slate-500 dark:text-white/45">Disponíveis</p>
                    <h3 id="kpi-available" class="mt-1 text-2xl font-semibold text-slate-900 dark:text-white">0</h3>
                </div>
            </div>

            <div class="rounded-md border border-slate-200 bg-white p-4 sm:p-5 dark:border-white/10 dark:bg-[#11151c]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3 mb-5">
                    <h2 class="text-base font-semibold text-slate-900 dark:text-white">Todos os certificados</h2>
                    <div class="flex flex-wrap gap-2 w-full md:w-auto">
                        <div class="relative flex-1 md:flex-none">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input id="search-certificates" type="text" placeholder="Buscar..."
                                class="w-full md:w-56 rounded-md border border-slate-300 bg-slate-50 py-2 pl-9 pr-3 text-sm text-slate-800 dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
                        </div>
                        <select id="filter-course"
                            class="rounded-md border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-800 dark:border-white/10 dark:bg-[#0c1017] dark:text-white">
                            <option value="">Todos os cursos</option>
                        </select>
                        <button id="sort-certificates" type="button" data-sort="newest"
                            class="rounded-md border border-slate-300 bg-slate-50 px-3 py-2 text-sm text-slate-700 dark:border-white/10 dark:bg-[#0c1017] dark:text-slate-200">
                            <i class="bi bi-sort-down"></i>
                            <span>Mais recentes</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="certificates-grid"></div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="certificate-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-start justify-center p-3 sm:p-6">
            <div class="fixed inset-0 bg-black/70" data-close-modal></div>

            <div class="relative modal-panel w-full max-w-6xl rounded-md shadow-xl my-4 sm:my-6">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 dark:border-white/10 sm:px-5">
                    <div class="min-w-0">
                        <h3 id="modal-title" class="truncate text-base font-semibold text-slate-900 dark:text-white">Visualização</h3>
                        <p class="text-xs text-slate-500 dark:text-white/45">Certificado válido e registado</p>
                    </div>
                    <button type="button" id="close-modal"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 dark:text-white/60 dark:hover:bg-white/5">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="p-3 sm:p-5">
                    <div id="modal-pdf-wrap" class="hidden overflow-hidden rounded-md border border-slate-200 dark:border-white/10 mb-4">
                        <iframe id="modal-pdf" title="Certificado PDF"></iframe>
                    </div>
                    <div id="modal-image-wrap" class="relative mb-4">
                        <img id="modal-image" src="" alt="Pré-visualização do certificado"
                            class="w-full rounded-md border border-slate-200 dark:border-white/10 bg-white object-contain max-h-[50vh]">
                        <p id="modal-pending-note" class="mt-2 text-center text-sm text-amber-600 dark:text-amber-400 hidden">PDF ainda não disponível.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-4 no-print">
                        <div class="meta-card rounded-md p-3">
                            <p class="meta-label text-xs mb-0.5">Curso</p>
                            <p id="modal-course" class="meta-value text-sm break-words">-</p>
                        </div>
                        <div class="meta-card rounded-md p-3">
                            <p class="meta-label text-xs mb-0.5">Data de Conclusão</p>
                            <p id="modal-date" class="meta-value text-sm">-</p>
                        </div>
                        <div class="meta-card rounded-md p-3">
                            <p class="meta-label text-xs mb-0.5">Código</p>
                            <p id="modal-code" class="meta-value text-sm font-mono break-all">-</p>
                        </div>
                        <div class="meta-card rounded-md p-3">
                            <p class="meta-label text-xs mb-0.5">Carga Horária</p>
                            <p id="modal-hours" class="meta-value text-sm">-</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center gap-2 no-print">
                        <button id="download-pdf" type="button"
                            class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-500">
                            <i class="bi bi-file-earmark-pdf"></i> Baixar PDF
                        </button>
                        <button id="share-certificate" type="button"
                            class="inline-flex items-center gap-2 rounded-md bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-emerald-500">
                            <i class="bi bi-share"></i> Compartilhar
                        </button>
                        <button id="print-certificate" type="button"
                            class="inline-flex items-center gap-2 rounded-md bg-violet-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-violet-500">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const certificatesRaw = <?= json_encode(
                                    $certificates ?? [],
                                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                                ) ?>;
        const certificateBg = "<?= base_url('assets/certificado/certificado-bg.png') ?>";
        const downloadBase = "<?= site_url('/certificados/download') ?>";
        const previewBase = "<?= site_url('/certificados/preview') ?>";

        function formatDateBr(value) {
            const d = new Date(value);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        const certificates = (certificatesRaw || []).map(r => {
            const issuedAt = r.issued_at_certificate ?? '';
            const enrollmentId = Number(r.enrollment_id ?? 0);
            const hasPdf = Boolean(r.pdf_path_certificate) && enrollmentId > 0;

            return {
                id: Number(r.id_certificate ?? r.id ?? 0),
                title: r.title_course ?? 'Certificado',
                course: r.title_course ?? '-',
                date: issuedAt ? formatDateBr(issuedAt) : '-',
                hours: r.hours_course ?? '-',
                code: r.number_certificate ?? r.uuid_certificate ?? '-',
                image: certificateBg,
                available: hasPdf,
                pdfUrl: hasPdf ? `${downloadBase}/${enrollmentId}` : '#',
                previewUrl: hasPdf ? `${previewBase}/${enrollmentId}` : '',
                issuedAtRaw: issuedAt
            };
        });

        const certificatesGrid = document.getElementById('certificates-grid');
        const modal = document.getElementById('certificate-modal');
        const closeModalBtn = document.getElementById('close-modal');
        const modalImage = document.getElementById('modal-image');
        const modalImageWrap = document.getElementById('modal-image-wrap');
        const modalPdfWrap = document.getElementById('modal-pdf-wrap');
        const modalPdf = document.getElementById('modal-pdf');
        const modalPendingNote = document.getElementById('modal-pending-note');
        const modalTitle = document.getElementById('modal-title');
        const modalCourse = document.getElementById('modal-course');
        const modalDate = document.getElementById('modal-date');
        const modalCode = document.getElementById('modal-code');
        const modalHours = document.getElementById('modal-hours');
        const downloadPdfBtn = document.getElementById('download-pdf');
        const printCertificateBtn = document.getElementById('print-certificate');
        const searchInput = document.getElementById('search-certificates');
        const filterCourse = document.getElementById('filter-course');
        const sortSelectBtn = document.getElementById('sort-certificates');
        const kpiTotal = document.getElementById('kpi-total');
        const kpiRecent = document.getElementById('kpi-recent');
        const kpiAvailable = document.getElementById('kpi-available');

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

        function loadCertificates(list) {
            certificatesGrid.innerHTML = '';

            if (!list.length) {
                certificatesGrid.innerHTML = '<div class="col-span-full py-10 text-center text-sm text-slate-500 dark:text-white/45">Nenhum certificado encontrado.</div>';
                return;
            }

            list.forEach(cert => {
                const card = document.createElement('div');
                card.className = 'certificate-card rounded-md overflow-hidden cursor-pointer';
                card.setAttribute('data-id', cert.id);

                const previewInner = cert.available && cert.previewUrl
                    ? `<iframe src="${escapeHtml(cert.previewUrl)}#toolbar=0&navpanes=0&scrollbar=0&view=FitH" title="Pré-visualização" loading="lazy"></iframe>
                       <img src="${escapeHtml(certificateBg)}" alt="" class="opacity-0" aria-hidden="true">`
                    : `<img src="${escapeHtml(certificateBg)}" alt="Modelo de certificado">`;

                card.innerHTML = `
                    <div class="cert-preview-frame">
                        ${previewInner}
                        <div class="absolute top-3 right-3 z-10">
                            <span class="status-badge ${cert.available ? 'active' : 'pending'}">${statusLabel(cert)}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-slate-500 dark:text-white/45 mb-1">Curso</p>
                        <h4 class="text-sm font-semibold text-slate-900 dark:text-white line-clamp-2 mb-3">${escapeHtml(cert.course)}</h4>
                        <div class="flex gap-2">
                            <button type="button" class="view-btn flex-1 rounded-md bg-blue-600 hover:bg-blue-500 text-white px-3 py-2 text-sm font-medium">
                                <i class="bi bi-eye"></i> Visualizar
                            </button>
                            <button type="button" class="download-btn flex-1 rounded-md border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-800 dark:border-white/10 dark:bg-[#0c1017] dark:text-slate-200 dark:hover:bg-white/5 px-3 py-2 text-sm font-medium">
                                <i class="bi bi-download"></i> PDF
                            </button>
                        </div>
                    </div>
                `;

                certificatesGrid.appendChild(card);

                card.querySelector('.view-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    openCertificateModal(cert);
                });

                const downloadBtn = card.querySelector('.download-btn');
                downloadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    downloadCertificatePdf(cert);
                });
                if (!cert.available) {
                    downloadBtn.disabled = true;
                    downloadBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }

                card.addEventListener('click', () => openCertificateModal(cert));
            });
        }

        function openCertificateModal(certificate) {
            currentCertificate = certificate;

            modalTitle.textContent = certificate.title;
            modalCourse.textContent = certificate.course;
            modalDate.textContent = certificate.date;
            modalCode.textContent = certificate.code;
            modalHours.textContent = certificate.hours;
            modalImage.src = certificate.image || certificateBg;

            const hasPdf = Boolean(certificate.previewUrl);
            if (modalPdfWrap && modalPdf && modalImageWrap) {
                if (hasPdf) {
                    modalPdf.src = certificate.previewUrl + '#toolbar=0&navpanes=0&view=FitH';
                    modalPdfWrap.classList.remove('hidden');
                    modalImageWrap.classList.add('hidden');
                } else {
                    modalPdf.removeAttribute('src');
                    modalPdfWrap.classList.add('hidden');
                    modalImageWrap.classList.remove('hidden');
                }
            }
            if (modalPendingNote) modalPendingNote.classList.toggle('hidden', hasPdf);
            if (downloadPdfBtn) {
                downloadPdfBtn.disabled = !hasPdf;
                downloadPdfBtn.classList.toggle('opacity-50', !hasPdf);
            }
            if (printCertificateBtn) {
                printCertificateBtn.disabled = !hasPdf;
                printCertificateBtn.classList.toggle('opacity-50', !hasPdf);
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCertificateModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (modalPdf) modalPdf.removeAttribute('src');
        }

        function downloadCertificatePdf(certificate) {
            if (!certificate.pdfUrl || certificate.pdfUrl === '#') {
                alert('PDF do certificado não está disponível.');
                return;
            }
            const link = document.createElement('a');
            link.href = certificate.pdfUrl;
            link.download = `certificado-${certificate.code}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function printCertificatePdf(certificate) {
            if (!certificate?.previewUrl) {
                alert('PDF do certificado não está disponível para impressão.');
                return;
            }
            const printWindow = window.open(certificate.previewUrl, '_blank');
            if (!printWindow) {
                alert('Permita pop-ups para imprimir o certificado.');
                return;
            }
            const tryPrint = () => {
                try {
                    printWindow.focus();
                    printWindow.print();
                } catch (err) {
                    console.error(err);
                }
            };
            printWindow.addEventListener('load', () => setTimeout(tryPrint, 400));
            setTimeout(tryPrint, 1200);
        }

        function shareCertificate() {
            if (navigator.share && currentCertificate) {
                navigator.share({
                    title: `Meu certificado: ${currentCertificate.title}`,
                    text: `Concluí o curso ${currentCertificate.course} e obtive este certificado!`,
                    url: currentCertificate.previewUrl || window.location.href,
                }).catch(console.error);
            } else {
                alert(`Compartilhe o link para o certificado: ${currentCertificate ? currentCertificate.title : 'Certificado'}`);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            buildCourseOptions();
            applyFiltersAndSort();

            closeModalBtn?.addEventListener('click', closeCertificateModal);
            modal?.querySelector('[data-close-modal]')?.addEventListener('click', closeCertificateModal);

            downloadPdfBtn?.addEventListener('click', () => {
                if (currentCertificate) downloadCertificatePdf(currentCertificate);
            });
            document.getElementById('share-certificate')?.addEventListener('click', shareCertificate);
            printCertificateBtn?.addEventListener('click', () => {
                if (currentCertificate) printCertificatePdf(currentCertificate);
            });

            searchInput?.addEventListener('input', applyFiltersAndSort);
            filterCourse?.addEventListener('change', applyFiltersAndSort);
            sortSelectBtn?.addEventListener('click', cycleSortMode);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeCertificateModal();
                }
            });
        });
    </script>
</div>

<?= $this->endSection() ?>


