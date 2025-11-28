<?= $this->extend('layouts/master') ?>
<?= $this->section('title') ?>Aula ao Vivo - <?= esc($aula->title_jitsi) ?><?= $this->endSection() ?>

<?= $this->section('jitsi') ?>

<div class="min-h-screen flex bg-slate-50 dark:bg-slate-900 rounded-md">

    <!-- ÁREA DO VÍDEO -->
    <div class="flex-1 flex flex-col">

        <!-- Header -->
        <div class="p-4 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">

            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                    <?= esc($aula->title_jitsi) ?>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    <?= esc($aula->description_jitsi ?? '') ?>
                </p>
            </div>

            <button onclick="endLive()"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow">
                Terminar Aula
            </button>
        </div>

        <!-- Jitsi Container -->
        <div class="flex-1 bg-black">
            <div id="jitsiContainer" class="w-full h-full"></div>
        </div>

    </div>

    <!-- SIDEBAR LATERAL -->
    <div class="w-80 bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700 p-4 space-y-4">

        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-200">
            Chat da Aula
        </h2>

        <div class="h-96 overflow-y-auto p-3 bg-slate-100 dark:bg-slate-700 rounded-xl" id="chatBox">
            <p class="text-slate-500 dark:text-slate-300 text-sm">Chat será implementado…</p>
        </div>

        <input type="text" id="chatInput"
            placeholder="Escreva aqui..."
            class="w-full px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 ring-blue-500 outline-none">

        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-xl">
            Enviar
        </button>

    </div>

</div>

<script src="https://meet.jit.si/external_api.js"></script>

<script>
    // INIT JITSI
    const domain = "meet.jit.si";
    const options = {
        roomName: "<?= esc($aula->room_jitsi) ?>",
        width: "100%",
        height: "100%",
        parentNode: document.querySelector('#jitsiContainer'),
        userInfo: {
            displayName: "<?= esc($user->name) ?>"
        },
        configOverwrite: {
            disableDeepLinking: true,
        },
        interfaceConfigOverwrite: {
            MOBILE_APP_PROMO: false,
        }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    function endLive() {
        Swal.fire({
            icon: "warning",
            title: "Terminar Aula?",
            text: "Deseja encerrar a aula ao vivo?",
            showCancelButton: true,
            confirmButtonText: "Sim, terminar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/instructor/dashboard/jitsi";
            }
        });
    }
</script>

<?= $this->endSection() ?>