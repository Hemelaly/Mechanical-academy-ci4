<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Painel do Instrutor<?= $this->endSection() ?>

<?= $this->section('home_instructor') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <div class="container mx-auto">
        
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-900 rounded-2xl p-6 md:p-8 mb-8 text-white shadow-lg">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold mb-3">
                        Olá, Professor <?php echo esc($user->username) ?>!
                    </h1>
                    <p class="text-blue-100 text-sm md:text-base max-w-2xl leading-relaxed">
                        Bem-vindo de volta ao seu painel de instrutor. Acompanhe seu desempenho e engajamento dos alunos.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/instructor/dashboard/novo_curso"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                        <i class="bi bi-plus-lg"></i>
                        Criar Novo Curso
                    </a>
                    <a href="/instructor/dashboard/meus_cursos"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white/10 text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-0.5 border border-white/20">
                        <i class="bi bi-gear"></i>
                        Gerenciar Cursos
                    </a>
                    <a href="/instructor/dashboard/financas"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-black/20 text-white font-semibold rounded-xl hover:bg-black/30 transition-all duration-300 transform hover:-translate-y-0.5 border border-white/10">
                        <i class="bi bi-graph-up"></i>
                        Ver Relatórios
                    </a>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total de Cursos -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Total de Cursos</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">24</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-green-500 text-sm"></i>
                            <span class="text-green-500 text-sm font-medium">+3 este mês</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-journal-text text-green-600 dark:text-green-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Alunos Inscritos -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Alunos Inscritos</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">1,245</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-green-500 text-sm"></i>
                            <span class="text-green-500 text-sm font-medium">+150 este mês</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-people text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Receita Mensal -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Receita Mensal</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">0,00 MZN</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-arrow-up-short text-green-500 text-sm"></i>
                            <span class="text-green-500 text-sm font-medium">0,00 MZN</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-currency-dollar text-purple-600 dark:text-purple-400 text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Avaliação Média -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Avaliação Média</p>
                        <h3 class="text-2xl font-bold text-slate-800 dark:text-white">4.8/5</h3>
                        <div class="flex items-center gap-1 mt-1">
                            <i class="bi bi-star-fill text-amber-500 text-sm"></i>
                            <span class="text-amber-500 text-sm font-medium">⭐ 4.8</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-star text-amber-600 dark:text-amber-400 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cursos em Destaque -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-2 sm:mb-0">Cursos em Destaque</h2>
                <a href="#" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-semibold hover:text-blue-700 dark:hover:text-blue-300 transition-colors text-sm">
                    Ver todos os cursos
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Curso 1 -->
                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors mb-1">
                                JavaScript Avançado
                            </h3>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-people"></i>
                                <span>234 alunos</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full text-xs font-semibold">
                            Ativo
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Progresso</span>
                            <span class="font-semibold text-slate-800 dark:text-white">85%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-500" style="width: 85%"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-slate-500 dark:text-slate-400 text-sm">85% completo</span>
                        <div class="flex items-center gap-1 text-amber-500">
                            <i class="bi bi-star-fill text-sm"></i>
                            <span class="text-sm font-semibold">4.9</span>
                        </div>
                    </div>
                </div>

                <!-- Curso 2 -->
                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-600 transition-all duration-300 group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors mb-1">
                                React para Iniciantes
                            </h3>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-people"></i>
                                <span>189 alunos</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-full text-xs font-semibold">
                            Popular
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Progresso</span>
                            <span class="font-semibold text-slate-800 dark:text-white">92%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-500" style="width: 92%"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-slate-500 dark:text-slate-400 text-sm">92% completo</span>
                        <div class="flex items-center gap-1 text-amber-500">
                            <i class="bi bi-star-fill text-sm"></i>
                            <span class="text-sm font-semibold">4.7</span>
                        </div>
                    </div>
                </div>

                <!-- Curso 3 -->
                <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-5 border border-slate-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300 group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-slate-800 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors mb-1">
                                Node.js Completo
                            </h3>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                <i class="bi bi-people"></i>
                                <span>152 alunos</span>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400 rounded-full text-xs font-semibold">
                            Em alta
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-400">Progresso</span>
                            <span class="font-semibold text-slate-800 dark:text-white">75%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-500" style="width: 75%"></div>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-slate-500 dark:text-slate-400 text-sm">75% completo</span>
                        <div class="flex items-center gap-1 text-amber-500">
                            <i class="bi bi-star-fill text-sm"></i>
                            <span class="text-sm font-semibold">4.8</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atividade e Metas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Atividade Recente -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Atividade Recente</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-person-plus text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-slate-800 dark:text-white mb-1">
                                25 novos alunos se inscreveram
                            </p>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Há 2 horas</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-slate-800 dark:text-white mb-1">
                                15 alunos completaram o curso
                            </p>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Hoje</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-star-fill text-amber-600 dark:text-amber-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-slate-800 dark:text-white mb-1">
                                8 novas avaliações recebidas
                            </p>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Há 1 dia</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximas Metas -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-lg border border-slate-200 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6">Próximas Metas</h3>
                
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-slate-800 dark:text-white">+100 alunos</span>
                            <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">65%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-500" style="width: 65%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-slate-800 dark:text-white">Avaliação 4.9+</span>
                            <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">80%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-500" style="width: 80%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-slate-800 dark:text-white">Novo curso</span>
                            <span class="text-slate-500 dark:text-slate-400 text-sm font-medium">30%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full transition-all duration-500" style="width: 30%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>