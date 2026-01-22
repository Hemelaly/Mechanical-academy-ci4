<?php
// dd($courses)
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Painel do Estudante<?= $this->endSection() ?>

<?= $this->section('home_student') ?>

<!-- Banner Hero -->
<div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8 rounded-3xl text-white mb-8 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-32 translate-x-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-cyan-300 rounded-full translate-y-24 -translate-x-24"></div>
    </div>
    
    <div class="relative z-10">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <h4 class="text-2xl font-bold mb-2">Olá, <?= esc($user->username) ?>! 👋</h4>
                <p class="text-blue-100 text-sm max-w-2xl leading-relaxed">
                    Bem-vindo de volta ao seu painel de estudante. Acompanhe seu 
                    desempenho e continue sua jornada de aprendizado.
                </p>
            </div>
            <div class="hidden md:flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl backdrop-blur-sm">
                <i class="bi bi-graph-up-arrow text-2xl text-white"></i>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-4 mt-6">
            <a href="/student/dashboard/meus_cursos"
                class="group px-6 py-3.5 bg-white text-blue-600 font-semibold rounded-xl hover:bg-white/95 transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl flex items-center gap-2">
                <i class="bi bi-play-circle text-sm"></i>
                Ver Meus Cursos
            </a>
            <a href="/student/dashboard/cursos"
                class="group px-6 py-3.5 bg-white/15 text-white font-semibold rounded-xl hover:bg-white/25 transition-all duration-300 transform hover:-translate-y-0.5 backdrop-blur-sm border border-white/20 hover:border-white/30 flex items-center gap-2">
                <i class="bi bi-collection text-sm"></i>
                Explorar Cursos
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Cursos Ativos</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1"><?= count($activeCourseIds) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                <i class="bi bi-book text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total de Cursos</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1"><?= count($courses) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                <i class="bi bi-grid-3x3 text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Progresso Médio</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                    <?php
                    $totalProgress = 0;
                    $activeCount = 0;
                    foreach ($courses as $course) {
                        if (in_array($course->id_course, $activeCourseIds)) {
                            $totalProgress += $progress->{$course->id_course}->progress;
                            $activeCount++;
                        }
                    }
                    echo $activeCount > 0 ? round($totalProgress / $activeCount) : 0;
                    ?>%
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                <i class="bi bi-graph-up text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Cursos em andamento -->
<div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="bi bi-play-circle text-white text-sm"></i>
                </div>
                <div>
                    <h5 class="text-xl font-bold text-slate-800 dark:text-white">Cursos em Andamento</h5>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Continue de onde parou</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full text-sm font-semibold">
                    <?= count($activeCourseIds) ?> ativos
                </span>
            </div>
        </div>
    </div>

    <!-- Cursos List -->
    <div class="p-6">
        <div class="grid gap-4">
            <?php foreach ($courses as $course): ?>
                <?php if (in_array($course->id_course, $activeCourseIds)): ?>
                    <div class="group p-5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 hover:shadow-md">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Course Info -->
                            <div class="flex-1">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-play-btn text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-sm font-semibold text-slate-800 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                <?= esc($course->title_course) ?>
                                            </h3>
                                            <span class="px-2.5 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-full">
                                                <?= esc($course->category ?? 'Curso') ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-sm">
                                            <i class="bi bi-person"></i>
                                            <span>Instrutor: <?= esc($course->name_instructor ?? 'N/A') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress -->
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-600 dark:text-slate-400">Seu progresso</span>
                                        <span class="font-semibold text-slate-800 dark:text-white">
                                            <?= esc((int) round($progress->{$course->id_course}->progress)) ?>%
                                        </span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div 
                                            class="h-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-500 ease-out"
                                            style="width: <?= esc((int) round($progress->{$course->id_course}->progress)) ?>%"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="flex lg:flex-col gap-3 lg:items-end">
                                <?php
                                $lessonUrl = (!empty($lesson[0]->courseSlug) && !empty($lesson[0]->resumeLessonSlug))
                                    ? site_url('student/dashboard/inscricoes/' . $lesson[0]->courseSlug . '/' . $lesson[0]->resumeLessonSlug)
                                    : site_url('student/dashboard/ver_aulas/' . $lesson[0]->resumeLessonId);
                                ?>
                                <a href="<?= $lessonUrl ?>?autoplay=1"
                                    class="group/btn px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25 flex items-center gap-2 whitespace-nowrap">
                                    <i class="bi bi-play-circle group-hover/btn:scale-110 transition-transform"></i>
                                    Continuar
                                </a>
                                
                                <button class="px-3 py-2.5 border border-slate-300 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors flex items-center gap-2">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <?php if (count($activeCourseIds) === 0): ?>
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-book text-slate-400 text-2xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-600 dark:text-slate-400 mb-2">
                    Nenhum curso em andamento
                </h3>
                <p class="text-slate-500 dark:text-slate-500 mb-6">
                    Comece um novo curso para ver seu progresso aqui.
                </p>
                <a href="/student/dashboard/cursos"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-xl transition-colors">
                    <i class="bi bi-plus-lg"></i>
                    Explorar Cursos
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
    <a href="/student/dashboard/meus_cursos" 
        class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 group text-center">
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="bi bi-collection-play text-blue-600 dark:text-blue-400 text-xl"></i>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Meus Cursos</span>
    </a>
    
    <a href="/student/dashboard/cursos" 
        class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-600 transition-all duration-300 group text-center">
        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="bi bi-compass text-green-600 dark:text-green-400 text-xl"></i>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Descobrir</span>
    </a>
    
    <a href="/student/profile" 
        class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 group text-center">
        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="bi bi-person text-purple-600 dark:text-purple-400 text-xl"></i>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Perfil</span>
    </a>
    
    <a href="/student/settings" 
        class="p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-300 group text-center">
        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform">
            <i class="bi bi-gear text-orange-600 dark:text-orange-400 text-xl"></i>
        </div>
        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Configurações</span>
    </a>
</div>

<?= $this->endSection() ?>
