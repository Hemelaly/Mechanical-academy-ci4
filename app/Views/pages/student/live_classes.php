<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Aulas ao Vivo<?= $this->endSection() ?>

<?= $this->section('jitsi') ?>
<?php
$classes = $classes ?? [];
$recordingsByClass = $recordingsByClass ?? [];
?>

<div class="min-w-0">
    <div class="container mx-auto space-y-6">
        <div class="flex flex-col gap-2">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Aulas ao Vivo</h1>
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Entre em aulas em tempo real e acesse gravacoes publicadas pelo instrutor.
            </p>
        </div>

        <?php if (empty($classes)): ?>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6">
                <p class="text-sm text-slate-500 dark:text-slate-300">Nenhuma aula ao vivo disponivel no momento.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($classes as $class): ?>
                    <?php
                    $status = (string) ($class->status_jitsi ?? 'Pendente');
                    $badgeClass = match ($status) {
                        'Ao vivo' => 'bg-green-500',
                        'Expirado' => 'bg-red-500',
                        default => 'bg-amber-500',
                    };
                    $classRecordings = $recordingsByClass[(int) $class->id_jitsi] ?? [];
                    ?>
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5">
                        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                            <div class="space-y-2 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white truncate">
                                        <?= esc($class->title_jitsi) ?>
                                    </h2>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white <?= $badgeClass ?>">
                                        <?= esc($status) ?>
                                    </span>
                                </div>

                                <p class="text-sm text-slate-600 dark:text-slate-300">
                                    <?= esc($class->description_jitsi ?: 'Sem descricao.') ?>
                                </p>

                                <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap gap-3">
                                    <span><i class="bi bi-book mr-1"></i><?= esc($class->title_course) ?></span>
                                    <span><i class="bi bi-person-workspace mr-1"></i><?= esc($class->instructor_name) ?></span>
                                    <span><i class="bi bi-calendar-event mr-1"></i><?= esc($class->meeting_date_jitsi ?: '-') ?></span>
                                    <span><i class="bi bi-clock mr-1"></i><?= esc(($class->start_time_jitsi ?: '--:--') . ' - ' . ($class->end_time_jitsi ?: '--:--')) ?></span>
                                </div>
                            </div>

                            <div class="flex flex-col items-start md:items-end gap-2">
                                <a href="<?= site_url('student/dashboard/aulas_ao_vivo/stream/' . (int) $class->id_jitsi) ?>"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-medium">
                                    <i class="bi bi-camera-video"></i>
                                    Entrar na Aula
                                </a>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    Gravacao: <?= (int) ($class->recording_jitsi ?? 0) === 1 ? 'ativada' : 'desativada' ?>
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Replays publicados</h3>

                            <?php if (empty($classRecordings)): ?>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Nenhum replay publicado para esta aula.</p>
                            <?php else: ?>
                                <div class="space-y-2">
                                    <?php foreach ($classRecordings as $rec): ?>
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 rounded-lg bg-slate-100 dark:bg-slate-700/50 px-3 py-2">
                                            <a href="<?= esc($rec->recording_url) ?>" target="_blank" rel="noopener"
                                                class="text-sm text-blue-700 dark:text-blue-300 hover:underline break-all">
                                                <?= esc($rec->recording_url) ?>
                                            </a>
                                            <span class="text-xs text-slate-500 dark:text-slate-300 whitespace-nowrap">
                                                <?= esc($rec->published_at ?: $rec->updated_at ?: $rec->created_at ?: '-') ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
