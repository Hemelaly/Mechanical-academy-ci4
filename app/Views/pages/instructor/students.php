<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Alunos<?= $this->endSection() ?>

<?= $this->section('students') ?>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-slate-50 dark:bg-slate-900">
  <div class="container mx-auto">

    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white mb-2">
        Gestão de Alunos
      </h1>
      <p class="text-slate-600 dark:text-slate-400 text-sm">
        Gerencie seus alunos e acompanhe o progresso dos cursos
      </p>
    </div>

    <!-- Cards de Estatísticas - Layout Melhorado -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
      <!-- Total de Alunos -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">
              Total de Alunos
            </p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">
              4
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-arrow-up-short text-green-500 text-sm flex-shrink-0"></i>
              <span class="text-green-500 text-sm font-medium truncate">
                +2 este mês
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-people text-blue-600 dark:text-blue-400 text-base sm:text-lg"></i>
          </div>
        </div>
      </div>

      <!-- Certificados Emitidos -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">
              Certificados Emitidos
            </p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">
              2
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-award text-amber-500 text-sm flex-shrink-0"></i>
              <span class="text-amber-500 text-sm font-medium truncate">
                50% dos alunos
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-file-earmark-check text-green-600 dark:text-green-400 text-base sm:text-lg"></i>
          </div>
        </div>
      </div>

      <!-- Taxa de Conclusão -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-6 shadow-lg border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
          <div class="flex-1 min-w-0">
            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1 truncate">
              Taxa de Conclusão
            </p>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-1">
              50%
            </h3>
            <div class="flex items-center gap-1">
              <i class="bi bi-graph-up text-purple-500 text-sm flex-shrink-0"></i>
              <span class="text-purple-500 text-sm font-medium truncate">
                Média geral
              </span>
            </div>
          </div>
          <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
            <i class="bi bi-percent text-purple-600 dark:text-purple-400 text-base sm:text-lg"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Pedidos de Inscrição -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 mb-8">
      <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
        <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">Pedidos de Inscrição</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Aprove ou rejeite solicitações de inscrição pendentes</p>
      </div>

      <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs">
        <table class="w-full text-sm text-left rtl:text-right text-body">
          <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
            <tr>
              <th scope="col" class="p-4">
                <div class="flex items-center">
                  <input type="checkbox" class="w-4 h-4 border border-default-medium rounded-md bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                </div>
              </th>
              <th scope="col" class="px-6 py-3">Aluno</th>
              <th scope="col" class="px-6 py-3">Curso</th>
              <th scope="col" class="px-6 py-3">Comprovativo</th>
              <th scope="col" class="px-6 py-3">Status</th>
              <th scope="col" class="px-6 py-3">Ações</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($payments as $payment): ?>
              <?php if ($payment->status_payment === 'Pendente'): ?>
                <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                  <td class="w-4 p-4">
                    <div class="flex items-center">
                      <input type="checkbox" class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                    </div>
                  </td>

                  <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                    <div class="min-w-0">
                      <div class="truncate"><?= esc($payment->username) ?></div>
                      <div class="text-xs text-body truncate"><?= esc($payment->email) ?></div>
                    </div>
                  </th>

                  <td class="px-6 py-4">
                    <?= esc($payment->title_course) ?>
                  </td>

                  <td class="px-6 py-4">
                    <button type="button"
                      class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-base text-xs"
                      data-bs-toggle="modal"
                      data-bs-target="#comprovativoModal<?= $payment->id_payment ?>">
                      <i class="bi bi-eye"></i>
                      Ver
                    </button>
                  </td>

                  <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900 text-amber-600 dark:text-amber-400">
                      Pendente
                    </span>
                  </td>

                  <td class="px-6 py-4">
                    <div class="flex items-center gap-2 flex-wrap">
                      <form class="acceptForm"
                        id="acceptForm<?= $payment->id_payment ?>"
                        action="/instructor/dashboard/meus_estudantes/<?= $payment->id_course ?>/<?= $payment->id_user_payment ?>"
                        method="post">
                        <input type="hidden" name="status_enrollment" value="Ativo">
                        <input type="hidden" name="status_payment" value="Aprovado">
                        <button type="submit"
                          class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-base text-xs">
                          <i class="bi bi-check-lg"></i>
                          Aceitar
                        </button>
                      </form>

                      <form action="/instructor/dashboard/meus_estudantes/<?= $payment->id_course ?>/<?= $payment->id_user_payment ?>" method="post">
                        <input type="hidden" name="status_payment" value="Rejeitado">
                        <button type="submit"
                          class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-base text-xs">
                          <i class="bi bi-x-lg"></i>
                          Rejeitar
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Estado Vazio -->
      <?php
      $hasPendingPayments = false;
      foreach ($payments as $payment) {
        if ($payment->status_payment == 'Pendente') {
          $hasPendingPayments = true;
          break;
        }
      }
      ?>
      <?php if (!$hasPendingPayments): ?>
        <div class="text-center py-8 sm:py-12">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
            <i class="bi bi-check2-circle text-slate-400 text-xl sm:text-2xl"></i>
          </div>
          <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 text-sm sm:text-base">Nenhum pedido pendente</h4>
          <p class="text-slate-500 dark:text-slate-500 text-xs sm:text-sm max-w-md mx-auto">
            Todos os pedidos de inscrição foram processados.
          </p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Alunos Inscritos -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700">
      <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700">
        <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-white">Alunos Inscritos</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Acompanhe o progresso e gerencie alunos ativos</p>
      </div>

      <div class="bg-neutral-primary-soft shadow-xs rounded-base overflow-hidden">
        <div class="w-full overflow-x-auto">
          <table class="w-full min-w-[720px] text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
              <tr>
                <th scope="col" class="p-4">
                  <div class="flex items-center">
                    <input type="checkbox"
                      class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                  </div>
                </th>

                <!-- teus THs aqui -->
                <th scope="col" class="px-6 py-3">Aluno</th>
                <th scope="col" class="px-6 py-3">Curso</th>
                <th scope="col" class="px-6 py-3">Progresso</th>
                <th scope="col" class="px-6 py-3">Último Acesso</th>
                <th scope="col" class="px-6 py-3">Status</th>
                <th scope="col" class="px-6 py-3">Ações</th>
              </tr>
            </thead>

            <!-- ✅ controla bordas aqui -->
            <tbody>
              <?php foreach ($enrollments as $enrollment): ?>
                <tr class="bg-neutral-primary-soft hover:bg-neutral-secondary-medium">
                  <td class="w-4 p-4">
                    <div class="flex items-center">
                      <input type="checkbox"
                        class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                    </div>
                  </td>

                  <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                    <div class="min-w-0">
                      <div class="truncate"><?= esc($enrollment->name_student) ?></div>
                      <div class="text-xs text-body truncate"><?= esc($enrollment->email_student) ?></div>
                    </div>
                  </th>

                  <td class="px-6 py-4"><?= esc($enrollment->title_course) ?></td>

                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-16 bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 35%"></div>
                      </div>
                      <span class="text-xs font-medium">35%</span>
                    </div>
                  </td>

                  <td class="px-6 py-4">2 horas</td>

                  <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400">
                      Ativo
                    </span>
                  </td>

                  <td class="px-6 py-4">
                    <button class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-base text-xs">
                      <i class="bi bi-lock"></i>
                      Bloquear
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- pager dentro do card, com padding -->
        <div class="flex justify-end p-4">
          <?= $pager->links('enrollments', 'tailwind_full') ?>
        </div>
      </div>

      <!-- Estado Vazio -->
      <?php
      $hasActiveEnrollments = false;
      foreach ($enrollments as $enrollment) {
        if ($enrollment->status_enrollment == 'Ativa') {
          $hasActiveEnrollments = true;
          break;
        }
      }
      ?>
      <?php if (!$hasActiveEnrollments): ?>
        <div class="text-center py-8 sm:py-12">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-slate-100 dark:bg-slate-700 rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
            <i class="bi bi-people text-slate-400 text-xl sm:text-2xl"></i>
          </div>
          <h4 class="font-bold text-slate-700 dark:text-slate-300 mb-2 text-sm sm:text-base">Nenhum aluno inscrito</h4>
          <p class="text-slate-500 dark:text-slate-500 text-xs sm:text-sm max-w-md mx-auto">
            Ainda não há alunos inscritos nos seus cursos.
          </p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  // Confirmação para aceitar aluno
  document.querySelectorAll('.acceptForm').forEach(function(form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Confirmar ação',
        text: "Deseja realmente aceitar este estudante?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, aceitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        background: '#1f2937',
        color: '#f9fafb'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Processando...',
            text: 'Estamos atualizando o status.',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
            background: '#1f2937',
            color: '#f9fafb'
          });
          form.submit();
        }
      });
    });
  });

  // Confirmação para rejeitar aluno
  document.querySelectorAll('form[action*="meus_estudantes"]').forEach(function(form) {
    if (!form.classList.contains('acceptForm')) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Confirmar ação',
          text: "Deseja realmente rejeitar este estudante?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sim, rejeitar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#6b7280',
          background: '#1f2937',
          color: '#f9fafb'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });
    }
  });

  // Confirmação para bloquear aluno
  document.querySelectorAll('button.bg-red-600').forEach(function(button) {
    if (button.closest('td')) {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
          title: 'Bloquear aluno',
          text: "Deseja realmente bloquear o acesso deste aluno?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sim, bloquear',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#6b7280',
          background: '#1f2937',
          color: '#f9fafb'
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire({
              title: 'Aluno bloqueado',
              text: 'O acesso do aluno foi bloqueado com sucesso.',
              icon: 'success',
              confirmButtonText: 'OK',
              background: '#1f2937',
              color: '#f9fafb'
            });
          }
        });
      });
    }
  });
</script>

<?= $this->endSection() ?>