// ======================
// Global Variables
// ======================
let currentStep = 1;
let tags = [];

// ======================
// Tooltips Initialization
// ======================
const tooltipTriggerList = [].slice.call(
  document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
const tooltipList = tooltipTriggerList.map(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

// ======================
// Tab Navigation
// ======================
document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll("#courseCreationTab button");
  const nextBtn = document.getElementById("next-step");
  const prevBtn = document.getElementById("prev-step");

  function updateStepIndicators(step) {
    const indicators = document.querySelectorAll(".step-indicator");
    const progressBar = document.querySelector(".progress-bar");
    const progressText = document.getElementById("progress-text");

    indicators.forEach((indicator, index) => {
      indicator.classList.remove("active", "completed");
      if (index + 1 < step) {
        indicator.classList.add("completed");
        indicator.innerHTML = '<i class="fas fa-check"></i>';
      } else if (index + 1 === step) {
        indicator.classList.add("active");
        indicator.innerHTML = index + 1;
      } else {
        indicator.innerHTML = index + 1;
      }
    });

    progressBar.style.width = step * 25 + "%";
    progressText.textContent = `Passo ${step} de 4`;
  }

  nextBtn.addEventListener("click", () => {
    if (currentStep < 4) {
      currentStep++;
      new bootstrap.Tab(tabs[currentStep - 1]).show();
      updateStepIndicators(currentStep);
      updateNavigationButtons();
    }
  });

  prevBtn.addEventListener("click", () => {
    if (currentStep > 1) {
      currentStep--;
      new bootstrap.Tab(tabs[currentStep - 1]).show();
      updateStepIndicators(currentStep);
      updateNavigationButtons();
    }
  });

  function updateNavigationButtons() {
    prevBtn.disabled = currentStep === 1;
    nextBtn.style.display = currentStep === 4 ? "none" : "inline-block";
  }

  tabs.forEach((tab, index) => {
    tab.addEventListener("click", function () {
      currentStep = index + 1;
      updateStepIndicators(currentStep);
      updateNavigationButtons();
    });
  });

  updateNavigationButtons();
});

// ======================
// Rich Text Editor
// ======================
document.addEventListener("DOMContentLoaded", () => {
  const editor = document.getElementById("courseDescription");
  if (!editor) return;

  function updatePlaceholder() {
    editor.classList.toggle("empty", editor.textContent.trim() === "");
  }

  editor.addEventListener("input", updatePlaceholder);
  editor.addEventListener("focus", () => {
    if (editor.textContent.trim() === "") editor.innerHTML = "";
  });
  updatePlaceholder();
});

// ======================
// Tags
// ======================
document.addEventListener("DOMContentLoaded", () => {
  const tagsInput = document.getElementById("courseTags");
  const tagsDisplay = document.getElementById("tags-display");

  if (!tagsInput) return;

  tagsInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      e.preventDefault();
      const tag = tagsInput.value.trim();
      if (tag && !tags.includes(tag)) {
        tags.push(tag);
        renderTags();
        tagsInput.value = "";
      }
    }
  });

  function renderTags() {
    tagsDisplay.innerHTML = tags
      .map(
        (tag) => `
            <span class="badge bg-primary me-1">
                ${tag} 
                <button type="button" class="btn-close btn-close-white btn-sm" onclick="removeTag('${tag}')"></button>
            </span>
        `
      )
      .join("");
  }

  window.removeTag = function (tagToRemove) {
    tags = tags.filter((tag) => tag !== tagToRemove);
    renderTags();
  };
});

// ======================
// Image Upload
// ======================
document.addEventListener("DOMContentLoaded", () => {
  const uploadArea = document.getElementById("upload-area");
  const fileInput = document.getElementById("courseImage");
  const previewImg = document.getElementById("preview-img");
  const imagePreview = document.getElementById("image-preview");
  const removeBtn = document.getElementById("remove-image");

  if (!fileInput) return;

  function handleImageUpload(file) {
    if (file.type.startsWith("image/")) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (previewImg) previewImg.src = e.target.result;
        if (imagePreview) imagePreview.style.display = "block";
        if (uploadArea) uploadArea.style.display = "none";
      };
      reader.readAsDataURL(file);
    }
  }

  if (uploadArea) {
    uploadArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      uploadArea.classList.add("dragover");
    });
    uploadArea.addEventListener("dragleave", (e) => {
      e.preventDefault();
      uploadArea.classList.remove("dragover");
    });
    uploadArea.addEventListener("drop", (e) => {
      e.preventDefault();
      uploadArea.classList.remove("dragover");
      if (e.dataTransfer.files.length)
        handleImageUpload(e.dataTransfer.files[0]);
    });
  }

  fileInput.addEventListener("change", (e) => {
    if (e.target.files.length) handleImageUpload(e.target.files[0]);
  });

  if (removeBtn)
    removeBtn.addEventListener("click", () => {
      fileInput.value = "";
      if (imagePreview) imagePreview.style.display = "none";
      if (uploadArea) uploadArea.style.display = "block";
    });
});

// ======================
// Modules and Lessons
// ======================
document.addEventListener("DOMContentLoaded", () => {
  const modulesContainer = document.getElementById("modules-container");
  let moduleIndex = 0;

  document.getElementById("add-module")?.addEventListener("click", () => {
    const moduleHtml = `
            <div class="module-card mb-3 border p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <input type="text" name="modules[${moduleIndex}][title]" 
                           class="form-control me-2" 
                           placeholder="Nome do Módulo">
                    <i class="bi bi-x-circle text-danger fs-5 remove-module" role="button" title="Remover módulo"></i>
                </div>
                <textarea name="modules[${moduleIndex}][description]" 
                          class="form-control mb-2" 
                          placeholder="Descrição do Módulo"></textarea>
                
                <div class="lessons-container"></div>
                <button type="button" class="btn btn-sm btn-primary add-lesson" data-module="${moduleIndex}">
                    + Adicionar Aula
                </button>
            </div>
        `;
    modulesContainer.insertAdjacentHTML("beforeend", moduleHtml);
    moduleIndex++;
  });

  modulesContainer?.addEventListener("click", (e) => {
    // Remover módulo
    if (e.target.classList.contains("remove-module")) {
      e.target.closest(".module-card").remove();
    }

    // Adicionar aula
    if (e.target.classList.contains("add-lesson")) {
      const moduleId = e.target.dataset.module;
      const lessonsContainer = e.target.previousElementSibling;
      const lessonCount =
        lessonsContainer.querySelectorAll(".lesson-item").length;

      const lessonHtml = `
                <div class="lesson-item mb-2 border p-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <input type="text" 
                               name="modules[${moduleId}][lessons][${lessonCount}][title]" 
                               class="form-control me-2" 
                               placeholder="Título da Aula">
                        <i class="bi bi-x-circle text-danger fs-6 remove-lesson" role="button" title="Remover aula"></i>
                    </div>
                    <select name="modules[${moduleId}][lessons][${lessonCount}][type]" class="form-select text-secondary mb-1">
                        <option value="video">Vídeo</option>
                        <option value="text">Texto</option>
                        <option value="quiz">Quiz</option>
                        <option value="exercise">Exercício</option>
                    </select>
                    <input type="number" name="modules[${moduleId}][lessons][${lessonCount}][duration]" 
                           class="form-control mb-1" placeholder="Duração (min)">
                    <input type="url" name="modules[${moduleId}][lessons][${lessonCount}][video_url]" 
                           class="form-control" placeholder="Link do vídeo">
                </div>
            `;
      lessonsContainer.insertAdjacentHTML("beforeend", lessonHtml);
    }

    // Remover aula
    if (e.target.classList.contains("remove-lesson")) {
      e.target.closest(".lesson-item").remove();
    }
  });
});

// ======================
// Course Type and Price
// ======================
document.addEventListener("DOMContentLoaded", () => {
  const radios = document.querySelectorAll('input[name="courseType"]');
  const priceSettings = document.getElementById("price-settings");

  radios.forEach((radio) => {
    radio.addEventListener("change", () => {
      if (radio.value === "paid") priceSettings.style.display = "block";
      else priceSettings.style.display = "none";
    });
  });
});

// ======================
// Preview Update
// ======================
document.addEventListener("DOMContentLoaded", () => {
  function updatePreview() {
    const title =
      document.getElementById("title_course")?.value || "Título do Curso";
    const subtitle =
      document.getElementById("courseSubtitle")?.value || "Subtítulo do curso";
    const description =
      document.getElementById("courseDescription")?.value ||
      "Descrição do curso aparecerá aqui...";

    document.getElementById("preview-title") &&
      (document.getElementById("preview-title").textContent = title);
    document.getElementById("preview-subtitle") &&
      (document.getElementById("preview-subtitle").textContent = subtitle);
    document.getElementById("preview-description") &&
      (document.getElementById("preview-description").innerHTML = description);
  }

  document.addEventListener("input", updatePreview);
  document.addEventListener("change", updatePreview);
  updatePreview();
});

// ======================
// Publish / Save Draft (com envio de FormData para permitir upload de imagem)
// ======================
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("courseForm");
  const publishBtn = document.getElementById("publish-course");
  const saveDraftBtn = document.getElementById("save-draft");

  function collectCourseData() {
    const formData = new FormData(form);

    // Adicionar tags
    formData.append("tags", JSON.stringify(tags));

    // Adicionar módulos e aulas
    const modules = [];
    document.querySelectorAll(".module-card").forEach((modCard, i) => {
      const moduleTitle =
        modCard.querySelector(`input[name^="modules"][name$="[title]"]`)
          ?.value || `Módulo ${i + 1}`;
      const moduleDescription =
        modCard.querySelector(
          `textarea[name^="modules"][name$="[description]"]`
        )?.value || "";
      const lessons = [];
      modCard.querySelectorAll(".lesson-item").forEach((lessonEl, j) => {
        const title =
          lessonEl.querySelector(`input[name$="[title]"]`)?.value ||
          `Aula ${j + 1}`;
        const type =
          lessonEl.querySelector(`select[name$="[type]"]`)?.value || "text";
        const duration =
          lessonEl.querySelector(`input[name$="[duration]"]`)?.value || 0;
        const video_url =
          lessonEl.querySelector(`input[name$="[video_url]"]`)?.value || null;
        lessons.push({ title, type, duration, video_url });
      });
      modules.push({
        title: moduleTitle,
        description: moduleDescription,
        lessons,
      });
    });

    formData.append("modules", JSON.stringify(modules));
    return formData;
  }

  // publishBtn?.addEventListener("click", () => {
  //     const data = collectCourseData();
  //     fetch(form.action, { method: 'POST', body: data })
  //     .then(res => res.json())
  //     .then(resp => { alert("Curso publicado com sucesso! 🎉"); console.log(resp); })
  //     .catch(err => { console.error(err); alert("Erro ao publicar."); });
  // });

  // saveDraftBtn?.addEventListener("click", () => {
  //     const data = collectCourseData();
  //     console.log("Rascunho salvo:", data);
  //     alert("Rascunho salvo! 💾");
  // });
});

// ======================
// Auto-save
// ======================
let autoSaveTimer;
document.addEventListener("input", () => {
  clearTimeout(autoSaveTimer);
  autoSaveTimer = setTimeout(() => {
    console.log("Auto-saving...");
  }, 5000);
});




// ======================
// Para edição de curso
// ======================
