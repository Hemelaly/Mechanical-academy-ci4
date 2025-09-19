<?= $this->extend('layouts/master') ?>

<?= $this->section('title') ?>Checkout<?= $this->endSection() ?>

<?= $this->section('checkout') ?>

<style>
    .border {
        border-color: #343554ff !important;
    }

    .card {
        background-color: var(--bs-dark-card);
        border: 1px solid var(--bs-dark-border);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }

    .form-control,
    .form-control:focus {
        background-color: #2d2d2d;
        border: 1px solid #444;
        color: #fff;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25);
        border-color: var(--bs-primary);
    }

    .btn-primary {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .btn-primary:hover {
        background-color: #6d28d9;
        border-color: #6d28d9;
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
        border: 2px dashed #7c3aed;
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
        background: #2b2b45ff;
        border-color: #8b5cf6;
    }

    .dropzone i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #7c3aed;
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

<?php if (isset($enrollment) && $enrollment->status_enrollment == 'Pendente'): ?>
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-md-8 col-lg-6">
                <div class="alert alert-success text-center">
                    <h4 class="alert-heading">O seu pedido de inscrição neste curso está sendo avaliado! Aguarde a confirmação</h4>
                </div>
            </div>
        </div>
    </div>
    <?php return; ?>
<?php endif; ?>

<div class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-8 col-lg-8">
            <div class="card p-4 border">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-white">Finalizar Pagamento</h2>
                    <p class="text-muted">Complete o processo de pagamento enviando o comprovante</p>
                </div>

                <div class="alert alert-info">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">Instruções de Pagamento</h5>
                            <p class="mb-0">Efetue o pagamento via transferência ou depósito para o número abaixo e envie o comprovante através desta página.</p>
                        </div>
                    </div>
                </div>

                <label for="phone" class="form-label fw-semibold">Número de Celular para Pagamento</label>
                <h3 class="text-white">+258 84 000 0000</h3>
                <div class="form-text text-muted">Este é o número para onde você deve enviar o pagamento.</div>

                <form action="/checkout/<?= $course->id_course ?>" enctype="multipart/form-data" method="post" id="checkout-form">
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
                            <input type="file" name="proof_file_payment" id="file-input" class="d-none" accept="image/*">
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

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="fas fa-paper-plane me-2"></i> Finalizar Pedido
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('file-input');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const removeImageBtn = document.getElementById('remove-image');
        const form = document.getElementById('checkout-form');

        // Abrir seletor de arquivo ao clicar no dropzone ou botão
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
                    alert('Por favor, selecione apenas arquivos de imagem.');
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

        // Remover imagem selecionada
        removeImageBtn.addEventListener('click', function() {
            fileInput.value = '';
            previewContainer.style.display = 'none';
            dropzone.style.display = 'flex';
        });

        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropzone.classList.add('dragover');
        }

        function unhighlight() {
            dropzone.classList.remove('dragover');
        }

        dropzone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length) {
                fileInput.files = files;

                // Disparar evento change manualmente
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        }

        // Validação do formulário
        form.addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Por favor, envie o comprovante de pagamento.');
                return;
            }

            // Simulação de envio
            alert('O seu pedido foi enviado! Por favor, aguarde até terminarmos de verificar, e lhe notificaremos por email.');
            previewContainer.style.display = 'none';
            dropzone.style.display = 'flex';
        });
    });
</script>

<?= $this->endSection() ?>