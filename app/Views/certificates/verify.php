<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar Certificado | Mechanical Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-3xl items-center px-4 py-10">
        <section class="w-full rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <div class="text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-xl font-black text-white">M</div>
                <h1 class="text-2xl font-black tracking-tight sm:text-3xl">Verificação de Certificado</h1>
                <p class="mt-2 text-sm leading-6 text-slate-600">Confirme se o certificado foi emitido oficialmente pela Mechanical Academy.</p>
            </div>

            <form action="<?= site_url('certificados/verificar') ?>" method="get" class="mt-7 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200 sm:flex sm:gap-3">
                <input
                    name="codigo"
                    value="<?= esc($searchedCode ?? '') ?>"
                    placeholder="Ex: MT-2026-EXCEL-001"
                    class="min-h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                <button class="mt-3 min-h-12 w-full rounded-xl bg-slate-900 px-5 text-sm font-bold text-white transition hover:bg-slate-800 sm:mt-0 sm:w-auto">
                    Verificar
                </button>
            </form>

            <?php if (!empty($message)): ?>
                <div class="mt-6 rounded-2xl border p-4 <?= !empty($isValid) ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' ?>">
                    <div class="flex gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full <?= !empty($isValid) ? 'bg-emerald-600' : 'bg-amber-500' ?> text-lg font-black text-white">
                            <?= !empty($isValid) ? '✓' : '!' ?>
                        </div>
                        <div>
                            <h2 class="font-bold <?= !empty($isValid) ? 'text-emerald-900' : 'text-amber-900' ?>">
                                <?= esc($message) ?>
                            </h2>
                            <?php if (!empty($cert)): ?>
                                <dl class="mt-4 grid gap-3 text-sm text-slate-700 sm:grid-cols-2">
                                    <div class="rounded-xl bg-white/70 p-3 ring-1 ring-black/5">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Código</dt>
                                        <dd class="mt-1 font-semibold text-slate-900"><?= esc($cert['number_certificate'] ?? '') ?></dd>
                                    </div>
                                    <div class="rounded-xl bg-white/70 p-3 ring-1 ring-black/5">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Estado</dt>
                                        <dd class="mt-1 font-semibold <?= !empty($isValid) ? 'text-emerald-700' : 'text-amber-700' ?>">
                                            <?= !empty($isValid) ? 'Válido' : 'Inválido/Revogado' ?>
                                        </dd>
                                    </div>
                                    <div class="rounded-xl bg-white/70 p-3 ring-1 ring-black/5 sm:col-span-2">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Formando</dt>
                                        <dd class="mt-1 font-semibold text-slate-900"><?= esc($cert['student_name'] ?? '—') ?></dd>
                                    </div>
                                    <div class="rounded-xl bg-white/70 p-3 ring-1 ring-black/5 sm:col-span-2">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Curso</dt>
                                        <dd class="mt-1 font-semibold text-slate-900"><?= esc($cert['title_course'] ?? '—') ?></dd>
                                    </div>
                                    <div class="rounded-xl bg-white/70 p-3 ring-1 ring-black/5 sm:col-span-2">
                                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Data de emissão</dt>
                                        <dd class="mt-1 font-semibold text-slate-900">
                                            <?php
                                                $issued = $cert['issued_at_certificate'] ?? null;
                                                echo $issued ? esc(date('d/m/Y', strtotime($issued))) : '—';
                                            ?>
                                        </dd>
                                    </div>
                                </dl>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
