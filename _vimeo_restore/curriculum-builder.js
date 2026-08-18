/**
 * Drag/drop estável (SortableJS) + importação Vimeo para o currículo.
 * Uso: CourseCurriculum.init({ container, onChange, createModule, createLessonHtml })
 */
(function (window) {
  "use strict";

  const state = {
    container: null,
    onChange: null,
    createModule: null,
    createLessonHtml: null,
    moduleSortable: null,
    lessonSortables: [],
  };

  function esc(str) {
    return String(str ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function reindex() {
    const root = state.container;
    if (!root) return;

    root.querySelectorAll(".module-card").forEach((mod, mi) => {
      mod.dataset.index = String(mi);

      mod.querySelectorAll("[name]").forEach((el) => {
        const name = el.getAttribute("name");
        if (!name) return;
        if (name.startsWith("modules[")) {
          el.setAttribute("name", name.replace(/^modules\[\d+\]/, "modules[" + mi + "]"));
        }
      });

      mod.querySelectorAll(".lesson-item").forEach((lesson, li) => {
        lesson.dataset.index = String(li);
        lesson.querySelectorAll("[name]").forEach((el) => {
          const name = el.getAttribute("name");
          if (!name) return;
          if (name.startsWith("modules[")) {
            el.setAttribute(
              "name",
              name
                .replace(/^modules\[\d+\]/, "modules[" + mi + "]")
                .replace(/\[lessons\]\[\d+\]/, "[lessons][" + li + "]")
            );
          } else if (/^lesson_files\[\d+\]\[\d+\]/.test(name)) {
            el.setAttribute("name", "lesson_files[" + mi + "][" + li + "]");
          }
        });
      });

      const addBtn = mod.querySelector(".btn-add-lesson, .add-lesson");
      if (addBtn) addBtn.dataset.module = String(mi);
    });
  }

  function destroyLessonSortables() {
    state.lessonSortables.forEach((s) => {
      try {
        s.destroy();
      } catch (_) {}
    });
    state.lessonSortables = [];
  }

  function bindLessonSortable(el) {
    if (!window.Sortable || !el) return;
    const s = Sortable.create(el, {
      group: "course-lessons",
      handle: ".drag-handle",
      animation: 180,
      ghostClass: "curriculum-ghost",
      chosenClass: "curriculum-chosen",
      dragClass: "curriculum-drag",
      fallbackOnBody: true,
      swapThreshold: 0.65,
      emptyInsertThreshold: 24,
      onEnd: function () {
        reindex();
        if (typeof state.onChange === "function") state.onChange();
      },
    });
    state.lessonSortables.push(s);
  }

  function refresh() {
    if (!state.container || !window.Sortable) return;

    destroyLessonSortables();
    state.container.querySelectorAll(".lessons-container").forEach(bindLessonSortable);

    if (state.moduleSortable) {
      try {
        state.moduleSortable.destroy();
      } catch (_) {}
      state.moduleSortable = null;
    }

    state.moduleSortable = Sortable.create(state.container, {
      group: "course-modules",
      handle: ".module-drag-handle",
      animation: 180,
      draggable: ".module-card",
      ghostClass: "curriculum-ghost",
      chosenClass: "curriculum-chosen",
      dragClass: "curriculum-drag",
      onEnd: function () {
        reindex();
        if (typeof state.onChange === "function") state.onChange();
      },
    });

    // Remover HTML5 draggable nativo (fonte comum de bugs)
    state.container.querySelectorAll(".lesson-item, .drag-handle, .module-card").forEach((el) => {
      el.removeAttribute("draggable");
    });

    reindex();
  }

  function init(options) {
    state.container = options.container || document.getElementById("modules-container");
    state.onChange = options.onChange || null;
    state.createModule = options.createModule || null;
    state.createLessonHtml = options.createLessonHtml || null;

    if (!state.container) return;
    refresh();
  }

  function importModules(modules, mode) {
    mode = mode || "append";
    if (!Array.isArray(modules) || !modules.length) return 0;

    if (mode === "replace" && state.container) {
      state.container.innerHTML = "";
    }

    let count = 0;
    modules.forEach((mod) => {
      if (typeof state.createModule === "function") {
        state.createModule({
          title: mod.title || "Módulo",
          description: mod.description || "",
          min_score: 80,
          lessons: Array.isArray(mod.lessons) ? mod.lessons : [],
        });
        count += 1;
      }
    });

    refresh();
    if (typeof state.onChange === "function") state.onChange();
    return count;
  }

  function importLessonsIntoModule(moduleCard, lessons) {
    if (!moduleCard || !Array.isArray(lessons) || !lessons.length) return 0;
    const lessonsContainer = moduleCard.querySelector(".lessons-container");
    if (!lessonsContainer || typeof state.createLessonHtml !== "function") return 0;

    const mi = Array.from(state.container.querySelectorAll(".module-card")).indexOf(moduleCard);
    let added = 0;
    lessons.forEach((lesson) => {
      const li = lessonsContainer.querySelectorAll(".lesson-item").length;
      const html = state.createLessonHtml(mi < 0 ? 0 : mi, li, {
        title: lesson.title || "Aula",
        type: "video",
        duration: lesson.duration || 0,
        video_url: lesson.video_url || "",
        is_preview: 0,
      });
      lessonsContainer.insertAdjacentHTML("beforeend", html);
      added += 1;
    });

    refresh();
    if (typeof state.onChange === "function") state.onChange();
    return added;
  }

  function listModuleOptions() {
    if (!state.container) return [];
    return Array.from(state.container.querySelectorAll(".module-card")).map((mod, i) => {
      const title =
        mod.querySelector('input[name$="[title]"]')?.value?.trim() || "Módulo " + (i + 1);
      return { index: i, title: title, el: mod };
    });
  }

  window.CourseCurriculum = {
    init,
    refresh,
    reindex,
    importModules,
    importLessonsIntoModule,
    listModuleOptions,
    esc,
  };
})(window);

/**
 * Modal Vimeo — depende de #vimeoImportModal e CourseCurriculum
 */
(function (window, document) {
  "use strict";

  const endpoints = {
    status: "/instructor/vimeo/status",
    folders: "/instructor/vimeo/folders",
    curriculum: (id) => "/instructor/vimeo/folders/" + encodeURIComponent(id) + "/curriculum",
  };

  let selectedFolder = null;
  let curriculum = null;

  function $(id) {
    return document.getElementById(id);
  }

  function setMsg(text, isError) {
    const el = $("vimeoImportMsg");
    if (!el) return;
    el.hidden = !text;
    el.textContent = text || "";
    el.classList.toggle("text-red-600", !!isError);
    el.classList.toggle("dark:text-red-400", !!isError);
    el.classList.toggle("text-slate-600", !isError);
    el.classList.toggle("dark:text-slate-300", !isError);
  }

  function openModal() {
    const modal = $("vimeoImportModal");
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.setAttribute("aria-hidden", "false");
    bootstrap();
  }

  function closeModal() {
    const modal = $("vimeoImportModal");
    if (!modal) return;
    modal.classList.add("hidden");
    modal.setAttribute("aria-hidden", "true");
  }

  async function fetchJson(url) {
    const res = await fetch(url, {
      headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
      credentials: "same-origin",
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.ok === false) {
      throw new Error(data.error || "Pedido Vimeo falhou (" + res.status + ")");
    }
    return data;
  }

  async function bootstrap() {
    setMsg("A verificar ligação Vimeo…");
    $("vimeoFolderList").innerHTML = "";
    $("vimeoPreview").innerHTML = "";
    $("vimeoImportActions").classList.add("hidden");
    selectedFolder = null;
    curriculum = null;

    try {
      const status = await fetchJson(endpoints.status);
      if (!status.ready && !status.ok) {
        setMsg(status.error || "Vimeo não está configurado. Active vimeo.* no .env.", true);
        return;
      }
      setMsg("A carregar pastas…");
      const folders = await fetchJson(endpoints.folders + "?per_page=50");
      renderFolders(folders.folders || []);
      setMsg((folders.folders || []).length ? "Escolha uma pasta." : "Nenhuma pasta encontrada.", !(folders.folders || []).length);
      refreshTargetModules();
    } catch (err) {
      setMsg(err.message || "Erro ao contactar Vimeo.", true);
    }
  }

  function renderFolders(folders) {
    const list = $("vimeoFolderList");
    if (!list) return;
    if (!folders.length) {
      list.innerHTML = '<p class="text-sm text-slate-500">Sem pastas.</p>';
      return;
    }
    list.innerHTML = folders
      .map(
        (f) => `
      <button type="button" class="vimeo-folder-btn w-full text-left px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-600 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-slate-800 text-sm"
              data-id="${CourseCurriculum.esc(f.id)}" data-name="${CourseCurriculum.esc(f.name)}">
        <i class="bi bi-folder2 mr-2 text-blue-500"></i>${CourseCurriculum.esc(f.name)}
      </button>`
      )
      .join("");
  }

  function refreshTargetModules() {
    const select = $("vimeoTargetModule");
    if (!select || !window.CourseCurriculum) return;
    const opts = CourseCurriculum.listModuleOptions();
    select.innerHTML =
      '<option value="">— Criar módulo(s) novo(s) —</option>' +
      opts
        .map(
          (o) =>
            `<option value="${o.index}">Adicionar aulas a: ${CourseCurriculum.esc(o.title)}</option>`
        )
        .join("");
  }

  async function loadCurriculum(folderId, folderName) {
    setMsg("A importar estrutura de “" + folderName + "”…");
    $("vimeoPreview").innerHTML = "";
    $("vimeoImportActions").classList.add("hidden");
    try {
      const data = await fetchJson(endpoints.curriculum(folderId));
      curriculum = data.curriculum || null;
      selectedFolder = { id: folderId, name: folderName };
      renderPreview(curriculum);
      $("vimeoImportActions").classList.remove("hidden");
      const modeHint = curriculum?.has_subfolders
        ? "Subpastas detectadas → cada uma será um módulo."
        : "Sem subpastas → um único módulo (podes editar depois).";
      setMsg(modeHint + " Total: " + (curriculum?.lesson_count || 0) + " aula(s).");
      refreshTargetModules();
    } catch (err) {
      setMsg(err.message || "Falha ao ler pasta.", true);
    }
  }

  function renderPreview(cur) {
    const box = $("vimeoPreview");
    if (!box || !cur) return;
    const modules = cur.modules || [];
    box.innerHTML = modules
      .map((mod, mi) => {
        const lessons = mod.lessons || [];
        const lessonRows = lessons
          .map(
            (l, li) => `
          <label class="flex items-start gap-2 py-1 text-xs text-slate-700 dark:text-slate-200">
            <input type="checkbox" class="vimeo-lesson-check mt-0.5" data-m="${mi}" data-l="${li}" checked>
            <span><strong>${CourseCurriculum.esc(l.title)}</strong> · ${l.duration || 0} min</span>
          </label>`
          )
          .join("");
        return `
        <div class="rounded-xl border border-slate-200 dark:border-slate-600 p-3 mb-2 bg-slate-50 dark:bg-slate-900/50">
          <div class="flex items-center justify-between mb-1">
            <p class="text-sm font-semibold text-slate-800 dark:text-white">${CourseCurriculum.esc(mod.title)}</p>
            <button type="button" class="vimeo-toggle-mod text-[11px] text-blue-600" data-m="${mi}">Alternar</button>
          </div>
          <div class="vimeo-mod-lessons max-h-40 overflow-auto">${lessonRows || '<p class="text-xs text-slate-500">Sem vídeos</p>'}</div>
        </div>`;
      })
      .join("");
  }

  function selectedCurriculum() {
    if (!curriculum) return [];
    const modules = curriculum.modules || [];
    const checks = Array.from(document.querySelectorAll(".vimeo-lesson-check:checked"));
    if (!checks.length) return [];

    const byModule = {};
    checks.forEach((ch) => {
      const mi = parseInt(ch.dataset.m, 10);
      const li = parseInt(ch.dataset.l, 10);
      if (!byModule[mi]) byModule[mi] = [];
      byModule[mi].push(li);
    });

    const out = [];
    Object.keys(byModule).forEach((miKey) => {
      const mi = parseInt(miKey, 10);
      const src = modules[mi];
      if (!src) return;
      const lessons = byModule[mi]
        .map((li) => src.lessons[li])
        .filter(Boolean);
      if (!lessons.length) return;
      out.push({
        title: src.title,
        description: src.description || "",
        lessons,
      });
    });
    return out;
  }

  function applyImport() {
    const picked = selectedCurriculum();
    if (!picked.length) {
      setMsg("Seleccione pelo menos uma aula.", true);
      return;
    }

    const target = $("vimeoTargetModule")?.value ?? "";
    if (target !== "" && window.CourseCurriculum) {
      const opts = CourseCurriculum.listModuleOptions();
      const mod = opts.find((o) => String(o.index) === String(target));
      if (!mod) {
        setMsg("Módulo de destino inválido.", true);
        return;
      }
      const flat = [];
      picked.forEach((m) => flat.push(...(m.lessons || [])));
      const n = CourseCurriculum.importLessonsIntoModule(mod.el, flat);
      setMsg(n + " aula(s) adicionada(s) a “" + mod.title + "”.");
      closeModal();
      return;
    }

    const n = CourseCurriculum.importModules(picked, "append");
    setMsg(n + " módulo(s) importado(s).");
    closeModal();
  }

  function wire() {
    document.getElementById("btn-import-vimeo")?.addEventListener("click", openModal);
    document.querySelectorAll("[data-close-vimeo-modal]").forEach((btn) => {
      btn.addEventListener("click", closeModal);
    });
    $("vimeoImportConfirm")?.addEventListener("click", applyImport);

    $("vimeoFolderList")?.addEventListener("click", (e) => {
      const btn = e.target.closest(".vimeo-folder-btn");
      if (!btn) return;
      $("vimeoFolderList")
        .querySelectorAll(".vimeo-folder-btn")
        .forEach((b) => b.classList.remove("ring-2", "ring-blue-500"));
      btn.classList.add("ring-2", "ring-blue-500");
      loadCurriculum(btn.dataset.id, btn.dataset.name || "Pasta");
    });

    $("vimeoPreview")?.addEventListener("click", (e) => {
      const toggle = e.target.closest(".vimeo-toggle-mod");
      if (!toggle) return;
      const mi = toggle.dataset.m;
      const boxes = document.querySelectorAll('.vimeo-lesson-check[data-m="' + mi + '"]');
      const allOn = Array.from(boxes).every((b) => b.checked);
      boxes.forEach((b) => {
        b.checked = !allOn;
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", wire);
  } else {
    wire();
  }

  window.VimeoImportUI = { open: openModal, close: closeModal, refresh: bootstrap };
})(window, document);
