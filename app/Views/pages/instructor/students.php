<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Estudantes<?= $this->endSection() ?>

<?= $this->section('students') ?>
<div class="min-w-0 space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Estudantes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Acompanhe alunos, acessos e pagamentos com dados em tempo real.</p>
        </div>
    </div>

    <div class="min-w-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-white">Pedidos de inscrição</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Aprove ou rejeite solicitacoes pendentes.</p>
        </div>
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label for="pending-search" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Buscar</label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-slate-400"></i>
                        </div>
                        <input id="pending-search" type="text" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 pl-10 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="Aluno, email ou curso">
                    </div>
                </div>
                <div>
                    <label for="pending-per-page" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Por página</label>
                    <select id="pending-per-page" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="relative w-full max-w-full overflow-x-auto">
            <table
                id="instructor-pending-students-table"
                class="w-full min-w-full text-left text-sm text-slate-500 dark:text-slate-400">
                <thead class="text-xs uppercase text-slate-600 bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Aluno</th>
                        <th scope="col" class="px-6 py-3">Curso</th>
                        <th scope="col" class="px-6 py-3">Comprovativo</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody id="pending-table-body"></tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <div id="pending-summary">Carregando...</div>
            <div id="pending-pagination" class="flex flex-wrap gap-2"></div>
        </div>
    </div>

    <div class="min-w-0 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl">
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg sm:text-xl font-semibold text-slate-900 dark:text-white">Alunos inscritos</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Acompanhe o progresso e controle o acesso.</p>
        </div>
        <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label for="enroll-search" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Buscar</label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-slate-400"></i>
                        </div>
                        <input id="enroll-search" type="text" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 pl-10 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="Aluno, email ou curso">
                    </div>
                </div>
                <div>
                    <label for="enroll-status" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Status</label>
                    <select id="enroll-status" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="">Todos</option>
                        <option value="ativa">Ativo</option>
                        <option value="cancelada">Bloqueado</option>
                    </select>
                </div>
                <div>
                    <label for="enroll-per-page" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Por página</label>
                    <select id="enroll-per-page" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="relative w-full max-w-full overflow-x-auto">
            <table
                id="instructor-enrollments-table"
                class="w-full min-w-full text-left text-sm text-slate-500 dark:text-slate-400">
                <thead class="text-xs uppercase text-slate-600 bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Aluno</th>
                        <th scope="col" class="px-6 py-3">Curso</th>
                        <th scope="col" class="px-6 py-3">Progresso</th>
                        <th scope="col" class="px-6 py-3">Último acesso</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody id="enrollments-table-body"></tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <div id="enrollments-summary">Carregando...</div>
            <div id="enrollments-pagination" class="flex flex-wrap gap-2"></div>
        </div>
    </div>
</div>

<div id="proofModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-2xl w-full">
        <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-700">
            <h4 class="text-sm font-semibold text-slate-800 dark:text-white">Comprovativo</h4>
            <button type="button" id="proofModalClose" class="text-slate-500 hover:text-slate-700 dark:text-slate-300">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-4">
            <img id="proofModalImg" src="" alt="Comprovativo" class="w-full max-h-[70vh] object-contain rounded-xl bg-slate-100 dark:bg-slate-900">
            <p id="proofModalEmpty" class="hidden text-sm text-slate-500 dark:text-slate-400 text-center">Comprovativo não disponível.</p>
        </div>
    </div>
</div>

<script>
    (function () {
        const enrollEndpoint = <?= json_encode(site_url('instructor/dashboard/meus_estudantes/data')) ?>;
        const pendingEndpoint = <?= json_encode(site_url('instructor/dashboard/meus_estudantes/pending')) ?>;
        const toggleEndpointBase = <?= json_encode(site_url('instructor/dashboard/meus_estudantes/toggle')) ?>;
        const approveBase = <?= json_encode(site_url('instructor/dashboard/meus_estudantes')) ?>;
        const csrfName = <?= json_encode(csrf_token()) ?>;
        let csrfHash = <?= json_encode(csrf_hash()) ?>;

        const enrollTable = document.getElementById('instructor-enrollments-table');
        const getEnrollBody = () => enrollTable?.querySelector('tbody');
        const enrollSummary = document.getElementById('enrollments-summary');
        const enrollPagination = document.getElementById('enrollments-pagination');
        const enrollSearch = document.getElementById('enroll-search');
        const enrollStatus = document.getElementById('enroll-status');
        const enrollPerPage = document.getElementById('enroll-per-page');

        const pendingTable = document.getElementById('instructor-pending-students-table');
        const getPendingBody = () => pendingTable?.querySelector('tbody');
        const pendingSummary = document.getElementById('pending-summary');
        const pendingPagination = document.getElementById('pending-pagination');
        const pendingSearch = document.getElementById('pending-search');
        const pendingPerPage = document.getElementById('pending-per-page');

        const proofModal = document.getElementById('proofModal');
        const proofModalImg = document.getElementById('proofModalImg');
        const proofModalEmpty = document.getElementById('proofModalEmpty');
        const proofModalClose = document.getElementById('proofModalClose');

        const stateEnroll = { page: 1, per_page: Number(enrollPerPage.value || 10), q: '', status: '' };
        const statePending = { page: 1, per_page: Number(pendingPerPage.value || 10), q: '' };

        const escapeHtml = (value) => {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const formatLastActivity = (value) => {
            if (!value) return '-';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '-';
            const now = new Date();
            const diffMs = Math.max(0, now - date);
            const diffMin = Math.floor(diffMs / 60000);
            if (diffMin < 60) return `${Math.max(1, diffMin)} min`;
            const today = now.toISOString().slice(0, 10);
            const yesterday = new Date(now.getTime() - 86400000).toISOString().slice(0, 10);
            const day = date.toISOString().slice(0, 10);
            const time = date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            if (day === today) return `Hoje ${time}`;
            if (day === yesterday) return `Ontem ${time}`;
            return date.toLocaleString('pt-BR');
        };

        const statusBadge = (status) => {
            const normalized = (status || '').toString().toLowerCase();
            const isActive = normalized === 'ativa';
            const classes = isActive
                ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
                : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200';
            const label = isActive ? 'Ativa' : 'Bloqueada';
            return `<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ${classes}">${label}</span>`;
        };

        const postAction = (url, payload) => {
            payload[csrfName] = csrfHash;
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(payload).toString()
            }).then(async (res) => {
                let data = {};
                try { data = await res.json(); } catch (e) { data = {}; }
                if (data.csrf) csrfHash = data.csrf;
                if (!res.ok) throw data;
                return data;
            });
        };

        const renderPagination = (paginationData, container, onPage) => {
            const totalPages = paginationData.total_pages || 1;
            const currentPage = paginationData.page || 1;
            const pages = [];
            const maxButtons = 5;
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPages, start + maxButtons - 1);
            if (end - start < maxButtons - 1) start = Math.max(1, end - maxButtons + 1);
            for (let i = start; i <= end; i += 1) pages.push(i);

            const button = (label, page, disabled, active) => {
                const base = 'rounded-lg border px-3 py-1.5 text-sm font-medium transition';
                const activeClass = active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-600';
                const disabledClass = disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
                return `<button class="${base} ${activeClass} ${disabledClass}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;
            };

            container.innerHTML = [
                button('Anterior', currentPage - 1, currentPage === 1, false),
                ...pages.map(page => button(page, page, false, page === currentPage)),
                button('Próxima', currentPage + 1, currentPage === totalPages, false)
            ].join('');

            container.onclick = (event) => {
                const btn = event.target.closest('button[data-page]');
                if (!btn) return;
                const nextPage = Number(btn.dataset.page);
                if (!Number.isFinite(nextPage) || nextPage < 1) return;
                onPage(nextPage);
            };
        };

        const renderSummary = (paginationData, el, label) => {
            const total = paginationData.total || 0;
            const page = paginationData.page || 1;
            const perPage = paginationData.per_page || 10;
            const start = total === 0 ? 0 : (page - 1) * perPage + 1;
            const end = Math.min(total, page * perPage);
            el.textContent = `Mostrando ${start}-${end} de ${total} ${label}`;
        };

        const loadEnrollments = () => {
            const enrollBody = getEnrollBody();
            if (!enrollBody) return;
            enrollBody.innerHTML = '<tr><td colspan="6" class="px-6 py-6 text-center text-slate-500">Carregando...</td></tr>';
            const url = new URL(enrollEndpoint, window.location.origin);
            url.searchParams.set('page', stateEnroll.page);
            url.searchParams.set('per_page', stateEnroll.per_page);
            if (stateEnroll.q) url.searchParams.set('q', stateEnroll.q);
            if (stateEnroll.status) url.searchParams.set('status', stateEnroll.status);

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    const items = data.items || [];
                    if (!items.length) {
                        enrollBody.innerHTML = '<tr><td colspan="6" class="px-6 py-6 text-center text-slate-500">Nenhum aluno encontrado.</td></tr>';
                    } else {
                        enrollBody.innerHTML = items.map(item => {
                            const progress = Math.max(0, Math.min(100, Number(item.progress_enrollment || 0)));
                            const status = item.status_enrollment || 'ativa';
                            const isActive = (status || '').toString().toLowerCase() === 'ativa';
                            return `
                                <tr class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                    <td class="px-6 py-4">
                                        <div class="min-w-0">
                                            <div class="font-medium text-slate-900 dark:text-white truncate">${escapeHtml(item.name_student)}</div>
                                            <div class="text-xs text-slate-500 truncate">${escapeHtml(item.email_student)}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">${escapeHtml(item.title_course)}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-24 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: ${progress}%"></div>
                                            </div>
                                            <span class="text-xs font-medium">${progress}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">${formatLastActivity(item.last_activity || item.last_enrollment_update)}</td>
                                    <td class="px-6 py-4">${statusBadge(status)}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button type="button" class="toggle-access inline-flex items-center gap-2 px-3 py-1.5 ${isActive ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'} text-white font-medium rounded-lg text-xs" data-id="${item.id_enrollment}" data-status="${status}">
                                            <i class="bi ${isActive ? 'bi-lock' : 'bi-unlock'}"></i>
                                            ${isActive ? 'Bloquear' : 'Permitir'}
                                        </button>
                                    </td>
                                </tr>`;
                        }).join('');
                    }
                    renderSummary(data.pagination || {}, enrollSummary, 'alunos');
                    renderPagination(data.pagination || {}, enrollPagination, (next) => { stateEnroll.page = next; loadEnrollments(); });
                })
                .catch(() => {
                    const errorBody = getEnrollBody();
                    if (errorBody) {
                        errorBody.innerHTML = '<tr><td colspan="6" class="px-6 py-6 text-center text-slate-500">Erro ao carregar alunos.</td></tr>';
                    }
                    enrollSummary.textContent = 'Erro ao carregar alunos.';
                    enrollPagination.innerHTML = '';
                });
        };

        const loadPending = () => {
            const pendingBody = getPendingBody();
            if (!pendingBody) return;
            pendingBody.innerHTML = '<tr><td colspan="5" class="px-6 py-6 text-center text-slate-500">Carregando...</td></tr>';
            const url = new URL(pendingEndpoint, window.location.origin);
            url.searchParams.set('page', statePending.page);
            url.searchParams.set('per_page', statePending.per_page);
            if (statePending.q) url.searchParams.set('q', statePending.q);

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    const items = data.items || [];
                    if (!items.length) {
                        pendingBody.innerHTML = '<tr><td colspan="5" class="px-6 py-6 text-center text-slate-500">Nenhum pedido pendente.</td></tr>';
                    } else {
                        pendingBody.innerHTML = items.map(item => {
                            return `
                                <tr class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                                    <td class="px-6 py-4">
                                        <div class="min-w-0">
                                            <div class="font-medium text-slate-900 dark:text-white truncate">${escapeHtml(item.username)}</div>
                                            <div class="text-xs text-slate-500 truncate">${escapeHtml(item.email)}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">${escapeHtml(item.title_course)}</td>
                                    <td class="px-6 py-4">
                                        <button type="button" class="proof-btn inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-xs" data-proof-src="${escapeHtml(item.proof_file_payment || '')}">
                                            <i class="bi bi-eye"></i>Ver
                                        </button>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400">Pendente</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" class="approve-payment inline-flex items-center gap-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg text-xs" data-course="${item.id_course}" data-user="${item.id_user_payment}">
                                                <i class="bi bi-check-lg"></i>Aceitar
                                            </button>
                                            <button type="button" class="reject-payment inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-xs" data-course="${item.id_course}" data-user="${item.id_user_payment}">
                                                <i class="bi bi-x-lg"></i>Rejeitar
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                        }).join('');
                    }
                    renderSummary(data.pagination || {}, pendingSummary, 'pedidos');
                    renderPagination(data.pagination || {}, pendingPagination, (next) => { statePending.page = next; loadPending(); });
                })
                .catch(() => {
                    const errorBody = getPendingBody();
                    if (errorBody) {
                        errorBody.innerHTML = '<tr><td colspan="5" class="px-6 py-6 text-center text-slate-500">Erro ao carregar pedidos.</td></tr>';
                    }
                    pendingSummary.textContent = 'Erro ao carregar pedidos.';
                    pendingPagination.innerHTML = '';
                });
        };

        enrollTable?.addEventListener('click', (event) => {
            const btn = event.target.closest('.toggle-access');
            if (!btn) return;
            const id = btn.dataset.id;
            if (!id) return;
            postAction(`${toggleEndpointBase}/${id}`, {})
                .then(() => loadEnrollments())
                .catch(() => loadEnrollments());
        });

        pendingTable?.addEventListener('click', (event) => {
            const proofBtn = event.target.closest('.proof-btn');
            if (proofBtn) {
                const src = proofBtn.getAttribute('data-proof-src') || '';
                if (src) {
                    proofModalImg.src = src.startsWith('/') ? src : `/${src}`;
                    proofModalImg.classList.remove('hidden');
                    proofModalEmpty.classList.add('hidden');
                } else {
                    proofModalImg.src = '';
                    proofModalImg.classList.add('hidden');
                    proofModalEmpty.classList.remove('hidden');
                }
                proofModal.classList.remove('hidden');
                proofModal.classList.add('flex');
                return;
            }

            const approveBtn = event.target.closest('.approve-payment');
            if (approveBtn) {
                const courseId = approveBtn.dataset.course;
                const userId = approveBtn.dataset.user;
                postAction(`${approveBase}/${courseId}/${userId}`, { status_payment: 'Aprovado' })
                    .then(() => { loadPending(); loadEnrollments(); });
                return;
            }

            const rejectBtn = event.target.closest('.reject-payment');
            if (rejectBtn) {
                const courseId = rejectBtn.dataset.course;
                const userId = rejectBtn.dataset.user;
                postAction(`${approveBase}/${courseId}/${userId}`, { status_payment: 'Rejeitado' })
                    .then(() => { loadPending(); });
            }
        });

        const closeProofModal = () => {
            proofModal.classList.add('hidden');
            proofModal.classList.remove('flex');
            proofModalImg.src = '';
        };

        proofModalClose?.addEventListener('click', closeProofModal);
        proofModal?.addEventListener('click', (e) => {
            if (e.target === proofModal) closeProofModal();
        });

        let searchTimer = null;
        enrollSearch.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                stateEnroll.q = enrollSearch.value.trim();
                stateEnroll.page = 1;
                loadEnrollments();
            }, 300);
        });

        enrollStatus.addEventListener('change', () => {
            stateEnroll.status = enrollStatus.value;
            stateEnroll.page = 1;
            loadEnrollments();
        });

        enrollPerPage.addEventListener('change', () => {
            stateEnroll.per_page = Number(enrollPerPage.value || 10);
            stateEnroll.page = 1;
            loadEnrollments();
        });

        let pendingTimer = null;
        pendingSearch.addEventListener('input', () => {
            clearTimeout(pendingTimer);
            pendingTimer = setTimeout(() => {
                statePending.q = pendingSearch.value.trim();
                statePending.page = 1;
                loadPending();
            }, 300);
        });

        pendingPerPage.addEventListener('change', () => {
            statePending.per_page = Number(pendingPerPage.value || 10);
            statePending.page = 1;
            loadPending();
        });

        loadEnrollments();
        loadPending();
    })();
</script>

<?= $this->endSection() ?>
