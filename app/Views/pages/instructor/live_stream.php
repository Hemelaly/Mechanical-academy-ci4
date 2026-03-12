<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Aula ao Vivo - <?= esc($aula->title_jitsi) ?><?= $this->endSection() ?>

<?= $this->section('jitsi') ?>
<?php
$canModerate = (bool) ($canModerate ?? false);
$canManageRecordings = (bool) ($canManageRecordings ?? false);
$jitsiDomain = trim((string) ($jitsiDomain ?? 'meet.jit.si'));
$jitsiExternalApiScript = trim((string) ($jitsiExternalApiScript ?? ('https://' . $jitsiDomain . '/external_api.js')));
$jitsiRoomName = (string) ($jitsiRoomName ?? ($aula->room_jitsi ?? ''));
$jitsiToken = trim((string) ($jitsiToken ?? ''));
$jitsiRecordingMode = trim((string) ($jitsiRecordingMode ?? 'file'));
$recordings = $recordings ?? [];
$backUrl = (string) ($backUrl ?? site_url('/'));
$endStreamUrl = (string) ($endStreamUrl ?? '');
$saveRecordingUrl = (string) ($saveRecordingUrl ?? '');
$publishToggleBaseUrl = (string) ($publishToggleBaseUrl ?? '');
$displayName = trim((string) ($user->username ?? $user->name ?? ('User ' . ($user->id ?? ''))));

$toolbarButtons = [
    'microphone',
    'camera',
    'desktop',
    'chat',
    'raisehand',
    'participants-pane',
    'tileview',
    'hangup',
    'settings',
    'fullscreen',
];
if (! (bool) ($aula->chat_jitsi ?? 1)) {
    $toolbarButtons = array_values(array_filter($toolbarButtons, static fn ($btn) => $btn !== 'chat'));
}
if (! (bool) ($aula->screenshare_jitsi ?? 1)) {
    $toolbarButtons = array_values(array_filter($toolbarButtons, static fn ($btn) => $btn !== 'desktop'));
}
?>

<div class="min-h-screen flex bg-slate-50 dark:bg-slate-900 rounded-md overflow-hidden">
    <div class="flex-1 flex flex-col min-w-0">
        <div class="p-4 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-slate-800 dark:text-white truncate">
                    <?= esc($aula->title_jitsi) ?>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-300 truncate">
                    <?= esc($aula->description_jitsi ?? '') ?>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= esc($backUrl) ?>" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white rounded-xl shadow">
                    Voltar
                </a>

                <?php if ($canModerate && $endStreamUrl !== ''): ?>
                    <button type="button" onclick="endLive()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow">
                        Terminar Aula
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex-1 bg-black min-h-[65vh]">
            <div id="jitsiContainer" class="w-full h-full"></div>
        </div>
    </div>

    <div class="w-full max-w-sm bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 p-4 space-y-4 overflow-y-auto">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Gravacoes</h2>

        <?php if ($canManageRecordings && $saveRecordingUrl !== ''): ?>
            <form action="<?= esc($saveRecordingUrl) ?>" method="POST" class="space-y-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">URL da gravacao</label>
                    <input type="url" name="recording_url" required class="w-full px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white" placeholder="https://...">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">Modo</label>
                        <select name="recording_mode" class="w-full px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white">
                            <?php foreach (['file', 'stream', 'local', 'manual'] as $mode): ?>
                                <option value="<?= $mode ?>" <?= $mode === $jitsiRecordingMode ? 'selected' : '' ?>><?= strtoupper($mode) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">Status</label>
                        <select name="status_recording" class="w-full px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white">
                            <option value="ready" selected>Ready</option>
                            <option value="processing">Processing</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">Provider ID</label>
                        <input type="text" name="provider_recording_id" class="w-full px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white" placeholder="rec_123">
                    </div>

                    <div>
                        <label class="block text-xs text-slate-600 dark:text-slate-300 mb-1">Duracao (s)</label>
                        <input type="number" min="0" name="duration_seconds" class="w-full px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white" placeholder="3600">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-200">
                    <input type="checkbox" name="publish_now" value="1" class="rounded text-blue-600">
                    Publicar para alunos agora
                </label>

                <button type="submit" class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Guardar gravacao
                </button>
            </form>
        <?php endif; ?>

        <div class="space-y-2">
            <?php if (empty($recordings)): ?>
                <p class="text-sm text-slate-500 dark:text-slate-300">Nenhuma gravacao registada.</p>
            <?php else: ?>
                <?php foreach ($recordings as $recording): ?>
                    <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">
                                <?= esc($recording->status_recording ?? 'ready') ?>
                            </span>
                            <span class="text-xs <?= ((int) ($recording->is_published ?? 0) === 1) ? 'text-green-600' : 'text-amber-600' ?>">
                                <?= ((int) ($recording->is_published ?? 0) === 1) ? 'Publicada' : 'Privada' ?>
                            </span>
                        </div>

                        <a href="<?= esc($recording->recording_url) ?>" target="_blank" rel="noopener" class="text-sm text-blue-600 dark:text-blue-400 break-all hover:underline">
                            <?= esc($recording->recording_url) ?>
                        </a>

                        <?php if (! empty($recording->duration_seconds)): ?>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Duracao: <?= (int) $recording->duration_seconds ?>s
                            </p>
                        <?php endif; ?>

                        <?php if ($canManageRecordings && $publishToggleBaseUrl !== ''): ?>
                            <form action="<?= esc($publishToggleBaseUrl . '/' . (int) $recording->id_jitsi_recording . '/publish') ?>" method="POST" class="mt-2">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-white">
                                    <?= ((int) ($recording->is_published ?? 0) === 1) ? 'Despublicar' : 'Publicar' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($jitsiToken === ''): ?>
            <div class="p-3 rounded-xl bg-amber-50 text-amber-700 text-xs">
                Token JWT nao configurado. A sala pode abrir sem controle de papel (moderador/participante).
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= esc($jitsiExternalApiScript) ?>"></script>
<script>
    const jitsiDomain = <?= json_encode($jitsiDomain) ?>;
    const jitsiRoomName = <?= json_encode($jitsiRoomName) ?>;
    const jitsiToken = <?= json_encode($jitsiToken !== '' ? $jitsiToken : null) ?>;
    const canModerate = <?= $canModerate ? 'true' : 'false' ?>;
    const autoRecord = <?= (int) ($aula->recording_jitsi ?? 0) === 1 ? 'true' : 'false' ?>;
    const recordingMode = <?= json_encode($jitsiRecordingMode !== '' ? $jitsiRecordingMode : 'file') ?>;
    const saveRecordingUrl = <?= json_encode($saveRecordingUrl) ?>;
    const endStreamUrl = <?= json_encode($endStreamUrl) ?>;
    const backUrl = <?= json_encode($backUrl) ?>;
    const csrfTokenName = <?= json_encode(csrf_token()) ?>;
    let csrfTokenValue = <?= json_encode(csrf_hash()) ?>;

    const options = {
        roomName: jitsiRoomName,
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsiContainer'),
        userInfo: {
            displayName: <?= json_encode($displayName) ?>
        },
        configOverwrite: {
            disableDeepLinking: true,
            prejoinPageEnabled: false,
            startWithAudioMuted: false,
            startWithVideoMuted: false,
        },
        interfaceConfigOverwrite: {
            MOBILE_APP_PROMO: false,
            TOOLBAR_BUTTONS: <?= json_encode($toolbarButtons) ?>,
        }
    };

    if (jitsiToken) {
        options.jwt = jitsiToken;
    }

    const api = new JitsiMeetExternalAPI(jitsiDomain, options);

    api.addListener('videoConferenceJoined', () => {
        if (canModerate && autoRecord) {
            try {
                api.executeCommand('startRecording', {
                    mode: recordingMode,
                    shouldShare: false
                });
            } catch (e) {
                console.warn('Nao foi possivel iniciar gravacao automaticamente.', e);
            }
        }
    });

    api.addListener('recordingLinkAvailable', async (payload) => {
        if (!saveRecordingUrl) {
            return;
        }

        const link = payload?.link || payload?.recordingLink || payload?.url || '';
        if (!link) {
            return;
        }

        const body = new URLSearchParams();
        body.append(csrfTokenName, csrfTokenValue);
        body.append('recording_url', link);
        body.append('recording_mode', recordingMode || 'file');
        body.append('status_recording', 'ready');
        // Publica automaticamente para aparecer em /student/dashboard/aulas_ao_vivo
        body.append('publish_now', '1');
        if (payload?.recordingSessionId) {
            body.append('provider_recording_id', String(payload.recordingSessionId));
        }

        try {
            const response = await fetch(saveRecordingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
            });

            const data = await response.json().catch(() => ({}));
            if (data?.csrf) {
                csrfTokenValue = data.csrf;
            }
        } catch (err) {
            console.warn('Falha ao guardar link da gravacao automaticamente.', err);
        }
    });

    function endLive() {
        Swal.fire({
            icon: 'warning',
            title: 'Terminar Aula?',
            text: 'Deseja encerrar a aula ao vivo?',
            showCancelButton: true,
            confirmButtonText: 'Sim, terminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            if (endStreamUrl) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = endStreamUrl;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = csrfTokenName;
                csrf.value = csrfTokenValue;

                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
                return;
            }

            window.location.href = backUrl;
        });
    }
</script>

<?= $this->endSection() ?>
