<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Meus Cursos<?= $this->endSection() ?>

<?= $this->section('my_courses') ?>

<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <div class="container mx-auto">
        
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
                    Meus Cursos
                </h1>
                <p class="text-slate-600 dark:text-slate-400 text-sm">
                    Gerencie e acompanhe todos os seus cursos criados
                </p>
            </div>
            
            <a href="/instructor/dashboard/novo_curso" 
               class="mt-4 lg:mt-0 inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-blue-500 to-blue-900 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25">
                <i class="bi bi-plus-circle"></i>
                Criar Novo Curso
            </a>
        </div>

        <!-- Filtros e Busca -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Filtros -->
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="filter-btn px-4 py-2 bg-blue-600 text-white font-medium rounded-full text-sm transition-all duration-300 shadow-sm" data-filter="all">
                        Todos
                    </button>
                    <button type="button" class="filter-btn px-4 py-2 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 font-medium rounded-full text-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300" data-filter="Ativo">
                        Ativos
                    </button>
                    <button type="button" class="filter-btn px-4 py-2 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 font-medium rounded-full text-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300" data-filter="Rascunho">
                        Rascunhos
                    </button>
                    <button type="button" class="filter-btn px-4 py-2 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 font-medium rounded-full text-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300" data-filter="Arquivado">
                        Arquivados
                    </button>
                </div>

                <!-- Busca -->
                <div class="relative lg:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-slate-400"></i>
                    </div>
                    <input
                        type="text"
                        id="searchInput"
                        class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                        placeholder="Procurar cursos..." />
                </div>
            </div>
        </div>

        <!-- Grid de Cursos -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="coursesContainer">
            <?php foreach ($courses as $course): ?>
                <div class="course-card bg-white dark:bg-slate-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 dark:border-slate-700 overflow-hidden group"
                     data-status="<?= esc($course->status_course) ?>" 
                     data-title="<?= strtolower(esc($course->title_course)) ?>">
                    
                    <!-- Imagem do Curso -->
                    <div class="relative overflow-hidden">
                        <img
                            src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>"
                            alt="<?= esc($course->title_course) ?>"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500" />
                        
                        <!-- Badge de Status -->
                        <div class="absolute top-3 right-3">
                            <?php
                            $statusColors = [
                                'Ativo' => 'bg-green-500',
                                'Rascunho' => 'bg-amber-500',
                                'Arquivado' => 'bg-slate-500'
                            ];
                            $color = $statusColors[$course->status_course] ?? 'bg-blue-500';
                            ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white <?= $color ?> shadow-lg">
                                <?= esc($course->status_course) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Conteúdo do Card -->
                    <div class="p-5">
                        <h3 class="font-bold text-slate-800 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                            <?= esc($course->title_course) ?>
                        </h3>
                        
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-2">
                            <?= esc($course->description_course) ?>
                        </p>

                        <!-- Estatísticas -->
                        <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400 text-sm mb-4">
                            <div class="flex items-center gap-1">
                                <i class="bi bi-people"></i>
                                <span>234</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="bi bi-clock"></i>
                                <span>12h</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="bi bi-check2-circle"></i>
                                <span>Avançado</span>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex gap-2 pt-4 border-t border-slate-200 dark:border-slate-700">
                            <a href="/instructor/dashboard/meus_cursos/editar/<?= $course->id_course ?>" 
                               class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                                <i class="bi bi-pencil"></i>
                                Editar
                            </a>
                            
                            <form class="deleteForm flex-1" action="/instructor/dashboard/meus_cursos/deletar/<?= $course->id_course ?>" method="POST">
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                                    <i class="bi bi-trash"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

        <!-- Estado Vazio -->
        <?php if (empty($courses)): ?>
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="bi bi-journal-text text-3xl text-slate-400"></i>
                </div>
                <h3 class="font-bold text-slate-700 dark:text-slate-300 mb-3 text-lg">
                    Nenhum curso encontrado
                </h3>
                <p class="text-slate-500 dark:text-slate-500 text-sm mb-8 max-w-md mx-auto">
                    Você ainda não criou nenhum curso. Comece criando seu primeiro curso para compartilhar seu conhecimento.
                </p>
                <a href="/instructor/dashboard/novo_curso" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-blue-500/25">
                    <i class="bi bi-plus-circle"></i>
                    Criar Primeiro Curso
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const filterButtons = document.querySelectorAll(".filter-btn");
        const courseCards = document.querySelectorAll(".course-card");
        const searchInput = document.getElementById("searchInput");

        // Filtro por status
        filterButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                // Ativar botão selecionado
                filterButtons.forEach(b => {
                    b.classList.remove("bg-blue-600", "text-white", "border-blue-600");
                    b.classList.add("bg-white", "dark:bg-slate-700", "text-slate-700", "dark:text-slate-300", "border-slate-300", "dark:border-slate-600");
                });
                
                btn.classList.remove("bg-white", "dark:bg-slate-700", "text-slate-700", "dark:text-slate-300", "border-slate-300", "dark:border-slate-600");
                btn.classList.add("bg-blue-600", "text-white", "border-blue-600");

                const filter = btn.getAttribute("data-filter");
                courseCards.forEach(card => {
                    const status = card.getAttribute("data-status");
                    if (filter === "all" || status === filter) {
                        card.style.display = "block";
                        setTimeout(() => {
                            card.style.opacity = "1";
                            card.style.transform = "scale(1)";
                        }, 10);
                    } else {
                        card.style.opacity = "0.5";
                        card.style.transform = "scale(0.95)";
                        setTimeout(() => {
                            card.style.display = "none";
                        }, 300);
                    }
                });
            });
        });

        // Filtro por pesquisa
        searchInput.addEventListener("keyup", () => {
            const search = searchInput.value.toLowerCase();
            courseCards.forEach(card => {
                const title = card.getAttribute("data-title");
                if (title.includes(search)) {
                    card.style.display = "block";
                    setTimeout(() => {
                        card.style.opacity = "1";
                        card.style.transform = "scale(1)";
                    }, 10);
                } else {
                    card.style.opacity = "0.5";
                    card.style.transform = "scale(0.95)";
                    setTimeout(() => {
                        card.style.display = "none";
                    }, 300);
                }
            });
        });

        // Animações iniciais
        courseCards.forEach((card, index) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            setTimeout(() => {
                card.style.transition = "all 0.5s ease";
                card.style.opacity = "1";
                card.style.transform = "translateY(0)";
            }, index * 100);
        });
    });

    // Confirmação de eliminação
    document.querySelectorAll('.deleteForm').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Confirmar ação',
                text: "Deseja realmente eliminar este curso?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                background: '#1f2937',
                color: '#f9fafb'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Estamos eliminando o curso.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        background: '#1f2937',
                        color: '#f9fafb'
                    });

                    setTimeout(() => {
                        form.submit();
                    }, 1000);
                }
            });
        });
    });

    // Notificações SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->has('swal')):
            $s = session()->get('swal'); ?>
            Swal.fire({
                icon: '<?= esc($s['icon']) ?>',
                title: '<?= esc($s['title']) ?>',
                text: '<?= esc($s['text']) ?>',
                confirmButtonText: 'OK',
                background: '#1f2937',
                color: '#f9fafb'
            });
        <?php endif; ?>
    });

    // Adicionar CSS para line clamping
    const style = document.createElement('style');
    style.textContent = `
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .course-card {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
</script>

<?= $this->endSection() ?>