<?php

// dd($courses);

?>

<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Aulas Virtuais<?= $this->endSection() ?>

<?= $this->section('jitsi') ?>

<?php
/** @var array $errors */
$errors = session('errors') ?? [];
$aulas  = $aulas ?? []; // lista de aulas vindas do controller
?>

<?php if (! empty($errors)): ?>
    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-w-0">
    <div class="container mx-auto">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
                        <i class="bi bi-camera-video text-blue-600 mr-3"></i>
                        Aulas Virtuais
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Gerencie as suas aulas virtuais: crie, agende, edite e exclua.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                        onclick="openCreateForm()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition">
                        <i class="bi bi-plus-circle"></i>
                        Criar Aula ao Vivo
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal: Lista + Form -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Tabela de Aulas -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="bi bi-list-ul text-blue-600"></i>
                            Minhas Aulas
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table
                            id="instructor-live-classes-table"
                            data-flowbite-datatable
                            data-datatable-per-page="8"
                            class="w-full text-sm text-left">
                            <thead class="bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-white">
                                <tr>
                                    <th class="px-6 py-3 whitespace-nowrap">Título</th>
                                    <th class="px-6 py-3 whitespace-nowrap">Tipo</th>
                                    <th class="px-6 py-3 whitespace-nowrap">Data</th>
                                    <th class="px-6 py-3 whitespace-nowrap">Horário</th>
                                    <th class="px-6 py-3 whitespace-nowrap">Estado</th>
                                    <th class="px-6 py-3 whitespace-nowrap text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                <?php if (empty($aulas)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-6 text-center text-slate-500 dark:text-slate-400">
                                            Nenhuma aula criada ainda.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($aulas as $aula): ?>
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/60 transition"
                                        data-id="<?= $aula->id_jitsi ?>"
                                        data-title="<?= esc($aula->title_jitsi, 'attr') ?>"
                                        data-description="<?= esc($aula->description_jitsi ?? '', 'attr') ?>"
                                        data-course="<?= esc($aula->id_course_jitsi ?? '', 'attr') ?>"
                                        data-class-type="<?= esc($aula->class_type_jitsi, 'attr') ?>"
                                        data-date="<?= esc($aula->meeting_date_jitsi ?? '', 'attr') ?>"
                                        data-start="<?= esc($aula->start_time_jitsi ?? '', 'attr') ?>"
                                        data-end="<?= esc($aula->end_time_jitsi ?? '', 'attr') ?>"
                                        data-status="<?= esc($aula->status_jitsi, 'attr') ?>"
                                        data-privacy="<?= esc($aula->privacy_jitsi, 'attr') ?>"

                                        data-recording="<?= esc($aula->recording_jitsi, 'attr') ?>"
                                        data-chat="<?= esc($aula->chat_jitsi, 'attr') ?>"
                                        data-screenshare="<?= esc($aula->screenshare_jitsi, 'attr') ?>">
                                        <td class="px-6 py-3 text-slate-800 dark:text-slate-50 font-medium">
                                            <?= esc($aula->title_jitsi) ?>
                                        </td>
                                        <td class="px-6 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                            <?= $aula->class_type_jitsi === 'instant' ? 'Instantânea' : 'Agendada' ?>
                                        </td>
                                        <td class="px-6 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                            <?= $aula->meeting_date_jitsi ?: '-' ?>
                                        </td>
                                        <td class="px-6 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                            <?php if ($aula->start_time_jitsi && $aula->end_time_jitsi): ?>
                                                <?= esc($aula->start_time_jitsi) ?> - <?= esc($aula->end_time_jitsi) ?>
                                            <?php elseif ($aula->start_time_jitsi): ?>
                                                <?= esc($aula->start_time_jitsi) ?> - --
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <?php
                                            $status = $aula->status_jitsi;
                                            $badgeClass = match ($status) {
                                                'Ao vivo'  => 'bg-green-500',
                                                'Pendente' => 'bg-amber-500',
                                                'Expirado' => 'bg-red-500',
                                                default    => 'bg-slate-500',
                                            };
                                            ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold text-white <?= $badgeClass ?>">
                                                <?= esc($status) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <!-- Entrar (placeholder, ajusta o link depois) -->
                                                <a href="<?= site_url('instructor/dashboard/jitsi/stream/' . $aula->id_jitsi) ?>"
                                                    class="text-blue-600 dark:text-blue-400 hover:underline text-xs md:text-sm">
                                                    Entrar
                                                </a>

                                                <!-- Editar -->
                                                <button type="button"
                                                    class="text-amber-600 dark:text-amber-400 hover:underline text-xs md:text-sm"
                                                    onclick="openEditForm(this)">
                                                    Editar
                                                </button>

                                                <!-- Excluir -->
                                                <button type="button"
                                                    class="text-red-600 dark:text-red-400 hover:underline text-xs md:text-sm"
                                                    onclick="confirmDelete(<?= $aula->id_jitsi ?>)">
                                                    Excluir
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulário Criar/Editar (mesmo form, reaproveitado) -->
                <div id="classFormCard"
                    class="hidden bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700">
                    <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <h2 id="formTitle" class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="bi bi-pencil-square text-blue-600"></i>
                            Criar Aula Virtual
                        </h2>
                        <button type="button"
                            onclick="closeForm()"
                            class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <form id="virtualClassForm"
                        action="<?= site_url('instructor/dashboard/jitsi/criar_sala') ?>"
                        method="POST"
                        class="p-6 space-y-6">

                        <?= csrf_field() ?>
                        <input type="hidden" name="id_jitsi" id="id_jitsi">

                        <!-- Informações Básicas -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-info-circle text-blue-500"></i>
                                Informações da Aula
                            </h3>

                            <!-- Título da Aula -->
                            <div>
                                <label for="classTitle" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                    Título da Aula *
                                </label>
                                <input type="text"
                                    id="classTitle"
                                    name="classTitle"
                                    value="<?= old('classTitle') ?>"
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                        <?= isset($errors['classTitle']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                        text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:border-transparent transition-all duration-200"
                                    placeholder="Ex: Aula de Matemática - Trigonometria"
                                    required>
                                <?php if (isset($errors['classTitle'])): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= esc($errors['classTitle']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Descrição -->
                            <div>
                                <label for="classDescription" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                    Descrição
                                </label>
                                <textarea id="classDescription"
                                    name="classDescription"
                                    rows="4"
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                        <?= isset($errors['classDescription']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                        text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="Descreva o conteúdo que será abordado na aula..."><?= old('classDescription') ?></textarea>
                                <?php if (isset($errors['classDescription'])): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= esc($errors['classDescription']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Curso Associado -->
                            <div>
                                <label for="associatedCourse" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                    Curso Associado
                                </label>
                                <select id="associatedCourse"
                                    name="associatedCourse"
                                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                        <?= isset($errors['associatedCourse']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                        text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                                    <option value="">Selecione um curso</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= $course->id_course ?>" <?= old('associatedCourse') == (string) $course->id_course ? 'selected' : '' ?>><?= $course->title_course ?></option>
                                    <?php endforeach ?>
                                </select>
                                <?php if (isset($errors['associatedCourse'])): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= esc($errors['associatedCourse']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Configurações de Data/Hora -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-calendar-event text-blue-500"></i>
                                Data e Horário
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Data -->
                                <div>
                                    <label for="classDate" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                        Data
                                    </label>
                                    <input type="date"
                                        id="classDate"
                                        name="classDate"
                                        value="<?= old('classDate') ?>"
                                        class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                            <?= isset($errors['classDate']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                            text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                                    <?php if (isset($errors['classDate'])): ?>
                                        <p class="mt-1 text-sm text-red-600">
                                            <?= esc($errors['classDate']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Horário -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="startTime" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                            Início
                                        </label>
                                        <input type="time"
                                            id="startTime"
                                            name="startTime"
                                            value="<?= old('startTime') ?>"
                                            class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                                <?= isset($errors['startTime']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                                text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                                        <?php if (isset($errors['startTime'])): ?>
                                            <p class="mt-1 text-sm text-red-600">
                                                <?= esc($errors['startTime']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label for="endTime" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                            Fim
                                        </label>
                                        <input type="time"
                                            id="endTime"
                                            name="endTime"
                                            value="<?= old('endTime') ?>"
                                            class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                                <?= isset($errors['endTime']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                                text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                                        <?php if (isset($errors['endTime'])): ?>
                                            <p class="mt-1 text-sm text-red-600">
                                                <?= esc($errors['endTime']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de Aula -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                    Tipo de Aula
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-3 border 
                                            <?= isset($errors['classType']) ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' ?>
                                            rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <input type="radio"
                                            name="classType"
                                            value="instant"
                                            class="text-blue-600 focus:ring-blue-500"
                                            <?= old('classType', 'instant') === 'instant' ? 'checked' : '' ?>>
                                        <div>
                                            <span class="font-medium text-slate-800 dark:text-white">Iniciar Agora</span>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Crie uma sala imediatamente</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 border 
                                            <?= isset($errors['classType']) ? 'border-red-500' : 'border-slate-300 dark:border-slate-600' ?>
                                            rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        <input type="radio"
                                            name="classType"
                                            value="scheduled"
                                            class="text-blue-600 focus:ring-blue-500"
                                            <?= old('classType') === 'scheduled' ? 'checked' : '' ?>>
                                        <div>
                                            <span class="font-medium text-slate-800 dark:text-white">Agendar</span>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">Programe para data futura</p>
                                        </div>
                                    </label>
                                </div>
                                <?php if (isset($errors['classType'])): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= esc($errors['classType']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Configurações da Sala -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                <i class="bi bi-gear text-blue-500"></i>
                                Configurações da Sala
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Status -->
                                <div>
                                    <label for="roomStatus" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                        Estado
                                    </label>
                                    <select id="roomStatus"
                                        name="roomStatus"
                                        class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                            <?= isset($errors['roomStatus']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                            text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                                        <option value="">Seleccione o estado da aula</option>
                                        <option value="Pendente" <?= old('roomStatus') == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="Ao vivo" <?= old('roomStatus') == 'Ao vivo' ? 'selected' : '' ?>>Ao vivo</option>
                                        <option value="Expirado" <?= old('roomStatus') == 'Expirado' ? 'selected' : '' ?>>Expirado</option>
                                    </select>
                                    <?php if (isset($errors['roomStatus'])): ?>
                                        <p class="mt-1 text-sm text-red-600">
                                            <?= esc($errors['roomStatus']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Privacidade -->
                                <div>
                                    <label for="roomPrivacy" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                        Privacidade
                                    </label>
                                    <select id="roomPrivacy"
                                        name="roomPrivacy"
                                        class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                            <?= isset($errors['roomPrivacy']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                            text-slate-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200">
                                        <option value="public" <?= old('roomPrivacy') === 'public' ? 'selected' : '' ?>>Pública - Qualquer um pode entrar</option>
                                        <option value="private" <?= old('roomPrivacy', 'private') === 'private' ? 'selected' : '' ?>>Privada - Apenas com link</option>
                                        <option value="password" <?= old('roomPrivacy') === 'password' ? 'selected' : '' ?>>Protegida por senha</option>
                                    </select>
                                    <?php if (isset($errors['roomPrivacy'])): ?>
                                        <p class="mt-1 text-sm text-red-600">
                                            <?= esc($errors['roomPrivacy']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Senha (condicional) -->
                                <div id="passwordField" class="<?= old('roomPrivacy') === 'password' ? '' : 'hidden' ?>">
                                    <label for="roomPassword" class="block text-sm font-semibold text-slate-800 dark:text-white mb-2">
                                        Senha da Sala
                                    </label>
                                    <input type="password"
                                        id="roomPassword"
                                        name="roomPassword"
                                        value="<?= old('roomPassword') ?>"
                                        class="w-full px-4 py-3 bg-white dark:bg-slate-700 border 
                                            <?= isset($errors['roomPassword']) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-600 focus:ring-blue-500' ?>
                                            text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all duration-200"
                                        placeholder="Digite uma senha">
                                    <?php if (isset($errors['roomPassword'])): ?>
                                        <p class="mt-1 text-sm text-red-600">
                                            <?= esc($errors['roomPassword']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Configurações Avançadas -->
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 border border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <input type="checkbox"
                                        name="enableRecording"
                                        id="enableRecording"
                                        class="rounded text-blue-600 focus:ring-blue-500"
                                        <?= old('enableRecording') ? 'checked' : '' ?>>
                                    <div>
                                        <span class="font-medium text-slate-800 dark:text-white">Gravar aula automaticamente</span>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">A gravação ficará disponível para os alunos</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3 border border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <input type="checkbox"
                                        name="enableChat"
                                        id="enableChat"
                                        class="rounded text-blue-600 focus:ring-blue-500"
                                        <?= old('enableChat', 'on') ? 'checked' : '' ?>>
                                    <div>
                                        <span class="font-medium text-slate-800 dark:text-white">Habilitar chat</span>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Permitir que alunos enviem mensagens</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3 border border-slate-300 dark:border-slate-600 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <input type="checkbox"
                                        name="enableScreenShare"
                                        id="enableScreenShare"
                                        class="rounded text-blue-600 focus:ring-blue-500"
                                        <?= old('enableScreenShare', 'on') ? 'checked' : '' ?>>
                                    <div>
                                        <span class="font-medium text-slate-800 dark:text-white">Permitir compartilhamento de tela</span>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">Alunos podem compartilhar suas telas</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <button type="submit"
                                id="submitFormBtn"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25">
                                <i class="bi bi-camera-video"></i>
                                <span id="submitFormText">Criar Sala de Aula</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar direita (Preview / Dicas) -->
            <div class="space-y-6">
                <!-- Preview da Sala (podes adaptar depois para mostrar info da linha selecionada) -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="bi bi-eye text-blue-500"></i>
                        Preview / Detalhes
                    </h3>

                    <div class="bg-slate-900 rounded-xl p-4 aspect-video flex items-center justify-center mb-4">
                        <div class="text-center">
                            <i class="bi bi-camera-video text-slate-400 text-3xl mb-2"></i>
                            <p class="text-slate-400 text-sm">Selecione uma aula na lista para ver detalhes.</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Título:</span>
                            <span class="text-slate-800 dark:text-white font-medium" id="previewTitle">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Tipo:</span>
                            <span class="text-slate-800 dark:text-white font-medium" id="previewType">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Data:</span>
                            <span class="text-slate-800 dark:text-white font-medium" id="previewDate">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Horário:</span>
                            <span class="text-slate-800 dark:text-white font-medium" id="previewTime">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600 dark:text-slate-400">Estado:</span>
                            <span class="text-slate-800 dark:text-white font-medium" id="previewStatus">-</span>
                        </div>
                    </div>
                </div>

                <!-- Dicas Rápidas -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <i class="bi bi-lightbulb text-blue-500"></i>
                        Dicas Rápidas
                    </h3>
                    <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                            Teste seu áudio e vídeo antes de iniciar.
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                            Compartilhe materiais com antecedência.
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                            Use senha para aulas privadas.
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="bi bi-check-circle text-green-500 mt-0.5"></i>
                            Grave aulas importantes.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Abre form em modo criação
    function openCreateForm() {
        const formCard = document.getElementById('classFormCard');
        const form = document.getElementById('virtualClassForm');
        const formTitle = document.getElementById('formTitle');
        const submitText = document.getElementById('submitFormText');
        const idField = document.getElementById('id_jitsi');

        form.reset();
        idField.value = '';
        form.action = "<?= site_url('instructor/dashboard/jitsi/criar_sala') ?>";
        formTitle.textContent = 'Criar Aula Virtual';
        submitText.textContent = 'Criar Sala de Aula';

        formCard.classList.remove('hidden');
        formCard.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // Abre form em modo edição, preenchendo com dados da linha
    function openEditForm(button) {
        const row = button.closest('tr');
        const formCard = document.getElementById('classFormCard');
        const form = document.getElementById('virtualClassForm');
        const formTitle = document.getElementById('formTitle');
        const submitText = document.getElementById('submitFormText');

        const id = row.dataset.id;
        document.getElementById('id_jitsi').value = id;

        // Preenche campos
        document.getElementById('classTitle').value = row.dataset.title || '';
        document.getElementById('classDescription').value = row.dataset.description || '';
        document.getElementById('associatedCourse').value = row.dataset.course || '';

        document.getElementById('classDate').value = row.dataset.date || '';
        document.getElementById('startTime').value = row.dataset.start || '';
        document.getElementById('endTime').value = row.dataset.end || '';

        document.getElementById('roomStatus').value = row.dataset.status || '';
        document.getElementById('roomPrivacy').value = row.dataset.privacy || '';
        document.getElementById('roomPassword').value = ''; // senha nunca volta do servidor

        document.getElementById('enableRecording').checked = row.dataset.recording === '1';
        document.getElementById('enableChat').checked = row.dataset.chat === '1';
        document.getElementById('enableScreenShare').checked = row.dataset.screenshare === '1';

        // Tipo de aula
        const type = row.dataset.classType || 'instant';
        document.querySelectorAll('input[name="classType"]').forEach(r => {
            r.checked = (r.value === type);
        });

        // Atualiza ação para rota de edição
        form.action = "<?= site_url('instructor/dashboard/jitsi/editar') ?>/" + id;

        formTitle.textContent = 'Editar Aula Virtual';
        submitText.textContent = 'Salvar Alterações';

        formCard.classList.remove('hidden');
        formCard.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // Fechar form
    function closeForm() {
        document.getElementById('classFormCard').classList.add('hidden');
    }

        // Confirmacao de exclusao
    function confirmDelete(id) {
        const ok = window.confirm('Excluir aula? Esta acao nao pode ser desfeita.');
        if (!ok) {
            return;
        }

        window.location.href = "<?= site_url('instructor/dashboard/jitsi/deletar') ?>/" + id;
    }

    // Atualiza preview quando clicar na linha
    document.addEventListener('DOMContentLoaded', function() {
        const liveClassesTable = document.getElementById('instructor-live-classes-table');

        liveClassesTable?.addEventListener('click', function(e) {
            // evita conflito quando clicar nos botões de ação
            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target.closest('button') || e.target.closest('a')) {
                return;
            }

            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const title = row.dataset.title || '-';
            const type = row.dataset.classType === 'instant' ? 'Instantânea' : 'Agendada';
            const date = row.dataset.date || '-';
            let time = '-';
            if (row.dataset.start && row.dataset.end) {
                time = row.dataset.start + ' - ' + row.dataset.end;
            } else if (row.dataset.start) {
                time = row.dataset.start + ' - --:--';
            }
            const status = row.dataset.status || '-';

            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewType').textContent = type;
            document.getElementById('previewDate').textContent = date || '-';
            document.getElementById('previewTime').textContent = time;
            document.getElementById('previewStatus').textContent = status;
        });

        // Mostrar/ocultar campo de senha conforme privacidade
        const roomPrivacy = document.getElementById('roomPrivacy');
        const passwordField = document.getElementById('passwordField');
        if (roomPrivacy) {
            roomPrivacy.addEventListener('change', function() {
                if (this.value === 'password') {
                    passwordField.classList.remove('hidden');
                } else {
                    passwordField.classList.add('hidden');
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>





