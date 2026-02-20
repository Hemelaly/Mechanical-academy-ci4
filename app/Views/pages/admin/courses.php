<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Dashboard - Cursos<?= $this->endSection() ?>

<?= $this->section('courses') ?>

<?php
helper(['number', 'text', 'form']);

$metrics = $metrics ?? [
    'total' => 0, 
    'ativos' => 0, 
    'rascunhos' => 0, 
    'arquivados' => 0, 
    'receita_mes' => 0, 
    'novos_alunos_mes' => 0
];

$filters = $filters ?? [
    'q' => '', 
    'status' => '', 
    'categoria' => '', 
    'ordem' => 'recentes'
];

$courses = $courses ?? [];
$categories = $categories ?? ['Programação', 'Design', 'Dados', 'Marketing', 'Negócios'];
?>

<!-- Header -->
<header class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Gestão de Cursos</h1>
            <p class="text-gray-400">Gerencie, edite e monitore o desempenho de todos os seus cursos</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 hover:border-gray-600 transition-all duration-200">
                    <i class="bi bi-download text-base"></i>
                    <span>Importar/Exportar</span>
                    <i class="bi bi-chevron-down text-xs"></i>
                </button>
                <div class="absolute right-0 mt-2 w-56 bg-gray-900 border border-gray-700 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                    <div class="py-1">
                        <a href="<?= route_to('admin.courses.import') ?>" class="flex items-center gap-2 px-4 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="bi bi-upload"></i>
                            <span>Importar CSV</span>
                        </a>
                        <div class="border-t border-gray-800 my-1"></div>
                        <a href="<?= route_to('admin.courses.export') ?>" class="flex items-center gap-2 px-4 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="bi bi-download"></i>
                            <span>Exportar CSV</span>
                        </a>
                        <a href="<?= route_to('admin.courses.export.excel') ?>" class="flex items-center gap-2 px-4 py-2.5 text-gray-300 hover:bg-gray-800 hover:text-white">
                            <i class="bi bi-file-earmark-excel"></i>
                            <span>Exportar Excel</span>
                        </a>
                    </div>
                </div>
            </div>
            <a href="<?= route_to('admin.courses.new') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-blue-900/30">
                <i class="bi bi-plus-circle"></i>
                <span>Novo Curso</span>
            </a>
        </div>
    </div>
</header>

<!-- KPIs -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <!-- Total de Cursos -->
    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl p-5 hover:border-gray-700 hover:shadow-xl transition-all duration-300 group">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-400 mb-1">Total de Cursos</p>
                <p class="text-2xl font-bold text-white"><?= number_to_amount(count($courses), 0, 'pt_BR') ?></p>
            </div>
            <div class="p-2.5 bg-blue-900/20 rounded-lg group-hover:bg-blue-900/30 transition-colors">
                <i class="bi bi-collection text-lg text-blue-400"></i>
            </div>
        </div>
        <div class="flex items-center text-sm text-green-400">
            <i class="bi bi-arrow-up-right mr-1"></i>
            <span>+<?= number_to_amount($metrics['novos_alunos_mes'] ?? 0, 0, 'pt_BR') ?> este mês</span>
        </div>
    </div>

    <!-- Cursos Ativos -->
    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl p-5 hover:border-gray-700 hover:shadow-xl transition-all duration-300 group">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-400 mb-1">Cursos Ativos</p>
                <p class="text-2xl font-bold text-white"><?= number_to_amount($activeCourses ?? 0, 0, 'pt_BR') ?></p>
            </div>
            <div class="p-2.5 bg-green-900/20 rounded-lg group-hover:bg-green-900/30 transition-colors">
                <i class="bi bi-check2-circle text-lg text-green-400"></i>
            </div>
        </div>
        <p class="text-sm text-gray-400">
            <?= number_format($activeCourses > 0 ? ($activeCourses / count($courses)) * 100 : 0, 1) ?>% do total
        </p>
    </div>

    <!-- Receita Mensal -->
    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl p-5 hover:border-gray-700 hover:shadow-xl transition-all duration-300 group">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-400 mb-1">Receita Mensal</p>
                <p class="text-xl font-bold text-white">0,00 MZN</p>
            </div>
            <div class="p-2.5 bg-yellow-900/20 rounded-lg group-hover:bg-yellow-900/30 transition-colors">
                <i class="bi bi-currency-dollar text-lg text-yellow-400"></i>
            </div>
        </div>
        <div class="flex items-center text-sm text-green-400">
            <i class="bi bi-graph-up mr-1"></i>
            <span>+15% vs mês anterior</span>
        </div>
    </div>

    <!-- Novos Alunos -->
    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl p-5 hover:border-gray-700 hover:shadow-xl transition-all duration-300 group">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-400 mb-1">Novos Alunos</p>
                <p class="text-2xl font-bold text-white"><?= number_to_amount($metrics['novos_alunos_mes'] ?? 0, 0, 'pt_BR') ?></p>
            </div>
            <div class="p-2.5 bg-cyan-900/20 rounded-lg group-hover:bg-cyan-900/30 transition-colors">
                <i class="bi bi-person-plus text-lg text-cyan-400"></i>
            </div>
        </div>
        <div class="w-full bg-gray-800 rounded-full h-1.5 mt-2">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-1.5 rounded-full" 
                 style="width: <?= min(($metrics['novos_alunos_mes'] ?? 0) / 100 * 100, 100) ?>%"></div>
        </div>
    </div>

    <!-- Rascunhos -->
    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl p-5 hover:border-gray-700 hover:shadow-xl transition-all duration-300 group">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-400 mb-1">Rascunhos</p>
                <p class="text-2xl font-bold text-white"><?= number_to_amount($metrics['rascunhos'] ?? 0, 0, 'pt_BR') ?></p>
            </div>
            <div class="p-2.5 bg-gray-700/20 rounded-lg group-hover:bg-gray-700/30 transition-colors">
                <i class="bi bi-file-earmark-text text-lg text-gray-400"></i>
            </div>
        </div>
        <a href="?status=rascunho" class="inline-flex items-center text-sm text-blue-400 hover:text-blue-300 transition-colors">
            <span>Ver todos</span>
            <i class="bi bi-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Taxa de Conclusão -->
    <div class="bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl p-5 hover:border-gray-700 hover:shadow-xl transition-all duration-300 group">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-400 mb-1">Taxa de Conclusão</p>
                <p class="text-2xl font-bold text-white">78%</p>
            </div>
            <div class="p-2.5 bg-green-900/20 rounded-lg group-hover:bg-green-900/30 transition-colors">
                <i class="bi bi-bar-chart text-lg text-green-400"></i>
            </div>
        </div>
        <p class="text-sm text-gray-400">Média global</p>
    </div>
</div>

<!-- Filtros -->
<div class="bg-gray-900/50 border border-gray-800 rounded-xl p-5 mb-6">
    <form action="<?= current_url() ?>" method="get" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Buscar -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-400 mb-2">Buscar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-500"></i>
                    </div>
                    <input type="text" 
                           name="q" 
                           value="<?= esc($filters['q']) ?>" 
                           class="pl-10 w-full px-4 py-2.5 bg-gray-900/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="Título, instrutor, ID...">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 bg-gray-900/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Todos Status</option>
                    <option value="ativo" <?= $filters['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                    <option value="rascunho" <?= $filters['status'] === 'rascunho' ? 'selected' : '' ?>>Rascunho</option>
                    <option value="arquivado" <?= $filters['status'] === 'arquivado' ? 'selected' : '' ?>>Arquivado</option>
                </select>
            </div>

            <!-- Categoria -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Categoria</label>
                <select name="categoria" class="w-full px-4 py-2.5 bg-gray-900/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Todas Categorias</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= strtolower($category) ?>" 
                            <?= $filters['categoria'] === strtolower($category) ? 'selected' : '' ?>>
                            <?= esc($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ordenar por -->
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-2">Ordenar por</label>
                <select name="ordem" class="w-full px-4 py-2.5 bg-gray-900/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="recentes" <?= $filters['ordem'] === 'recentes' ? 'selected' : '' ?>>Mais Recentes</option>
                    <option value="populares" <?= $filters['ordem'] === 'populares' ? 'selected' : '' ?>>Mais Populares</option>
                    <option value="melhor_nota" <?= $filters['ordem'] === 'melhor_nota' ? 'selected' : '' ?>>Melhor Avaliação</option>
                    <option value="preco_alto" <?= $filters['ordem'] === 'preco_alto' ? 'selected' : '' ?>>Preço: Maior</option>
                    <option value="preco_baixo" <?= $filters['ordem'] === 'preco_baixo' ? 'selected' : '' ?>>Preço: Menor</option>
                </select>
            </div>

            <!-- Botões -->
            <div class="flex gap-2 items-end">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all hover:shadow-lg hover:shadow-blue-900/30">
                    <i class="bi bi-funnel"></i>
                    <span class="hidden sm:inline">Filtrar</span>
                </button>
                <a href="<?= current_url() ?>" class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-all">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Ações e Visualização -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center">
            <input type="checkbox" id="checkAll" class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500 focus:ring-2">
            <label for="checkAll" class="ml-2 text-sm text-gray-400">Selecionar todos</label>
        </div>
        
        <div class="flex gap-1">
            <button id="bulkPublish" disabled class="inline-flex items-center gap-2 px-3 py-2 text-sm border border-blue-700 text-blue-400 rounded-lg hover:bg-blue-900/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                <i class="bi bi-check2-circle"></i>
                <span class="hidden sm:inline">Publicar</span>
            </button>
            <button id="bulkArchive" disabled class="inline-flex items-center gap-2 px-3 py-2 text-sm border border-amber-700 text-amber-400 rounded-lg hover:bg-amber-900/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                <i class="bi bi-archive"></i>
                <span class="hidden sm:inline">Arquivar</span>
            </button>
            <button id="bulkDelete" disabled class="inline-flex items-center gap-2 px-3 py-2 text-sm border border-red-700 text-red-400 rounded-lg hover:bg-red-900/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                <i class="bi bi-trash"></i>
                <span class="hidden sm:inline">Excluir</span>
            </button>
        </div>
    </div>
    
    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-400 hidden md:inline">
            <?php if (!empty($pageMeta) && $pageMeta['total'] > 0): ?>
                Mostrando <?= number_format($pageMeta['first']) ?>–<?= number_format($pageMeta['last']) ?>
                de <?= number_format($pageMeta['total']) ?> cursos
            <?php endif; ?>
        </span>
        
        <div class="inline-flex rounded-lg border border-gray-700 p-1">
            <input type="radio" name="viewMode" id="viewTable" checked class="hidden">
            <label for="viewTable" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-md cursor-pointer view-toggle hover:bg-gray-800 data-[active=true]:bg-gray-800 data-[active=true]:text-white text-gray-400" data-active="true">
                <i class="bi bi-list-ul"></i>
                <span class="hidden sm:inline">Tabela</span>
            </label>
            <input type="radio" name="viewMode" id="viewGrid" class="hidden">
            <label for="viewGrid" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm rounded-md cursor-pointer view-toggle hover:bg-gray-800 data-[active=false]:text-gray-400" data-active="false">
                <i class="bi bi-grid-3x3-gap"></i>
                <span class="hidden sm:inline">Grade</span>
            </label>
        </div>
    </div>
</div>

<!-- Vista de Tabela -->
<section id="tableView" class="bg-gray-900/50 border border-gray-800 rounded-xl overflow-hidden">
    <?php if (empty($courses)): ?>
        <div class="text-center py-12">
            <div class="inline-flex p-4 rounded-full bg-gray-800/50 mb-4">
                <i class="bi bi-collection text-4xl text-gray-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">Nenhum curso encontrado</h3>
            <p class="text-gray-400 mb-6">Tente ajustar os filtros ou criar um novo curso</p>
            <a href="<?= route_to('admin.courses.new') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all hover:shadow-lg hover:shadow-blue-900/30">
                <i class="bi bi-plus-circle"></i>
                <span>Criar primeiro curso</span>
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table
                id="admin-courses-table"
                data-flowbite-datatable
                data-datatable-searchable="false"
                data-datatable-paging="false"
                data-datatable-sortable="false"
                data-datatable-per-page-select="false"
                class="w-full">
                <thead class="bg-gray-900/80 border-b border-gray-800">
                    <tr>
                        <th class="py-4 px-6 text-left">
                            <input type="checkbox" id="tableCheckAll" class="w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500 focus:ring-2">
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Curso</th>
                        <th class="py-4 px-6 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Aulas</th>
                        <th class="py-4 px-6 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Alunos</th>
                        <th class="py-4 px-6 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Preço</th>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Progresso</th>
                        <th class="py-4 px-6 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    <?php foreach ($courses as $c): ?>
                        <tr class="hover:bg-gray-800/30 transition-colors" data-id="<?= (int)$c->id_course ?>">
                            <td class="py-4 px-6">
                                <input type="checkbox" class="row-check w-4 h-4 text-blue-600 bg-gray-900 border-gray-700 rounded focus:ring-blue-500 focus:ring-2" value="<?= (int)$c->id_course ?>">
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-4">
                                    <img src="<?= esc('/assets/instructor/img/courses/' . ($c->image_course ?? 'placeholder.jpg')) ?>" 
                                         alt="<?= esc($c->title_course) ?>" 
                                         class="w-14 h-14 rounded-lg object-cover border border-gray-700">
                                    <div>
                                        <a href="<?= route_to('admin.courses.edit', $c->id_course) ?>" 
                                           class="font-semibold text-white hover:text-blue-400 transition-colors line-clamp-1">
                                            <?= esc($c->title_course) ?>
                                        </a>
                                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <i class="bi bi-person text-xs"></i>
                                                <?= esc($courses2->instructor_name ?? '—') ?>
                                            </span>
                                            <span>•</span>
                                            <span>ID: #<?= (int)$c->id_course ?></span>
                                            <span>•</span>
                                            <span><?= esc(date('d/m/Y', strtotime($c->updated_at ?? 'now'))) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-gray-800 text-sm font-medium text-gray-300">
                                    <?= esc($totalLessons ?? 0) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="text-lg font-semibold text-white"><?= number_format(($enrolledCounts[$c->id_course] ?? 0), 0, '', '.') ?></div>
                                <div class="text-xs text-gray-400">inscritos</div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="text-lg font-bold text-white">
                                    0,00 MZN
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full" 
                                             style="width: <?= ($c->progresso ?? 0) ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-400 min-w-[40px]"><?= ($c->progresso ?? 0) ?>%</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <?php
                                $status = $c->status_course ?? 'rascunho';
                                $statusColors = [
                                    'ativo' => 'bg-green-900/20 text-green-400 border-green-800',
                                    'rascunho' => 'bg-gray-800/50 text-gray-400 border-gray-700',
                                    'arquivado' => 'bg-amber-900/20 text-amber-400 border-amber-800'
                                ];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border <?= $statusColors[$status] ?? $statusColors['rascunho'] ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="<?= route_to('admin.courses.preview', $c->id_course) ?>" 
                                       class="inline-flex items-center justify-center w-9 h-9 border border-gray-700 text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors"
                                       title="Pré-visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= route_to('admin.courses.edit', $c->id_course) ?>" 
                                       class="inline-flex items-center justify-center w-9 h-9 border border-gray-700 text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors"
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="inline-flex items-center justify-center w-9 h-9 border border-red-800/50 text-red-400 rounded-lg hover:bg-red-900/20 hover:text-red-300 transition-colors btn-delete"
                                            data-id="<?= (int)$c->id_course ?>"
                                            data-title="<?= esc($c->title_course) ?>"
                                            title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <?php if (!empty($pagerHtml) || !empty($pageMeta)): ?>
            <div class="border-t border-gray-800 px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-400">
                        <?php if (!empty($pageMeta) && $pageMeta['total'] > 0): ?>
                            Página <?= $pageMeta['current'] ?? 1 ?> de <?= $pageMeta['last_page'] ?? 1 ?> • 
                            <?= number_format($pageMeta['total']) ?> cursos no total
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <?= $pagerHtml ?? '' ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<!-- Vista de Grade -->
<section id="gridView" class="hidden">
    <?php if (empty($courses)): ?>
        <div class="bg-gray-900/50 border border-gray-800 rounded-xl p-12 text-center">
            <div class="inline-flex p-4 rounded-full bg-gray-800/50 mb-4">
                <i class="bi bi-collection text-4xl text-gray-600"></i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">Nenhum curso para exibir</h3>
            <p class="text-gray-400">Os cursos serão exibidos aqui em formato de grade</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($courses as $c): ?>
                <div class="group bg-gradient-to-br from-gray-900/50 to-gray-800/30 border border-gray-800 rounded-xl overflow-hidden hover:border-gray-700 hover:shadow-xl transition-all duration-300">
                    <!-- Imagem do curso -->
                    <div class="relative overflow-hidden">
                        <img src="<?= esc('/assets/instructor/img/courses/' . ($c->image_course ?? 'placeholder.jpg')) ?>" 
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                             alt="<?= esc($c->title_course) ?>">
                        
                        <!-- Badge de status -->
                        <?php
                        $status = $c->status_course ?? 'rascunho';
                        $statusColors = [
                            'ativo' => 'bg-green-900/90 text-green-300',
                            'rascunho' => 'bg-gray-800/90 text-gray-400',
                            'arquivado' => 'bg-amber-900/90 text-amber-300'
                        ];
                        ?>
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium <?= $statusColors[$status] ?? $statusColors['rascunho'] ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </div>
                        
                        <!-- Overlay na imagem -->
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                            <div class="flex justify-between items-center">
                                <span class="px-3 py-1 bg-blue-600 rounded-full text-xs font-medium text-white">
                                    <?= esc($totalLessons ?? 0) ?> aulas
                                </span>
                                <span class="text-lg font-bold text-white">
                                    0,00 MZN
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conteúdo -->
                    <div class="p-5">
                        <!-- Título -->
                        <h3 class="font-bold text-white mb-2 line-clamp-2 h-12">
                            <?= esc($c->title_course) ?>
                        </h3>
                        
                        <!-- Instrutor e alunos -->
                        <div class="flex items-center text-gray-400 text-sm mb-4">
                            <div class="flex items-center gap-1">
                                <i class="bi bi-person"></i>
                                <span><?= esc($courses2->instructor_name ?? 'Instrutor') ?></span>
                            </div>
                            <span class="mx-2">•</span>
                            <div class="flex items-center gap-1">
                                <i class="bi bi-people"></i>
                                <span><?= number_format(($enrolledCounts[$c->id_course] ?? 0), 0, '', '.') ?></span>
                            </div>
                        </div>
                        
                        <!-- Progresso -->
                        <div class="mb-5">
                            <div class="flex justify-between text-sm text-gray-400 mb-1">
                                <span>Progresso dos alunos</span>
                                <span><?= ($c->progresso ?? 0) ?>%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full" 
                                     style="width: <?= ($c->progresso ?? 0) ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Rodapé -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-800/50">
                            <div class="text-sm text-gray-400">
                                <?= esc(date('d/m/Y', strtotime($c->updated_at ?? 'now'))) ?>
                            </div>
                            <div class="flex gap-1">
                                <a href="<?= route_to('admin.courses.edit', $c->id_course) ?>" 
                                   class="inline-flex items-center justify-center w-9 h-9 border border-gray-700 text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= route_to('admin.courses.preview', $c->id_course) ?>" 
                                   class="inline-flex items-center justify-center w-9 h-9 border border-gray-700 text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition-colors"
                                   title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" 
                                        class="inline-flex items-center justify-center w-9 h-9 border border-red-800/50 text-red-400 rounded-lg hover:bg-red-900/20 hover:text-red-300 transition-colors btn-delete"
                                        data-id="<?= (int)$c->id_course ?>"
                                        data-title="<?= esc($c->title_course) ?>"
                                        title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Modal de Exclusão -->
<div id="deleteModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-900/20 rounded-lg">
                    <i class="bi bi-exclamation-triangle text-xl text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white">Excluir Curso</h3>
                    <p class="text-sm text-gray-400 mt-1">Esta ação não pode ser desfeita</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <p class="text-gray-300 mb-4">Tem certeza que deseja excluir o curso?</p>
            <div class="bg-red-900/10 border border-red-800/30 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="bi bi-exclamation-circle text-red-400 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-white" id="deleteCourseTitle"></p>
                        <p class="text-sm text-gray-400 mt-1">Todos os dados serão permanentemente removidos</p>
                    </div>
                </div>
            </div>
            
            <form method="post" action="<?= route_to('admin.courses.delete') ?>" id="deleteForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="deleteCourseId">
            </form>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2.5 border border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg font-medium transition-all">
                    Cancelar
                </button>
                <button type="button" onclick="confirmDelete()" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all">
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ação em Massa -->
<div id="bulkModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-900/20 rounded-lg">
                    <i class="bi bi-collection text-xl text-blue-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-white" id="bulkModalTitle"></h3>
                    <p class="text-sm text-gray-400 mt-1" id="bulkModalSubtitle"></p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <p class="text-gray-300 mb-4" id="bulkModalMessage"></p>
            
            <div class="bg-blue-900/10 border border-blue-800/30 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3">
                    <i class="bi bi-info-circle text-blue-400"></i>
                    <div>
                        <p class="text-sm text-gray-300">
                            <span id="bulkSelectedCount">0</span> curso(s) selecionado(s)
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeBulkModal()" class="px-4 py-2.5 border border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg font-medium transition-all">
                    Cancelar
                </button>
                <button type="button" onclick="confirmBulkAction()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Alternar entre visualizações
    document.addEventListener('DOMContentLoaded', function() {
        const viewToggles = document.querySelectorAll('.view-toggle');
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        
        viewToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const isTable = this.getAttribute('for') === 'viewTable';
                
                // Atualizar estado dos botões
                viewToggles.forEach(t => {
                    t.setAttribute('data-active', t === this ? 'true' : 'false');
                });
                
                // Mostrar/ocultar visualizações
                if (isTable) {
                    tableView.classList.remove('hidden');
                    gridView.classList.add('hidden');
                    localStorage.setItem('coursesViewMode', 'table');
                } else {
                    gridView.classList.remove('hidden');
                    tableView.classList.add('hidden');
                    localStorage.setItem('coursesViewMode', 'grid');
                }
            });
        });
        
        // Restaurar visualização salva
        const savedView = localStorage.getItem('coursesViewMode');
        if (savedView === 'grid') {
            document.getElementById('viewGrid').checked = true;
            const gridToggle = document.querySelector('[for="viewGrid"]');
            gridToggle?.click();
        }
    });

    // Seleção em massa
    let selectedCourses = new Set();
    
    function updateBulkButtons() {
        const count = selectedCourses.size;
        const bulkPublish = document.getElementById('bulkPublish');
        const bulkArchive = document.getElementById('bulkArchive');
        const bulkDelete = document.getElementById('bulkDelete');
        
        [bulkPublish, bulkArchive, bulkDelete].forEach(btn => {
            if (btn) {
                btn.disabled = count === 0;
            }
        });
        
        return count;
    }
    
    // Check all
    const checkAll = document.getElementById('checkAll');
    const tableCheckAll = document.getElementById('tableCheckAll');
    const coursesTable = document.getElementById('admin-courses-table');

    function getRowChecks() {
        return Array.from(document.querySelectorAll('#admin-courses-table .row-check'));
    }

    function syncMasterChecks() {
        const rowChecks = getRowChecks();
        const allChecked = rowChecks.length > 0 && rowChecks.every(c => c.checked);
        if (checkAll) checkAll.checked = allChecked;
        if (tableCheckAll) tableCheckAll.checked = allChecked;
    }

    function handleCheckAll(checkbox) {
        const isChecked = checkbox.checked;
        const rowChecks = getRowChecks();
        rowChecks.forEach(check => {
            check.checked = isChecked;
            if (isChecked) {
                selectedCourses.add(check.value);
            } else {
                selectedCourses.delete(check.value);
            }
        });
        updateBulkButtons();
    }
    
    if (checkAll) {
        checkAll.addEventListener('change', () => handleCheckAll(checkAll));
    }
    
    if (tableCheckAll) {
        tableCheckAll.addEventListener('change', () => handleCheckAll(tableCheckAll));
    }
    
    // Check individual
    coursesTable?.addEventListener('change', function(event) {
        const target = event.target;
        if (!(target instanceof HTMLInputElement) || !target.classList.contains('row-check')) {
            return;
        }

        if (target.checked) {
            selectedCourses.add(target.value);
        } else {
            selectedCourses.delete(target.value);
        }

        syncMasterChecks();
        updateBulkButtons();
    });

    // Modal de exclusão
    let deleteCourseId = null;
    let deleteCourseTitle = '';
    
    document.addEventListener('click', function(event) {
        const deleteButton = event.target.closest('.btn-delete');
        if (!deleteButton) return;

        deleteCourseId = deleteButton.getAttribute('data-id');
        deleteCourseTitle = deleteButton.getAttribute('data-title');

        document.getElementById('deleteCourseTitle').textContent = deleteCourseTitle;
        document.getElementById('deleteCourseId').value = deleteCourseId;

        document.getElementById('deleteModal').classList.remove('hidden');
    });
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteCourseId = null;
        deleteCourseTitle = '';
    }
    
    function confirmDelete() {
        if (deleteCourseId) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Modal de ação em massa
    let currentBulkAction = '';
    let bulkActionRoute = '';
    
    document.getElementById('bulkPublish').addEventListener('click', () => {
        showBulkModal('publish', 'Publicar Cursos', 
            `Tem certeza que deseja publicar ${selectedCourses.size} curso(s) selecionado(s)?`,
            '<?= route_to('admin.courses.bulkPublish') ?>');
    });
    
    document.getElementById('bulkArchive').addEventListener('click', () => {
        showBulkModal('archive', 'Arquivar Cursos', 
            `Tem certeza que deseja arquivar ${selectedCourses.size} curso(s) selecionado(s)?`,
            '<?= route_to('admin.courses.bulkArchive') ?>');
    });
    
    document.getElementById('bulkDelete').addEventListener('click', () => {
        showBulkModal('delete', 'Excluir Cursos', 
            `Tem certeza que deseja excluir ${selectedCourses.size} curso(s) selecionado(s)? Esta ação não pode ser desfeita.`,
            '<?= route_to('admin.courses.bulkDelete') ?>');
    });
    
    function showBulkModal(action, title, message, route) {
        currentBulkAction = action;
        bulkActionRoute = route;
        
        document.getElementById('bulkModalTitle').textContent = title;
        document.getElementById('bulkModalMessage').textContent = message;
        document.getElementById('bulkSelectedCount').textContent = selectedCourses.size;
        document.getElementById('bulkModalSubtitle').textContent = `${selectedCourses.size} curso(s) selecionado(s)`;
        
        document.getElementById('bulkModal').classList.remove('hidden');
    }
    
    function closeBulkModal() {
        document.getElementById('bulkModal').classList.add('hidden');
        currentBulkAction = '';
        bulkActionRoute = '';
    }
    
    function confirmBulkAction() {
        if (selectedCourses.size === 0 || !bulkActionRoute) {
            closeBulkModal();
            return;
        }
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = bulkActionRoute;
        form.style.display = 'none';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '<?= csrf_token() ?>';
        csrfToken.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfToken);
        
        // IDs selecionados
        selectedCourses.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
        
        closeBulkModal();
    }

    // Fechar modais ao clicar fora
    document.addEventListener('click', function(event) {
        const deleteModal = document.getElementById('deleteModal');
        const bulkModal = document.getElementById('bulkModal');
        
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
        
        if (event.target === bulkModal) {
            closeBulkModal();
        }
    });

    // Fechar modais com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDeleteModal();
            closeBulkModal();
        }
    });

</script>

<?= $this->endSection() ?>
