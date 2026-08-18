<?php
/**
 * Modal de importação Vimeo (currículo).
 * Requer: SortableJS + curriculum-builder.js + CourseCurriculum.init(...)
 */
?>
<div id="vimeoImportModal"
     class="hidden fixed inset-0 z-[80] flex items-center justify-center p-4"
     aria-hidden="true"
     role="dialog"
     aria-labelledby="vimeoImportTitle">
    <div class="absolute inset-0 bg-slate-900/60" data-close-vimeo-modal></div>
    <div class="relative w-full max-w-3xl max-h-[90vh] overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl flex flex-col">
        <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-200 dark:border-slate-700">
            <div>
                <h3 id="vimeoImportTitle" class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="bi bi-vimeo text-blue-500"></i>
                    Importar do Vimeo
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Subpastas → módulos. Sem subpastas → 1 módulo. Depois podes editar e arrastar.
                </p>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-700 dark:hover:text-white text-xl" data-close-vimeo-modal aria-label="Fechar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-4">
            <p id="vimeoImportMsg" class="text-sm text-slate-600 dark:text-slate-300" hidden></p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Pastas</h4>
                    <div id="vimeoFolderList" class="space-y-2 max-h-64 overflow-y-auto"></div>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Pré-visualização</h4>
                    <div id="vimeoPreview" class="max-h-64 overflow-y-auto"></div>
                </div>
            </div>

            <div id="vimeoImportActions" class="hidden space-y-3 border-t border-slate-200 dark:border-slate-700 pt-4">
                <div>
                    <label for="vimeoTargetModule" class="block text-xs font-semibold text-slate-700 dark:text-slate-200 mb-1">
                        Destino
                    </label>
                    <select id="vimeoTargetModule"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-sm text-slate-800 dark:text-slate-100">
                        <option value="">— Criar módulo(s) novo(s) —</option>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">
                        Ou escolhe um módulo existente para receber só as aulas seleccionadas.
                    </p>
                </div>
                <button type="button"
                        id="vimeoImportConfirm"
                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl">
                    <i class="bi bi-download"></i>
                    Importar seleccionados
                </button>
            </div>
        </div>
    </div>
</div>
