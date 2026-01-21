<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Estudantes<?= $this->endSection() ?>

<?= $this->section('students') ?>
<div class="space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Estudantes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Gerencie estudantes com carregamento dinamico.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button id="open-student-create" type="button" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 cursor-pointer">
                <i class="bi bi-person-plus"></i>Novo estudante
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="md:col-span-2">
                <label for="students-search" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Buscar</label>
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="bi bi-search text-slate-400"></i>
                    </div>
                    <input id="students-search" type="text" class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 pl-10 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" placeholder="Nome, email ou ID">
                </div>
            </div>
            <div>
                <label for="students-status" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Status</label>
                <select id="students-status" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div>
                <label for="students-per-page" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Por pagina</label>
                <select id="students-per-page" class="mt-1 block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl">
        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-500 dark:text-slate-400">
                <thead class="text-xs uppercase text-slate-600 bg-slate-50 dark:bg-slate-700 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">Estudante</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Ultimo acesso</th>
                        <th scope="col" class="px-6 py-3">Criado em</th>
                        <th scope="col" class="px-6 py-3 text-right">Acoes</th>
                    </tr>
                </thead>
                <tbody id="students-table-body"></tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400 md:flex-row md:items-center md:justify-between">
            <div id="students-summary">Carregando...</div>
            <div id="students-pagination" class="flex flex-wrap gap-2"></div>
        </div>
    </div>
</div>

<div id="student-drawer-backdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50"></div>
<aside id="student-drawer" class="fixed right-0 top-0 z-50 h-full w-[95%] max-w-lg translate-x-full overflow-y-auto bg-white shadow-xl transition-transform sm:w-[90%] md:w-[70%] lg:w-[520px] dark:bg-slate-900">
    <div class="flex items-center justify-between border-b border-slate-200 p-5 dark:border-slate-800">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Detalhes do estudante</p>
            <h2 id="student-drawer-name" class="text-lg font-semibold text-slate-900 dark:text-white">—</h2>
        </div>
        <button id="student-drawer-close" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="space-y-4 p-5">
        <div class="flex items-center gap-4">
            <img id="student-drawer-avatar" class="h-16 w-16 rounded-full object-cover" src="<?= base_url('assets/img/user-default.png') ?>" alt="Avatar">
            <div>
                <p id="student-drawer-email" class="text-sm text-slate-500 dark:text-slate-400">—</p>
                <p id="student-drawer-status" class="text-sm font-medium text-slate-900 dark:text-white">—</p>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <p class="text-xs uppercase text-slate-500">ID</p>
                <p id="student-drawer-id" class="text-sm font-medium text-slate-900 dark:text-white">—</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-500">Ultimo acesso</p>
                <p id="student-drawer-last" class="text-sm font-medium text-slate-900 dark:text-white">—</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-500">Criado em</p>
                <p id="student-drawer-created" class="text-sm font-medium text-slate-900 dark:text-white">—</p>
            </div>
            <div>
                <p class="text-xs uppercase text-slate-500">Role</p>
                <p id="student-drawer-role" class="text-sm font-medium text-slate-900 dark:text-white">—</p>
            </div>
        </div>
    </div>
</aside>

<div id="student-create-backdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50"></div>
<aside id="student-create-drawer" class="fixed right-0 top-0 z-50 h-full w-[96%] max-w-3xl translate-x-full overflow-y-auto bg-white shadow-xl transition-transform sm:w-[92%] md:w-[85%] lg:w-[720px] dark:bg-slate-900">
    <div class="flex items-center justify-between border-b border-slate-200 p-5 dark:border-slate-800">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Novo usuario</p>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Criar estudante</h2>
        </div>
        <button id="student-create-close" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="space-y-4 p-5">
        <div id="student-create-feedback" class="hidden rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700"></div>
        <?php if (session('error') !== null) : ?>
            <div class="rounded-lg bg-rose-50 p-3 text-sm text-rose-700"><?= esc(session('error')) ?></div>
        <?php elseif (session('errors') !== null) : ?>
            <div class="rounded-lg bg-rose-50 p-3 text-sm text-rose-700">
                <?php if (is_array(session('errors'))) : ?>
                    <?php foreach (session('errors') as $error) : ?>
                        <?= esc($error) ?><br>
                    <?php endforeach ?>
                <?php else : ?>
                    <?= esc(session('errors')) ?>
                <?php endif ?>
            </div>
        <?php endif ?>

        <form id="student-create-form" action="<?= url_to('register') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="role" value="student">

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
                <input type="email" name="email" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nome de usuario</label>
                <input type="text" name="username" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Senha</label>
                <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Confirmar senha</label>
                <input type="password" name="password_confirm" required class="mt-1 w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="student-create-cancel" class="rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Cancelar</button>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Criar</button>
            </div>
        </form>
    </div>
</aside>

<script>
    (function () {
        const endpoint = <?= json_encode(site_url('admin/dashboard/estudantes/data')) ?>;
        const tableBody = document.getElementById('students-table-body');
        const summary = document.getElementById('students-summary');
        const pagination = document.getElementById('students-pagination');
        const searchInput = document.getElementById('students-search');
        const statusSelect = document.getElementById('students-status');
        const perPageSelect = document.getElementById('students-per-page');
        const drawer = document.getElementById('student-drawer');
        const drawerBackdrop = document.getElementById('student-drawer-backdrop');
        const drawerClose = document.getElementById('student-drawer-close');
        const createButton = document.getElementById('open-student-create');
        const createDrawer = document.getElementById('student-create-drawer');
        const createBackdrop = document.getElementById('student-create-backdrop');
        const createClose = document.getElementById('student-create-close');
        const createCancel = document.getElementById('student-create-cancel');
        const createForm = document.getElementById('student-create-form');
        const createFeedback = document.getElementById('student-create-feedback');
        const csrfName = <?= json_encode(csrf_token()) ?>;
        let csrfHash = <?= json_encode(csrf_hash()) ?>;

        const state = {
            page: 1,
            per_page: Number(perPageSelect.value || 10),
            q: '',
            status: '',
            loading: false
        };

        const escapeHtml = (value) => {
            if (value === null || value === undefined) return '';
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        const formatDate = (value) => {
            if (!value) return '—';
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return '—';
            return date.toLocaleString('pt-BR');
        };

        const statusBadge = (active) => {
            const label = active ? 'Ativo' : 'Inativo';
            const classes = active
                ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
                : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
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
            })
                .then(async (res) => {
                    const data = await res.json();
                    if (data.csrf) {
                        csrfHash = data.csrf;
                    }
                    if (!res.ok) {
                        throw data;
                    }
                    return data;
                });
        };

        const renderRows = (items) => {
            if (!items.length) {
                tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-6 text-center text-slate-500">Nenhum estudante encontrado.</td></tr>`;
                return;
            }
            tableBody.innerHTML = items.map(item => {
                const isActive = Number(item.active) === 1;
                const toggleLabel = isActive ? 'Desativar' : 'Ativar';
                const toggleIcon = isActive ? 'bi-slash-circle text-amber-500' : 'bi-check-circle text-green-600';
                return `
                <tr class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img class="h-10 w-10 rounded-full object-cover" src="${escapeHtml(item.img || '<?= base_url('assets/img/user-default.png') ?>')}" alt="Avatar">
                            <div>
                                <div class="font-medium text-slate-900 dark:text-white">${escapeHtml(item.username || '—')}</div>
                                <div class="text-xs text-slate-500">ID #${escapeHtml(item.id)}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">${escapeHtml(item.email || '—')}</td>
                    <td class="px-6 py-4">${statusBadge(Number(item.active) === 1)}</td>
                    <td class="px-6 py-4">${formatDate(item.last_active)}</td>
                    <td class="px-6 py-4">${formatDate(item.created_at)}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="relative inline-flex items-center justify-end">
                            <button class="student-view inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 cursor-pointer" data-user='${encodeURIComponent(JSON.stringify(item))}'>
                                <i class="bi bi-pencil-square"></i>Editar
                            </button>
                            <button class="student-actions ml-2 inline-flex items-center rounded-lg border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 cursor-pointer" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="student-actions-menu absolute right-0 top-10 z-10 hidden w-56 rounded-lg border border-slate-200 bg-white py-2 text-left text-sm shadow-lg dark:border-slate-700 dark:bg-slate-800" data-user='${encodeURIComponent(JSON.stringify(item))}'>
                                <button class="student-action-item flex w-full items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700 cursor-pointer" data-action="toggle">
                                    <i class="bi ${toggleIcon}"></i>${toggleLabel}
                                </button>
                                <button class="student-action-item flex w-full items-center gap-2 px-4 py-2 text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700 cursor-pointer" data-action="message">
                                    <i class="bi bi-envelope"></i>Mensagem
                                </button>
                                <button class="student-action-item flex w-full items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-900/30 cursor-pointer" data-action="delete">
                                    <i class="bi bi-trash"></i>Excluir
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            }).join('');
        };

        const renderPagination = (paginationData) => {
            const totalPages = paginationData.total_pages || 1;
            const currentPage = paginationData.page || 1;

            const pages = [];
            const maxButtons = 5;
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(totalPages, start + maxButtons - 1);
            if (end - start < maxButtons - 1) {
                start = Math.max(1, end - maxButtons + 1);
            }
            for (let i = start; i <= end; i += 1) {
                pages.push(i);
            }

            const button = (label, page, disabled, active) => {
                const base = 'rounded-lg border px-3 py-1.5 text-sm font-medium transition';
                const activeClass = active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-600';
                const disabledClass = disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
                return `<button class="${base} ${activeClass} ${disabledClass}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;
            };

            const html = [
                button('Anterior', currentPage - 1, currentPage === 1, false),
                ...pages.map(page => button(page, page, false, page === currentPage)),
                button('Proxima', currentPage + 1, currentPage === totalPages, false)
            ];

            pagination.innerHTML = html.join('');
        };

        const renderSummary = (paginationData) => {
            const total = paginationData.total || 0;
            const page = paginationData.page || 1;
            const perPage = paginationData.per_page || state.per_page;
            const start = total === 0 ? 0 : (page - 1) * perPage + 1;
            const end = Math.min(total, page * perPage);
            summary.textContent = `Mostrando ${start}-${end} de ${total} estudantes`;
        };

        const loadData = () => {
            if (state.loading) return;
            state.loading = true;
            tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-6 text-center text-slate-500">Carregando...</td></tr>`;

            const url = new URL(endpoint, window.location.origin);
            url.searchParams.set('page', state.page);
            url.searchParams.set('per_page', state.per_page);
            if (state.q) url.searchParams.set('q', state.q);
            if (state.status) url.searchParams.set('status', state.status);

            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    renderRows(data.items || []);
                    renderSummary(data.pagination || {});
                    renderPagination(data.pagination || {});
                })
                .catch(() => {
                    tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-6 text-center text-slate-500">Erro ao carregar dados.</td></tr>`;
                    summary.textContent = 'Erro ao carregar estudantes.';
                    pagination.innerHTML = '';
                })
                .finally(() => {
                    state.loading = false;
                });
        };

        const openDrawer = (item) => {
            drawer.classList.remove('translate-x-full');
            drawerBackdrop.classList.remove('hidden');
            document.getElementById('student-drawer-name').textContent = item.username || '—';
            document.getElementById('student-drawer-email').textContent = item.email || '—';
            document.getElementById('student-drawer-status').textContent = Number(item.active) === 1 ? 'Ativo' : 'Inativo';
            document.getElementById('student-drawer-id').textContent = item.id || '—';
            document.getElementById('student-drawer-last').textContent = formatDate(item.last_active);
            document.getElementById('student-drawer-created').textContent = formatDate(item.created_at);
            document.getElementById('student-drawer-role').textContent = item.role || 'student';
            document.getElementById('student-drawer-avatar').src = item.img || <?= json_encode(base_url('assets/img/user-default.png')) ?>;
        };

        const closeDrawer = () => {
            drawer.classList.add('translate-x-full');
            drawerBackdrop.classList.add('hidden');
        };

        drawerBackdrop.addEventListener('click', closeDrawer);
        drawerClose.addEventListener('click', closeDrawer);

        const openCreateDrawer = () => {
            createDrawer.classList.remove('translate-x-full');
            createBackdrop.classList.remove('hidden');
        };

        const closeCreateDrawer = () => {
            createDrawer.classList.add('translate-x-full');
            createBackdrop.classList.add('hidden');
        };

        createButton.addEventListener('click', openCreateDrawer);
        createBackdrop.addEventListener('click', closeCreateDrawer);
        createClose.addEventListener('click', closeCreateDrawer);
        createCancel.addEventListener('click', closeCreateDrawer);

        createForm.addEventListener('submit', (event) => {
            event.preventDefault();
            createFeedback.classList.add('hidden');
            const formData = new FormData(createForm);
            const payload = Object.fromEntries(formData.entries());
            postAction(<?= json_encode(site_url('admin/dashboard/usuarios/create')) ?>, payload)
                .then(data => {
                    if (window.Swal) {
                        Swal.fire({ icon: 'success', title: 'Sucesso', text: data.message || 'Usuario criado.' });
                    }
                    createForm.reset();
                    loadData();
                    setTimeout(closeCreateDrawer, 600);
                })
                .catch((error) => {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Erro', text: error?.message || 'Nao foi possivel criar o usuario.' });
                    }
                    createFeedback.textContent = error?.message || 'Nao foi possivel criar o usuario.';
                    createFeedback.classList.remove('hidden');
                    createFeedback.classList.remove('bg-emerald-50', 'text-emerald-700');
                    createFeedback.classList.add('bg-rose-50', 'text-rose-700');
                });
        });

        const closeAllMenus = () => {
            document.querySelectorAll('.student-actions-menu').forEach(menu => menu.classList.add('hidden'));
        };

        document.addEventListener('click', (event) => {
            if (!event.target.closest('.student-actions')) {
                closeAllMenus();
            }
        });

        tableBody.addEventListener('click', (event) => {
            const viewButton = event.target.closest('.student-view');
            if (viewButton) {
                const item = JSON.parse(decodeURIComponent(viewButton.getAttribute('data-user')));
                openDrawer(item);
                return;
            }

            const actionButton = event.target.closest('.student-actions');
            if (actionButton) {
                const menu = actionButton.parentElement.querySelector('.student-actions-menu');
                const isHidden = menu.classList.contains('hidden');
                closeAllMenus();
                if (isHidden) {
                    menu.classList.remove('hidden');
                }
                return;
            }

            const actionItem = event.target.closest('.student-action-item');
            if (actionItem) {
                const action = actionItem.dataset.action;
                const menu = actionItem.closest('.student-actions-menu');
                const item = JSON.parse(decodeURIComponent(menu.getAttribute('data-user')));
                closeAllMenus();

                if (action === 'toggle') {
                    postAction(<?= json_encode(site_url('admin/dashboard/usuarios/toggle')) ?>, { id: item.id, role: 'student' })
                        .then(data => {
                            if (window.Swal) {
                                Swal.fire({ icon: 'success', title: 'Atualizado', text: data.message });
                            }
                            loadData();
                        });
                    return;
                }

                if (action === 'delete') {
                    const confirmDelete = () => postAction(<?= json_encode(site_url('admin/dashboard/usuarios/delete')) ?>, { id: item.id, role: 'student' })
                        .then(data => {
                            if (window.Swal) {
                                Swal.fire({ icon: 'success', title: 'Excluido', text: data.message });
                            }
                            loadData();
                        });

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Excluir estudante?',
                            text: 'Esta acao nao pode ser desfeita.',
                            showCancelButton: true,
                            confirmButtonText: 'Excluir',
                            cancelButtonText: 'Cancelar'
                        }).then(result => {
                            if (result.isConfirmed) confirmDelete();
                        });
                    } else if (confirm('Excluir estudante?')) {
                        confirmDelete();
                    }
                    return;
                }

                if (action === 'message') {
                    const sendMessage = (message) => postAction(<?= json_encode(site_url('admin/dashboard/usuarios/message')) ?>, { id: item.id, role: 'student', message })
                        .then(data => {
                            if (window.Swal) {
                                Swal.fire({ icon: 'success', title: 'Enviado', text: data.message });
                            }
                        });

                    if (window.Swal) {
                        Swal.fire({
                            title: 'Enviar mensagem',
                            input: 'textarea',
                            inputPlaceholder: 'Digite sua mensagem...',
                            showCancelButton: true,
                            confirmButtonText: 'Enviar',
                            cancelButtonText: 'Cancelar'
                        }).then(result => {
                            if (result.isConfirmed && result.value) {
                                sendMessage(result.value);
                            }
                        });
                    } else {
                        const msg = prompt('Mensagem para o estudante:');
                        if (msg) sendMessage(msg);
                    }
                }
            }
        });

        let searchTimer = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                state.q = searchInput.value.trim();
                state.page = 1;
                loadData();
            }, 300);
        });

        statusSelect.addEventListener('change', () => {
            state.status = statusSelect.value;
            state.page = 1;
            loadData();
        });

        perPageSelect.addEventListener('change', () => {
            state.per_page = Number(perPageSelect.value || 10);
            state.page = 1;
            loadData();
        });

        pagination.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-page]');
            if (!button) return;
            const nextPage = Number(button.dataset.page);
            if (!Number.isFinite(nextPage) || nextPage < 1) return;
            state.page = nextPage;
            loadData();
        });

        loadData();
    })();
</script>
<?= $this->endSection() ?>
