<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>
Finanças
<?= $this->endSection() ?>

<?= $this->section('financial') ?>
<div class="container-fluid">
  <div
    style="
                color: #ffffff;
                padding: 30px;
                min-height: 100vh;
              ">
    <!-- Header -->
    <div style="margin-bottom: 40px">
      <h1 style="font-size: 28px; font-weight: 600; margin: 0">
        Financeiro
      </h1>
    </div>

    <!-- Top Stats Cards -->
    <div class="row">
      <!-- Receita Total -->
      <div class="col-md-4 mb-4">
        <div
          style="
                      background: linear-gradient(
                        135deg,
                        #2563eb 0%,
                        #1e40af 100%
                      );
                      border-radius: 12px;
                      padding: 24px;

                      display: flex;
                      flex-direction: column;
                      justify-content: space-between;
                    ">
          <div
            style="
                        color: #cbd5e1;
                        font-size: 14px;
                        font-weight: 500;
                        margin-bottom: 8px;
                      ">
            Receita Total
          </div>
          <div
            style="
                        font-size: 32px;
                        font-weight: 700;
                        margin-bottom: 8px;
                      ">
            R$ 15.758,00
          </div>
          <div
            style="color: #22c55e; font-size: 14px; font-weight: 500">
            +15% este mês
          </div>
        </div>
      </div>

      <!-- Próximo Pagamento -->
      <div class="col-md-4 mb-4">
        <div
          style="
                      background: linear-gradient(
                        135deg,
                        #059669 0%,
                        #047857 100%
                      );
                      border-radius: 12px;
                      padding: 24px;

                      display: flex;
                      flex-direction: column;
                      justify-content: space-between;
                    ">
          <div
            style="
                        color: #d1fae5;
                        font-size: 14px;
                        font-weight: 500;
                        margin-bottom: 8px;
                      ">
            Próximo Pagamento
          </div>
          <div
            style="
                        font-size: 32px;
                        font-weight: 700;
                        margin-bottom: 8px;
                      ">
            R$ 3.245,00
          </div>
          <div
            style="color: #d1fae5; font-size: 14px; font-weight: 500">
            Em 5 dias
          </div>
        </div>
      </div>

      <!-- Receita Média/Mês -->
      <div class="col-md-4 mb-4">
        <div
          style="
                      background: linear-gradient(
                        135deg,
                        #7c3aed 0%,
                        #5b21b6 100%
                      );
                      border-radius: 12px;
                      padding: 24px;

                      display: flex;
                      flex-direction: column;
                      justify-content: space-between;
                    ">
          <div
            style="
                        color: #e9d5ff;
                        font-size: 14px;
                        font-weight: 500;
                        margin-bottom: 8px;
                      ">
            Receita Média/Mês
          </div>
          <div
            style="
                        font-size: 32px;
                        font-weight: 700;
                        margin-bottom: 8px;
                      ">
            R$ 4.890,00
          </div>
          <div
            style="color: #22c55e; font-size: 14px; font-weight: 500">
            +8% vs média anual
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section -->
    <div class="row">
      <!-- Receita por Mês (Chart Placeholder) -->
      <div class="col-md-6 mb-3">
        <div
          style="
                      background-color: #2a3441;
                      border-radius: 12px;
                      padding: 24px;
                      height: 400px;
                      display: flex;
                      flex-direction: column;
                    ">
          <h3
            style="
                        font-size: 18px;
                        font-weight: 600;
                        margin-bottom: 20px;
                      ">
            Receita por Mês
          </h3>
          <div
            style="
                        flex: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #64748b;
                      ">
            <div style="text-align: center">
              <div
                style="
                            width: 60px;
                            height: 60px;
                            margin: 0 auto 16px;
                            opacity: 0.3;
                          ">
                <svg
                  width="60"
                  height="60"
                  fill="currentColor"
                  viewBox="0 0 16 16">
                  <path
                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                  <path
                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                </svg>
              </div>
              <div
                style="
                            font-size: 16px;
                            font-weight: 500;
                            margin-bottom: 4px;
                          ">
                Gráfico de receita mensal
              </div>
              <div style="font-size: 14px; color: #64748b">
                (Área para implementação do gráfico)
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimas Transações -->
      <div class="col-md-6 mb-3">
        <div
          style="
                      background-color: #2a3441;
                      border-radius: 12px;
                      padding: 24px;
                      height: 400px;
                    ">
          <h3
            style="
                        font-size: 18px;
                        font-weight: 600;
                        margin-bottom: 20px;
                      ">
            Últimas Transações
          </h3>

          <!-- Transaction 1 -->
          <div
            style="
                        padding: 16px;
                        background-color: #1e2837;
                        border-radius: 8px;
                        margin-bottom: 12px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                      ">
            <div>
              <div style="font-weight: 600; margin-bottom: 4px">
                Pagamento - Dezembro
              </div>
              <div style="color: #22c55e; font-weight: 600">
                +R$ 3.245,00
              </div>
            </div>
            <div
              style="
                          background-color: #374151;
                          color: #9ca3af;
                          padding: 4px 12px;
                          border-radius: 6px;
                          font-size: 12px;
                        ">
              15 Dez 2024
            </div>
          </div>

          <!-- Transaction 2 -->
          <div
            style="
                        padding: 16px;
                        background-color: #1e2837;
                        border-radius: 8px;
                        margin-bottom: 12px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                      ">
            <div>
              <div style="font-weight: 600; margin-bottom: 4px">
                Pagamento - Novembro
              </div>
              <div style="color: #22c55e; font-weight: 600">
                +R$ 2.890,00
              </div>
            </div>
            <div
              style="
                          background-color: #374151;
                          color: #9ca3af;
                          padding: 4px 12px;
                          border-radius: 6px;
                          font-size: 12px;
                        ">
              15 Nov 2024
            </div>
          </div>

          <!-- Transaction 3 -->
          <div
            style="
                        padding: 16px;
                        background-color: #1e2837;
                        border-radius: 8px;
                        margin-bottom: 12px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                      ">
            <div>
              <div style="font-weight: 600; margin-bottom: 4px">
                Taxa de Plataforma
              </div>
              <div style="color: #ef4444; font-weight: 600">
                -R$ 289,00
              </div>
            </div>
            <div
              style="
                          background-color: #374151;
                          color: #9ca3af;
                          padding: 4px 12px;
                          border-radius: 6px;
                          font-size: 12px;
                        ">
              10 Nov 2024
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>