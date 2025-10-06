<?php

$session = session();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout - <?= $course->title_course ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .container {
      max-width: 980px !important;
    }

    body {
      font-family: "Inter", sans-serif;
      background-color: #fff;
      color: #000;
    }

    /* ==============================
       HEADER / HERO
    ============================== */
    .hero {
      color: #fff;
      position: relative;
      overflow: hidden;
      padding: 4rem 1rem;
      text-align: center;
      width: 100%;
      height: 25vh;
    }

    .hero::before,
    .hero::after {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background-image: url(<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>);
      background-size: cover;
      background-position: center center;
    }

    .hero h1 {
      font-weight: 800;
      font-size: 2.8rem;
    }

    .hero h1 span {
      color: #ffcc00;
    }

    .hero small {
      display: block;
      font-size: 1rem;
      color: #ccc;
      margin-top: 0.5rem;
    }

    /* ==============================
       MAIN CONTENT
    ============================== */
    .course-section {
      padding: 4rem 1rem;
    }

    .course-thumb {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .features {
      margin-top: 2rem;
      list-style: none;
      padding: 0;
    }

    .features li {
      margin-bottom: 0.5rem;
      padding-left: 1.2rem;
      position: relative;
    }

    .features li::before {
      content: "•";
      color: #ffcc00;
      position: absolute;
      left: 0;
      font-size: 1.2rem;
      line-height: 1;
    }

    .testimonial {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 1rem;
      margin-top: 1rem;
      font-size: 0.95rem;
      border: 1px solid #eee;
    }

    .testimonial strong {
      display: block;
      margin-top: 0.5rem;
    }

    /* ==============================
       PRICE BOX (RIGHT SIDE)
    ============================== */
    .gradient-bg {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }

    .course-card {
      box-shadow: 0px 10px 0px 0px #dcdcdcff;
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

    .rounded {
      border-radius: 10px !important;
    }

    footer {
      text-align: center;
      padding: 2rem 1rem;
      font-size: 0.9rem;
      color: #777;
    }

    @media (max-width: 991px) {
      .hero {
        padding: 3rem 1rem;
      }

      .hero h1 {
        font-size: 2.2rem;
      }

      .price-box {
        position: static;
        margin-top: 2rem;
      }
    }
  </style>
</head>

<body>

  <!-- HERO -->
  <section class="hero">
    <div class="container">
    </div>
  </section>

  <!-- CONTENT -->
  <section class="course-section">
    <div class="container">
      <div class="row g-5 align-items-start">
        <!-- LEFT -->
        <div class="col-lg-7">
          <div class="course-thumb mb-4">
            <img src="<?= base_url('assets/instructor/img/courses/' . $course->image_course) ?>" alt="Modern JavaScript" class="img-fluid rounded w-100">
          </div>

          <h3 class="fw-bold mb-3"><?= $course->title_course ?></h3>

          <ul class="features">
            <li>37 horas de vídeo sob demanda</li>
            <li>20+ recursos para download</li>
            <li>Documentação completa para cada vídeo e todos os códigos</li>
            <li>Acesso vitalício</li>
            <li>Acesso via dispositivos móveis e TV</li>
            <li>Certificado de conclusão</li>
            <li>Acesso à comunidade no Discord</li>
          </ul>

          <h5 class="fw-bold mt-5 mb-3">O que as pessoas estão dizendo:</h5>

          <div class="testimonial">
            “Conteúdo excelente, tanto para profissionais quanto para iniciantes. Este é mais um exemplo do porquê o Brad é tão bem-sucedido como instrutor.
            OURO é difícil de encontrar se você não souber onde procurar nesta área.”
            <strong>— Benny V.</strong>
          </div>

          <div class="testimonial">
            “Brad é o melhor. Todos os cursos dele são incríveis. A minha parte favorita é o quanto ele é detalhista e nunca se esquece dos iniciantes.”
            <strong>— Brandon W.</strong>
          </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-5">
          <div class="course-card shadow bg-white rounded-3 p-4 sticky-top">
            <div class="mb-4">

              <p class="h2 fw-bold text-dark mb-1"><?= number_format($course->price_course, 2, ",", ".") ?> MZN</p>
            </div>

            <?php if ($isEnrolled): ?>
              <div class="alert alert-success text-center">
                <h4 class="alert-heading">Você já está inscrito neste curso!</h4>
                <p>Já tem acesso completo ao curso <?= $course->title_course ?>.</p>
                <hr>
                <a href="<?= base_url('/student/dashboard/meus_cursos') ?>" class="btn btn-primary">Ir para meus cursos</a>
              </div>
            <?php elseif (($user) && ($user->role == "instructor")): ?>
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
                  <div class="input-group">
                    <input type="text" class="form-control text-sm" id="coupon" placeholder="Código do Cupom">
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
                  <a href="<?= base_url('/login') ?>" type="button" class="w-100 mb-3 nav-link text-primary fw-bold text-end">
                    Fazer login
                  </a>

                  <div class="mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="Endereço de Email" required>
                    <div class="invalid-feedback">
                      Por favor, insira um email válido.
                    </div>
                  </div>

                  <div class="mb-3">
                    <input type="text" name="username" class="form-control" id="name" placeholder="Nome e Sobrenome" required>
                    <div class="invalid-feedback">
                      Por favor, insira seu nome e sobrenome.
                    </div>
                  </div>
                <?php endif; ?>


                <div class="mb-3">
                  <div class="d-flex flex-column gap-4">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="payment" id="mobile">
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
  </section>

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