<?php
/**
 * Lista de módulos/aulas do curso (drawer do player).
 *
 * @var array $modules
 * @var object $lesson
 * @var array $completedLessonIds
 * @var array $lessonSlugById
 * @var callable $resolveLessonUrl
 * @var bool $closeOnNavigate Fechar drawer ao clicar numa aula (mobile overlay)
 */
$completedLessonIds = $completedLessonIds ?? [];
$lessonSlugById = $lessonSlugById ?? [];
$closeOnNavigate = (bool) ($closeOnNavigate ?? true);
?>
<div class="space-y-3">
    <?php foreach ($modules as $index => $m): ?>
        <div class="bg-gray-50 dark:bg-gray-700/80 border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
            <button type="button"
                class="module-header w-full flex justify-between items-center p-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                onclick="toggleModule(<?= (int) $index ?>)"
                aria-expanded="false"
                aria-controls="module-<?= (int) $index ?>">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-2 h-2 bg-blue-500 rounded-full shrink-0"></span>
                    <span class="font-medium text-gray-900 dark:text-white text-left text-sm truncate"><?= esc($m->title_module) ?></span>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-gray-600 dark:text-gray-400 text-xs"><?= count($m->lessons) ?> aulas</span>
                    <i class="bi bi-chevron-down text-gray-500 text-xs transition-transform duration-300"></i>
                </div>
            </button>

            <div id="module-<?= (int) $index ?>" class="module-content hidden">
                <?php foreach ($m->lessons as $l): ?>
                    <?php
                    $isCurrent = ((int) $l->id_lesson === (int) $lesson->id_lesson);
                    $isDone = in_array($l->id_lesson, $completedLessonIds, true);
                    $isQuizLessonRow = ($l->type_lesson === 'quiz');
                    $lessonSlug = $lessonSlugById[(int) $l->id_lesson] ?? '';
                    $iconClass = in_array($l->type_lesson, ['quiz', 'text'], true) ? 'bi-file-text' : 'bi-camera-video';
                    $lessonUrl = $resolveLessonUrl((int) $l->id_lesson, $lessonSlug !== '' ? $lessonSlug : null);
                    ?>
                    <div class="lesson-row flex items-center justify-between p-3 border-t border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors <?= $isCurrent ? 'bg-blue-50 dark:bg-blue-900/30 border-l-2 border-blue-500' : '' ?>"
                        data-lesson-id="<?= (int) $l->id_lesson ?>"
                        data-lesson-type="<?= esc($l->type_lesson) ?>">

                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="relative">
                                <input type="checkbox"
                                    class="lesson-check w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                    <?= $isDone ? 'checked' : '' ?>
                                    aria-label="Marcar aula como concluída"
                                    <?= $isQuizLessonRow ? 'disabled title="A conclusão deste quiz é controlada pelas respostas."' : '' ?>>
                            </div>

                            <a class="lesson-link flex items-center gap-2 text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex-1 min-w-0"
                                href="<?= esc($lessonUrl) ?>"
                                <?php if ($closeOnNavigate): ?>onclick="if(!window.matchMedia('(min-width:1024px)').matches){closeDrawerFunc()}"<?php endif; ?>>
                                <i class="bi <?= $iconClass ?> text-slate-400"></i>
                                <span class="truncate text-sm"><?= esc($l->title_lesson) ?></span>
                                <?php if (! empty($l->attachment_path_lesson)): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-200 text-slate-700 dark:bg-slate-600 dark:text-slate-100 rounded-full text-[10px] font-medium">
                                        <i class="bi bi-paperclip"></i>
                                        Arquivo
                                    </span>
                                <?php endif; ?>
                                <?php if ($isCurrent): ?>
                                    <span class="badge-current font-medium px-2 py-0.5 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-full whitespace-nowrap text-xs">
                                        Atual
                                    </span>
                                <?php endif; ?>
                            </a>
                        </div>

                        <span class="text-gray-500 dark:text-gray-400 ml-2 whitespace-nowrap text-xs">
                            <?= esc($l->duration_lesson) ?> min
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
