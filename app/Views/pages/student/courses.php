<?php
// dd($lesson)
?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Todos Cursos<?= $this->endSection() ?>

<?= $this->section('all_courses') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <div class="container mx-auto">

        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
                <div class="flex-1">
                    <h1 class="text-2xl lg:text-2xl font-bold text-slate-800 dark:text-white mb-3">
                        Catálogo de Cursos
                    </h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 max-w-2xl">
                        Explore nossa seleção completa de cursos e continue sua jornada de aprendizado.
                    </p>
                </div>

                <!-- Search and Filters -->
                <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-80">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-search text-slate-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Pesquisar cursos..."
                            class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-2xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <button class="inline-flex cursor-pointer items-center gap-2 px-6 py-3 bg-gradient-to-br from-blue-500 to-blue-900 text-white font-medium rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">
                        <i class="bi bi-sliders text-white"></i>
                        Filtrar
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Total de Cursos</p>
                            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                                <?= count($courses ?? []) ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                            <i class="bi bi-journal-text text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Cursos Ativos</p>
                            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                                <?= count($activeCourseIds ?? []) ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                            <i class="bi bi-play-circle text-green-600 dark:text-green-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Cursos Pendentes</p>
                            <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">
                                <?= count($pendingCourseIds ?? []) ?>
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900 rounded-xl flex items-center justify-center">
                            <i class="bi bi-clock text-amber-600 dark:text-amber-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cursos em Andamento -->
        <?php if (!empty($activeCourseIds)): ?>
            <div class="mb-8">
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
                            <span class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full text-sm font-semibold">
                                <?= count($activeCourseIds) ?> ativos
                            </span>
                        </div>
                    </div>

                    <!-- Active Courses List -->
                    <div class="p-6">
                        <div class="grid gap-4">
                            <?php foreach ($courses as $key => $course): ?>
                                <?php if (in_array($course->id_course, $activeCourseIds)): ?>
                                    <div class="group p-5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 hover:shadow-md">
                                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                            <!-- Course Info -->
                                            <div class="flex-1">
                                                <div class="flex items-start gap-3 mb-3">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                                        <i class="bi bi-play text-white text-sm"></i>
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
                                                            <span>Instrutor: <?= esc($lesson[$key]->username ?? 'N/A') ?></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Progress -->
                                                <div class="space-y-2">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-slate-600 dark:text-slate-400">Seu progresso</span>
                                                        <span class="font-semibold text-slate-800 dark:text-white">
                                                            <?= (int) ($lesson[$key]->progress ?? 0) ?>%
                                                        </span>
                                                    </div>
                                                    <div class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                                        <div
                                                            class="h-2.5 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-500 ease-out"
                                                            style="width: <?= (int) ($lesson[$key]->progress ?? 0) ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Button -->
                                            <a href="<?= '/student/dashboard/ver_aulas/' . ($lesson[$key]->resumeLessonId ?? '') ?>?autoplay=1"
                                                class="group/btn px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25 flex items-center gap-2 whitespace-nowrap">
                                                <i class="bi bi-play-circle group-hover/btn:scale-110 transition-transform"></i>
                                                Continuar
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Todos os Cursos Disponíveis -->
        <div>
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <i class="bi bi-mortarboard text-white text-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-xl font-bold text-slate-800 dark:text-white">Todos os Cursos Disponíveis</h5>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">Explore nossa biblioteca completa</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full text-sm font-semibold">
                            <?= count($courses) ?> Cursos
                        </span>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <?php foreach ($courses as $key => $course): ?>
                            <div class="course-card-item bg-white dark:bg-slate-800 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 border border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600 group"
                                data-title="<?= strtolower(esc($course->title_course)) ?>"
                                data-category="<?= strtolower(esc($course->category ?? '')) ?>">

                                <!-- Course Image -->
                                <div class="relative overflow-hidden">
                                    <img src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
                                        alt="<?= esc($course->title_course) ?>"
                                        class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">

                                    <!-- Status Badge -->
                                    <div class="absolute top-4 right-4">
                                        <?php if (in_array($course->id_course, $activeCourseIds)): ?>
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white text-sm font-semibold rounded-full shadow-lg">
                                                <i class="bi bi-play-circle"></i>
                                                <span>Ativo</span>
                                            </span>
                                        <?php elseif (in_array($course->id_course, $pendingCourseIds)): ?>
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 text-white text-sm font-semibold rounded-full shadow-lg">
                                                <i class="bi bi-clock"></i>
                                                <span>Pendente</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500 text-white text-sm font-semibold rounded-full shadow-lg">
                                                <i class="bi bi-unlock"></i>
                                                <span>Disponível</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Category Badge -->
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-black/80 text-white text-sm font-semibold rounded-full backdrop-blur-sm">
                                            <i class="bi bi-tag"></i>
                                            <?= esc($course->category ?? 'Curso') ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Course Content -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                            <?= esc($course->title_course) ?>
                                        </h3>
                                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 text-sm mb-3">
                                            <i class="bi bi-person"></i>
                                            <span>Prof. <?= esc($lesson[$key]->username ?? 'N/A') ?></span>
                                        </div>
                                        <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-2">
                                            <?= esc($course->description_course) ?>
                                        </p>
                                    </div>

                                    <!-- Price and Action -->
                                    <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-700">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800 dark:text-white">
                                                MT <?= number_format($course->price_course, 2, ',', '.') ?>
                                            </span>
                                        </div>

                                        <div class="flex gap-2">
                                            <?php if (in_array($course->id_course, $activeCourseIds)): ?>
                                                <a href="<?= '/student/dashboard/ver_aulas/' . ($lesson[$key]->resumeLessonId ?? '') ?>?autoplay=1"
                                                    class="group/btn inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-green-500/25">
                                                    <i class="bi bi-play-circle group-hover/btn:scale-110 transition-transform"></i>
                                                    Continuar
                                                </a>
                                            <?php elseif (in_array($course->id_course, $pendingCourseIds)): ?>
                                                <a href="/checkout/<?= $course->id_course ?>"
                                                    class="group/btn inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-amber-500/25">
                                                    <i class="bi bi-clock group-hover/btn:scale-110 transition-transform"></i>
                                                    Pendente
                                                </a>
                                            <?php else: ?>
                                                <a href="/checkout/<?= $course->id_course ?>"
                                                    class="group/btn inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25">
                                                    <i class="bi bi-cart3 group-hover/btn:scale-110 transition-transform"></i>
                                                    Inscrever
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Empty State -->
                    <?php if (empty($courses)): ?>
                        <div class="text-center py-16">
                            <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                <i class="bi bi-journal-text text-slate-400"></i>
                            </div>
                            <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-3">
                                Nenhum curso disponível
                            </h3>
                            <p class="text-slate-500 dark:text-slate-500 text-sm mb-8 max-w-md mx-auto">
                                Não há cursos disponíveis no momento. Volte em breve para novas oportunidades de aprendizado.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchInput = document.getElementById("searchInput");
        const courseCards = document.querySelectorAll(".course-card-item");

        searchInput.addEventListener("keyup", () => {
            const search = searchInput.value.toLowerCase();
            courseCards.forEach(card => {
                const title = card.getAttribute("data-title");
                const category = card.getAttribute("data-category");

                const matchesSearch = title.includes(search) || category.includes(search);
                card.style.display = matchesSearch ? "block" : "none";

                // Smooth animation
                if (matchesSearch) {
                    card.style.opacity = "1";
                    card.style.transform = "scale(1)";
                } else {
                    card.style.opacity = "0.5";
                    card.style.transform = "scale(0.95)";
                }
            });
        });

        // Add CSS for line clamping and animations
        const style = document.createElement('style');
        style.textContent = `
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .course-card-item {
                transition: all 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    });
</script>

<?= $this->endSection() ?>