<?php

$session = session();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout - <?= $course->title_course ?></title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

    body {
      font-family: "Poppins", sans-serif;
    }

    .gradient-bg {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }

    .course-card {
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .course-card:hover {
      transform: translateY(-5px);
    }

    /* Customização para manter a aparência do Tailwind */
    .header-bg {
      background: url(<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>) center/cover no-repeat;
      position: relative;
    }

    .header-bg::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .sticky-top {
      position: sticky;
      top: 20px;
    }

    .text-green-700 {
      color: #15803d;
    }

    .bg-green-50 {
      background-color: #f0fdf4;
    }

    .bg-green-700 {
      background-color: #15803d;
    }

    .text-blue-500 {
      color: #3b82f6;
    }

    .bg-blue-500 {
      background-color: #3b82f6;
    }

    .hover\:bg-blue-600:hover {
      background-color: #2563eb;
    }

    .border-gray-300 {
      border-color: #d1d5db;
    }

    .focus\:ring-blue-500:focus {
      box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    /* Ajustes para o accordion */
    details summary {
      list-style: none;
    }

    details summary::-webkit-details-marker {
      display: none;
    }

    details[open] summary svg {
      transform: rotate(180deg);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
      color: #6c757d;
    }

    .divider::before,
    .divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background-color: #444;
    }

    .divider span {
      padding: 0 15px;
    }

    .dropzone {
      min-height: 200px;
      border: 2px dashed #0d6efd;
      border-radius: 10px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .dropzone:hover,
    .dropzone.dragover {
      background: #ecececff;
      border-color: #0d6efd;
    }

    .dropzone i {
      font-size: 3rem;
      margin-bottom: 15px;
      color: #0d6efd;
    }

    .preview-container {
      display: none;
      margin-top: 20px;
      text-align: center;
    }

    .preview-image {
      max-width: 100%;
      max-height: 200px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
  </style>
</head>

<body class="">
  <!-- Header -->
  <header class="header-bg text-white py-5 py-md-6">
    <div class="container position-relative z-1">
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
          <h1 class="h2 fw-bold mb-1 text-uppercase"><?= $course->title_course ?></h1>
          <p class="h5 mb-0">Análise de Dados</p>
        </div>
        <div class="col-md-6 text-center text-lg-end">
          <p class="h5 mb-1">70+ Projectos Reais</p>
          <p class="h5 mb-0">Para solidificar o teu conhecimento</p>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container my-5">
    <div class="row g-4">
      <!-- Course Details -->
      <div class="col-lg-8">
        <div class="sticky-top">
          <div class="course-card bg-white rounded-3 p-4 mb-4">
            <h2 class="h3 fw-bold text-dark mb-3">Sobre o Curso</h2>
            <p class="text-muted mb-4">
              Aprenda estruturas de dados e algoritmos essenciais através de
              mais de 70 desafios práticos em JavaScript. Este curso é ideal
              para desenvolvedores que desejam melhorar suas habilidades de
              resolução de problemas e preparar-se para entrevistas técnicas.
            </p>

            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-play-circle text-dark fs-5 me-3"></i>
                  <span>12.5 horas de vídeo sob demanda</span>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-file-alt text-dark fs-5 me-3"></i>
                  <span>Documentação completa e sandbox com testes</span>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-infinity text-dark fs-5 me-3"></i>
                  <span>Acesso vitalício</span>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-mobile-alt text-dark fs-5 me-3"></i>
                  <span>Acesso no celular e TV</span>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-certificate text-dark fs-5 me-3"></i>
                  <span>Certificado de conclusão</span>
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <div class="d-flex align-items-center">
                  <i class="fab fa-discord text-dark fs-5 me-3"></i>
                  <span>Acesso à comunidade do Discord</span>
                </div>
              </div>
            </div>

            <div class="border-top pt-3">
              <h3 class="h5 fw-semibold mb-2">O que você aprenderá</h3>
              <ul class="list-unstyled text-muted">
                <li class="mb-1">
                  <i class="bi bi-check-circle-fill text-dark me-2"></i>
                  Implementar estruturas de dados como arrays, listas ligadas,
                  pilhas e filas
                </li>
                <li class="mb-1">
                  <i class="bi bi-check-circle-fill text-dark me-2"></i>
                  Dominar algoritmos de ordenação e busca
                </li>
                <li class="mb-1">
                  <i class="bi bi-check-circle-fill text-dark me-2"></i>
                  Resolver problemas complexos com técnicas de programação
                  dinâmica
                </li>
                <li class="mb-1">
                  <i class="bi bi-check-circle-fill text-dark me-2"></i>
                  Analisar a complexidade de tempo e espaço dos algoritmos
                </li>
                <li class="mb-1">
                  <i class="bi bi-check-circle-fill text-dark me-2"></i>
                  Preparar-se para entrevistas técnicas em empresas de
                  tecnologia
                </li>
              </ul>
            </div>
          </div>

          <a href="https://hemelaly.github.io/MT-Academy---EXCEL/" class="btn btn-primary">
            <i class="fas fa-chevron-left py-2 px-2"></i> Voltar à Página do Curso
          </a>
        </div>

        <!-- Additional Course Option -->
        <!-- <div class="course-card bg-white rounded-3 p-4">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="add-course">
              <label class="form-check-label fw-semibold" for="add-course">
                Precisa aprender o básico primeiro?
              </label>
            </div>
            <p class="text-muted mb-3">
              Adquira Modern JS From The Beginning por +$25
            </p>
            <div class="bg-green-50 p-3 rounded-3">
              <p class="fw-semibold text-green-700 mb-1">
                Modern JavaScript from The Beginning 2.0
              </p>
              <p class="small text-muted mb-0">
                Aprenda JavaScript moderno do zero, incluindo ES6+, módulos,
                promises, async/await e muito mais.
              </p>
            </div>
          </div> -->
      </div>

      <!-- Purchase Section -->
      <div class="col-lg-4">
        <div class="course-card bg-white rounded-3 p-4 sticky-top">
          <div class="text-center mb-4">
            <?php

            $price1 = ($course->price_course * 0.75) + $course->price_course;

            $price2 = $course->price_course;

            ?>
            <p class="text-muted text-decoration-line-through mb-1"><?= number_format($price1, 2, ",", ".") ?> MZN</p>
            <p class="h2 fw-bold text-dark mb-1"><?= number_format($price2, 2, ",", ".") ?> MZN</p>
            <p class="text-primary fw-semibold">75% de desconto!</p>
          </div>

          <?php if ($isEnrolled): ?>
            <div class="alert alert-success text-center">
              <h4 class="alert-heading">Você já está inscrito neste curso!</h4>
              <p>Parabéns! Já tens acesso completo ao curso <?= $course->title_course ?>.</p>
              <hr>
              <a href="<?= base_url('/student/dashboard/meus_cursos') ?>" class="btn btn-primary">Ir para meus cursos</a>
            </div>
          <?php elseif(($user) && ($user->role == "instructor")): ?>
            <div class="alert alert-warning text-center">
              <h4 class="alert-heading">Você é um instrutor!</h4>
              <p>Não pode se inscrever neste curso.</p>
              <hr>
              <a href="<?= base_url('/instructor/dashboard/') ?>" class="btn btn-primary">Ir para meus cursos</a>
            </div>
          <?php else: ?>
            <form id="checkout-form" action="/checkout/pending/<?= $course->id_course ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
              <?= csrf_field() ?>

              <div class="mb-3">
                <label for="coupon" class="form-label">Código do Cupom</label>
                <div class="input-group">
                  <input type="text" class="form-control text-sm" id="coupon">
                  <button class="btn btn-outline-secondary" type="button">Aplicar</button>
                </div>
              </div>

              <?php if (($user) && ($user->role !== "instructor")): ?>
                <!-- Se já estiver logado, passa os dados como hidden -->
                <input type="hidden" name="id_user" value="<?= $user->id ?>">
                <input type="hidden" name="email" value="<?= $user->email ?>">
                <input type="hidden" name="username" value="<?= $user->username ?>">

              <?php else: ?>
                <!-- Se NÃO estiver logado, mostra os campos -->
                <a href="<?= base_url('/login') ?>" type="button" class="btn btn-outline-primary w-100 mb-3">
                  <i class="fa-solid fa-right-to-bracket me-2"></i> Fazer login
                </a>

                <div class="position-relative text-center my-3">
                  <hr>
                  <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted small">ou</span>
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Endereço de Email</label>
                  <input type="email" name="email" class="form-control" id="email" required>
                  <div class="invalid-feedback">
                    Por favor, insira um email válido.
                  </div>
                </div>

                <div class="mb-3">
                  <label for="name" class="form-label">Nome Completo</label>
                  <input type="text" name="username" class="form-control" id="name" required>
                  <div class="invalid-feedback">
                    Por favor, insira seu nome completo.
                  </div>
                </div>
              <?php endif; ?>


              <div class="mb-3">
                <p class="form-label">Método de Pagamento</p>
                <div class="d-flex gap-4">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment" id="mobile" checked>
                    <label class="form-check-label" for="mobile">
                      Mpesa/E-mola
                    </label>
                  </div>
                  <!-- <div class="form-check">
                  <input class="form-check-input" type="radio" name="payment" id="paypal">
                  <label class="form-check-label" for="paypal">
                    PayPal
                  </label>
                </div> -->
                </div>
              </div>

              <!-- Div oculta que será exibida quando o radio estiver selecionado -->
              <div id="payment-info" class="mt-3 d-none">
                <p><strong>Contacto para transferência:</strong></p>
                <p style="margin-top: -10px;">+258 84 123 4567 - Mpesa</p>
                <p style="margin-top: -10px;">+258 87 123 4567 - Emola</p>

                <!-- Dropzone -->
                <div class="divider text-muted">
                  <span>Envio do Comprovativo</span>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-semibold">Envie a imagem do comprovativo</label>
                  <div class="dropzone text-muted" id="dropzone">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p class="mb-1">Arraste e solte a imagem aqui</p>
                    <p class="text-muted">ou</p>
                    <button type="button" class="btn btn-sm btn-outline-primary">Selecionar arquivo</button>
                    <input type="file" name="proof_file_payment" id="file-input" class="d-none" accept="image/*" required>
                    <div class="invalid-feedback">
                      Por favor, carregue o comprovativo antes de submeter o seu pedido.
                    </div>
                  </div>

                  <div class="preview-container" id="preview-container">
                    <img src="" class="preview-image" id="preview-image" alt="Preview do comprovante">
                    <div>
                      <button type="button" class="btn btn-sm btn-danger" id="remove-image">
                        <i class="fas fa-trash me-1"></i> Remover imagem
                      </button>
                    </div>
                  </div>
                </div>

                <input type="hidden" name="amount_payment" value="<?= $course->price_course ?>">
              </div>


              <!-- <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="newsletter">
              <label class="form-check-label small" for="newsletter">
                Inscrever-se na nossa lista de email
              </label>
            </div> -->

              <button type="submit" class="btn bg-blue-500 text-white w-100 py-2 fw-semibold hover:bg-blue-600">
                Finalizar minha compra
                <i class="fas fa-arrow-right ms-2"></i>
              </button>

              <p class="small text-muted text-center mt-3">
                Ao finalizar a compra, você concorda com nossos
                <a href="#" class="text-blue-500 text-decoration-none">Termos de Serviço</a>
              </p>
            </form>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </main>

  <!-- FAQ Section -->
  <section class="bg-light py-5">
    <div class="container">
      <h2 class="h2 fw-bold text-center mb-4">Perguntas Frequentes</h2>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <!-- Accordion Item -->
          <details class="bg-white rounded-3 shadow-sm mb-3">
            <summary class="d-flex justify-content-between align-items-center p-3 fw-semibold fs-5 cursor-pointer">
              Por quanto tempo terei acesso ao curso?
              <svg class="transition-transform" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </summary>
            <div class="p-3 text-muted border-top">
              Você terá acesso vitalício ao curso, incluindo todas as
              atualizações futuras.
            </div>
          </details>

          <!-- Accordion Item -->
          <details class="bg-white rounded-3 shadow-sm mb-3">
            <summary class="d-flex justify-content-between align-items-center p-3 fw-semibold fs-5 cursor-pointer">
              Há algum pré-requisito para este curso?
              <svg class="transition-transform" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </summary>
            <div class="p-3 text-muted border-top">
              É recomendado ter conhecimento básico de JavaScript. Se você é
              iniciante, sugerimos adicionar o curso "Modern JavaScript from The
              Beginning".
            </div>
          </details>

          <!-- Accordion Item -->
          <details class="bg-white rounded-3 shadow-sm mb-3">
            <summary class="d-flex justify-content-between align-items-center p-3 fw-semibold fs-5 cursor-pointer">
              Receberei um certificado?
              <svg class="transition-transform" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
              </svg>
            </summary>
            <div class="p-3 text-muted border-top">
              Sim, ao completar o curso você receberá um certificado de
              conclusão que pode ser compartilhado no LinkedIn.
            </div>
          </details>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <p class="mb-3">&copy; 2025 <?= $course->title_course ?>. Todos os direitos reservados.</p>
    </div>
  </footer>

  <!-- SCRIPTS (junta ao final da página, depois de carregar o DOM) -->
  <script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (session()->getFlashdata('swal')): ?>
        const swalData = <?= json_encode(session()->getFlashdata('swal')) ?>;
        Swal.fire({
          icon: swalData.icon,
          title: swalData.title,
          text: swalData.text
        });
      <?php endif; ?>
    });

    // Validação do formulário
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();


    // Pagamento Mpesa/Emola
    const radios = document.querySelectorAll('input[name="payment"]');
    const paymentInfo = document.getElementById('payment-info');

    function togglePaymentInfo() {
      if (document.getElementById('mobile').checked) {
        paymentInfo.classList.remove('d-none');
      } else {
        paymentInfo.classList.add('d-none');
      }
    }

    // Escuta quando QUALQUER radio mudar
    radios.forEach(radio => {
      radio.addEventListener('change', togglePaymentInfo);
    });

    // Estado inicial
    togglePaymentInfo();


    // Dropzone
    document.addEventListener('DOMContentLoaded', function() {
      const dropzone = document.getElementById('dropzone');
      const fileInput = document.getElementById('file-input');
      const previewContainer = document.getElementById('preview-container');
      const previewImage = document.getElementById('preview-image');
      const removeImageBtn = document.getElementById('remove-image');
      const form = document.getElementById('checkout-form');

      // Abrir seletor de arquivo
      dropzone.addEventListener('click', function(e) {
        if (e.target.tagName !== 'BUTTON') {
          fileInput.click();
        }
      });

      dropzone.querySelector('button').addEventListener('click', function() {
        fileInput.click();
      });

      // Manipular seleção de arquivo
      fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const file = this.files[0];

          // Verificar se é uma imagem
          if (!file.type.match('image.*')) {
            Swal.fire({
              icon: 'error',
              title: 'Arquivo inválido',
              text: 'Por favor, selecione apenas arquivos de imagem.'
            });
            return;
          }

          const reader = new FileReader();
          reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
            dropzone.style.display = 'none';
          }
          reader.readAsDataURL(file);
        }
      });

      // Remover imagem
      removeImageBtn.addEventListener('click', function() {
        fileInput.value = '';
        previewContainer.style.display = 'none';
        dropzone.style.display = 'flex';
      });

      // Drag and drop
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
      });

      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
      });

      dropzone.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length) {
          fileInput.files = files;
          fileInput.dispatchEvent(new Event('change'));
        }
      });

      // Validação do formulário
      form.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
          e.preventDefault();
          Swal.fire({
            icon: 'warning',
            title: 'Comprovante obrigatório',
            text: 'Por favor, envie o comprovante de pagamento.'
          });
          return;
        }

        // Se quiseres mostrar feedback antes de enviar:
        Swal.fire({
          icon: 'info',
          title: 'Enviando pedido...',
          text: 'Estamos processando o seu comprovante.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });
      });
    });
  </script>
</body>

</html>