<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Alunos<?= $this->endSection() ?>

<?= $this->section('students') ?>
<style>
  /* Cards de estatísticas */
  .cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 40px;
  }

  .card {
    flex: 1 1 250px;
    background: linear-gradient(135deg, #2a3441 0%, #1e2837 100%);
    border: 1px solid #2a3441;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .card .icon {
    width: 48px;
    height: 48px;
    background-color: #4a90e2;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
  }

  .card .number {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .card .label {
    font-size: 14px;
    color: #8a9ba8;
  }

  /* Tabelas arredondadas */
  .table-container {
    overflow-x: auto;
    margin-bottom: 40px;
    border-radius: 12px;
    background-color: #2a3441;
    padding: 10px;
  }

  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 600px;
  }

  th,
  td {
    padding: 12px 16px;
    text-align: left;
  }

  th {
    background-color: #2a3441;
    color: #8a9ba8;
    font-weight: 600;
  }

  tbody tr {
    background-color: #1f2a38;
  }

  tbody tr:first-child td:first-child {
    border-top-left-radius: 8px;
  }

  tbody tr:first-child td:last-child {
    border-top-right-radius: 8px;
  }

  tbody tr:last-child td:first-child {
    border-bottom-left-radius: 8px;
  }

  tbody tr:last-child td:last-child {
    border-bottom-right-radius: 8px;
  }

  tr:hover {
    background-color: #3a4450;
  }

  .status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
  }

  .status-ativo {
    background-color: rgba(56, 161, 105, 0.2);
    color: #68d391;
  }

  .status-inativo {
    background-color: rgba(229, 62, 62, 0.2);
    color: #e53e3e;
  }

  .status-pendente {
    background-color: rgba(229, 162, 0, 0.2);
    color: #d69e2e;
  }

  .actions button {
    background: none;
    border: none;
    padding: 4px 8px;
    margin: 0 2px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
  }

  .btn-accept {
    background-color: #68d391;
    color: #fff;
  }

  .btn-reject {
    background-color: #e53e3e;
    color: #fff;
  }

  .btn-block {
    background-color: #f6e05e;
    color: #000;
  }

  @media(max-width:768px) {
    .cards {
      flex-direction: column;
    }
  }
</style>

<h1>Alunos</h1>
<p>Gerencie seus alunos e acompanhe o progresso</p>

<!-- Cards -->
<div class="cards">
  <div class="card">
    <div class="icon">
      <svg width="24" height="24" fill="white" viewBox="0 0 16 16">
        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
        <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
        <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
      </svg>
    </div>
    <div class="number text-white">4</div>
    <div class="label">Total de Alunos</div>
  </div>
  <div class="card">
    <div class="icon">
      <svg width="24" height="24" fill="white" viewBox="0 0 16 16">
        <path d="M9.5 3A1.5 1.5 0 0 0 8 4.5h-1A1.5 1.5 0 0 0 5.5 3H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h1.5a1.5 1.5 0 0 0 1.5-1.5V4.5A1.5 1.5 0 0 1 9.5 3H10a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H9.5a1.5 1.5 0 0 1-1.5-1.5V7a.5.5 0 0 1 1 0v1a.5.5 0 0 0 .5.5H10V4h-.5z" />
        <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z" />
      </svg>
    </div>
    <div class="number text-white">2</div>
    <div class="label">Certificados Emitidos</div>
  </div>
  <div class="card">
    <div class="icon">
      <svg width="24" height="24" fill="white" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
      </svg>
    </div>
    <div class="number text-white">50%</div>
    <div class="label">Taxa de Conclusão</div>
  </div>
</div>

<!-- Tabela Pedidos de Inscrição -->
<h3>Pedidos de Inscrição</h3>
<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Aluno</th>
        <th>Curso</th>
        <th>Progresso</th>
        <th>Último Acesso</th>
        <th>Status</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($enrollments as $key => $enrollment): ?>
        <?php if ($enrollment->status_enrollment == 'Pendente'): ?>
          <tr class="">
            <td><?= $enrollment->name_student ?><br><small style="color:#8a9ba8;"><?= $enrollment->email_student ?></small></td>
            <td><?= $enrollment->title_course ?></td>
            <td>35%</td>
            <td>2 horas</td>
            <td><span class="status-badge status-pendente">Pendente</span></td>
            <td class="actions d-flex align-items-center gap-2">
              <form action="/instructor/dashboard/meus_estudantes/<?= $enrollment->id_enrollment ?>" method="post">
                <input type="hidden" name="status_enrollment" value="Ativo">
                <input type="hidden" name="status_payment" value="Aprovado">
                <button type="submit" class="btn btn-success btn-sm my-1">Aceitar</button>
              </form>

              <form action="/instructor/dashboard/meus_estudantes/<?= $enrollment->id_enrollment ?>" method="post">
                <input type="hidden" name="status_payment" value="Rejeitado">
                <button type="submit" class="btn btn-danger btn-sm my-1">Rejeitar</button>
              </form>

            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Tabela Alunos Inscritos -->
<h3>Alunos Inscritos</h3>
<div class="table-container">
  <table>
    <thead>
      <tr>
        <th>Aluno</th>
        <th>Curso</th>
        <th>Progresso</th>
        <th>Último Acesso</th>
        <th>Status</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($enrollments as $key => $enrollment): ?>
        <?php if ($enrollment->status_enrollment == 'Ativo'): ?>
          <tr>
            <td><?= $enrollment->name_student ?><br><small style="color:#8a9ba8;"><?= $enrollment->email_student ?></small></td>
            <td><?= $enrollment->title_course ?></td>
            <td>35%</td>
            <td>2 horas</td>
            <td><span class="status-badge status-ativo">Ativo</span></td>
            <td class="actions g-4">
              <a href="" class="btn btn-danger btn-sm">Bloquear</a>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>