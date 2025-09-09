<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Alunos<?= $this->endSection() ?>

<?= $this->section('students') ?>
<div class="container-fluid">
  <!-- Header -->
  <div style="margin-bottom: 40px">
    <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 8px">
      Alunos
    </h1>
    <p style="color: #8a9ba8; font-size: 16px; margin: 0">
      Gerencie seus alunos e acompanhe o progresso
    </p>
  </div>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-4 mb-3">
      <div
        style="
                    background: linear-gradient(
                      135deg,
                      #2a3441 0%,
                      #1e2837 100%
                    );
                    border: 1px solid #2a3441;
                    border-radius: 12px;
                    padding: 24px;
                  ">
        <div
          style="
                      width: 48px;
                      height: 48px;
                      background-color: #4a90e2;
                      border-radius: 10px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      margin-bottom: 16px;
                    ">
          <svg
            width="24"
            height="24"
            fill="white"
            viewBox="0 0 16 16">
            <path
              d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
            <path
              fill-rule="evenodd"
              d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
            <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
          </svg>
        </div>
        <div
          style="
                      font-size: 32px;
                      font-weight: 700;
                      margin-bottom: 4px;
                    ">
          1,234
        </div>
        <div style="color: #8a9ba8; font-size: 14px">
          Total de Alunos
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div
        style="
                    background: linear-gradient(
                      135deg,
                      #2a3441 0%,
                      #1e2837 100%
                    );
                    border: 1px solid #2a3441;
                    border-radius: 12px;
                    padding: 24px;
                  ">
        <div
          style="
                      width: 48px;
                      height: 48px;
                      background-color: #4a90e2;
                      border-radius: 10px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      margin-bottom: 16px;
                    ">
          <svg
            width="24"
            height="24"
            fill="white"
            viewBox="0 0 16 16">
            <path
              d="M9.5 3A1.5 1.5 0 0 0 8 4.5h-1A1.5 1.5 0 0 0 5.5 3H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h1.5a1.5 1.5 0 0 0 1.5-1.5V4.5A1.5 1.5 0 0 1 9.5 3H10a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H9.5a1.5 1.5 0 0 1-1.5-1.5V7a.5.5 0 0 1 1 0v1a.5.5 0 0 0 .5.5H10V4h-.5z" />
            <path
              d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z" />
          </svg>
        </div>
        <div
          style="
                      font-size: 32px;
                      font-weight: 700;
                      margin-bottom: 4px;
                    ">
          892
        </div>
        <div style="color: #8a9ba8; font-size: 14px">
          Certificados Emitidos
        </div>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div
        style="
                    background: linear-gradient(
                      135deg,
                      #2a3441 0%,
                      #1e2837 100%
                    );
                    border: 1px solid #2a3441;
                    border-radius: 12px;
                    padding: 24px;
                  ">
        <div
          style="
                      width: 48px;
                      height: 48px;
                      background-color: #4a90e2;
                      border-radius: 10px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      margin-bottom: 16px;
                    ">
          <svg
            width="24"
            height="24"
            fill="white"
            viewBox="0 0 16 16">
            <path
              d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
          </svg>
        </div>
        <div
          style="
                      font-size: 32px;
                      font-weight: 700;
                      margin-bottom: 4px;
                    ">
          72%
        </div>
        <div style="color: #8a9ba8; font-size: 14px">
          Taxa de Conclusão
        </div>
      </div>
    </div>
  </div>

  <!-- Students List Section -->
  <div
    style="
                background-color: #2a3441;
                border-radius: 12px;
                overflow: hidden;
              ">
    <!-- Header -->
    <div
      style="
                  padding: 24px;
                  border-bottom: 1px solid #3a4450;
                  display: flex;
                  justify-content: space-between;
                  align-items: center;
                ">
      <h3 style="font-size: 20px; font-weight: 600; margin: 0">
        Lista de Alunos
      </h3>
      <div style="display: flex; gap: 12px; align-items: center">
        <input
          type="text"
          placeholder="Buscar aluno..."
          style="
                      background-color: #1e2837;
                      border: 1px solid #3a4450;
                      color: #ffffff;
                      border-radius: 8px;
                      padding: 8px 16px;
                      width: 200px;
                    " />
        <button
          style="
                      background-color: #4a90e2;
                      border: none;
                      color: white;
                      border-radius: 8px;
                      padding: 8px 16px;
                      font-weight: 500;
                      cursor: pointer;
                    ">
          Exportar
        </button>
      </div>
    </div>

    <!-- Table Header -->
    <div
      style="
                  display: grid;
                  grid-template-columns: 2fr 2fr 1.5fr 1.5fr 1fr 1fr;
                  padding: 16px 24px;
                  border-bottom: 1px solid #3a4450;
                  color: #8a9ba8;
                  font-size: 14px;
                  font-weight: 500;
                ">
      <div>Aluno</div>
      <div>Curso</div>
      <div>Progresso</div>
      <div>Último Acesso</div>
      <div>Status</div>
      <div>Ações</div>
    </div>

    <!-- Student Row 1 -->
    <div
      style="
                  display: grid;
                  grid-template-columns: 2fr 2fr 1.5fr 1.5fr 1fr 1fr;
                  padding: 20px 24px;
                  border-bottom: 1px solid #3a4450;
                  align-items: center;
                ">
      <div style="display: flex; align-items: center">
        <div
          style="
                      width: 40px;
                      height: 40px;
                      background-color: #4a5568;
                      border-radius: 50%;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      font-weight: 600;
                      margin-right: 12px;
                    ">
          AS
        </div>
        <div>
          <div style="font-weight: 600; margin-bottom: 2px">
            Ana Silva
          </div>
          <div style="color: #8a9ba8; font-size: 13px">
            ana@email.com
          </div>
        </div>
      </div>
      <div>JavaScript Avançado</div>
      <div>
        <div
          style="
                      width: 120px;
                      height: 8px;
                      background-color: #3a4450;
                      border-radius: 4px;
                      overflow: hidden;
                    ">
          <div
            style="
                        width: 35%;
                        height: 100%;
                        background-color: #4a90e2;
                        border-radius: 4px;
                      "></div>
        </div>
      </div>
      <div>Há 2 horas</div>
      <div>
        <span
          style="
                      background-color: rgba(56, 161, 105, 0.2);
                      color: #68d391;
                      padding: 4px 12px;
                      border-radius: 20px;
                      font-size: 12px;
                      font-weight: 500;
                    ">Ativo</span>
      </div>
      <div>
        <button
          style="
                      background: none;
                      border: none;
                      color: #8a9ba8;
                      padding: 4px;
                      margin: 0 2px;
                      cursor: pointer;
                    ">
          👁
        </button>
        <button
          style="
                      background: none;
                      border: none;
                      color: #8a9ba8;
                      padding: 4px;
                      margin: 0 2px;
                      cursor: pointer;
                    ">
          💬
        </button>
      </div>
    </div>

    <!-- Student Row 2 -->
    <div
      style="
                  display: grid;
                  grid-template-columns: 2fr 2fr 1.5fr 1.5fr 1fr 1fr;
                  padding: 20px 24px;
                  align-items: center;
                ">
      <div style="display: flex; align-items: center">
        <div
          style="
                      width: 40px;
                      height: 40px;
                      background-color: #4a5568;
                      border-radius: 50%;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      font-weight: 600;
                      margin-right: 12px;
                    ">
          CS
        </div>
        <div>
          <div style="font-weight: 600; margin-bottom: 2px">
            Carlos Santos
          </div>
          <div style="color: #8a9ba8; font-size: 13px">
            carlos@email.com
          </div>
        </div>
      </div>
      <div>React para Iniciantes</div>
      <div>
        <div
          style="
                      width: 120px;
                      height: 8px;
                      background-color: #3a4450;
                      border-radius: 4px;
                      overflow: hidden;
                    ">
          <div
            style="
                        width: 65%;
                        height: 100%;
                        background-color: #4a90e2;
                        border-radius: 4px;
                      "></div>
        </div>
      </div>
      <div>Há 1 dia</div>
      <div>
        <span
          style="
                      background-color: rgba(56, 161, 105, 0.2);
                      color: #68d391;
                      padding: 4px 12px;
                      border-radius: 20px;
                      font-size: 12px;
                      font-weight: 500;
                    ">Ativo</span>
      </div>
      <div>
        <button
          style="
                      background: none;
                      border: none;
                      color: #8a9ba8;
                      padding: 4px;
                      margin: 0 2px;
                      cursor: pointer;
                    ">
          👁
        </button>
        <button
          style="
                      background: none;
                      border: none;
                      color: #8a9ba8;
                      padding: 4px;
                      margin: 0 2px;
                      cursor: pointer;
                    ">
          💬
        </button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>